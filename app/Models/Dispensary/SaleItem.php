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
 * Class Sale.
 */
class SaleItem extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'dispensary_sale_items';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['quantity','sale_price','tax_category_id','is_confirmed','group_ref'];
	protected $dates = ['created_at','updated_at','deleted_at'];
    protected $casts = ['quantity'=>'float','price'=>'float','discount'=>'float','tax'=>'float','sale_price'=>'float','thc_equivalent_grams'=>'float', 'is_confirmed'=>'boolean'];



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	/*  location this sale belongs to */
	public function location(){
		return $this->belongsTo(Location::class,'location_id','id');
	}
	
    public function sale() {
        return $this->belongsTo(Sale::class,'sale_id','id')->withoutGlobalScope(LocationScope::class);
    }

    public function inventory() {
        return $this->hasOne(Inventory::class,'id','inventory_id')->withoutGlobalScope(LocationScope::class);
    }
	
	



    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    public function scopeOfSaleStatus($query,$status){
        
        return $query->wherehas('sale',function($q)use($status){
            $q->where('status',$status);
        });

    }


    /* filter based on a a customer from saleorder */
    public function scopeOfCustomerSettledSale($query, $customerId){
        
        if(!$customerId) return $query;

        return $query->whereHas('sale',function($q)use($customerId){
                $q->where('customer_id', $customerId)->where('status','settled');
            });

    }




    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/








    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/










    /* get and dynamically inject ui schema for this models frontend vue filtering */
    public static function _getSchema(){
        
        $schema = AppSchema::getSchema('saleitem_schema');

        return $schema;
        
    }



    /* model boot util - generate a unique suite integer upon creation */
    protected static function boot(){
        
        parent::boot();
        
        static::addGlobalScope(new LocationScope);                              // add location_id tenant scope
        static::creating(function ($model) {
            $model->location_id = Auth::guard('api')->user()->location_id;
            $model->created_at = $model->created_at ?: Carbon::now()->toDateTimeString();
        });
        
    }

}