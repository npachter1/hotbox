<?php

namespace App\Models\Dispensary;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

use App\Models\AppSchema;
use App\Models\Auth\Location;
use App\Models\Auth\Addressbook;
use App\Models\Auth\User;

use App\Services\Dispensary\InventoryService;

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
class Inventory extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'dispensary_inventories';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['amount_unit','cost_unit','priceset_id','retail_unit','expires_at','weight_potency','room_ref','item_strain'];
	protected $dates = ['expires_at','audited_at','metrc_exception_at','metrc_synced_at','archived_at','created_at','updated_at','deleted_at'];
    protected $casts = ['quantity_received'=>'float','quantity_sold'=>'float','quantity_on_hand'=>'float','quantity_adjust'=>'float','quantity_requested'=>'float','quantity_pending'=>'float','amount_unit'=>'float','amount_unit_sale'=>'float','weight_potency'=>'float','cost_unit'=>'float','retail_unit'=>'float'];



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	public function product(){
		return $this->hasOne(Product::class,'id','product_id')->withoutGlobalScope(LocationScope::class);
	}
	
	public function location(){
		return $this->belongsTo(Location::class, 'location_id', 'id');
	}
	
	public function receiving(){
		return $this->belongsTo(Receiving::class, 'receiving_id', 'id')->withoutGlobalScope(LocationScope::class);
	}

	public function vendor(){
		return $this->hasOne(Addressbook::class,'id','addressbook_id');
	}
	
    public function pricing(){
		return $this->hasOne(Priceset::class,'id','priceset_id')->withoutGlobalScope(LocationScope::class);
	}

    public function taxes(){
		return $this->hasMany(Tax::class,'location_id','location_id')->ofActive()->orderBy('created_at','desc'); // get last tax group for this product type and location.
	}
	
	public function sales(){
	    return $this->hasMany(SaleItem::class,'inventory_id','id');
	}
	public function globalsales(){
	    return $this->hasMany(SaleItem::class,'inventory_id','id')->withoutGlobalScope(LocationScope::class);
	}
	

	public function adjustments(){
		return $this->hasMany(InventoryLog::class,'inventory_id','id')->orderBy('created_at','desc');
	}
	
	public function strain(){
		return $this->hasOne(InventoryStrain::class,'name','item_strain');
	}

	public function auditor(){
		return $this->belongsTo(User::class,'audited_by','id');
	}
	
    public function groupings(){
	    return $this->belongsToMany(Product::class,'disp_inventory_disp_product','disp_inventory_id','disp_product_id')->whereIn('type',['grouping','kit'])->withPivot('quantity','policy');
	}


    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){
        
        if (!$text) return $query;

            $query->where(function($q) use ($text) {
                $q->where('item_barcode', 'like', '%'.$text.'%')
                    ->orWhere('item_notes', 'like', '%'.$text.'%')
                    ->orWhere('item_strain', 'like', '%'.$text.'%')
                    ->orWhere('item_batch', 'like', '%'.$text.'%')
                    ->orWhere('metrc_tag', 'like', '%'.$text.'%')
                    ->orWhereHas('product', function($q) use ($text) {
                        $q->where(function($q) use ($text) {
                            $q->where('name', 'LIKE', '%'.$text.'%');
                        });
                    })
                    ->orWhereHas('product.category', function($q) use ($text) {
                        $q->where(function($q) use ($text) {
                            $q->where('name', 'LIKE', '%'.$text.'%');
                        });
                    })
                    ->orWhereHas('pricing', function($q) use ($text) {
                        $q->where(function($q) use ($text) {
                            $q->where('name_grade', 'LIKE', '%'.$text.'%');
                        });
                    });
            });

    }
    
    public function scopeOfSimilarBarcodeRfidSku($query, $code){
        if ($code) {
            $query->where(function($q) use ($code) {
                $q->where('item_barcode', 'like', '%'.$code.'%');
                $q->orWhere('metrc_tag', 'like', '%'.$code.'%');
            });
        }
        return $query;
    }


    /**
     * Scope a query to filter by an exact barcode, rfid or sku code.
     */
    public function scopeOfExactBarcodeRfidSku($query, $code){
        if ($code) {
            $query->where(function($q) use ($code) {
                $q->where('item_barcode', '=', $code)
                    ->orWhere('metrc_tag', '=', $code)
                    ->orWhereHas('product', function($q) use ($code) {
                        $q->where('sku','=', $code);
                    });
            });
        }
        return $query;
    }
    

    public function scopeOfMetrcCategory($query, $metrcCategoryId){
        if ($metrcCategoryId) {
            $query->whereHas('product.category', function($q) use ($metrcCategoryId) {
                $q->where('metrc_category_id', $metrcCategoryId);
            });
        }
        return $query;
    }
    
    
    /* special filter by inventory products nature type */
    public function scopeOfNatureType($query, $filter){
        
        if(!$filter || in_array(strtolower($filter),['','null','undefined',null,'all'])) return $query;

        return $query->whereHas('product',function($q)use($filter){
            foreach(explode(',',$filter) as $ind=> $filt)
                ($ind==0 ? $q->where('nature_type',$filt) : $q->orWhere('nature_type',$filt));
            
        });
        
    }
    
    
    /* filter by frontend query orderBy filter */
    public function scopeOfActive($query){
        
        return $query->whereNull('archived_at');

    }
    



    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/


    public function setItemNameAttribute($init=''){
        
        $this->attributes['item_name'] = $this->product->name;
        
    }

    
    public function setQuantityPendingAttribute($init=0){
        
        $this->attributes['quantity_pending'] = SaleItem::whereHas('sale',function($q){ $q->where('status','pending')->ofActive(); })->where('inventory_id',$this->id)->sum('quantity');
        
    }


    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/





    /* get ui schema injections for this model */
    public static function _getSchema(){
        
        $schema = AppSchema::getSchema('inventory_schema');

        data_set($schema,'form.addressbook_id.values',Addressbook::query()->select('id','name','licensenum')->where('type','vendor')->orderBy('name')->ofActive()->get(),true); // all vendor addresses registered
        data_set($schema,'form.priceset_id.values',Priceset::query()->select('*','name_grade as name')->with('productIds','categoryIds')->ofLive()->orderBy('rank')->get(),true); // all tax categories registered
        data_set($schema,'form.item_strain.values',InventoryStrain::_getFilterList(),true); // all indexed strains in a list

        data_set($schema,'filters.addressbook_id.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'addressbook_id'),true);
        data_set($schema,'filters.product_id.values',Product::select('id','name','category_id')->wherehas('inventory')->orderBy('name')->ofActive()->get(),true);
        data_set($schema,'filters.priceset_id.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'priceset_id'),true);

        
        /* agg data */



        return $schema;
        
    }



    /* model boot util - generate a unique suite integer upon creation */
    protected static function boot(){
        
        parent::boot();
        
        static::addGlobalScope(new LocationScope);                              // add location_id tenant scope
        
        static::creating(function ($model) {
            $model->location_id = $model->location_id ?: Auth::guard('api')->user()->location_id;      // inventory is scoped to location
            $model->created_at = $model->created_at ?: Carbon::now()->toDateTimeString();
        });
        static::saving(function ($model) {
            $model->setQuantityPendingAttribute();                              // when we save inventory (via sale update) we should update the qty pending.
            $model->setItemNameAttribute();                                     // indexing product name for inentory searhes by name
        });         
        static::saved(function ($model) {
            $invService = new InventoryService();
            if(!$model->archived_at) $invService->_saveStrain($model->item_strain); // this service will update the strain index   

            if($model->product) $model->product->save();                        // by saving product (HINT: touch wont work for this) it re-aggregates the inv_meta field for parent product displays
            
            foreach($model->groupings as $gps)
                $gps->save();                                                   // if this item belongs to any grouping type product - update those as well.
            
        });        
        
    }

}