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
 * Class Package.
 */
class Package extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'grow_packages';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = [
        'label',
        'package_type',
        'source_harvest_names',
        'plant_batch_id',
        'room_id',
        'location_id',
        'quantity',
        'unit_of_measure',
        'patient_license_number',
        'product_id',
        'packaged_at',
        'is_production_batch',
        'production_batch_number',
        'is_testing_sample',
        'is_process_validation_sample,',
        'contains_remediated_product',
        'remediation_at',
        'received_from_manifest_number',
        'received_from_facility_license_number',
        'received_from_facility_name',
        'received_price',
        'received_at',
        'lab_testing_state',
        'lab_testing_state_date',
        'is_on_hold',
        'archived_at',
        'finished_at'
    ];
	protected $dates = ['created_at','updated_at','deleted_at'];
    protected $casts = ['received_price'=>'float'];



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	public function location(){
		return $this->belongsTo(Location::class, 'location_id', 'id');
	}

    public function room() {
        return $this->hasOne(Room::class,'id', 'room_id');
    }

    public function item() {
        return $this->hasOne(Item::class,'id', 'product_id');
    }

    public function harvest() {
        return $this->hasMany(Harvest::class,'name', 'source_harvest_names');
    }

    public function plant_batch() {
        return $this->hasOne(PlantBatch::class, 'id', 'plant_batch_id');
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
        //return $query;

    }
    
    public function scopeOfHasQuantity($query){
        
        return $query->where('quantity','>',0);

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
                    'label'      => $item->name
                ];
        });
        
    }


    /* get ui schema for this model */
    public static function _getSchema(){
        
        $schema = AppSchema::getSchema('package_schema');

        data_set($schema,'form.product_id.values',Item::_getList() ?: [],true); // do this first before we get the filtered values in data set
        data_set($schema,'form.room_id.values',Room::_getList() ?: [],true); // do this first before we get the filtered values in data set
        data_set($schema,'form.plant_batch_id.values',PlantBatch::_getList() ?: [],true); // do this first before we get the filtered values in data set
        data_set($schema,'form.strain_id.values',Strain::_getList() ?: [],true); // do this first before we get the filtered values in data set

        return $schema;
        
    }



    /* model boot util - generate a unique suite integer upon creation */
    protected static function boot(){
        
        parent::boot();
        
        static::addGlobalScope(new LocationScope);                              // add location_id tenant scope
        static::creating(function ($model) {
            $model->location_id = Auth::guard('api')->user()->location_id;      // package is scoped to location
        });
        
    }


    public static function findByMetrcId($metrc_id)
    {
        return self::where('metrc_id',$metrc_id)
                    ->first();
    }

}