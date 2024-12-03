<?php

namespace App\Models\Grow;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

use App\Models\AppSchema;
use App\Models\Auth\Location;
use App\Models\Dispensary\CategoryMetrc;

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
 * Class Item.
 */
class Item extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'grow_items';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = [
        'name',
        'metrc_category_id',
        'category_type',
        'unit_of_measure',
        'strain_id',
        'item_brand',
        'administration_method',
        'unit_cbd_percent',
        'unit_cbd_content',
        'unit_cbd_content_unit_of_measure',
        'unit_thc_percent',
        'unit_thc_content',
        'unit_thc_content_unit_of_measure',
        'unit_volume',
        'unit_volume_unit_of_measure',
        'unit_weight',
        'unit_weight_unit_of_measure',
        'serving_size',
        'supply_duration_days',
        'ingredients'
    ];
	protected $dates = ['created_at','updated_at','deleted_at'];
    protected $casts = [];



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	public function location(){
		return $this->belongsTo(Location::class, 'location_id', 'id');
	}

    public function metrc_category(){
        return $this->hasOne(CategoryMetrc::class,'id', 'metrc_category_id');
    }

    public function strain(){
        return $this->hasOne(Strain::class,'id', 'strain_id');
    }


    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){
        
        if (!$text) return $query;

        return $query->where(function ($query) use ($text) {
            $query->where('name', 'like', '%'.$text.'%');
                });
    }
    
    
    /* filter by frontend query orderBy filter */
    public function scopeOfActive($query){
        
        return $query->whereNull('deleted_at');
        //return $query;

    }
    




    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/








    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/

    /* parse the cateogry_type, either from metrc category or name match via schema */
    public static function _parseCategoryType($mcatid,$name=null,$reg='none'){
        
        $schema = AppSchema::getSchema('item_schema');
        $mcat = CategoryMetrc::find($mcatid);                                   // hard load via a category id vs relation - for creates
        
        return data_get(collect(data_get($schema,'form.category_type.values',[]))->filter(function($itm,$ky)use($mcat,$name,$reg){
            foreach(data_get($itm,'migration_matches',[]) as $finder)
                if(preg_match("/$finder/i",data_get($mcat,'product_category_type',$name)) && (in_array($reg,data_get($itm,'scope',[])) || data_get($itm->scope,'0',null)=='all'))
                    return true;                                                // first match from schema category_type values. either by metrc category id or name
        })->first(),'id','misc');

    }
    
    

    public static function _getList(){
        
        return self::select('id','name')->orderBy('name')->get()->map(function($item,$key){
            return (object)[
                    'id'        => $item->id,
                    'name'      => $item->name
                ];
        });
        
    }

    public static function _getListByCategory($category = null){
        
        return self::select('id','name')->where('category_type','=',$category)->orderBy('name')->get()->map(function($item,$key){
            return (object)[
                    'id'        => $item->id,
                    'name'      => $item->name
                ];
        });
        
    }


    /* get ui schema for this model */
    public static function _getSchema(){
        
        $schema = AppSchema::getSchema('item_schema');

        data_set($schema,'form.metrc_category_id.values',CategoryMetrc::_getMetrcList() ?: [],true); // do this first before we get the filtered values in data set
        data_set($schema,'filters.metrc_category_id.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'metrc_category_id'),true);
        data_set($schema,'filters.category_type.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'category_type'),true);

        data_set($schema,'form.strain_id.values',Strain::_getList() ?: [],true); // do this first before we get the filtered values in data set
        data_set($schema,'filters.strain_id.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'strain_id'),true);

        return $schema;
        
    }



    /* model boot util - generate a unique suite integer upon creation */
    protected static function boot(){
        
        parent::boot();
        
        static::addGlobalScope(new LocationScope);                              // add location_id tenant scope
        static::creating(function ($model) {
            $model->location_id = Auth::guard('api')->user()->location_id;      // item is scoped to location
        });
        
    }

}