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
 * Class PlantBatch.
 */
class PlantBatch extends Eloquent
{

    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/

    protected $table = 'grow_plant_batches';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify

	protected $fillable = [
        'name',
        'label',
        'type',
        'location_id',
        'room_id',
        'strain_id',
        'patient_license_number',
        'count',
        'live_count',
        'packaged_count',
        'harvested_count',
        'destroyed_count',
        'source_package_id',
        'source_plant_id',
        'last_modified',
        'planted_at'
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

    public function room(){
        return $this->hasOne(Room::class,'id', 'room_id');
    }

    public function source_package() {
        return $this->hasOne(Package::class,'id', 'soure_package_id');
    }

    public function source_plant() {
        return $this->hasOne(Plant::class, 'id', 'source_plant_id');
    }


    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){

        if (!$text) return $query;

        return $query->where(function ($query) use ($text) {
            $query->where('name', 'like', '%'.$text.'%')->orWhere('label', 'like', '%'.$text.'%');
                });
    }


    /* filter by frontend query orderBy filter */
    public function scopeOfActive($query){

        return $query->whereNull('deleted_at');

    }

    public function scopeOfHasCount($query){

        return $query->where('count','>',0);

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

        $schema = AppSchema::getSchema('plantbatch_schema');

        data_set($schema,'form.strain_id.values',Strain::_getList() ?: [],true); // do this first before we get the filtered values in data set
        data_set($schema,'form.room_id.values',Room::_getList() ?: [],true); // do this first before we get the filtered values in data set
        data_set($schema,'form.item_id.values',Item::_getListByCategory('immature') ?: [],true); // do this first before we get the filtered values in data set
        data_set($schema,'form.source_plant_id.values',Plant::_getList() ?: [],true); // do this first before we get the filtered values in data set

        data_set($schema,'filters.strain_id.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'strain_id'),true);
        data_set($schema,'filters.room_id.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'room_id'),true);

        $growth_phases = [
            [
                'id' => 'Young',
                'name' => 'Young'
            ],
            [
                'id' => 'Vegetative',
                'name' => 'Vegetative'
            ],
            [
                'id' => 'Flowering',
                'name' => 'Flowering'
            ],
            [
                'id' => 'Destroyed',
                'name' => 'Destroyed'
            ]
        ];
        data_set($schema,'form.growth_phase.values',$growth_phases,true);

        return $schema;

    }



    /* model boot util - generate a unique suite integer upon creation */
    protected static function boot(){

        parent::boot();

        static::addGlobalScope(new LocationScope);                              // add location_id tenant scope
        static::creating(function ($model) {
            $model->location_id = Auth::guard('api')->user()->location_id;      // plantbatch is scoped to location
        });

    }

    public static function findByMetrcId($metrc_id)
    {
        return self::where('metrc_id',$metrc_id)
                    ->first();
    }

}
