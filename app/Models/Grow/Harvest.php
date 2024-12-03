<?php

namespace App\Models\Grow;

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
 * Class Harvest.
 */
class Harvest extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'grow_harvests';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = [
        'name',
        'location_id',
        'harvest_type',
        'source_strain_count',
        'drying_room_id',
        'patient_license_number',
        'current_weight',
        'total_waste_weight',
        'total_trim_weight',
        'plant_count',
        'total_wet_weight',
        'total_restored_weight',
        'package_count',
        'total_packaged_weight',
        'unit_of_weight',
        'lab_testing_state',
        'lab_testing_state_date',
        'is_on_hold',
        'harvest_start_at',
        'finished_at',
        'archived_at'
    ];
	protected $dates = ['created_at','updated_at','deleted_at'];
    protected $casts = [];


    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	public function location(){
		return $this->belongsTo(Location::class, 'location_id', 'id');
	}

    public function strain(){
        return $this->hasOne(Strain::class,'id', 'strain_id');
    }

    public function room() {
        return $this->hasOne(Room::class,'id', 'drying_room_id');
    }

    public function plant() {
        return $this->hasMany(Plant::class,'harvest_id', 'id');
    }


    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){
        
        if (!$text) return $query;

        return $query->where(function ($query) use ($text) {
            $query->where('name', 'like', '%'.$text.'%');
                });
    }
    
    
    /* filter by frontend query orderBy filter */
    public function scopeOfActive($query){
        
        return $query->whereNull('deleted_at');
        //return $query;

    }
    
    public function scopeOfIsActive($query){
        
        return $query->whereNull('finished_at');

    }



    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/




    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/


    public static function _getList(){
        
        return self::select('id','name')->orderBy('name')->get()->map(function($item,$key){
            return (object)[
                    'id'        => $item->id,
                    'name'      => $item->name
                ];
        });
        
    }


    /* get ui schema for this model */
    public static function _getSchema(){
        
        $schema = AppSchema::getSchema('harvest_schema');

        data_set($schema,'form.strain_id.values',Strain::_getList() ?: [],true); // do this first before we get the filtered values in data set

        data_set($schema,'form.drying_room_id.values',Room::_getList() ?: [],true); // do this first before we get the filtered values in data set
        data_set($schema,'filters.drying_room_id.values',Util::getActiveFilterList(self::query()->ofActive()->ofIsActive(),$schema,'drying_room_id'),true);
 
        data_set($schema,'form.item_id.values',Item::_getList() ?: [],true); // do this first before we get the filtered values in data set

        return $schema;
        
    }



    /* model boot util - generate a unique suite integer upon creation */
    protected static function boot(){
        
        parent::boot();
        
        static::addGlobalScope(new LocationScope);                              // add location_id tenant scope
        static::creating(function ($model) {
            $model->location_id = Auth::guard('api')->user()->location_id;      // harvest is scoped to location
        });
        
    }

    public static function findByMetrcId($metrc_id)
    {
        return self::where('metrc_id',$metrc_id)
                    ->first();
    }

}