<?php

namespace App\Models\Auth;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

use App\Models\AppSchema;
use App\Models\Dispensary\Inventory;
use App\Models\Dispensary\Receiving;
use App\Models\Grow\Transfer;

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
class Addressbook extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'auth_addressbook';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['type','name','address1','address2','city','region','country','zip','phone','contact_notes','email','licensenum'];
	protected $dates = ['archived_at','created_at','updated_at','deleted_at'];
    protected $casts = [];



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	/*  location this address belongs to */
	public function location(){
		return $this->belongsTo(Location::class,'location_id','id');
	}
	
	
	public function inventory(){
		return $this->hasMany(Inventory::class,'addressbook_id','id');
	}
	
	public function receiving(){
		return $this->hasMany(Receiving::class,'addressbook_id','id');
	}
	
	public function transfers(){
		return $this->hasMany(Transfer::class,'addressbook_id','id');
	}



    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){
        
        if (!$text) return $query;

        return $query->where(function ($query) use ($text) {
            $query->where('name', 'like', '%'.$text.'%')
                ->orWhere('email', 'like', '%'.$text.'%')
                ->orWhere('address1', 'like', '%'.$text.'%')
                ->orWhere('address2', 'like', '%'.$text.'%')
                ->orWhere('city', 'like', '%'.$text.'%')
                ->orWhere('region', 'like', '%'.$text.'%')
                ->orWhere('phone', 'like', '%'.$text.'%')
                ->orWhere('industry','like', '%'.$text.'%')
                ->orWhere('licensenum','like', '%'.$text.'%');
        });

    }
    
    
    /* filter by frontend query orderBy filter */
    public function scopeOfActive($query){
        
        return $query;
        //return $query->whereNull('archived_at');

    }
    




    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/








    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/

    public static function _getVendorsList($types=['vendor']){
        
        
        $locs = Location::whereNotNull('addressbook_id')->select('addressbook_id')->pluck('addressbook_id')->unique()->values()->toArray();
        $lics = Location::select('licensenum','name')->pluck('licensenum','name')->toArray();

        return self::query()->select('id','name','licensenum')->whereIn('type',$types)->orderBy('name')->orderBy('updated_at','desc')->limit(1000)->ofActive()->get()->map(function($item,$key)use($locs,$lics){
            return (object)[
                    'id'        => $item->id,
                    'name'      => ((in_array($item->id,$locs) && in_array($item->licensenum,$lics)) ? array_search($item->licensenum,$lics) : ucwords($item->name)).' '.$item->licensenum,
                    'type'      => ((in_array($item->id,$locs) && in_array($item->licensenum,$lics)) ? 'internal' : 'external')
                ];
        })->unique('name')->values();
        
    }
    
    
    /* get product filters from inventory */    
    public static function _getProductVendorFilters(){
        
        return self::select('id','name')->with('inventory')->where('type','vendor')->whereHas('inventory')->whereNull('archived_at')->orderBy('name')->get()->map(function($item,$key){
            return (object)[
                'id'        => $item->id,
                'name'      => $item->name,
                'count'     => $item->inventory->unique('product_id')->count()
            ];
        });
        
    }



    /* get ui schema for this model */
    public static function _getSchema(){
        
        $schema = AppSchema::getSchema('addressbook_schema');

        data_set($schema,'form.country.values',Util::getCountries(),true);

        data_set($schema,'filters.type.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'type'),true);
        data_set($schema,'filters.region.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'region'),true);


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
        
        //static::addGlobalScope(new LocationScope);                            // add location_id tenant scope
        static::creating(function ($model) {
            //$model->location_id = Auth::guard('api')->user()->location_id;
            $model->created_at = $model->created_at ?: Carbon::now()->toDateTimeString();
        });
        
    }

}
