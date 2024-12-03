<?php

namespace App\Models\Dispensary;

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
 * Class User.
 */
class ReceivingPackage extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'metrc_dispensary_packages';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['received_at'];
	protected $dates = ['received_at','archived_at','created_at','updated_at','deleted_at'];
    protected $casts = ['package_data' => 'object','weight_potency'=>'float'];


    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	public function location(){
		return $this->belongsTo(Location::class, 'location_id', 'id');
	}
	
	public function inventory(){
		return $this->hasOne(Inventory::class, 'metrc_tag', 'label');
	}

	public function vendor(){
		return $this->hasOne(Addressbook::class, 'id', 'vendor_id');
	}
	
	public function product(){
		return $this->hasOne(Product::class, 'id', 'product_id');
	}

	public function transfer(){
		return $this->belongsTo(ReceivingTransfer::class, 'manifest_id', 'manifest_id');
	}


    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){
        
        if (!$text) return $query;

        return $query->where(function ($query) use ($text) {
            $query->where('metrc_id',$text)
                ->orWhere('label',$text)
                ->orWhere('name', 'like', '%'.$text.'%')
                ->orWhereHas('vendor',function($v)use($text){
                    $v->where('name', 'like', '%'.$text.'%')
                        ->orWhere('licensenum','like','%'.$text.'%');
                });
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

    public static function _getParsedPotencyAmount($name){
        
        preg_match('/([0-9]{2,4}) *mg|(\d*\.?\d{1,4}) *g|(\d*\.?\d{1,4}) *ml/i',$name,$parse);

        if(($first = (float)data_get($parse,'1',0))>0) return ($first/1000);    // 1000mg = .1g
        elseif(($second = (float)data_get($parse,'2',0))>0) return ($second*1); // 1.0g = 1g
        elseif(($third = (float)data_get($parse,'3',0))>0) return ($third*1);   // 1000ml = 1g TODO: what is the conversion for this??
        else return 1;

    }


    /* get list (w equiv ratio) for form selection */
    public static function _getMetrcPackageList(){
        
        return self::select('id','label','name','package_data','is_held','received_at')->orderBy('created_at','desc')->get()->map(function($item,$key){
            return (object)[
                    'id'        => $item->id,
                    'name'      => $item->label.' - '.$item->name.' '.($item->is_held===true ? ($item->received_at ? 'Ready' : 'In Transit') : 'On Hold')
                ];
        });
        
    }

    public static function _getVendorsList(){
        
        return self::with('vendor')->select(DB::raw('vendor_id,COUNT(id) as count'))->where('status','confirmed')->whereDoesntHave('inventory')->groupBy('vendor_id')->get()->map(function($item,$key){
            return (object)[
                    'id'        => $item->vendor_id,
                    'name'      => ucwords(data_get($item->vendor,'name','Misc Vendor')).' '.data_get($item->vendor,'licensenum',null).' ('.$item->count.')'
                ];
        });
        
    }






    /* get and inject ui schema for this model */
    public static function _getSchema(){
        
        $schema = AppSchema::getSchema('receiving_schema');
        return $schema;
        
    }



    /* model boot util - generate a unique suite integer upon creation */
    protected static function boot(){
        
        parent::boot();
        
        static::addGlobalScope(new LocationScope);                              // add location_id tenant scope
        static::creating(function ($model) {
            if (Auth::guard('api')->user())
                $model->location_id = Auth::guard('api')->user()->location_id;      // inventory is scoped to location
            $model->created_at = $model->created_at ?: Carbon::now()->toDateTimeString();
        });
        
    }

}