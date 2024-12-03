<?php

namespace App\Services\Dispensary;

use App\Services\BaseService;

use App\Http\Resources\Dispensary\ReceivingCollectionExport;

use App\Models\Dispensary\Receiving;
use App\Models\Dispensary\ReceivingPackage;
use App\Models\Dispensary\ReceivingTransfer;
use App\Models\Dispensary\Product;
use App\Models\Dispensary\Category;
use App\Models\Dispensary\CategoryMetrc;

use App\Models\Auth\Addressbook;

use App\Services\Metrc\MetrcRequestService;
use App\Services\Dispensary\InventoryService;

use App\Helpers\Util;
use App\Helpers\Generator;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use Excel;
use Storage;
use Log;


class ReceivingService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;


    public function search(array $data){
        
        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);

        $query = Receiving::query()
            ->with('vendor')
            ->ofListFilters($filters)
            ->ofTextFilter($search)
            ->ofActive();

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('updated_at', 'desc');


        return $query->paginate($limit);
        
    }

    public function searchTransfers(array $data){
        
        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);

        $query = ReceivingTransfer::query()
            ->with('packages')
            ->ofListFilters($filters)
            ->ofTextFilter($search)
            ->whereDoesntHave('receiving')
            ->ofActive();

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('created_at', 'desc');


        return $query->paginate($limit);
        
    }

    public function searchPackages(array $data){
        
        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);

        $query = ReceivingPackage::query()
            ->with('vendor')
            ->ofListFilters($filters)
            ->whereDoesntHave('inventory')
            ->whereNotNull('received_at')
            ->whereNotNull('package_data')
            ->ofTextFilter($search)
            ->ofActive();

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('created_at', 'desc');


        return $query->paginate($limit);
        
    }
    

    /* get Receiving record */
    public function show(array $data,$id){
        
        return Receiving::query()
            ->with('vendor','transfer')
            ->where('id',$id)
            ->first();
            
    }
    
    
    /* create Receiving record  */
    public function create($data,$user=null){
        
        /* new record */
        $rec = new Receiving();
        $rec->fill($data);
        
        $rec->type = data_get($data,'type','purchaseorder'); // for traditional create a po (vs metrc import)
        $rec->status = data_get($data,'status','confirmed');
        
        
        $rec->save();
        
        return $this->show([],$rec->id);
        
    }


    /* update Receiving*/
    public function update($data,$id,$user=null){
        
        if(($upd = Receiving::find($id)) == null) abort(400,'Whoops, Receiving Data is temporarially disonnected - please try again later');

        $upd->fill($data);
        $upd->save();
        
        
        $items = data_get($data,'items',[]);
        
        /* receiving hook */
        if(data_get($data,'action',null)=='received'){
            
            $invService = new InventoryService();
            $upd->articles_received = 0;
            
            foreach($items as &$item){
                try{
                    $newItem = $invService->create($item,$upd,$user);
                    $upd->articles_received++;
                    data_set($item,'status','received',true);
                    data_set($item,'item_barcode',$newItem->item_barcode);
                    data_set($item,'inventory_id',$newItem->id);
                }catch(Exception $e){
                    data_set($item,'status','exception',true);
                    Log::warning($e);
                }
            }
         
            $upd->status = 'received';
            $upd->received_at = Carbon::now()->toDateTimeString();
            
        }else{
            
            /* ensure items.item_barcode is generated */
            foreach($items as &$item)
                if(data_get($item,'item_barcode',null)==null)
                    data_set($item,'item_barcode',Generator::genBarcode([
                        'metrc_tag'     => data_get($item,'metrc_tag',null),
                        'category_id'   => data_get($item,'product.category_id',null)]),true); // generate barcode if not exist
                    
        }


        $upd->items = $items;
        $upd->save();
        return $upd;
        
    }

    
    /* process an export */
    public function exportCollection($data,$typ,$file){
        
        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Receiving_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all') 
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;
        
        
        return $this->export(new ReceivingCollectionExport($data),$file,$name,$typ,[]);

    }
    
    
    /* removal service */
    public function archive($data,$id,$user=null){
        
        if(($record = Receiving::find($id)) == null) abort(409,'Cannot Archive - No Record found');

        // Any conditions to not archive, speak now or forever hold youre peace..
        

        $record->archived_at = Carbon::now()->toDateTimeString();
        $record->save();        
        //$record->delete();                                                    // A soft-delete

        return true;
        
    }
    
    
    /* 4.1.20 auto-generate a series of pos by vendorlist.items */
    public function generate($data,$user){
        
        if(!is_array(($vendors = data_get($data,'vendorlist',[])))) abort(422,'the vendor list is incorrectly formatted - please send as list/array.');
        elseif(count($vendors)<=0) abort(409,'There are no vendor(s) to generate a PurchaseOrder for - aborting.');
        
        $created = [];
        
        
        /* go thru each vendor and the selected items/qty - last received is used to generate po/items data */
        foreach($vendors as $vend){
            
            $adr = Addressbook::where('id',data_get($vend,'addressbook_id',null))->first();
            $sel = data_get($vend,'items',[]) ?: [];
            
            $rec = new Receiving;
            $rec->addressbook_id = data_get($adr,'id',data_get($vend,'addressbook_id',null));
            $rec->po_number = data_get($vend,'po_number','RESTOCK-'.date('mdY',time()));
            $rec->type = (data_get($user->location,'settings.regulatory_agent','metrc')=='metrc' ? 'metrc' : 'purchaseorder');
            $rec->status = 'confirmed';
            $rec->confirmed_at = Carbon::now()->toDateTimeString();

            $rec->items = collect($sel)->map(function($item,$key)use($adr){
                
                if(($prod = Product::with('category')->where('id',data_get($item,'product_id',null))->first())!=null) // product/inventory and last received data
                    return (object)[
                        'id'                => 'AGP-'.data_get($item,'id',rand(444,44444)),
                        'product'           => $prod ?: null,
                        'package'           => null,                                // if metrc is regulatory agent, and is medicated, po form will prompt for recent package of vendor
                        'priceset_id'       => data_get($prod,'inv_meta.priceset',null),
                        'metrc_tag'         => null,
                        'product_id'        => data_get($prod,'id',null),
                        'item_barcode'      => null,                                // this will be generated at point of receiving, then tags can be printed..
                        //'item_barcode'      => Generator::genBarcode(['metrc_tag'=>$item->label,'category_id'=>data_get($prod,'category.id','12345')]),
                        'item_strain'       => data_get($prod,'inv_meta.strain','misc'),
                        'unit_of_measure'   => data_get($prod,'inv_meta.uom','ea'),
                        'amount_unit'       => data_get($prod,'inv_meta.amount_unit',1),
                        'weight_potency'    => data_get($prod,'inv_meta.weight_potency',1),
                        'cost_unit'         => data_get($prod,'inv_meta.cost',0.01),
                        'retail_unit'       => data_get($prod,'inv_meta.retail',0.02),
                        'expires_at'        => null,
                        'quantity_requested'=> data_get($item,'quantity_requested',10),
                        'quantity_received' => data_get($item,'quantity_requested',10),
                        'quantity_split'    => null,
                        'unit_price'        => 0,
                        'can_delete'        => false,
                        '_showSplit'        => false,
                        '_showDetails'      => ($prod->nature_type=='noncannabis' ? false : true)
                    ];
            })->filter()->values()->toArray();


            $rec->save();
            $created[] = $rec->id;

        }


        return $created;                                                        // this returns a list of successfully created receiving (purchaseorder) ids
        
    }
    
    

    /* SYNC METRC TRANSFERS SERVICE */
    public function syncMetrcTransfers($user,$start,$caller='api'){

            $api = new MetrcRequestService;
            $count = 0;
            $transfers = [];


            for($i=$start;$i<=time();$i+=86400){                                // for each day in period, cause metrc only lets you fetch transfers per 24hr period
                foreach(($api->getIncomingTransfers($user,Carbon::createFromTimestamp($i),Carbon::createFromTimestamp($i+86399)) ?: []) as $transfer){
                    data_set($transfer,'hotbox_status',(data_get($transfer,'ReceivedDateTime',null) ? 'confirmed' : 'intransit'),true);
                    $transfers[] = $transfer;
                }
                foreach(($api->getRejectedTransfers($user,Carbon::createFromTimestamp($i),Carbon::createFromTimestamp($i+86399)) ?: []) as $transfer){
                    data_set($transfer,'hotbox_status','rejected',true);
                    $transfers[] = $transfer;
                }
            }


            foreach($transfers as $row){                                        // map all metrc transfers

                $trans = ReceivingTransfer::where('metrc_id',data_get($row,'Id',null))->orderBy('created_at','desc')->first() ?: new ReceivingTransfer;
                
                $trans->metrc_id = data_get($row,'ManifestNumber',null);
                $trans->manifest_id = data_get($row,'ManifestNumber',null);
                $trans->vendor_ref = data_get($row,'ShipperFacilityName',null);
                $trans->vendor_licensenum = data_get($row,'ShipperFacilityLicenseNumber',null);
                
                $trans->manifest_data = $row;                                   // store the returned data for later uses
                
                $trans->status = data_get($row,'hotbox_status','unknown');
                $trans->metrc_status = 'synced';
                $trans->received_at = (($rec = data_get($row,'ReceivedDateTime',null))!=null ? Carbon::createFromTimestamp(strtotime($rec))->toDateTimeString() : null);
                $trans->created_at = (($rec = data_get($row,'CreatedDateTime',null))!=null ? Carbon::createFromTimestamp(strtotime($rec))->toDateTimeString() : null);
                $trans->save();
                
                // add/edit vendor to addressbook.
                if(Addressbook::where('licensenum',$trans->vendor_licensenum)->count()<=0)
                    $vend = Addressbook::create([
                        'location_id'       => $user->location_id,
                        'type'              => 'vendor',
                        'name'              => $trans->vendor_ref,
                        'licensenum'        => $trans->vendor_licensenum
                    ]);

                $count++;
            }
            
            
            /* then do a package sync if behind */
            if(($packStart = data_get($user->location->settings,'metrc_last_package_sync',(time()-(86400*14))))<$start || 1==1)
                $success = $this->syncMetrcPackages($user,$packStart,'internal');
            

            $user->location->_setSetting('metrc_last_transfer_sync',time())->save();
            return ($caller=='api' ? [
                'status'    => 200,                
                'count'     => $count,
                'message'   => 'Checked with Metrc and Synced '.$count.' Newly Updated Transfers',
                'schema'    => Receiving::_getSchema()                          // include schema as any new metrc categories will be injected there, and need be used in a vue form
            ] : true);
            
    }


    /* SYNC METRC TRANSFERS SERVICE */
    public function syncMetrcPackages($user,$start,$caller='api'){

            $api = new MetrcRequestService;
            $count = 0;
            $packages = [];

            for($i=$start;$i<=time();$i+=86400){                                // for each day in period, cause metrc only lets you fetch transfers per 24hr period
                foreach(($api->getActivePackages($user,Carbon::createFromTimestamp($i),Carbon::createFromTimestamp($i+86399)) ?: []) as $package){
                    data_set($package,'hotbox_status',(data_get($package,'ReceivedDateTime',null) ? 'confirmed' : 'intransit'),true);
                    $packages[] = $package;
                }
                foreach(($api->getOnHoldPackages($user,Carbon::createFromTimestamp($i),Carbon::createFromTimestamp($i+86399)) ?: []) as $package){
                    data_set($package,'hotbox_status','held',true);
                    $packages[] = $package;
                }
            }


            /* go through each set of packages.. */            
            $vendHash = Addressbook::select('id','licensenum')->orderBy('created_at')->get()->pluck('id','licensenum');
            //$catHash = Category::withCount('products')->select('id','metrc_category_id')->get()->sortBy('products_count')->pluck('id','metrc_category_id');
            $catHash = Category::select('id','metrc_category_id')->orderBy('created_at')->get()->pluck('id','metrc_category_id');
            $mcatHash = CategoryMetrc::select('id','name')->orderBy('created_at')->get()->pluck('id','name');
            
            foreach($packages as $row){                                         // map all metrc packages data

                $mpid = (int)data_get($row,'ProductId',null);
                $prod = Product::select('id')->whereHas('inventory',function($q)use($mpid){ $q->where('metrc_product_id',$mpid); })->orderBy('created_at','desc')->first();
                $license = data_get($row,'ReceivedFromFacilityLicenseNumber','XXXXX');
                $catname = data_get($row,'ProductCategoryName',null);
                
                $pack = ReceivingPackage::where('metrc_id',data_get($row,'Id',null))->orderBy('created_at','desc')->first() ?: new ReceivingPackage;
                $pack->metrc_id = data_get($row,'ManifestNumber',data_get($row,'ReceivedFromManifestNumber',null));
                $pack->metrc_product_id = (int)data_get($row,'ProductId',null);
                $pack->label = data_get($row,'Label',null);
                $pack->quantity = (float)data_get($row,'Quantity',null);
                $pack->name = data_get($row,'ProductName',null);
                $pack->uom = data_get($row,'UnitOfMeasureAbbreviation','g');
                $pack->manifest_id = data_get($row,'ReceivedFromManifestNumber',null);
                $pack->product_id = ($prod ? $prod->id : null);
                $pack->product_search = Product::_getBestSearchFromName($pack->name); // parse best 3 words for metrc product name to be used in search init
                $pack->weight_potency = ReceivingPackage::_getParsedPotencyAmount($pack->name);
                
                $pack->vendor_id = (isset($vendHash[$license]) ? $vendHash[$license] : null);
                $pack->metrc_category_id = (isset($mcatHash[$catname]) ? $mcatHash[$catname] : null);
                $pack->category_id = (isset($catHash[$pack->metrc_category_id]) ? $catHash[$pack->metrc_category_id] : null);

                $pack->package_data = $row;                                     // store the returned data for later uses
                
                $pack->status = data_get($row,'hotbox_status','unknown');
                $pack->metrc_status = 'synced';
                $pack->received_at = (($rec = data_get($row,'ReceivedDateTime',null))!=null ? Carbon::createFromTimestamp(strtotime($rec))->toDateTimeString() : null);
                $pack->save();

                $count++;
            }


            $user->location->_setSetting('metrc_last_package_sync',time())->save();
            return ($caller=='api' ? [
                'status'    => 200,
                'count'     => $count,
                'message'   => 'Checked with Metrc and Synced '.$count.' Newly Updated Packages',
                'schema'    => Receiving::_getSchema()                          // include schema as any new metrc categories will be injected there, and need be used in a vue form
            ] : true);
            
    }


    /* CALL TO IMPORT A METRC TRANSFER */
    public function importMetrcTransfer($id,$synctime,$user){

        if(($trans = ReceivingTransfer::with('packages')->find($id))==null) abort(412,'Could not find transfer file - aborting');
        elseif(Receiving::query()->where('transfer_id',$trans->manifest_id)->ofActive()->count()>0) abort(409,'This Transfer has already been assigned to another PurchaseOrder - aborting');
        
        $api = new MetrcRequestService;

        $rec = new Receiving;
        $rec->addressbook_id = (($adr = Addressbook::where('licensenum',$trans->vendor_licensenum)->orderBy('created_at','desc')->first())!=null ? $adr->id : null);
        $rec->transfer_id = $trans->manifest_id;
        $rec->po_number = 'METRC-'.$trans->manifest_id;
        $rec->type = 'metrc';
        $rec->status = 'confirmed';
        $rec->confirmed_at = $trans->received_at;

        $rec->items = $trans->packages->map(function($item,$key)use($trans){
            $prod = Product::with('category','inventory.sales.sale')->where('id',$item->product_id)->first(); // if there was a previous receiving when we pulled the package, a product_id will be provided.
            return (object)[
                'id'                => 'MP-'.$item->id,
                'product'           => $prod ?: null,
                'package'           => $item,
                'priceset_id'       => data_get($prod,'inv_meta.priceset',null),
                'metrc_tag'         => $item->label,
                'product_id'        => data_get($prod,'id',null),
                'item_barcode'      => null,                                    // this will be generated at point of receiving, then tags can be printed..
                //'item_barcode'      => Generator::genBarcode(['metrc_tag'=>$item->label,'category_id'=>data_get($prod,'category.id','12345')]),
                'item_strain'       => data_get($item,'package_data.ItemStrainName',null),
                'unit_of_measure'   => data_get($prod,'inv_meta.uom',$item->uom),
                'amount_unit'       => data_get($prod,'inv_meta.amount_unit',1),
                'weight_potency'    => data_get($prod,'inv_meta.weight_potency',($item->weight_potency ?: 1)),
                'cost_unit'         => data_get($prod,'inv_meta.cost',0.01),
                'retail_unit'       => data_get($prod,'inv_meta.retail',0.02),
                'expires_at'        => null,
                'quantity_requested'=> $item->quantity,
                'quantity_received' => (data_get($prod,'inv_meta.uom',$item->uom)==$item->uom ? $item->quantity : 0),
                'quantity_split'    => (data_get($prod,'inv_meta.uom',$item->uom)!=$item->uom ? ($item->quantity % (float)data_get($prod,'inv_meta.amount_unit',1)) : ($item->quantity/2)),
                'unit_price'        => 0,
                'can_delete'        => false,
                '_showSplit'        => false,
                '_showDetails'      => true
            ];
        })->toArray();


        $rec->save();


        $user->location->_setSetting('metrc_last_package_sync',time())->save();
        return [
            'status'    => 200,
            'id'        => $rec->id,
            'message'   => 'We have imported Transfer '.$rec->transfer_id.' into a new Purchase Order - You may proceed to receiving..',
            'schema'    => Receiving::_getSchema()                              // include schema as any new metrc categories will be injected there, and need be used in a vue form
        ];
            
    }
    

}