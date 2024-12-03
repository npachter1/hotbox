<?php

namespace App\Services\Dispensary;

use App\Services\BaseService;

use App\Http\Resources\Dispensary\GroupCollectionExport;

use App\Models\Dispensary\Group;
use App\Models\Dispensary\Customer;
use App\Models\Dispensary\Product;
use App\Models\Dispensary\Category;
use App\Models\Auth\Location;
use App\Models\AppSchema;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use Excel;
use Storage;


class GroupService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;


    public function search(array $data){
        
        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);

        $query = Group::query()
            ->withCount('customers','discounts','campaigns')
            ->ofListFilters($filters)
            ->ofTextFilter($search);

        if(data_get($data,'archived',0)==0) $query->ofActive();                 // unless we are searhing w archived toggle on, then only get active.

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('updated_at', 'desc');


        return $query->paginate($limit);
        
    }
    

    /* filter customers from group filters */
    public function filterCustomers(array $data,$user){

        $matchTypes = data_get($data, 'matchTypes', []);
        $minPoints = (int) data_get($data, 'minPoints', 0);
        $minSpent = (int) data_get($data, 'minSpent', 0);

        $locations = data_get($data, 'locations', []);
        $products = data_get($data, 'products', []);
        $categories = data_get($data, 'categories', []);
        $preferences = data_get($data, 'preferences', []);

        $query = Customer::query()->with('address')->select(Customer::$abbv_fields)
            ->ofLocations($locations,data_get($matchTypes,'locations','and'))
            ->ofProducts($products,data_get($matchTypes,'products','and'))
            ->ofCategories($categories, data_get($matchTypes,'categories','and'))
            ->ofPreferences($preferences,data_get($matchTypes,'preferences','and'))
            ->ofMinSpent($minSpent)
            ->ofMinPoints($minPoints)
            ->ofActive();

        return $query->get();    
    
    }
    

    /* get filters data */
    public function getFilters(array $data,$user){


        return [
            'products'      => Product::_getCustomerFilterListData(),
            'categories'    => Category::_getCustomerFilterListData(),
            'locations'     => Location::query()->with('address')->withCount('customers')->where('status','activated')->where('type',$user->location->type)->get()
        ];

    }
    
    
    /* get Group record */
    public function show(array $data,$id){
        
        return Group::query()
            ->with('customers')
            ->where('id',$id)
            ->first();
            
    }
    
    
    /* create Group record  */
    public function create($data,$user=null){
        
        /* new record */
        $add = new Group();
        $add->fill($data);
        
        if($add->type=='auto') $add->last_synced_at = Carbon::now()->toDateTimeString();
        $add->save();
        

        $add->customers()->sync((array)collect(data_get($data, 'customer_ids', []))->pluck('is_manual','id')->map(function($itm,$ky){ return ['is_manual'=>$itm]; })->toArray()); // sync customer_ids in pivot table
        return $add;
        
    }


    /* update Group */
    public function update($data,$id,$user=null){
        
        if(($upd = Group::find($id)) == null) abort(400,'Whoops, Group Data is temporarially disonnected - please try again later');

        $upd->fill($data);
        
        if($upd->type=='auto') $upd->last_synced_at = Carbon::now()->toDateTimeString();
        $upd->save();


        $upd->customers()->sync((array)collect(data_get($data, 'customer_ids', []))->pluck('is_manual','id')->map(function($itm,$ky){ return ['is_manual'=>$itm]; })->toArray()); // sync customer_ids in pivot table
        return $upd;
        
    }
    

    /* process an export */
    public function exportCollection($data,$typ,$file){
        
        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Group_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all') 
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;
        
        
        return $this->export(new GroupCollectionExport($data),$file,$name,$typ,[]);

    }
    

    /* Removal service */
    public function archive($data,$id,$user){
        
        if(($record = Group::withCount('discounts','campaigns')->find($id)) == null) abort(409,'Cannot Archive - No Record found');
        elseif($record->discounts_count) abort(409,'Cannot Archive - Discounts are associated');
        elseif($record->campaigns_count) abort(409,'Cannot Archive - Campaigns are associated');

        // Any conditions to not archive, speak now or forever hold youre peace..
        

        $record->archived_at = Carbon::now()->toDateTimeString();
        $record->save();        
        //$record->delete();                                                    // A soft-delete

        return true;
        
    }


}