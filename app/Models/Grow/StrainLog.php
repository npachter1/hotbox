<?php

namespace App\Models\Grow;

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
 * Class Strain.
 */
class StrainLog extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'grow_strains_log';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = [
        'name',
        'testing_status',
        'location_id',
        'metrc_id',
        'thc_level',
        'cbd_level',
        'indica_percentage',
        'sativa_percentage',
        'breakdown_thc',
        'breakdown_thca',
        'breakdown_cbd',
        'breakdown_cbda',
        'breakdown_cbn',
        'breakdown_cbg',
        'breakdown_thcv',
        'breakdown_cbc',
        'breakdown_cbl'
    ];
	protected $dates = ['created_at','updated_at','deleted_at'];
    protected $casts = [];



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	public function location(){
		return $this->belongsTo(Location::class, 'location_id', 'id');
	}




    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/


    




    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/








    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/





    /* get ui schema for this model */
    public static function _getSchema(){
        
        $schema = AppSchema::getSchema('strain_schema');

        return $schema;
        
    }



    /* model boot util - generate a unique suite integer upon creation */
    protected static function boot(){
        
        parent::boot();
        
        static::addGlobalScope(new LocationScope);                              // add location_id tenant scope
        static::creating(function ($model) {
            $model->location_id = Auth::guard('api')->user()->location_id;      // room is scoped to location
        });
        
    }

}