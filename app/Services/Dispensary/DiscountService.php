<?php

namespace App\Services\Dispensary;

use App\Services\BaseService;

use App\Http\Resources\Dispensary\DiscountCollectionExport;

use App\Models\Dispensary\Discount;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use Excel;
use Storage;


class DiscountService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;


    public function search(array $data){
        
        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);
        
        $groupIds = data_get($data,'filter.group_id',[]);
        $categoryIds = data_get($data,'filter.category_id',[]);
        $locationIds = data_get($data,'filter.location_id',[]);
        $pricesetIds = data_get($data,'filter.priceset_id',[]);
        $productIds = data_get($data,'filter.product_id',[]);


        $query = Discount::query()
            ->with('locations')
            ->withCount('groups','locations','categories','products','sales','campaigns','pricesets')
            ->ofListFilters($filters)
            ->ofTextFilter($search)
            ->ofAssociationFilter('groups',$groupIds)
            ->ofAssociationFilter('categories',$categoryIds)
            ->ofAssociationFilter('products',$productIds)
            ->ofAssociationFilter('locations',$locationIds)
            ->ofAssociationFilter('pricesets',$pricesetIds);

        if(data_get($data,'archived',0)==0) $query->ofActive();                 // unless we are searhing w archived toggle on, then only get active.

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('updated_at', 'desc');


        return $query->paginate($limit);
        
    }
    
    
    /* get Discount record */
    public function show(array $data,$id){
        
        return Discount::query()
            ->with('groups','categories','products','locations','sales','campaigns','pricesets')
            ->where('id',$id)
            ->first();
            
    }
    
    
    /* create Discount record  */
    public function create($data,$user=null){
        
        /* new record */
        $add = new Discount();
        $add->fill($data);
        
        $add->save();
        
        $add->groups()->sync((array)array_get($data, 'group_ids', [])); // sync customer_group_ids in pivot table
        $add->categories()->sync((array)array_get($data, 'category_ids', [])); // sync product_category_ids in pivot table
        $add->products()->sync((array)array_get($data, 'product_ids', [])); // sync product_ids in pivot table
        $add->locations()->sync((array)array_get($data, 'location_ids', [])); // sync location_ids in pivot table      
        $add->pricesets()->sync((array)array_get($data, 'priceset_ids', [])); // sync priceset_ids in pivot table

        return $add;
        
    }


    /* get a batch (abbreviated) collection */
    public function getBatch(array $data){

        $ids = explode(',',data_get($data,'batch_ids',''));
        
        $query = Discount::query()
            ->whereIn('id',$ids)
            ->orderBy('updated_at')
            ->ofActive();


        return $query->get();
        
    }   


    /* update Discount */
    public function update($data,$id,$user=null){
        
        if(($upd = Discount::find($id)) == null) abort(400,'Whoops, Discount Data is temporarially disonnected - please try again later');

        $upd->fill($data);
        $upd->save();
        
        $upd->groups()->sync((array)array_get($data, 'group_ids', [])); // sync customer_group_ids in pivot table
        $upd->categories()->sync((array)array_get($data, 'category_ids', [])); // sync product_category_ids in pivot table
        $upd->products()->sync((array)array_get($data, 'product_ids', [])); // sync product_ids in pivot table
        $upd->locations()->sync((array)array_get($data, 'location_ids', [])); // sync location_ids in pivot table    
        $upd->pricesets()->sync((array)array_get($data, 'priceset_ids', [])); // sync priceset_ids in pivot table 

        return $upd;
        
    }
    
    
    /* update batch edit data */
    public function updateBatchData($data,$user=null){
        
        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no Discount batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach($batch as $row){
            
            if(!isset($row['id'])) continue;
            elseif(($store = Discount::find($row['id'])) == null) continue;

            $store->fill($row);
            $store->save();
            $saved++;
            
        }
        

        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'Updated '.$saved.' of '.count($batch).' Records - Please check updates below..' : 'Successfully Updated '.$saved.' of '.count($batch).' Records!'),
            'schema'    => Discount::_getSchema()                            // include schema as these batch changes may update the scope of some filters.
        ];

    }
    
    /* archive batch list */
    public function updateBatchInactivate($data,$user=null){
        
        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no Discount batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach(Discount::whereIn('id',$batch)->get() as $store){
            
            $store->is_active = false;
            $store->save();
            $saved++;
            
        }
        

        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'Archived '.$saved.' of '.count($batch).' Records - Please review updates below..' : 'Successfully Updated '.$saved.' of '.count($batch).' Records!'),
            'schema'    => Discount::_getSchema()                            // include schema as these batch changes may update the scope of some filters.
        ];

    } 
    
    
    /* process an export */
    public function exportCollection($data,$typ,$file){
        
        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Discount_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all') 
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;
        
        
        return $this->export(new DiscountCollectionExport($data),$file,$name,$typ,[]);

    }
    

    /* Removal service */
    public function archive($data,$id,$user){
        
        if(($record = Discount::with('groups','sales','campaigns')->find($id)) == null) abort(409,'Cannot Archive - No Record found');

        // Any conditions to not archive, speak now or forever hold youre peace..
        //elseif($record->groups->count()>0) abort(422,'You cannot archive a discount rule that is attached to a customer group');
        elseif($record->sales->count()>0) abort(422,'You cannot archive a discount rule that is attached to a sale');
        elseif($record->campaigns->where('archived_at',null)->count()>0) abort(422,'You cannot archive a discount rule that is attached to a live campaign');
        

        $record->archived_at = Carbon::now()->toDateTimeString();
        $record->save();        
        //$record->delete();                                                    // A soft-delete

        return true;
        
    }
    
    

    /* check if category name exists */
    public function checkCodeExists($data,$user){
    
     $code = strtolower(data_get($data,'code',''));

     return (Discount::query()->where(function($query)use($code){
        $query->whereRaw('LOWER(`discount_code`)="'.strtolower($code).'"')
        ->orWhereHas('campaigns',function($q)use($code){                // or if it is a campaign code and campaign is linked to this discount                
            $q->whereRaw('LOWER(`campaign_code`)="'.strtolower($code).'"')->whereIn('status',['working','completed','pending']);
        });
     })->ofActive()->count()>=1 ? true : false);
     
    }
    
    
    
    /**                                             **/
	/**            Discount Service Actions         **/
	/**                                             **/
	
	
	
	


}