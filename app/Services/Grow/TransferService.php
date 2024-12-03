<?php

namespace App\Services\Grow;

use App\Services\BaseService;

use App\Http\Resources\Grow\TransferCollectionExport;

use App\Models\AppSchema;

use App\Models\Auth\User;
use App\Models\Auth\Location;
use App\Models\Auth\Addressbook;

use App\Models\Grow\Transfer;
use App\Models\Grow\Package;
use App\Models\Grow\Room;
use App\Models\Grow\Item;

use App\Models\Dispensary\Category;
use App\Models\Dispensary\Product;
use App\Models\Dispensary\Receiving;

use App\Services\Metrc\MetrcRequestService;

use App\Helpers\LocationScope;
use App\Helpers\Conversions;
use App\Helpers\Util;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use Excel;
use Storage;
use PDF;


class TransferService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;


    public function search(array $data){

        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);

        $query = Transfer::query()
            ->with('receiver','packages.item')
            ->ofTextFilter($search)
            ->ofListFilters($filters)
            ->ofActive();

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('created_at', 'desc');


        return $query->paginate($limit);

    }
    


    /* get package record */
    public function show(array $data,$id){

        return Transfer::query()
            ->with('packages.item','receiver')
            ->where('id',$id)
            ->first();

    }



    /* create a transfer service  */
    public function create($data){

        $user = Auth::guard('api')->user();
        $schema = AppSchema::getSchema('item_schema');
        $transfer = new Transfer;
        
        $saved = 0;
        $mes = '';
        $packageIds = data_get($data,'package_ids',[]) ?: [];
        $packagePrices = data_get($data,'package_prices',[]) ?: [];
        
        
        $transfer->fill($data);
        
        $transfer->status = (data_get($data,'type','metrc')=='external' ? 'unpaid' : 'intransit');
        $transfer->created_by = $user->id;
        $transfer->save();
        
        $transfer = Transfer::with('receiver')->where('id',$transfer->id)->first();
        foreach($packageIds as $packId){
            
            if(($pack = Package::find($packId))==null) continue;                // cannot find package
            elseif($pack->transfer_id) continue;                                // already transferred
            
            $pack->transfer_id = $transfer->id;                                 // link package with this transfer
            $pack->received_from_manifest_number = $transfer->manifest_number;
            $pack->received_from_facility_license_number = data_get($transfer,'receiver.licensenum',null);
            $pack->received_from_facility_name = data_get($transfer,'receiver.name',null);
            
            $pack->received_price = data_get(collect($packagePrices)->firstWhere('id',$pack->id),'price',null);
            
            
            $pack->save();
            $saved++;
        
        }
        
        
        if($transfer->type=='internal'){
            if(($success = $this->_importTransfer($transfer->id))===true)       // attempt an import to dispensary location (true) or record error
                $mes .= 'And Your Dispensary Location has been requested a PurchaseOrder for Receiving';
            else $mes .= 'However we could not submit a PurchaseOreder to your Dispensary Location: '.$success;
        }

        
        return [
            'status'    => ($saved==count($packageIds) ? 200 : 207),
            'message'   => ($saved!=count($packageIds) ? 'Transferred '.$saved.' of '.count($packageIds).' Packages' : 'Successfully Transferred '.$saved.' of '.count($packageIds).' Packages '.$mes),
            'schema'    => Transfer::_getSchema()                               // include schema as this may update the scope of some filters.
        ];

    }
    


    /* cancel a transfer service */
    public function cancel($data,$id,$user){

        if(($transfer = Transfer::with('packages')->find($id)) == null) abort(422,'Could not find Transfer to destroy - aborting');
        elseif($transfer->received_at) abort(422,'This transfer has already been received - aborting');

        // Rollback packages and cancel any linked receiving or error
        foreach($transfer->packages as $pack){
            $pack->transfer_id = null;
            $pack->received_from_manifest_number = null;
            $pack->received_from_facility_license_number = null;
            $pack->received_from_facility_name = null;
            $pack->save();
        }

        if(($po = Receiving::withoutGlobalScope(LocationScope::class)->where('transfer_id',$transfer->id)->orderBy('created_at','desc')->first())!=null){
            $po->status = 'rejected';
            $po->save();
        }


        $transfer->metrc_status = 'deleting';
        $transfer->delete();

        return true;

    }

    /* modify transfer statuss */
    public function modify($data,$id,$user){

        if(($transfer = Transfer::with('packages')->find($id)) == null) abort(409,'Could not find Transfer to modify - aborting');
        elseif($transfer->received_at) abort(409,'This transfer has already been received - aborting');

        $transfer->status = data_get($data,'status',$transfer->status) ?: $transfer->status;
        $transfer->save();


        return true;

    }

    public function exportPdf($data,$id,$file){
       
        $name = Carbon::now()->timestamp.'_invoice.pdf';
        $pdf = PDF::loadHTML($data);

        return $this->export($pdf->output($name),$file,$name,'pdf',[]);

    }

    /* process an export */
    public function exportCollection($data,$typ,$file){

        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Transfer_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all')
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;


        return $this->export(new TransferCollectionExport($data),$file,$name,$typ,[]);

    }
    
    
    
    
    /**                                 **/
	/**       TRANSFORMATIONS          **/
	/**                                 **/

    /* process an internal transfer - create PO if dispensary location is addressbook - update packages */
    public function _importTransfer($id) {                                      // returns true or a message string

        if(($user = Auth::guard('api')->user())==null) return 'Need to be from asn authenticated user';
        elseif(($sendloc = $user->location)==null) return 'Users Location wasnt found';
        elseif(($trans = Transfer::with('packages.item.strain','receiver','location')->find($id))==null) return 'Could not Locate Transfer file';
        elseif($trans->type!='internal') return 'Can Not Import a transfer of type '.$trans->type;
        elseif(($recloc = Location::where(function($q)use($trans){
            $q->where('addressbook_id',$trans->addressbook_id)->orWhere('licensenum',data_get($trans->receiver,'licensenum','XXXXX')); 
        })->orderBy('created_at','desc')->first())==null) return 'Transfer is not linked to an active Location';
        //elseif(data_get($recloc->settings,'is_medical',false)!=data_get($recloc->settings,'is_medical',false)) return 'Location Mismatch - Receiver and Sender has to either be of Medical or Recreational';
        elseif(data_get($user->location,'settings.regulatory_agent','metrc')=='metrc') return 'Intenral Transfers are not supported for Metrc Locations.';
        else{

            /* 1. We want to reset the user with the dispensary location id to handle the scopes */
            $oldloc = $user->location_id;
            $user->location_id = $recloc->id;
            Auth::guard('api')->setUser($user); // set suites prime user - for scoped data sets= Auth::guard('api')->user();
            

            /* 2. Initiate PO data */            
            $po = Receiving::create();
            $po->type = 'grow';
            $po->addressbook_id = $sendloc->addressbook_id ?: data_get(Addressbook::where('licensenum',$sendloc->licensenum)->whereIn('type',['vendor','retailer','location'])->orderBy('created_at','desc')->first(),'id',null);
            $po->po_number = $trans->manifest_number;
            $po->transfer_id = $trans->id;
            $po->status = 'confirmed';
            $po->sent_at = Carbon::now()->toDateTimeString();
            $po->confirmed_at = Carbon::now()->toDateTimeString();


            /* 3. Go through each package */
            $po->items = $trans->packages->map(function($pack,$key)use($trans,$recloc,$sendloc,$user){
                
                /* map or assign dispensary category */
                // TODO refine: this assigns first category of category_type, assuming the taxonomy of types in the schema stays consustant w eachother.
                if(($cat = Category::where('equivalency_type',data_get($pack,'item.category_type','flower'))->whereNull('archived_at')->orderBy('created_at')->first())==null){
                    $cat = new Category;
                    $cat->name = ucwords(data_get($pack,'item.category_type','Misc Grow Item'));
                    $cat->contains_thc = 1; // its from grow, so..
                    $cat->notes = 'Imported from Grow on '.Carbon::now()->toDateTimeString();

                    $cat->equivalency_type = data_get($pack,'item.category_type',Category::_parseCategoryType(null,$cat->name,data_get($recloc,'settings.regulatory_agent','none')));
                    $cat->created_at = Carbon::now()->toDateTimeString();
                    $cat->save();
                }
                
                /*map or assign dispensary product */
                if(($prod = Product::with('category','inventory.sales.sale')->where('category_id',$cat->id)->whereNull('archived_at')->where(function($q)use($pack){
                    $q->where('name',data_get($pack,'item.name','XXXXX'))->orWhereHas('inventory',function($s)use($pack){
                        $s->where('grow_product_id',data_get($pack,'item.id','XXXXX'));
                    });                                                         // if a prev inventory item has the grow_items.id or the name simply matches.
                })->orderBy('created_at','desc')->first())==null){
                    $prod = new Product;
                    $prod->category_id = $cat->id;
                    $prod->name = data_get($pack,'item.name','Misc Grow Product');
                    $prod->description = 'Imported from Grow Item on '.Carbon::now()->toDateTimeString();
                    $prod->nature_type = (data_get($sendloc->settings,'is_medical',false)===true ? 'medical' : 'recreational'); // coming from grow, so..
                    $prod->created_at = Carbon::now()->toDateTimeString();
                    $prod->save();
                    
                    $prod = Product::with('category','inventory.sales.sale')->where('id',$prod->id)->first();
                }

                /* map package data */
                $package = (object)[
                  'id'                  => data_get($pack,'id',null),
                  'grow_product_id'     => data_get($pack,'item.id',null),
                  'label'               => data_get($pack,'label',null),
                  'name'                => data_get($pack,'item.name',null),
                  'manifest_id'         => $trans->manifest_number,
                  'status'              => 'confirmed', // internal grow transfer = confirmed
                  'quantity'            => (float)data_get($pack,'quantity',null),
                  'uom'                 => data_get($pack,'unit_of_measure','g'),
                  'product_id'          => $prod->id,
                  'vendor_id'           => $trans->addressbook_id,
                  'category_id'         => $cat->id,
                  'weight_potency'      => 1, //TODO
                  'package_data'        => (object)[
                      'SourceHarvestNames'  => data_get($pack,'source_harvest_names',null),
                      'ItemStrainName'      => data_get($pack,'item.strain.name',null),
                  ],
                  'created_at'          => data_get($pack,'created_at',null)
                ];
                
                
                /* 4. Populate the receiving items row */
                return (object)[
                    'id'                => 'XFER-'.$package->id,
                    'product'           => $prod ?: null,
                    'package'           => $package,
                    'priceset_id'       => data_get($prod,'inv_meta.priceset',null),
                    'metrc_tag'         => $package->label,
                    'product_id'        => data_get($prod,'id',null),
                    'item_barcode'      => null,                                    // this will be generated at point of receiving, then tags can be printed..
                    //'item_barcode'      => Generator::genBarcode(['metrc_tag'=>$package->label,'category_id'=>data_get($prod,'category.id','12345')]),
                    'item_strain'       => data_get($package,'package_data.ItemStrainName',null),
                    'unit_of_measure'   => data_get($prod,'inv_meta.uom',$package->uom),
                    'amount_unit'       => data_get($prod,'inv_meta.amount_unit',1),
                    'weight_potency'    => data_get($prod,'inv_meta.weight_potency',($package->weight_potency ?: 1)),
                    'cost_unit'         => data_get($prod,'inv_meta.cost',0.01),
                    'retail_unit'       => data_get($prod,'inv_meta.retail',0.02),
                    'expires_at'        => null,
                    'quantity_requested'=> $package->quantity,
                    'quantity_received' => (data_get($prod,'inv_meta.uom',$package->uom)==$package->uom ? $package->quantity : 0),
                    'quantity_split'    => (data_get($prod,'inv_meta.uom',$package->uom)!=$package->uom ? ($package->quantity % (float)data_get($prod,'inv_meta.amount_unit',1)) : ($package->quantity/2)),
                    'unit_price'        => 0,
                    'can_delete'        => false,
                    '_showSplit'        => false,
                    '_showDetails'      => true
                ];
            })->toArray();


            /* 5. Return user to grow location and Update */
            $po->save();
            $user->location_id = $oldloc;
            Auth::guard('api')->setUser($user); // set suites prime user - for scoped data sets= Auth::guard('api')->user();

            
            return true;
            
        }

    }


}
