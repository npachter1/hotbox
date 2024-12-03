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
class Drawer extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'dispensary_drawers';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['opening_balance','current_balance','closing_balance','closed_at','name'];
	protected $dates = ['closed_at','created_at','updated_at','deleted_at'];
    protected $casts = ['opening_balance'=>'float','closing_balance'=>'float','current_balance'=>'float','total_sale_price'=>'float','total_thc_equivalent_grams'=>'float'];



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	/*  location this drawer belongs to */
	public function location(){
		return $this->belongsTo(Location::class,'location_id','id');
	}
	
	public function user(){
		return $this->belongsTo(User::class,'user_id','id');
	}

    public function events() {
        return $this->hasMany(DrawerEvent::class,'drawer_id','id')->orderBy('created_at', 'desc');
    }
    
    public function esum() {
        return $this->hasMany(DrawerEvent::class,'drawer_id','id')->select('event_type','total','created_at')->orderBy('created_at', 'desc');
    }

    public function sales() {
        return $this->hasMany(Sale::class,'drawer_id','id')->orderBy('created_at', 'desc');
    }
    
    



    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/


    /* filter by frontend query orderBy filter */
    public function scopeOfActive($query){
        
        return $query->whereNull('closed_at');

    }
    




    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/








    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/

    /* get drawers with staff name for filtering selection */
    public static function _getStaffDrawers(){
        
        return self::with('user')->orderBy('created_at','desc')->get()->map(function($item,$key){
            return (object)[
                    'id'        => $item->id,
                    'name'      => $item->name.' - '.data_get($item,'user.name',null).' $'.number_format((float)$item->current_balance,2)
                ];
        });
        
    }



    /* get and dynamically inject ui schema for this models frontend vue filtering */
    public static function _getSchema(){
        
        $schema = AppSchema::getSchema('drawer_schema');

        data_set($schema,'form.user_id.values',User::select('id','name')->orderBy('name')->get(),true);
        data_set($schema,'filters.user_id.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'user_id'),true);
        data_set($schema,'filters.created_at.values',Util::getActiveFilterDateRange('INIT'),true);

        
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
        
        static::addGlobalScope(new LocationScope);                              // add location_id tenant scope
        static::creating(function ($model)use($user) {
            $model->location_id = $user->location_id;
            $model->created_at = Carbon::now()->toDateTimeString();
            $model->name = $model->name ?: data_get($user->location,'settings.drawer_prefix','DRW').'-'.Carbon::now()->format('mdyHis');
        });
        
    }

}