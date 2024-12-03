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
 * Class Campaign.
 */
class CampaignLog extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'dispensary_campaign_logs';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['scheduled_at','notified_at','visited_at','purchased_at','code_used_at','response_error'];
	protected $dates = ['created_at','updated_at','deleted_at','scheduled_at','notified_at','visited_at','purchased_at'];
    protected $casts = [];



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

    /* relations */
    public function campaign(){
        return $this->belongsTo(Campaign::class,'campaign_id','id');
    }

    public function customer(){
        return $this->belongsTo(Customer::class,'customer_id','id');
    }
    
    public function customerAbbv(){
        return $this->belongsTo(Customer::class,'customer_id','id')->select(Customer::$abbv_fields);
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
        
        $schema = AppSchema::getSchema('campaignlog_schema');

        return $schema;
        
    }



    /* model boot util */
    protected static function boot(){
        
        parent::boot();
        static::saved(function ($model) {
            if($model->campaign_id && $model->notified_at)
                $model->campaign->save();                                       // touch the campaign parent, so the aggregate data can be saved.
        });
        
    }

}