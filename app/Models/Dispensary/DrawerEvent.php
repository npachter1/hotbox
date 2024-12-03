<?php

namespace App\Models\Dispensary;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

use App\Models\AppSchema;
use App\Models\Auth\Location;
use App\Models\Auth\User;

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
 * Class Drawer.
 */
class DrawerEvent extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'dispensary_drawer_events';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['event_type','extra','total','bill_1','bill_5','bill_10','bill_20','bill_50','bill_100','coin_1','coin_5','coin_10','coin_25','coin_50'];
	protected $dates = ['created_at','updated_at','deleted_at'];
    protected $casts = ['extra'=>'float','total'=>'float'];



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	/*  location this drawer belongs to */
	public function location(){
		return $this->belongsTo(Location::class,'location_id','id');
	}
	
    public function drawer() {
        return $this->belongsTo(Drawer::class,'drawer_id','id');
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










    /* get and dynamically inject ui schema for this models frontend vue filtering */
    public static function _getSchema(){
        
        $schema = AppSchema::getSchema('drawerevent_schema');
        return $schema;
        
    }



    /* model boot util - generate a unique suite integer upon creation */
    protected static function boot(){
        
        parent::boot();
        
        static::addGlobalScope(new LocationScope);                              // add location_id tenant scope
        static::creating(function ($model) {
            $model->location_id = Auth::guard('api')->user()->location_id;
            $model->created_at = Carbon::now()->toDateTimeString();
        });
        
    }

}