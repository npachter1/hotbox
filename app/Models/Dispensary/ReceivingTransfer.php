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
 * Class User.
 */
class ReceivingTransfer extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'metrc_dispensary_transfers';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['manifest_id','manifest_data','received_at'];
	protected $dates = ['received_at','archived_at','created_at','updated_at','deleted_at'];
    protected $casts = ['manifest_data' => 'object'];


    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	public function location(){
		return $this->belongsTo(Location::class, 'location_id', 'id');
	}

	public function receiving(){
		return $this->belongsTo(Receiving::class, 'manifest_id', 'transfer_id');
	}
	
	public function packages(){
		return $this->hasMany(ReceivingPackage::class, 'manifest_id', 'manifest_id');
	}



    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){
        
        if (!$text) return $query;

        return $query->where(function ($query) use ($text) {
            $query->where('metrc_id',$text)
                ->orWhere('manifest_id', 'like', '%'.$text.'%');
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

    /* get list (w equiv ratio) for form selection */
    public static function _getMetrcTransferList(){
        
        return self::select('id','manifest_id','manifest_data','received_at')->orderBy('created_at','desc')->get()->map(function($item,$key){
            return (object)[
                    'id'        => $item->id,
                    'name'      => $item->manifest_id.' from '.data_get($item,'manifest_data.TransporterFacilityName',null).' ('.data_get($item,'manifest_data.PackageCount',null).') Packages. '.($item->received_at ? ' - Ready' : '')
                ];
        });
        
    }

    public static function _getMetrcTransferStatusList(){
        
        return self::select(DB::raw('status,COUNT(id) as count'))->groupBy('status')->get()->map(function($item,$key){
            return (object)[
                    'id'        => $item->status,
                    'name'      => ucwords($item->status).' ('.$item->count.')'
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