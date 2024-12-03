<?php

namespace App\Services\Dispensary;

use App\Services\BaseService;

use App\Http\Resources\Dispensary\PricesetCollectionExport;

use App\Models\Dispensary\Priceset;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use Excel;
use Storage;


class PricesetService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;


    public function search(array $data){
        
        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);
        $categoryIds = data_get($data,'filter.category_id',[]);

        $query = Priceset::query()
            ->withCount('categories','products','inventory')
            ->with('categories')
            ->ofListFilters($filters)
            ->ofTextFilter($search)
            ->ofAssociationFilter('categories',$categoryIds)
            ->ofAssocProduct(data_get($filters,'product_id',null));             // if we are filtering by product_id of assigned inventory
            
            
        if(data_get($data,'archived',0)==0) $query->ofActive();                 // unless we are searhing w archived toggle on, then only get active.

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('updated_at', 'desc');


        return $query->paginate($limit);
        
    }
    
    
    /* get Priceset record */
    public function show(array $data,$id){
        
        return Priceset::query()
            ->with('categories','products','inventory')
            ->where('id',$id)
            ->first();
            
    }
    
    
    /* create Priceset record  */
    public function create($data,$user=null){
        
        /* new record */
        $add = new Priceset();
        $add->fill($data);
        $add->save();
        
        $add->categories()->sync((array)array_get($data, 'category_ids', [])); // sync category_ids in pivot table
        $add->products()->sync((array)array_get($data, 'product_ids', [])); // sync product_ids in pivot table

        return $add;
        
    }


    /* update Priceset*/
    public function update($data,$id,$user=null){
        
        if(($upd = Priceset::find($id)) == null) abort(400,'Whoops, Priceset Data is temporarially disonnected - please try again later');

        $upd->fill($data);
        $upd->save();
        
        $upd->categories()->sync((array)array_get($data, 'category_ids', [])); // sync product_category_ids in pivot table
        $upd->products()->sync((array)array_get($data, 'product_ids', [])); // sync product_ids in pivot table
        return $upd;
        
    }


    /* process an export */
    public function exportCollection($data,$typ,$file){
        
        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Priceset_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all') 
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;
        
        
        return $this->export(new PricesetCollectionExport($data),$file,$name,$typ,[]);

    }
    
    
    /* removal service */
    public function archive($data,$id,$user=null){
        
        if(($record = Priceset::find($id)) == null) abort(409,'Cannot Archive - No Record found');

        // Any conditions to not archive, speak now or forever hold youre peace..
        

        $record->archived_at = Carbon::now()->toDateTimeString();
        $record->save();        
        //$record->delete();                                                    // A soft-delete

        return true;
        
    }
    

}