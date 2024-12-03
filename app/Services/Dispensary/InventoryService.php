<?php

namespace App\Services\Dispensary;

use App\Services\BaseService;

use App\Http\Resources\Dispensary\InventoryCollectionExport;

use App\Models\Dispensary\Inventory;
use App\Models\Dispensary\InventoryStrain;
use App\Models\Dispensary\InventoryLog;
use App\Models\Dispensary\Product;
use App\Models\Dispensary\Sale;
use App\Models\Dispensary\SaleItem;
use App\Models\Dispensary\Receiving;

use App\Helpers\Util;
use App\Helpers\Generator;
use App\Events\InventoryItemUpdated;

use Auth;
use Exception;
use DB;
use stdClass;
use Carbon\Carbon;
use Excel;
use Storage;
use Log;


class InventoryService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;

    public function searchByBarcodeOrRfid($barcode) {

        if (!$barcode) abort(422,'Whoops - No barcode presented');

        // inventory is scoped to location
        // Make sure we're sorting by oldest, first, as we want to take inventory using FIFO system:
        $query = Inventory::query()
            ->with('receiving.vendor','product.category','pricing')
            ->ofExactBarcodeRfidSku($barcode)
            ->orderBy('created_at', 'asc')
            ->limit(10);

        return $query->get();
        
    }


    public function search(array $data){
        
        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);

        $query = Inventory::query()
            ->with('receiving.vendor','product.category','taxes.rates','pricing')
            ->withCount('adjustments')
            ->ofListFilters($filters)
            ->ofTextFilter($search);

        if(data_get($data,'is_expiring',0)==1)                                  // if an is_expiring=1 param is included.
            $query->whereNotNull('expires_at');

        if(($mcat = data_get($data,'typ',0))!='0'){                             // scope by metrc category
            $query->whereHas('product.category',function($q)use($mcat){
                $q->where('id',$mcat);
                //$q->where('metrc_category_type',$mcat);
            });
        }

        if(data_get($data,'archived',0)==0) $query->ofActive();                 // unless we are searhing w archived toggle on, then only get active.
        if(data_get($data,'is_avail',0)==1) $query->where('quantity_on_hand','>=',1)->ofActive();

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('updated_at', 'desc');
        

        return $query->paginate($limit);
        
    }
    

    /*  on hand */
    public function onHand(array $data){

        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);

        $query = Inventory::query()
            ->with('receiving.vendor','product.category','taxes.rates','auditor')
            ->ofListFilters($filters)
            ->ofTextFilter($search)
            ->ofNatureType(data_get($filters,'nature_type','all'));

        if(data_get($data,'archived',0)==0) $query->ofActive();                 // unless we are searhing w archived toggle on, then only get active.

        if(($low = data_get($data,'custom_filters.useStockLow',false))=='true') // Low quantity filter
            $query->where('quantity_on_hand','<=',(int)data_get($data,'custom_filters.stockLow',0));

        if(($exp = data_get($data,'custom_filters.useStockExpire',false))=='true') // Low quantity filter
            $query->whereNotNull('expires_at')->where('expires_at','<=',Carbon::parse(data_get($data,'custom_filters.stockExpire',Carbon::now()->toDateTimeString()))->toDateTimeString());

        if(($last = data_get($data,'custom_filters.useStockLast',false))=='true') // last received filter
            $query->whereNotNull('created_at')->where('created_at','<=',Carbon::parse(data_get($data,'custom_filters.stockLast',Carbon::now()->subMonths(3)->toDateTimeString()))->toDateTimeString());


        if($sort=='product_id') $query->orderBy('product.name');
        elseif($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('created_at','desc');

        return $query->paginate($limit);

    }


    /* get Inventory record */
    public function show(array $data,$id){
        
        return Inventory::query()
            ->with('receiving.vendor','product.category','product.pricing','taxes.rates','adjustments','location')
            ->where('id',$id)
            ->orderBy('created_at','desc')
            ->first();
            
    }

    /* show strain index manager */
    public function strainShow(array $data){
        
        return InventoryStrain::query()
            ->orderBy('name')
            ->get();                                                            // get all strains - the form is a list to edit/add to.
            
    }
    

    /* get a batch (abbreviated) collection */
    public function getBatch(array $data){

        $ids = explode(',',data_get($data,'batch_ids',''));
        
        $query = Inventory::query()
            ->with('product.lastReceived.vendor')
            ->whereIn('id',$ids)
            ->orderBy('updated_at')
            ->ofActive();


        return $query->get();
        
    }
    
    
    /* Receive Inventory Record (from a receiving call) */
    public function create($data,$rec,$user=null){
        
		if(!$user->location) abort(422,'User is not properly regiatered to this location');
		elseif(data_get($data,'product.nature_type','noncannabis')!='noncannabis' && !data_get($data,'metrc_tag')) abort(409,'NEEDS_METRC_TAG'); // Ensure a thc product contains a metrc tag
	    //elseif(data_get($data,'product.category.contains_thc',false)===true && !data_get($data,'metrc_tag')) abort(409,'NEEDS_METRC_TAG'); // Ensure a thc product contains a metrc tag

        $tag = data_get($data,'metrc_tag',Generator::uuid4());
        $item = Inventory::where('receiving_id',$rec->id)->where('metrc_tag',$tag)->where('product_id',data_get($data,'product_id',null))->first() ?: new Inventory; //only 1 package per transfer
        
            $item->product_id           = data_get($data,'product_id',null);
            $item->addressbook_id       = $rec->addressbook_id;
            $item->receiving_id         = $rec->id;
            $item->priceset_id          = data_get($data,'priceset_id',null);
            $item->metrc_tag            = data_get($data,'metrc_tag',null);
            $item->metrc_parent_tags    = null; // future use
            $item->metrc_product_id     = data_get($data,'package.metrc_product_id',null);
            $item->grow_product_id      = data_get($data,'package.grow_product_id',null);
            
            //$item->item_barcode         = (data_get($data,'product.nature_type',null)=='noncannabis' ? (data_get($data,'item_barcode',null)!=null ? Generator::genBarcode() : null) : Generator::genBarcode(['metrc_tag'=>$item->metrc_tag,'category_id'=>data_get($data,'product.category_id',null)]));
            $item->item_barcode         = data_get($data,'item_barcode',null) ?: (data_get($data,'product.nature_type',null)=='noncannabis' ? Generator::genBarcode() : Generator::genBarcode(['metrc_tag'=>$item->metrc_tag,'category_id'=>data_get($data,'product.category_id',null)]));
            
            $item->item_batch           = data_get($data,'package.package_data.SourceHarvestNames',null);
            $item->item_strain          = substr(data_get($data,'package.package_data.ItemStrainName',''),0,175);
            $item->item_notes           = null; // future use
            $item->quantity_received    = data_get($data,'quantity_received',null);
            $item->quantity_requested   = data_get($data,'quantity_requested',null);
            $item->quantity_on_hand     = data_get($data,'quantity_received',null);
            $item->unit_of_measure      = data_get($data,'unit_of_measure',data_get($data,'package.uom',null));
            $item->amount_unit          = data_get($data,'amount_unit',1);
            $item->weight_potency       = data_get($data,'weight_potency',null);
            $item->cost_unit            = data_get($data,'cost_unit',0.01);
            $item->retail_unit          = data_get($data,'retail_unit',0.02);
            $item->expires_at           = (($exp = data_get($data,'expires_at',null))!=null ? Carbon::createFromTimestamp(strtotime($exp))->toDateTimeString() : null);  // vue has wierd timestmaps

        $item->save();      // new inventory item received!

        if($item->product && $item->priceset_id){                               // attach the priceset to the product (for receiving and inventory adjustment forms)
            $item->product->pricing()->syncWithoutDetaching($item->priceset_id);
            if($item->product->category)
                $item->product->category->pricing()->syncWithoutDetaching($item->priceset_id);
        }
        
        // if product is part of product grouping and the has a restock policy - then, attach it to..
        foreach(Product::with('grouped')->whereIn('type',['grouping','kit'])->whereHas('grouped',function($q)use($item){
            $q->where('product_id',$item->product_id);
        })->get() as $grps)
            if(($lg = $grps->grouped->where('pivot.policy','restock')->firstWhere('product_id',$item->product_id))!=null)
                $grps->grouped()->syncWithoutDetaching([$item->id => ['quantity'=>$lg->pivot->quantity,'policy'=>$lg->pivot->policy]]);
        
        
        return $item;

    }


    /* update Inventory*/
    public function update($data,$id,$user=null){
        
        if(($upd = Inventory::with('product.category')->find($id)) == null) abort(400,'Whoops, Inventory Data is temporarially disonnected - please try again later');

        $upd->fill($data);


        /* if we have conversions */
        if((!empty($conversions = data_get($data,'conversions',[])))){
            
            /* a. get new tally of converted item - make adjustment */
            $tally = (collect($conversions)->reduce(function($cry,$itm){        // what is left
                return $cry - (data_get($itm,'quantity_received',0)*data_get($itm,'amount_unit',1));
             },($upd->quantity_on_hand*$upd->amount_unit))/($upd->amount_unit ?: 1));
            
            $remains = ($upd->quantity_on_hand - ($upd->unit_of_measure=='ea' ? floor($tally) : round($tally,4)));
            $waste = 0; // TODO for metrc - do we have waste from the converisons (ie: shake isnt enough to account for 1 prepackaged unit)
            
            $upd->quantity_adjust = ($upd->quantity_adjust + ($remains*-1));

            $log = new InventoryLog;
            $log->inventory_id = $upd->id;
            $log->fill([
                'type'      => 'quantity',
                'value'     => ($tally*-1),
                'reason_code'=> 'conversion',
                'notes'     => 'Converted into other items on '.Util::localDate('m/d/y')
            ]);
            $log->save();

            // TODO dispatch a job to update metrc on the conversion of package adjustmnet and any waste
            
            
            
            /* b. receive converted items into inventory */
            $rec = new Receiving();
            $rec->po_number = 'CONVERSION-'.Carbon::now()->format('mdy');
            $rec->type = 'custom';
            $rec->addressbook_id = $upd->addressbook_id;
            $rec->save();
            
            $rec->articles_received = 0;
            foreach($conversions as &$item){
                try{
                    $newItem = $this->create($item,$rec,$user);
                    $rec->articles_received++;
                    data_set($item,'status','received',true);
                    data_set($item,'item_barcode',$newItem->item_barcode);
                    data_set($item,'inventory_id',$newItem->id);
                }catch(Exception $e){
                    data_set($item,'status','exception',true);
                    Log::warning($e);
                }
            }
         
            $rec->items = $conversions;
            $rec->status = 'received';
            $rec->received_at = Carbon::now()->toDateTimeString();
            $rec->save();
            
        }


        /* log adjustment */
        if(($adj = data_get($data,'adjustment.value',0)) != 0){
        
            $upd->quantity_adjust = ($upd->quantity_adjust + (float)$adj);

            $log = new InventoryLog;
            $log->inventory_id = $upd->id;
            $log->fill(data_get($data,'adjustment',null));
            $log->save();


            // TODO dispatch a job to update metrc on the adjustmnet

            
        }
        
        $upd->audited_at = Carbon::now()->toDateTimeString();                   // record the auditor and time for this update.
        $upd->audited_by = $user->id;
        $upd->save();
        
        if($upd->product && $upd->priceset_id){                                 // attach the priceset to the product (for receiving and inventory adjustment forms)
            $upd->product->pricing()->syncWithoutDetaching($upd->priceset_id);
            if($upd->product->category)
                $upd->product->category->pricing()->syncWithoutDetaching($upd->priceset_id);
        }
        
        if($adj!=0 || !empty($conversions))
            $this->_syncItemsTransactions([$upd->id]);                          // recon inventory and reload w result..


        return $this->show([],$upd->id);
        
    }


    public function strainUpdate($data,$id,$user=null){
        
        $registry = collect([]);
        foreach(data_get($data,'strains',[]) as $row){                          // update same type/amount to avoid user mistaken duplication
            $strain = InventoryStrain::where(function($q)use($row){
                $q->where('id',data_get($row,'id',0))->orWhere('name',data_get($row,'name',null));
            })->first() ?: new InventoryStrain;
            $strain->name = strtolower(data_get($row,'name','misc'));
            $strain->notes = data_get($row,'notes',null);
            $strain->save();
            $registry[] = $strain;
        }
        
        // delete anything not registered from this post
        DB::table('dispensary_inventory_strains')->whereNotIn('id',$registry->pluck('id')->toArray())->update(['deleted_at' => Carbon::now()->toDateTimeString()]);

        return [
            'status'    => 200,
            'message'   => 'Updated Strain Index with '.$registry->count().' Strains',
            'schema'    => Inventory::_getSchema()                              // include schema as these batch changes may update the scope of some filters.
        ];

        
    }


    /* update batch edit data */
    public function updateBatchData($data,$user=null){
        
        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no Product batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach($batch as $row){
            
            if(!isset($row['id'])) continue;
            elseif(($store = Inventory::find($row['id'])) == null) continue;

            $store->fill($row);
            $store->save();
            $saved++;
            
        }
        

        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'Updated '.$saved.' of '.count($batch).' Records - Please check updates below..' : 'Successfully Updated '.$saved.' of '.count($batch).' Records!'),
            'schema'    => Inventory::_getSchema()                            // include schema as these batch changes may update the scope of some filters.
        ];

    }
    
    
    /* archive batch list */
    public function updateBatchAudit($data,$user=null){
        
        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no Product batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach(Inventory::whereIn('id',$batch)->get() as $store){
            
            $store->audited_at = Carbon::now()->toDateTimeString();             // record the auditor and time for this update.
            $store->audited_by = $user->id;
            $store->save();
            $saved++;
            
        }
        

        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'You Checked '.$saved.' of '.count($batch).' Records - Please review updates below..' : 'Successfully Checked '.$saved.' of '.count($batch).' Records!'),
            'schema'    => Inventory::_getSchema()                              // include schema as these batch changes may update the scope of some filters.
        ];

    }


    /* process an export */
    public function exportCollection($data,$typ,$file){
        
        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Inventory_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all') 
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;
        
        
        return $this->export(new InventoryCollectionExport($data),$file,$name,$typ,[]);

    }

    /* removal service */
    public function archive($data,$id,$user=null){
        
        if(($record = Inventory::find($id)) == null) abort(409,'Cannot Archive - No Record found');

        // Any conditions to not archive, speak now or forever hold youre peace..
        abort(409,'Archiving Inventory Records is not allowed.');

        //$record->archived_at = Carbon::now()->toDateTimeString();
        //$record->save();        
        //$record->delete();                                                    // A soft-delete

        return true;
        
    }




	/**                                         **/
	/**            Inventory Actions            **/
	/**                                         **/

    /* THC equavalency amount calculations.. */
    protected function _getUnitThcAmount ($itemId) {

        if(($item = Inventory::with('product.category')->where('id',$itemId)->first())==null) return false;
        
        $schema = collect(data_get(AppSchema::getSchema('category_schema'),'form.equivalency_type.values',[]))->firstWhere('id',data_get($item,'product.category.equivalency_type','noncannabis'));
        $ratio = data_get($schema,'value',0);
        $type = data_get($schema,'prompt','amount');
        $quantity = 1;
        
        switch($type){
            case 'amount':
                return ($item->amount_unit * $quantity) * $ratio;
                break;
            case 'potency':
                return (($item->weight_potency * $quantity) * $ratio);          // the weight_potency (in grams) times quantity, then ratio
                break;
            default:                                                            // weight.  the quantity is weighed in grams, so simply multiply by the metrc category ratio
                if(data_get($item,'unit_of_measure','ea')=='g') return ($quantity * $ratio);
                else return ($item->amount_unit * $quantity * $ratio);          // if selling as ea, and the equivalency type prompt is weight based, then use the amount_unit of grams x quantity
        }

    }


	/* sync items inventory quantities from items sold in confirmed transactions */
	public function _syncSaleOrders($ids=[]){

	    if(!is_array($ids)) abort(422,'INCORRECT_SALEORDER_IDS_ARRAY');
	    return self::_syncItemsTransactions(SaleItem::whereIn('sale_id',$ids)->groupBy('inventory_id')->pluck('inventory_id')->toArray());
	    
	}
	
	public function _syncSaleOrder($id=null){
	    
	    if($id==null) abort(422,'INCORRECT_SALE_ID');
	    return self::_syncItemsTransactions(SaleItem::where('sale_id',$id)->groupBy('inventory_id')->pluck('inventory_id')->toArray());
	    
	}
	
	
	/* do a recon on all sales order items passed */
	protected function _syncItemsTransactions($items=[]){       
	        
	    if(!is_array($items)) return 'INCORRECT_ITEMIDS_ARRAY';
	    elseif(($user = Auth::guard()->user()) == null) return 'USER_NOT_AUTHENTICATED';
        elseif(!$user->location) return 'NO_USER_LOCATION';
	    
	    $updated = 0;
	    $itemQtyMap = [];

        
        // first, delete any sale log entries for this item, as we are about to reconcille
        DB::table('dispensary_inventory_logs')->where('type','sale')->whereIn('inventory_id',$items)->delete();

		// Then, map total quantities sold for each inventory item we need to sync
    	foreach(SaleItem::select('id','sale_id','inventory_id','quantity','thc_equivalent_grams','created_at','updated_at')
	    		->whereIn('inventory_id',$items)
	    		->whereHas('sale',function($q){ $q->whereIn('status',['settled','refunded']); })
	    		//->whereNull('migrated_at')  //dont include migrated sale items in this recon.
	    		->orderBy('created_at')->get() as $sold){

	    	    $log = new InventoryLog;
                $log->inventory_id = $sold->inventory_id;
                $log->type = 'sale';
                $log->value = $sold->quantity*-1;
                $log->reason_code = 'na';
                $log->created_at = $sold->created_at;                           // sync the date the item was sold for the log
                //$log->synced_at = date('Y-m-d H:i:s', time());                // log current UTC time for last sync
                $log->notes = ($sold->quantity>=0 ? 'Purchased via POS' : 'Returned Via POS');
                $log->save();
	    	    
	    	    data_set($itemQtyMap,$sold->inventory_id,(data_get($itemQtyMap,$sold->inventory_id,0)+$sold->quantity),true);
	    }


		// Finally, sync the recon: quantity_received - quantity_sold + quantity_adjusted for each inventory item
		foreach(Inventory::whereIn('id',$items)->get() as $item){ 
    	    $item->quantity_sold = (float)data_get($itemQtyMap,$item->id,0);
    		$item->quantity_on_hand = (float)($item->quantity_received - $item->quantity_sold + $item->quantity_adjust);
    		
    		if($item->quantity_on_hand > 0 && $item->archived_at)
                $item->archived_at = null;                                       // unarchive if we now have stock
    		
    		$item->save();
    		$updated++;
        }
        
        try {
            $items = Inventory::with('receiving.vendor','product.category.type','taxes.rates')->withCount('adjustments')->whereIn('id',$items)->get(['id','quantity_on_hand','quantity_pending']);
            event(new InventoryItemUpdated($user, $items->toJson()));
        } catch(\Exception $e){
                //TODO: handle error because the socket service is temporarily down
        }


	    return $updated;
	    
	}



    /* get the current price of an item, considering quantity and passigned pricing product_set */
    public function _getCurrentPrice($data,$id,$user=null){
        
        if(($item = Inventory::with('pricing')->find((int)$id)) == null) throw new Exception('No Inventory item Found');
        
        $_qty = (float)data_get($data, 'quantity', 1);
        $_name = 'fixed';
        $_price = 0;
        

        /* Get the current physical price assigned based on qty via the priceset or fixed retail price */
        if(($set = $item->pricing)==null) $_price = $item->retail_unit*$_qty;   // if there is no pricing set/table, simply take the retail_unit price
        elseif(!$set->amount_tiers && !$set->amount_default) $_price = ($set->amount_default ? $set->amount_default*$_qty : $item->retail_unit*$_qty); // no price set data we can work with
        else{
            
            $_tiers = $set->amount_tiers;
            $_name = $set->name_grade;
            usort($_tiers, function($a, $b) { return ($a->amount < $b->amount) ? -1 : 1; }); // just in case, we need the tiers to be in ascending order
            
            if(($heavy = data_get($data,'quantity_priced_at',0))>0) $_qty = $heavy;
            switch($set->type_uom){
                case 'g':
                    $_thresh = ($item->amount_unit_sale ? $item->amount_unit_sale : 1)*$_qty; // if set is in grams, take amount_unit in case saleable item is a preset qty of metrc grams
                    break;
                case 'mg':
                    $_thresh = ($item->amount_unit_sale ? $item->amount_unit_sale*1000 : 1000)*$_qty; // TODO future use
                    break;
                case 'ea':
                    $_thresh = 1;
                    break;
                default: $_thresh = ($item->amount_unit_sale ? $item->amount_unit_sale : 1)*$_qty;
            }
            
            foreach($_tiers as $tier)                                           // loop through each (ascending ordered) tier
                if($tier->amount <= $_thresh)                                   // if we matched amount thershold, assign the price
                    $_price = $tier->price*$_qty;

        }

        if($_price<=0) $_price = ($set->amount_default ? $set->amount_default*$_qty : $item->retail_unit*$_qty); // failsafe


        return [
                'current_price'         => $_price,
                'pricing_name'          => $_name
            ];
        
    }
    
    
    /* update the strain index service */
    public function _saveStrain($name=null){
        
        if(!$name) return false;
        
        $strain = InventoryStrain::where('name',$name)->first() ?: new InventoryStrain;
        $strain->name = strtolower($name);
        $strain->save();
        
        return true;
        
    }
    
    

}