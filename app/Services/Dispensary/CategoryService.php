<?php

namespace App\Services\Dispensary;

use App\Services\BaseService;

use App\Http\Resources\Dispensary\CategoryCollectionExport;

use App\Models\Auth\User;
use App\Models\Dispensary\Category;
use App\Models\Dispensary\CategoryMetrc;
use App\Models\Dispensary\Product;

use App\Models\Grow\Item;

use App\Services\Metrc\MetrcRequestService;

use App\Helpers\Util;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use Excel;
use Storage;


class CategoryService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;


    public function search(array $data){
        
        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);

        $query = Category::query()
            ->withCount('products','pricing')
            ->ofListFilters($filters)
            ->ofTextFilter($search);

        if(data_get($data,'archived',0)==0) $query->ofActive();                 // unless we are searhing w archived toggle on, then only get active.

        if(data_get($data,'has_items',0)==1)
            $query->whereHas('products',function($q){
                $q->whereHas('activeInventory')
                    ->orWhereHas('grouped');
            });

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('updated_at', 'desc');


        return $query->paginate($limit);
        
    }
    
    
    /* get Category record */
    public function show(array $data,$id){
        return Category::query()
            ->with('products')
            ->where('id',$id)
            ->first();
    }

    
    /* create Category record  */
    public function create($data,$user=null){
        
        /* new record */
        $add = new Category();
        $add->fill($data);
        
        
        // conditionals
        if(!$add->contains_thc)
            $add->equivalency_type = 'noncannabis';                             // if no thc - update equivalency_type
        elseif($add->contains_thc && $add->equivalency_type=='noncannabis')
            abort(409,'Incorrect Type: Please specify a Equivalency Type');


        $add->save();
        return $add;
        
    }


    /* get a batch (abbreviated) collection */
    public function getBatch(array $data){

        $ids = explode(',',data_get($data,'batch_ids',''));
        
        $query = Category::query()
            ->whereIn('id',$ids)
            ->orderBy('updated_at')
            ->ofActive();


        return $query->get();
        
    }   


    /* update Category*/
    public function update($data,$id,$user=null){
        
        if(($upd = Category::find($id)) == null) abort(400,'Whoops, Category Data is temporarially disonnected - please try again later');

        $upd->fill($data);
        
        if(!$upd->contains_thc)                                                 // conditionals
            $upd->equivalency_type = 'noncannabis';
        elseif($upd->contains_thc && $upd->equivalency_type=='noncannabis')
            abort(409,'Incorrect Type: Please specify a Equivalency Type');


        $upd->save();
        
        
        foreach(Product::where('category_id',$upd->id)->get() as $prod)
            $prod->save();                                                      // save all products of category based on update for meta info / nature type
        
        
        return $upd;
        
    }
    


    /* check if category name exists */
    public function checkNameExists($data,$user){
    
     $name = strtolower(data_get($data,'value',''));
     $singName = preg_replace('/\'s$|es$|s$/i','',$name);

     return (Category::where(function($q)use($name,$singName){
         $q->whereRaw('LOWER(`name`)="'.$name.'"')
            ->orWhereRaw('LOWER(`name`)="'.$singName.'"');
        })->ofActive()->count()>=1 ? true : false);

    }


    /* update batch edit data */
    public function updateBatchData($data,$user=null){
        
        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no Category batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach($batch as $row){
            
            if(!isset($row['id'])) continue;
            elseif(($store = Category::find($row['id'])) == null) continue;

            $store->fill($row);
            $store->save();
            $saved++;
            
        }
        

        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'Updated '.$saved.' of '.count($batch).' Records - Please check updates below..' : 'Successfully Updated '.$saved.' of '.count($batch).' Records!'),
            'schema'    => Category::_getSchema()                            // include schema as these batch changes may update the scope of some filters.
        ];

    }
    
    /* archive batch list */
    public function updateBatchArchive($data,$user=null){
        
        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no Category batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach(Category::whereIn('id',$batch)->get() as $store){
            
            $store->archived_at = Carbon::now()->toDateTimeString();
            $store->save();
            $saved++;
            
        }
        

        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'Archived '.$saved.' of '.count($batch).' Records - Please review updates below..' : 'Successfully Updated '.$saved.' of '.count($batch).' Records!'),
            'schema'    => Category::_getSchema()                            // include schema as these batch changes may update the scope of some filters.
        ];

    } 
    
    
    /* process an export */
    public function exportCollection($data,$typ,$file){
        
        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Category_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all') 
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;
        
        
        return $this->export(new CategoryCollectionExport($data),$file,$name,$typ,[]);

    }
    

    /* removal service */
    public function archive($data,$id,$user){
        
        if(($record = Category::find($id)) == null) abort(409,'Cannot Archive - No Record found');

        // Any conditions to not archive, speak now or forever hold youre peace..
        

        $record->archived_at = Carbon::now()->toDateTimeString();
        $record->save();        
        //$record->delete();                                                    // A soft-delete

        return true;
        
    }
    
    
    /* sync metrc categories */
    public function syncMetrcCategories($user,$caller='api'){

            $api = new MetrcRequestService;
            
            if(!($user instanceof User)) abort(422,'NO_USER_ASSIGNED');
            elseif(($data = $api->getItemCategories($user)) == null) abort(422,'Metrc was unable to process this request');

            $count = 0;

            
            foreach($data as $row){                                             // map all metrc categories to this (location scoped) table

                $mact = CategoryMetrc::where('name',data_get($row,'Name',null))->orderBy('created_at','desc')->first() ?: new CategoryMetrc;
                
                $mact->name = data_get($row,'Name',null);
                $mact->product_category_type = data_get($row,'ProductCategoryType',null);
                $mact->quantity_type = data_get($row,'QuantityType',null);
                $mact->requires_strain = data_get($row,'RequiresStrain',true);
                $mact->requires_item_brand = data_get($row,'RequiresItemBrand',false);
                $mact->requires_administration_method = data_get($row,'RequiresAdministrationMethod',false);
                $mact->requires_unit_cbd_percent = data_get($row,'RequiresUnitCbdPercent',false);
                $mact->requires_unit_cbd_content = data_get($row,'RequiresUnitCbdContent',false);
                $mact->requires_unit_thc_percent = data_get($row,'RequiresUnitThcPercent',false);
                $mact->requires_unit_thc_content = data_get($row,'RequiresUnitThcContent',false);
                $mact->requires_unit_volume = data_get($row,'RequiresUnitVolume',false);
                $mact->requires_unit_weight = data_get($row,'RequiresUnitWeight',false);
                $mact->requires_serving_size = data_get($row,'RequiresServingSize',false);
                $mact->requires_supply_duration_days = data_get($row,'RequiresSupplyDurationDays',false);
                $mact->requires_ingredients = data_get($row,'RequiresIngredients',false);
                $mact->requires_product_photo = data_get($row,'RequiresProductPhoto',false);
                $mact->can_contain_seeds = data_get($row,'CanContainSeeds',false);
                $mact->can_be_remediated = data_get($row,'CanBeRemediated',false);

                $mact->thc_equiv_ratio = (float)Util::getEquivalencyType(data_get($row,'ProductCategoryType','Buds'),'value',1);
                $mact->thc_equiv_prompt = Util::getEquivalencyType(data_get($row,'ProductCategoryType','Buds'),'prompt','amount');


                $mact->save();
                $count++;
            }


            return ($caller=='api' ? [
                'status'    => 200,
                'message'   => 'Checked with Metrc and Synced '.$count.' Metrc Category Records.',
                'schema'    => Category::_getSchema(),                           // include schema as any new metrc categories will be injected there, and need be used in a vue form
                'grow_schema'=> Item::_getSchema()
            ] : true);
            
    }
    

}