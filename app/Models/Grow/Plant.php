<?php

namespace App\Models\Grow;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

use App\Models\AppSchema;
use App\Models\Auth\Location;
use App\Services\Metrc\MetrcRequestService;

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
 * Class Plant.
 */
class Plant extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'grow_plants';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = [
        'label',
        'growth_phase',
        'state',
        'plant_batch_id',
        'strain_id',
        'room_id',
        'location_id',
        'patient_license_number',
        'harvest_id',
        'harvested_unit_of_weight',
        'harvested_wet_weight',
        'is_on_hold',
        'planted_at',
        'vegetative_at',
        'flowering_at',
        'harvested_at',
        'destroyed_at',
        'last_modified',
        'destroyed_note'
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
        return $this->hasOne(Room::class,'id', 'room_id');
    }

    public function plant_batch() {
        return $this->hasOne(PlantBatch::class, 'id', 'plant_batch_id');
    }

    public function harvest() {
        return $this->hasOne(Harvest::class,'id', 'harvest_id');
    }


    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){
        
        if (!$text) return $query;

        return $query->where(function ($query) use ($text) {
            $query->where('label', 'like', '%'.$text.'%');
                });
    }
    
    
    /* filter by frontend query orderBy filter */
    public function scopeOfActive($query){
        
        return $query->whereNull('deleted_at');

    }

    public function scopeOfGrowing($query){
        
        return $query->whereNull('deleted_at')->whereNull('harvested_at')->whereNull('destroyed_at');

    }
    




    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/








    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/

    public static function _getList(){
        
        return self::select('id','label')->orderBy('label')->get()->map(function($item,$key){
            return (object)[
                    'id'        => $item->id,
                    'label'      => $item->label
                ];
        });
        
    }

    public static function _getActiveList(){
        
        return self::select('*')->with('strain')->where('harvested_at','=',null)->where('destroyed_at','=',null)->orderBy('label')->get()->map(function($item,$key){
            return (object)[
                    'id'        => $item->id,
                    'label'      => $item->label,
                    'strain' => ['id' => $item->strain ? $item->strain->id : null, 'name' => $item->strain ? $item->strain->name : null]
                ];
        });
        
    }



    /* get ui schema for this model */
    public static function _getSchema(){
        
        $schema = AppSchema::getSchema('plant_schema');

        data_set($schema,'form.strain_id.values',Strain::_getList() ?: [],true); // do this first before we get the filtered values in data set
        data_set($schema,'form.plant_id.values',self::_getActiveList() ?: [],true); // do this first before we get the filtered values in data set
        data_set($schema,'filters.strain_id.values',Util::getActiveFilterList(self::query()->ofGrowing(),$schema,'strain_id'),true);

        data_set($schema,'form.room_id.values',Room::_getList() ?: [],true); // do this first before we get the filtered values in data set
        data_set($schema,'filters.room_id.values',Util::getActiveFilterList(self::query()->ofGrowing(),$schema,'room_id'),true);

        data_set($schema,'filters.growth_phase.values',Util::getActiveFilterList(self::query()->ofGrowing(),$schema,'growth_phase'),true);

        data_set($schema,'filters.state.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'state'),true);

        data_set($schema,'form.plant_batch_id.values',PlantBatch::_getList() ?: [],true); // do this first before we get the filtered values in data set

        data_set($schema,'form.harvest_id.values',Harvest::_getList() ?: [],true); // do this first before we get the filtered values in data set

        // determine if current location can assign rooms when creating Plant Batches
        $user = Auth::guard('api')->user();
        $can_assign_plant_batch_rooms = true;
        // if (!$user->location->is_demo && $user->location->settings->regulatory_agent == 'metrc') {
        //     $api = new MetrcRequestService;
        //     $facilities = $api->getFacilities($user);
        //     if ($facilities) {
        //         foreach($facilities as $facility)
        //             if ($facility->License->Number == $user->location->licensenum)
        //                 $can_assign_plant_batch_rooms = data_get($facility,'FacilityType.CanAssignRoomsToPlantBatches',true);
        //     }
        // }

        data_set($schema,'form.can_assign_plant_batch_rooms',$can_assign_plant_batch_rooms, true);


        $location = Auth::guard('api')->user()->location_id;

        /* agg data */
        $agg = DB::select("select 'Growing Plants' as 'name', IFNULL(count(label),0) as count from grow_plants where deleted_at is null and location_id='$location' and state = 'Tracked'
        union select gp.name, IFNULL(sum(gp.count),0) as count from 
        ( select 'Vegetative' as 'name', 0 as count  
        union select 'Flowering' as 'name', 0 as count  
        union select growth_phase as 'name', IFNULL(count(`label`),0) as count from grow_plants where deleted_at is null and location_id='$location' and state = 'Tracked' group by growth_phase
        ) as gp group by gp.name desc
        union select 'Plant Batches' as 'name', IFNULL(sum(count),0) as count from grow_plant_batches where location_id='$location' and deleted_at is null 
        union select 'Harvests' as 'name', IFNULL(count(name),0) as count from grow_harvests where deleted_at is null and location_id='$location' and finished_at is null 
        union select 'Packages' as 'name', IFNULL(count(label),0) as count from grow_packages where deleted_at is null and location_id='$location' and transfer_id is null");
        
        data_set($schema,'agg',$agg,true);


        return $schema;
        
    }



    /* model boot util - generate a unique suite integer upon creation */
    protected static function boot(){
        
        parent::boot();
        
        static::addGlobalScope(new LocationScope);                              // add location_id tenant scope
        static::creating(function ($model) {
            $model->location_id = Auth::guard('api')->user()->location_id;      // plant is scoped to location
        });
        
    }


    public static function findByMetrcId($metrc_id)
    {

        return self::where('metrc_id',$metrc_id)
                    ->first();
    }

}