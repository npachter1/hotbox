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
 * Class Group.
 */
class Group extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'dispensary_groups';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['name','notes','type','filters'];
	protected $dates = ['archived_at','created_at','updated_at','deleted_at'];
    protected $casts = ['filters'=>'object'];



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

    /* relations */
    public function customers(){
        return $this->belongsToMany(Customer::class,'dispensary_customer_dispensary_group','group_id','customer_id')->withPivot('is_manual')->select(Customer::$abbv_fields);
    }
    
    public function discounts(){
        return $this->belongsToMany(Discount::class,'dispensary_discount_dispensary_group','group_id','discount_id');
    }
    
    public function campaigns(){
        return $this->hasMany(Campaign::class,'group_id','id');
    }
    
    /* list of customers that are sms optedin and has a mobile number entered */
    public function customersSms(){
        return $this->belongsToMany(Customer::class,'dispensary_customer_dispensary_group','group_id','customer_id')->where('sms_optin',1)->select(Customer::$abbv_fields);
    }

    /* list of customers that are email optedin and has an email entered */
    public function customersEmail(){
        return $this->belongsToMany(Customer::class,'dispensary_customer_dispensary_group','group_id','customer_id')->where('email_optin',1)->select(Customer::$abbv_fields);
    }


    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){
        
        if (!$text) return $query;

        return $query->where(function ($query) use ($text) {
            $query->where('name', 'like', '%'.$text.'%')
                ->orWhere('notes', 'like', '%'.$text.'%')
                ->orWhereHas('customers',function($q)use($text){
                    $q->where('first_name', 'like', '%'.$text.'%')
                        ->orWhere('last_name', 'like', '%'.$text.'%');
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

    /* Cast properties and/or assume defaults of this settings array by schema.form. */
    public function setFiltersAttribute($data){
        
        if(($schema = self::_getSchema()) == null) $this->attributes['filters'] = json_encode($data);
        elseif(($store = Util::setObjectFromSchema($data,$schema,'filters')) == null) $this->attributes['filters'] = json_encode($data);
        else $this->attributes['filters'] = json_encode($store);
        
    }







    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/

    /* get customer groups descriptor list data */
    public static function _getFilterList($rel='all'){

        return self::select('id','name')->withCount('customers','customersSms','customersEmail')->ofHasFilter($rel)->ofActive()->get()->map(function($item,$key){
            return (object)[
                'id'        => $item->id,
                'name'      => $item->name.' '.$item->customers_sms_count.'/'.$item->customers_count.' SMS',
                'customers_sms_count'   => $item->customers_sms_count,
                'customers_email_count' => $item->customers_email_count,
                'customers_count'       => $item->customers_count
            ];
        });

    }



    /* get and dynamically inject ui schema for this models frontend vue filtering */
    public static function _getSchema(){
        
        $user = Auth::guard('api')->user();
        $schema = AppSchema::getSchema('group_schema');

        data_set($schema,'filters.discount_id.values',Discount::_getFilterList('groups'),true);
        data_set($schema,'filters.created_at.values',Util::getActiveFilterDateRange('INIT'),true);
        
        data_set($schema,'form.location_ids.values',Location::query()->select('id','name')->where('status','activated')->where('type',$user->location->type)->get(),true);
        data_set($schema,'form.category_ids.values',Category::query()->select('id','name')->get(),true);
        
        data_set($schema,'form.filters.properties.preferences.values',Customer::_getPreferenceFilterList(),true);


        /* agg data */
        $todayAgo = Carbon::now()->subDays(1)->toDateTimeString();
        data_set($schema,'agg',self::select(DB::raw('
            COUNT(DISTINCT(id)) as `all`,
            COUNT(DISTINCT(CASE WHEN created_at >= "'.$todayAgo.'" THEN id ELSE NULL END)) as all_today
        '))->ofActive()->first(),true);


        return $schema;
        
    }



    /* model boot util - generate a unique suite integer upon creation */
    protected static function boot(){
        
        parent::boot();
        
        static::creating(function ($model) {
            $model->created_at = Carbon::now()->toDateTimeString();
        });
        
    }

}