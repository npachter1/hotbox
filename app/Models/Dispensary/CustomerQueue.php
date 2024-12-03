<?php

namespace App\Models\Dispensary;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

use App\Models\AppSchema;
use App\Models\Auth\Location;
use App\Models\Auth\Addressbook;
use App\Models\Auth\User;

use App\Services\Dispensary\CampaignService;

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
class CustomerQueue extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'dispensary_customer_queues';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['created_by','created_at'];
	protected $dates = ['created_at','updated_at','deleted_at','serviced_at','deactivated_at'];
    protected $casts = [];


    const HASH_ALGORITHM = 'sha256';
    const HASH_SECRET_CONFIG_KEY = 'hotbox.hash_secret';
    

    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	/*  location this customer belongs to */
	public function location(){
		return $this->belongsTo(Location::class,'location_id','id');
	}
	
	
	/* get prime user of this location */
	public function customer(){
		return $this->belongsTo(Customer::class,'customer_id','id');
	}
	
	public function customerAbbv(){
		return $this->belongsTo(Customer::class,'customer_id','id')->select(Customer::$abbv_fields);
	}
	
	public function createdBy(){
		return $this->belongsTo(User::class,'created_by','id');
	}

	public function ServicedBy(){
		return $this->belongsTo(User::class,'serviced_by','id');
	}



    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){
        
        if (!$text) return $query;

        $hash = hash_hmac(
            self::HASH_ALGORITHM,
            $text,
            \Config::get(self::HASH_SECRET_CONFIG_KEY));

        return $query->where(function ($query) use ($text,$hash) {
            $query->whereHas('customer',function($q)use($text,$hash){
                $q->where('first_name','like', '%'.$text.'%')
                    ->orWhere('last_name', 'like', '%'.$text.'%')
                    ->orWhere('type', 'like', '%'.$text.'%')
                    ->orWhere('mmj_card',$hash)
                    ->orWhere('drivers_license',$hash);
                });
        });

    }
    
    
    /* filter by frontend query orderBy filter */
    public function scopeOfActive($query){
        
        return $query->whereNull('serviced_at')->whereNull('deactivated_at');

    }
    




    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/





    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/








    /* get ui schema for this model */
    public static function _getSchema(){
        
        $user = Auth::guard('api')->user();
        $schema = AppSchema::getSchema('customerqueue_schema');

        return $schema;
        
    }



    /* model boot util - generate a unique suite integer upon creation */
    protected static function boot(){
        $user = Auth::guard('api')->user();
        
        parent::boot();
        
        static::addGlobalScope(new LocationScope);                              // add location_id tenant scope
        static::creating(function ($model)use($user) {
            $model->location_id = $user->location_id;
            $model->created_at = Carbon::now()->toDateTimeString();
            $model->created_by = $user->id;
        });
        static::saved(function ($model)use($user) {
            
            if($model->customer) $model->customer->save();                      // by saving customer (hint touch wont work for this) it re-aggregates the visit count from the queue

            $campaignService = new CampaignService();
            $campaignService->_logPosVisit($model);                             // this service will log a visit (non settled sale) or purhase or campaign code use is customer belongs to campaigns!   

        });
    }

}