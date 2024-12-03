<?php

namespace App\Models\Dispensary;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

use App\Models\AppSchema;
use App\Models\Auth\Location;
use App\Models\Auth\Addressbook;

use App\Services\ReceivingService;

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
class Receiving extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'dispensary_receivings';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['transfer_id', 'invoice_refs','po_number', 'addressbook_id','items'];
	protected $dates = ['sent_at','confirmed_at','archived_at','received_at', 'created_at','updated_at','deleted_at'];
    protected $casts = ['items'=>'object'];



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	/*  suite this receiving belongs to */
	public function location(){
		return $this->belongsTo(Location::class,'loaction_id','id');
	}
	
	public function vendor(){
		return $this->hasOne(Addressbook::class,'id','addressbook_id');
	}
	
	public function transfer(){
		return $this->hasOne(ReceivingTransfer::class,'manifest_id','transfer_id');
	}



    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){
        
        if (!$text) return $query;

        return $query->where(function ($query) use ($text) {
            $query->where('transfer_id', 'like', '%'.$text.'%')
                ->orWhere('invoice_refs', 'like', '%'.$text.'%')
                ->orWhereHas('vendor', function($q) use ($text) {
                    $q->where('name','like', '%'.$text.'%');
                });
        });

    }
    
    
    /* filter by frontend query orderBy filter */
    public function scopeOfActive($query){
        
        return $query->whereNull('archived_at');

    }
    




    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/

    /* get preferences comma seperated into array, and set back as comma seperated for concat search in groups filters */
    public function getInvoiceRefsAttribute($val){

        return ($val ? explode(',',$val) : []);
    
    }

    public function setInvoicerefsAttribute($data){
        
        $this->attributes['invoice_refs'] = ($data ? join(',',$data) : null);
        
    }
    
    
    /* articles sent and total value attributes */
    public function setArticlesSentAttribute($data){
        
        $this->attributes['articles_sent'] = collect(($data ?: []))->count();
        
    }  
    
    public function setArticlesValueAttribute($data){
        
        $this->attributes['articles_value'] = collect(($data ?: []))->reduce(function($cry,$item){
            return $cry + (((float)data_get($item,'quantity_received',0)/(data_get($item,'amount_unit',1) ?: 1))*data_get($item,'cost_unit',0));
        },0);
        
    }



    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/










    /* get ui schema for this model */
    public static function _getSchema(){
        
        $schema = AppSchema::getSchema('receiving_schema');

        data_set($schema,'form.transfer_search.values',ReceivingTransfer::_getMetrcTransferStatusList(),true);
        data_set($schema,'form.addressbook_id.values',Addressbook::_getVendorsList(['vendor']),true);

        data_set($schema,'filters.created_at.values',Util::getActiveFilterDateRange('INIT'),true);
        data_set($schema,'filters.status.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'status'),true);
        data_set($schema,'filters.addressbook_id.values',Addressbook::whereHas('receiving')->select('id','name')->orderBy('name')->ofActive()->get(),true);

        data_set($schema,'form.items.properties.priceset_id.values',Priceset::query()->select('*','name_grade as name')->with('productIds','categoryIds')->ofLive()->orderBy('rank')->orderBy('name')->get(),true); // all pricesets..
        data_set($schema,'form.package_search.vendors',ReceivingPackage::_getVendorsList(),true);


        /* agg data */
        $todayAgo = Carbon::now()->subDays(1)->toDateTimeString();
        data_set($schema,'agg',self::select(DB::raw('
            COUNT(DISTINCT(id)) as `all`,
            COUNT(DISTINCT(CASE WHEN created_at >= "'.$todayAgo.'" AND archived_at IS NULL THEN id ELSE NULL END)) as all_today
        '))->ofActive()->first(),true);


        return $schema;
        
    }



    /* model boot util - generate a unique suite integer upon creation */
    protected static function boot(){
        
        parent::boot();
        
        static::addGlobalScope(new LocationScope);                              // add location_id tenant scope
        static::creating(function ($model) {
            $model->location_id = Auth::guard('api')->user()->location_id;
            $model->created_at = Carbon::now()->toDateTimeString();
        });
        static::saving(function ($model) {
            $model->setAttribute('articles_sent',$model->items);
            $model->setAttribute('articles_value',$model->items);
        });
        
    }

}