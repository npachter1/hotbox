<?php

namespace App\Models\Grow;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

use App\Models\AppSchema;
use App\Models\Auth\Location;
use App\Models\Auth\Addressbook;

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
 * Class Transfer.
 */
class Transfer extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'grow_transfers';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['type','addressbook_id','manifest_number','manifest_data','transporter_name','transporter_licensenum','transfersale_fee','transfersale_total'];
	protected $dates = ['received_at','archived_at','created_at','updated_at','deleted_at'];
    protected $casts = ['manifest_data'=>'object','transfersale_fee'=>'float','transfersale_total'=>'float'];


    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	public function location(){
		return $this->belongsTo(Location::class, 'location_id', 'id');
	}

    public function packages() {
        return $this->hasMany(Package::class,'transfer_id', 'id');
    }

    public function receiver() {
        return $this->hasOne(Addressbook::class,'id', 'addressbook_id');
    }
    
    

    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){
        
        if (!$text) return $query;

        return $query->where(function ($query) use ($text){
            $query->where('manifest_id', 'like', '%'.$text.'%')
                ->orWhereHas('packages', function($q) use ($text) {
                        $q->whereHas('item',function($s)use($text){
                            $s->where('name','like','%'.$text.'%');
                    });
                });
        });
        
    }
    
    
    /* filter by frontend query orderBy filter */
    public function scopeOfActive($query){
        
        return $query->whereNull('deleted_at');
        //return $query;

    }
    




    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/








    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/


    public static function _getList(){
        
        return self::select('id','manifest_id')->orderBy('manifest_id')->get()->map(function($item,$key){
            return (object)[
                    'id'        => $item->id,
                    'name'      => $item->manifest_id
                ];
        });
        
    }


    /* get ui schema for this model */
    public static function _getSchema(){
        
        $schema = AppSchema::getSchema('transfer_schema');

        data_set($schema,'form.addressbook_id.values',Addressbook::_getVendorsList(['retailer','vendor','location']),true); // do this first before we get the filtered values in data set
        
        data_set($schema,'filters.status.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'status'),true);
        data_set($schema,'filters.addressbook_id.values',Addressbook::whereHas('transfers')->select('id','name')->orderBy('name')->ofActive()->get(),true);


        return $schema;
        
    }



    /* model boot util - generate a unique suite integer upon creation */
    protected static function boot(){
        
        parent::boot();
        $user = Auth::guard('api')->user();
        
        static::addGlobalScope(new LocationScope);                              // add location_id tenant scope
        static::creating(function ($model)use($user) {
            $model->location_id = $model->location_id ?: ($user ? $user->location_id : null);  // transfer is scoped to location - or can be added on create
            $model->created_at = $model->created_at ?: Carbon::now()->toDateTimeString();
        });
        
    }


}