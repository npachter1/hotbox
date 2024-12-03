<?php

namespace App\Services\Dispensary;

use App\Services\BaseService;

use App\Http\Resources\Dispensary\ProductCollectionExport;

use App\Models\Dispensary\Product;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use Excel;
use Storage;


class ProductService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;


    public function search(array $data){
        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);

        $pricesetIds = data_get($data,'filter.priceset_id',[]);
        $vendorIds = data_get($data,'filter.vendor_id',[]);

        $query = Product::query()
            ->with('category','lastReceived')
            ->ofListFilters($filters)
            ->ofPricingFilter($pricesetIds)
            ->ofVendors($vendorIds)
            ->ofTextFilter($search,3);

        if(data_get($data,'archived',0)==0) $query->ofActive();                 // unless we are searhing w archived toggle on, then only get active.

        if(data_get($data,'onlyReceived',0)==1)                                 // filter by products that have at least 1 inventory item received against it
            $query->whereHas('inventory');

        if(data_get($data,'onlyGroupings',0)==1)
            $query->whereIn('type',['grouping','kit']);

        if(($cat = data_get($data,'type',null))>0)                              // scope by category type
            $query->whereHas('category',function($q)use($cat){
                $q->where('id',$cat);
                //$q->where('metrc_category_type',$cat);
            });


        if(($id = data_get($data,'custom_filters.id',null))) $query->where('id',$id);

        if(($low = data_get($data,'custom_filters.useStockLow',false))=='true') // Low quantity filter
            $query->whereHas('inventory')->whereDoesntHave('inventory',function($q)use($data){
                $q->where('quantity_on_hand','>',(int)data_get($data,'custom_filters.stockLow',0));
            });

        if(($exp = data_get($data,'custom_filters.useStockExpire',false))=='true') // Low quantity filter
            $query->whereHas('inventory',function($q)use($data){
                $q->whereNotNull('expires_at')->where('expires_at','<=',Carbon::parse(data_get($data,'custom_filters.stockExpire',Carbon::now()->toDateTimeString()))->toDateTimeString());
            });

        if(($last = data_get($data,'custom_filters.useStockLast',false))=='true') // last received filter
            $query->whereHas('inventory',function($q)use($data){
                $q->whereNotNull('created_at')->where('created_at','<=',Carbon::parse(data_get($data,'custom_filters.stockLast',Carbon::now()->subMonths(3)->toDateTimeString()))->toDateTimeString());
            });

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('updated_at', 'desc');


        return $query->paginate($limit);

    }


    /* get Product record */
    public function show(array $data,$id){

        return Product::query()
            ->with('category','inventory.sales.sale','inventory.vendor','inventory.pricing','grouped.pricing','grouped.product.category')
            ->where('id',$id)
            ->first();

    }


    /* create Product record  */
    public function create($data,$user=null){

        /* new record */
        $add = new Product();
        $add->fill($data);
        $add->save();

        $add = Product::with('category')->find($add->id);
        $add->save();                           // resave with category relation to get nature_type.


        return $this->show([],$add->id);

    }


    /* get a batch (abbreviated) collection */
    public function getBatch(array $data){

        $ids = explode(',',data_get($data,'batch_ids',''));

        $query = Product::query()
            ->with('category','lastReceived.vendor')
            ->whereIn('id',$ids)
            ->orderBy('updated_at')
            ->ofActive();


        return $query->get();

    }


    /* update Product*/
    public function update($data,$id,$user=null){

        if(($upd = Product::with('category','inventory')->find($id)) == null) abort(400,'Whoops, Product Data is temporarially disonnected - please try again later');

        $upd->fill($data);

        if(in_array($upd->type,['grouping','kit']))                             // if we have managed grouping/kit type of product - sync items grouped
            $upd->grouped()->sync(collect(data_get($data, 'grouped', []))->map(function($item,$ky){
                return (object)[
                    'id'    => data_get($item,'id',null),
                    'pivot' => [
                        'quantity'  => data_get($item,'quantity',1),
                        'policy'    => data_get($item,'policy','restock')
                    ]
                ];
            })->pluck('pivot','id')->toArray());


        $upd->save();

        foreach($upd->inventory as $inv)
            $inv->save();                                                       // save any new aggregates (ie: item_name) and also reset inv_agg

        return $this->show([],$upd->id);

    }


    /* check if product name exists */
    public function checkNameExists($data,$user){

     $name = strtolower(data_get($data,'value',''));
     $singName = preg_replace('/\'s$|es$|s$/i','',$name);

     return (Product::where(function($q)use($name,$singName){
         $q->whereRaw('LOWER(`name`)="'.$name.'"')
            ->orWhereRaw('LOWER(`name`)="'.$singName.'"');
        })->ofActive()->count()>=1 ? true : false);

    }


    /* update batch edit data */
    public function updateBatchData($data,$user=null){

        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no Product batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach($batch as $row){

            if(!isset($row['id'])) continue;
            elseif(($store = Product::find($row['id'])) == null) continue;

            $store->fill($row);
            $store->save();
            $saved++;

        }


        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'Updated '.$saved.' of '.count($batch).' Records - Please check updates below..' : 'Successfully Updated '.$saved.' of '.count($batch).' Records!'),
            'schema'    => Product::_getSchema()                            // include schema as these batch changes may update the scope of some filters.
        ];

    }

    /* archive batch list */
    public function updateBatchArchive($data,$user=null){

        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no Product batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach(Product::whereIn('id',$batch)->get() as $store){

            $store->archived_at = Carbon::now()->toDateTimeString();
            $store->save();
            $saved++;

        }


        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'Archived '.$saved.' of '.count($batch).' Records - Please review updates below..' : 'Successfully Updated '.$saved.' of '.count($batch).' Records!'),
            'schema'    => Product::_getSchema()                            // include schema as these batch changes may update the scope of some filters.
        ];

    }


    /* process an export */
    public function exportCollection($data,$typ,$file){

        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Product_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all')
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;


        return $this->export(new ProductCollectionExport($data),$file,$name,$typ,[]);

    }


    /* removal service */
    public function archive($data,$id,$user=null){

        if(($record = Product::find($id)) == null) abort(409,'Cannot Archive - No Record found');

        // Any conditions to not archive, speak now or forever hold youre peace..


        $record->archived_at = Carbon::now()->toDateTimeString();
        $record->save();
        //$record->delete();                                                    // A soft-delete

        return true;

    }


}
