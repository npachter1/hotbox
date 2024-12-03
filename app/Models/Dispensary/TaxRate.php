<?php

namespace App\Models\Dispensary;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

use App\Models\AppSchema;
use App\Models\Auth\Location;
use App\Models\Dispensary\Tax;

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
class TaxRate extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'dispensary_tax_rates';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['name','description','tax_rate_level','rate_percent','state_code','county_name','city_name','zipcode'];
	protected $dates = ['archived_at','created_at','updated_at','deleted_at'];
    protected $casts = ['rate_percent'=>'float'];



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	/*  suite this taxrate belongs to */
	public function categories(){
	    return $this->belongsToMany(Tax::class,'dispensary_tax_dispensary_tax_rate','tax_rate_id','tax_id');
	}
	
	



    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){
        
        if (!$text) return $query;

        return $query->where(function ($query) use ($text) {
            $query->where('name', 'like', '%'.$text.'%')
                ->orWhere('description', 'like', '%'.$text.'%')
                ->orWhere('tax_rate_level', 'like', '%'.$text.'%')
                ->orWhere('county_name', 'like', '%'.$text.'%')
                ->orWhere('city_name', 'like', '%'.$text.'%')
                ->orWhere('zipcode', 'like', '%'.$text.'%');
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



    /* get ui schema for this model */
    public static function _getSchema(){
        
        $schema = AppSchema::getSchema('taxrate_schema');

        data_set($schema,'form.state_code.values',Util::getStates('US'),true);
        data_set($schema,'filters.tax_rate_level.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'tax_rate_level'),true);
        data_set($schema,'filters.state_code.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'state_code'),true);


        
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
        $user = Auth::guard('api')->user();
        
        parent::boot();
        static::creating(function ($model) use($user) {
            $model->created_at = Carbon::now()->toDateTimeString();
            $model->created_by = ($user ? $user->id : null);
        });
        static::updated(function ($model)use($user) {
            $model->updated_by = ($user ? $user->id : null);
        });        
        static::deleted(function ($model)use($user) {
            $model->deleted_by = ($user ? $user->id : null);
        });
    }

}