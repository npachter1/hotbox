<?php

namespace App\Models\Dispensary;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

use App\Models\AppSchema;
use App\Models\Auth\Location;

use App\Helpers\LocationScope;
use App\Helpers\FilterScopes;
use App\Helpers\Generator;
use App\Helpers\Util;

use Auth;
use DB;
use Carbon\Carbon;
use Exception;
use Log;


/**
 * Class User.
 */
class Category extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'dispensary_categories';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['name','equivalency_type','contains_thc','settings','notes','public_img'];
	protected $dates = ['archived_at','created_at','updated_at','deleted_at'];
    protected $casts = ['settings'=>'object','contains_thc'=>'boolean'];

    public $appends = ['items_count'];


    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	public function location(){
		return $this->belongsTo(Location::class, 'location_id', 'id');
	}

	/*  suite this category belongs to */
	public function type(){
		return $this->hasOne(CategoryMetrc::class,'id','metrc_category_id');
	}
	
    public function pricing(){
        return $this->belongsToMany(Priceset::class,'dispensary_category_dispensary_priceset','category_id','priceset_id');
    }
    
    public function discounts(){
        return $this->belongsToMany(Discount::class,'dispensary_category_dispensary_discount','discount_id','category_id');
    }
    
    public function products(){
		return $this->hasMany(Product::class, 'category_id', 'id');
	}



    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){
        
        if (!$text) return $query;

        return $query->where(function ($query) use ($text) {
            $query->where('name', 'like', '%'.$text.'%')
                ->orWhere('notes', 'like', '%'.$text.'%')
                ->orWhere('equivalency_type','like', '%'.$text.'%');
        });

    }
    
    
    /* filter by frontend query orderBy filter */
    public function scopeOfActive($query){
        
        return $query->whereNull('archived_at');

    }
    




    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/

    public function getItemsCountAttribute(){
        
        return Inventory::whereIn('product_id',Product::select('id')->where('category_id',$this->id)->pluck('id')->all())->where('quantity_on_hand','>',0)->whereNull('archived_at')->count();
        
    }



    /* Cast properties and/or assume defaults of this settings array by schema.form. */
    public function setSettingsAttribute($data){
        
        if(($schema = self::_getSchema()) == null) $this->attributes['settings'] = json_encode($data);
        elseif(($store = Util::setObjectFromSchema($data,$schema,'settings')) == null) $this->attributes['settings'] = json_encode($data);
        else $this->attributes['settings'] = json_encode($store);
        
    }



    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/

    /* parse the equivalency_type, either from metrc category or name match via schema */
    public static function _parseCategoryType($mcatid,$name=null,$reg='none'){
        
        $schema = AppSchema::getSchema('category_schema');
        $mcat = CategoryMetrc::find($mcatid);                                   // hard load via a category id vs relation - for creates
        
        return data_get(collect(data_get($schema,'form.equivalency_type.values',[]))->filter(function($itm,$ky)use($mcat,$name,$reg){
            foreach(data_get($itm,'migration_matches',[]) as $finder)
                if(preg_match("/$finder/i",data_get($mcat,'product_category_type',$name)) && (in_array($reg,data_get($itm,'scope',[])) || data_get($itm->scope,'0',null)=='all'))
                    return true;                                                // first match from schema category_type values. either by matrc category id or name
        })->first(),'id','noncannabis');

    }
    

    public static function _getCustomerFilterListData(){

        
        $hash = [];
        foreach(Customer::select('id','category_ids_purchased')->whereNotNull('category_ids_purchased')->ofActive()->get() as $cust)
            foreach(explode(',',$cust->category_ids_purchased) as $cid)
                if($cid!='' && $cid)
                    data_set($hash,$cid,data_get($hash,$cid,0)+1,true);

     
        return self::query()->withoutGlobalScope(LocationScope::class)->whereIn('id',array_keys($hash))->select('id','name','location_id','equivalency_type')->ofActive()->get()->map(function($item,$key)use($hash){
            data_set($item,'customers_count',data_get($hash,$item->id,0),true);
            return $item;
        });

    }
    
    
    /* get category type filter list data */
    public static function _getFilterTypesList($rel='all'){

        return self::select('equivalency_type')->groupBy('equivalency_type')->orderBy('equivalency_type')->ofActive()->get()->map(function($item,$key){
            return (object)[
                'id'        => $item->type,
                'name'      => ucwords($item->type)
            ];
        });

    }
    

    /* get product filters from inventory */    
    public static function _getPricesetFilters(){
        
        return self::select('id','name')->with('pricing')->whereHas('pricing')->orderBy('name')->ofActive()->get()->map(function($item,$key){
            return (object)[
                'id'        => $item->id,
                'name'      => $item->name,
                'count'     => $item->pricing->count()
            ];
        });
        
    }


    /* get ui schema for this model */
    public static function _getSchema(){
        
        $schema = AppSchema::getSchema('category_schema');

        data_set($schema,'form.metrc_category_id.values',CategoryMetrc::_getMetrcList() ?: [],true); // do this first before we get the filtered values in data set
        //data_set($schema,'filters.metrc_category_id.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'metrc_category_id'),true);
        data_set($schema,'filters.equivalency_type.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'equivalency_type'),true);

        return $schema;
        
    }



    /* model boot util - generate a unique suite integer upon creation */
    protected static function boot(){
        
        parent::boot();
        
        static::addGlobalScope(new LocationScope);                              // add location_id tenant scope
        static::creating(function ($model) {
            $model->location_id = $model->location_id ?: Auth::guard('api')->user()->location_id;      // inventory is scoped to location
            $model->created_at = $model->created_at ?: Carbon::now()->toDateTimeString();
        });
        static::saved(function ($model) {

        });
        
    }

}