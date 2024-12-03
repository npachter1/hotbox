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
class CategoryMetrc extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'metrc_dispensary_categories';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['name','thc_equiv_ratio','thc_equiv_prompt','public_img'];
	protected $dates = ['archived_at','created_at','updated_at','deleted_at'];
    protected $casts = [
        'requires_strain'                => 'boolean',
        'requires_item_brand'            => 'boolean',
        'requires_administration_method' => 'boolean',
        'requires_unit_cbd_percent'      => 'boolean',
        'requires_unit_cbd_content'      => 'boolean',
        'requires_unit_thc_percent'      => 'boolean',
        'requires_unit_thc_content'      => 'boolean',
        'requires_unit_volume'           => 'boolean',
        'requires_unit_weight'           => 'boolean',
        'requires_serving_size'          => 'boolean',
        'requires_supply_duration_days'  => 'boolean',
        'requires_ingredients'           => 'boolean',
        'requires_product_photo'         => 'boolean',
        'can_contain_seeds'              => 'boolean',
        'can_be_remediated'              => 'boolean'
    ];



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	public function location(){
		return $this->belongsTo(Location::class, 'location_id', 'id');
	}
	
	public function categories(){
		return $this->hasMany(Category::class,'metrc_category_id','id');
	}



    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){
        
        if (!$text) return $query;

        return $query->where(function ($query) use ($text) {
            $query->where('name', 'like', '%'.$text.'%')
                ->orWhere('id', 'like', '%'.$text.'%');
        });

    }
    
    
    /* filter by frontend query orderBy filter */
    public function scopeOfActive($query){
        
        return $query;

    }
    




    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/








    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/

    /* get list (w equiv ratio) for form selection */
    public static function _getMetrcList(){
        
        return self::select('id','name','product_category_type','thc_equiv_ratio','thc_equiv_prompt')->orderBy('name')->get()->map(function($item,$key){
            return (object)[
                    'id'        => $item->id,
                    'type'      => $item->product_category_type,
                    'name'      => $item->name.' THC ratio: '.$item->thc_equiv_ratio,
                    'thc_equiv_ratio'   => $item->thc_equiv_ratio,
                    'thc_equiv_prompt'  => $item->thc_equiv_prompt
                ];
        });
        
    }


    /* get and inject ui schema for this model */
    public static function _getSchema(){
        
        $schema = AppSchema::getSchema('categorymetrc_schema');
        return $schema;
        
    }



    /* model boot util - generate a unique suite integer upon creation */
    protected static function boot(){
        
        parent::boot();
        
        static::addGlobalScope(new LocationScope);                              // add location_id tenant scope
        static::creating(function ($model) {
            if (Auth::guard('api')->user())
                $model->location_id = Auth::guard('api')->user()->location_id;  // inventory is scoped to location
            $model->created_at = Carbon::now()->toDateTimeString();
        });
        
    }

}