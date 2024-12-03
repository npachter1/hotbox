<?php

namespace App\Models\Auth;

use Eloquent;
use Illuminate\Support\Facades\Schema;

use App\Models\AppSchema;
use App\Models\Auth\Location;

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
class Servicelog extends Eloquent
{
    
    use FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'auth_servicelogs';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['name','entity_ref','request_ref','response_ref'];
	protected $dates = ['created_at','updated_at'];
    protected $casts = [];



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	/*  location this servicelog belongs to */
	public function location(){
		return $this->belongsTo(Location::class,'location_id','id');
	}
	
	



    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){
        
        if (!$text) return $query;

        return $query->where(function ($query) use ($text) {
            $query->where('name', 'like', '%'.$text.'%')
                ->orWhere('entity_ref', 'like', '%'.$text.'%')
                ->orWhere('request_ref', 'like', '%'.$text.'%')
                ->orWhere('response_ref', 'like', '%'.$text.'%');
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










    /* get ui schema for this model */
    public static function _getSchema(){
        
        $schema = AppSchema::getSchema('servicelog_schema');

        data_set($schema,'filters.method.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'method'),true);
        data_set($schema,'filters.http_code.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'http_code'),true);
        data_set($schema,'filters.type.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'type'),true);
        data_set($schema,'filters.location_id.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'location_id'),true);
        data_set($schema,'filters.created_at.values',Util::getActiveFilterDateRange('SOM'),true);


        
        /* agg data */
        $todayAgo = Carbon::now()->subDays(1)->toDateTimeString();
        data_set($schema,'agg',self::select(DB::raw('
            COUNT(DISTINCT(id)) as `all`,
            COUNT(DISTINCT(CASE WHEN created_at >= "'.$todayAgo.'" THEN id ELSE NULL END)) as all_today
        '))->ofActive()->first(),true);


        return $schema;
        
    }



    /* model boot util - generate a unique location integer upon creation */
    protected static function boot(){
        
        parent::boot();
        
        static::creating(function ($model) {
            $model->created_at = Carbon::now()->toDateTimeString();
        });
        
    }

}