<?php

namespace App\Models\Dispensary;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

use App\Models\AppSchema;
use App\Models\Auth\Location;
use App\Models\Dispensary\TaxRate;

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
class Tax extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'dispensary_taxes';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['name','location_id','nature_type'];
	protected $dates = ['archived_at','created_at','updated_at','deleted_at'];
    protected $casts = [];



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	public function location(){
		return $this->belongsTo(Location::class, 'location_id', 'id');
	}

    /* tax rates assigned */
	public function rates(){
	    return $this->belongsToMany(Taxrate::class,'dispensary_tax_dispensary_tax_rate','tax_id','tax_rate_id');
	}
	
	



    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){
        
        if (!$text) return $query;

        return $query->where(function ($query) use ($text) {
            $query->where('name', 'like', '%'.$text.'%')
                ->orWhere('nature_type', 'like', '%'.$text.'%');
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
        
        $user = Auth::guard('api')->user();
        $schema = AppSchema::getSchema('tax_schema');


        data_set($schema,'form.location_id.values',Location::query()->select('id','name')->whereIn('status',['activated','migrating'])->get(),true); // current locations for default location
        data_set($schema,'form.rate_ids.values',TaxRate::query()->select('id','name')->ofActive()->get(),true); // rate names to select for category form
        data_set($schema,'filters.location_id.values',Util::getActiveFilterList(self::query()->ofActive()->whereIn('location_id',$user->locationsAssigned()->where('type',$user->location->type)->pluck('id')->toArray()),$schema,'location_id'),true);
        
        data_set($schema,'filters.location_id.focus',($user ? $user->location->id : null)); // focus the location id filter tabs to the users current location id
        data_set($schema,'model.location_id',($user ? $user->location->id : null)); // default location id for creating tax category

        
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
        
        static::creating(function ($model)use($user) {
            $model->created_at = Carbon::now()->toDateTimeString();
            $model->created_by = ($user ? $user->id : null);
        });
        static::updated(function ($model)use($user) {
            $model->updated_by = ($user ? $user->id : null);
        });
    }

}