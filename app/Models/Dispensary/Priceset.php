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
class Priceset extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'dispensary_pricesets';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['name_grade','category_type','rank','is_active','type_uom','amount_tiers','amount_default','notes_announcement'];
	protected $dates = ['archived_at','created_at','updated_at','deleted_at'];
    protected $casts = ['amount_tiers'=>'object','is_active'=>'boolean','is_default'=>'boolean','amount_default'=>'float'];



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	/*  location this priceset belongs to */
	public function location(){
		return $this->belongsTo(Location::class,'location_id','id');
	}
	
	public function categories(){
	    return $this->belongsToMany(Category::class,'dispensary_category_dispensary_priceset','priceset_id','category_id');
	}
	public function categoryIds(){
	    return $this->belongsToMany(Category::class,'dispensary_category_dispensary_priceset','priceset_id','category_id')->select('id','name');
	}
	
	public function products(){
	    return $this->belongsToMany(Product::class,'dispensary_priceset_dispensary_product','priceset_id','product_id');
	}
	public function productIds(){
	    return $this->belongsToMany(Product::class,'dispensary_priceset_dispensary_product','priceset_id','product_id')->select('id','name');
	}

	public function inventory(){
		return $this->hasMany(Inventory::class, 'priceset_id', 'id')->orderBy('created_at');
	}
	
	
	public function discounts(){
        return $this->belongsToMany(Discount::class,'dispensary_discount_dispensary_priceset','priceset_id','discount_id');
    }
	



    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){
        
        if (!$text) return $query;

        return $query->where(function ($query) use ($text) {
            $query->where('name_grade', 'like', '%'.$text.'%')
                ->orWhere('notes_announcement', 'like', '%'.$text.'%')
                ->orWhereHas('categories', function($q) use ($text) {
                    $q->where('name','like', '%'.$text.'%');
                });
        });

    }
    
    
    /* filter by product association */
    public function scopeOfAssocProduct($query,$ids){
        
        if(!$ids || strtolower($ids) == 'all') return $query;
        
        return $query->whereHas('inventory',function($q)use($ids){
            $q->whereIn('product_id',explode(',',$ids));
        });

    }

    public function scopeOfAssocCategory($query,$ids){
        
        if(!$ids || strtolower($ids) == 'all') return $query;
        
        return $query->whereHas('categories',function($q)use($ids){
            foreach(explode(',',$ids) as $ind => $pid)
                $q->{($ind==0 ? 'where' : 'orWhere')}('id',$pid);
        });

    }
    
    
    
    /* filter by frontend query orderBy filter */
    public function scopeOfLive($query){
        
        return $query->whereNull('archived_at')->where('is_active',1);

    }
    
    public function scopeOfActive($query){
        
        return $query->whereNull('archived_at');

    }



    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/








    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/


    /* get product filters from inventory */    
    public static function _getProductSetFilters(){
        
        return self::select('id','name_grade')->with('inventory')->whereHas('inventory')->ofActive()->get()->map(function($item,$key){
            return (object)[
                'id'        => $item->id,
                'name'      => $item->name_grade,
                'count'     => $item->inventory->unique('product_id')->count()
            ];
        });
        
    }




    /* get ui schema for this model */
    public static function _getSchema(){
        
        $schema = AppSchema::getSchema('priceset_schema');

        data_set($schema,'form.category_type.values',Category::_getFilterTypesList('all'),true); // all categories registered
        data_set($schema,'form.category_ids.values',Category::query()->select('id','name')->ofActive()->orderBy('name')->get(),true);
        data_set($schema,'form.product_ids.values',Product::query()->select('id','name')->ofActive()->orderBy('name')->get(),true);

        data_set($schema,'filters.category_type.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'category_type'),true);
        data_set($schema,'filters.category_id.values',Category::_getPricesetFilters(),true);
        data_set($schema,'filters.product_id.values',Product::_getPricesetFilters(),true);

        
        /* agg data */
        $todayAgo = Carbon::now()->subDays(1)->toDateTimeString();
        data_set($schema,'agg',self::select(DB::raw('
            COUNT(DISTINCT(id)) as `all`,
            COUNT(DISTINCT(CASE WHEN created_at >= "'.$todayAgo.'" THEN id ELSE NULL END)) as all_today
        '))->ofActive()->first(),true);


        return $schema;
        
    }



    /* model boot util - generate a unique suite integer upon creation */
    protected static function boot(){
        
        parent::boot();
        
        static::addGlobalScope(new LocationScope);                              // add location_id tenant scope
        static::creating(function ($model) {
            $model->location_id = Auth::guard('api')->user()->location_id;      // inventory is scoped to location
            $model->created_at = $model->created_at ?: Carbon::now()->toDateTimeString();
        });
        
    }

}