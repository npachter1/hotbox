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
 * Class Reward.
 */
class InventoryStrain extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'dispensary_inventory_strains';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['name','notes'];
	protected $dates = ['created_at','updated_at','deleted_at'];
    protected $casts = ['items_count'=>'float'];



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

    public function inventory(){
        return $this->hasMany(Inventory::class,'name','item_strain');
    }
    


    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){
        
        if (!$text) return $query;

        return $query->where(function ($query) use ($text) {
            $query->where('name', 'like', '%'.$text.'%')
                ->orWhere('notes', 'like', '%'.$text.'%');
        });

    }
    
    
    /* filter by frontend query orderBy filter */
    public function scopeOfActive($query){
        
        return $query->where('items_count','>',0);

    }




    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/

    public function setItemAggAttribute($init=null){
        
        $this->attributes['inventory_count'] = Inventory::where('item_strain',$this->name)->ofActive()->count();
        $this->attributes['items_count'] = Inventory::where('item_strain',$this->name)->ofActive()->sum(DB::raw('dispensary_inventories.quantity_on_hand * dispensary_inventories.amount_unit'));
        
    }






    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/

    /* get discounts filter list data */
    public static function _getFilterList($rel='all'){

        $schema = AppSchema::getSchema('inventory_schema');
        return self::select('id','name','inventory_count')->ofHasFilter($rel)->orderBy('name')->get()->map(function($item,$key)use($schema){
            return (object)[
                'id'        => $item->name,
                'name'      => ucwords($item->name).' ('.$item->inventory_count.' Items)'
            ];
        });

    }


    /* get and dynamically inject ui schema for this models frontend vue filtering */
    public static function _getSchema(){
        
        $schema = AppSchema::getSchema('inventory_schema'); // is a sub-resource.
        return $schema;
        
    }



    /* model boot util - generate a unique suite integer upon creation */
    protected static function boot(){
        
        parent::boot();
        
        static::creating(function ($model) {
            $model->created_at = Carbon::now()->toDateTimeString();
        });
        static::saving(function ($model) {
            $model->setItemAggAttribute();                                      // indexing inventory and item counts
        }); 
        
    }

}