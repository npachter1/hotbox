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
 * Class Reward.
 */
class Reward extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'dispensary_rewards';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['descriptor','notes','points_transacted'];
	protected $dates = ['archived_at','created_at','updated_at','deleted_at'];
    protected $casts = ['points_transacted'=>'float'];



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

    public function trigger(){
        return $this->belongsTo(RewardTrigger::class,'trigger_id','id');
    }    

    public function customer(){
        return $this->belongsTo(Customer::class)->select(Customer::$abbv_fields);
    }
    
    public function sale(){
        return $this->belongsTo(Sale::class,'sale_id');
    }
    
    public function saleAbbv(){
        return $this->belongsTo(Sale::class,'sale_id')->select(Sale::$abbv_fields);
    }
    
    

    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){
        
        if (!$text) return $query;

        return $query->where(function ($query) use ($text) {
            $query->where('descriptor', 'like', '%'.$text.'%')
                ->orWhere('notes', 'like', '%'.$text.'%')
                ->orWhereHas('trigger',function($q)use($text){
                    $q->where('type', 'like', '%'.$text.'%')
                        ->orWhere('descriptor', 'like', '%'.$text.'%');
                })
                ->orWhereHas('customer',function($q)use($text){
                    $q->where('first_name', 'like', '%'.$text.'%')
                        ->orWhere('last_name', 'like', '%'.$text.'%');
                });
        });

    }
    
    
    /* filter by frontend query orderBy filter */
    public function scopeOfActive($query){
        
        return $query->whereNull('archived_at');

    }




    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/








    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/


    /* get and dynamically inject ui schema for this models frontend vue filtering */
    public static function _getSchema(){
        
        $schema = AppSchema::getSchema('reward_schema');

        data_set($schema,'form.customer_id.values',Customer::_getFilterList(),true);

        data_set($schema,'filters.created_at.values',Util::getActiveFilterDateRange('INIT'),true);
        data_set($schema,'filters.customer_id.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'customer_id'),true);
        data_set($schema,'filters.trigger_id.values',RewardTrigger::_getFilterList('rewards'),true);

        
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
        
        static::creating(function ($model) {
            $model->created_at = $model->created_at ?: Carbon::now()->toDateTimeString();
        });
        
    }

}