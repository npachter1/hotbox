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
 * Class Discount.
 */
class Discount extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'dispensary_discounts';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['name','descriptor','type','discount_type','discount_amount','discount_code','rank','is_exclusive','distribution_type','scheduled_at','settings','is_active','max_per_customer'];
	protected $dates = ['scheduled_at','end_at','archived_at','created_at','updated_at','deleted_at'];
    protected $casts = ['settings'=>'object','is_active'=>'boolean','is_exclusive'=>'boolean','discount_amount'=>'float'];



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

    /* relations (associations) */
    public function groups(){
        return $this->belongsToMany(Group::class,'dispensary_discount_dispensary_group','discount_id','group_id');
    }

    public function locations(){
        return $this->belongsToMany(Location::class,'auth_location_dispensary_discount','discount_id','location_id');
    }    
    
    public function categories(){
        return $this->belongsToMany(Category::class,'dispensary_category_dispensary_discount','discount_id','category_id')->withoutGlobalScope(LocationScope::class);
    }
    
    public function products(){
        return $this->belongsToMany(Product::class,'dispensary_discount_dispensary_product','discount_id','product_id')->withoutGlobalScope(LocationScope::class);
    }
    
    public function pricesets(){
        return $this->belongsToMany(Priceset::class,'dispensary_discount_dispensary_priceset','discount_id','priceset_id')->withoutGlobalScope(LocationScope::class);
    }
    
    public function sales(){
        return $this->belongsToMany(Sale::class,'dispensary_discount_dispensary_sale','discount_id','sale_id')->whereIn('status',['settled']);
    }
    
    public function campaigns(){
        return $this->hasMany(Campaign::class,'discount_id','id');
    }
    public function campaigncodes(){
        return $this->hasMany(Campaign::class,'discount_id','id')->select('id','group_id','discount_id','campaign_code');
    }



    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){
        
        if (!$text) return $query;

        return $query->where(function ($query) use ($text) {
            $query->where('name', 'like', '%'.$text.'%')
                ->orWhere('descriptor', 'like', '%'.$text.'%')
                ->orWhereRaw('LOWER(`discount_code`)="'.strtolower($text).'"')
                ->orWhereHas('campaigns',function($q)use($text){                // or if it is a campaign code and campaign is linked to this discount                
                    $q->whereRaw('LOWER(`campaign_code`)="'.strtolower($text).'"')->whereIn('status',['working','completed','pending']);
                });
        });

    }


    /* filter based on any belongsToMany found */
    public function scopeOfAnyAssociation($query, $rel, $ids){
        
        if(empty($ids)) return $query->whereDoesntHave($rel);                   // if no ids then make sure the resource doesnt have any specific matches
        
        $query->where(function($q)use($rel,$ids){
            $q->whereHas($rel,function($y)use($ids){ 
                $y->whereIn('id',$ids);
            })->orWhereDoesntHave($rel);                                        // basically, if we have a match of any of the passed ids OR the rule doesnt have any assoviations for the relation
        });

        return $query;

    }

    
    /* filter by frontend query orderBy filter */
    public function scopeOfActive($query){
        
        return $query->whereNull('archived_at');

    }
    
    public function scopeOfLive($query){
        
        return $query->whereNull('archived_at')
            ->where('is_active',1)->where(function($q){
                $q->where('scheduled_at','<',Carbon::now()->toDateTimeString())
                    ->orWhereNull('scheduled_at');
            });

    }
    



    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/

    /* Cast properties and/or assume defaults of this settings array by schema.form. */
    public function setSettingsAttribute($data){
        
        if(($schema = self::_getSchema()) == null) $this->attributes['settings'] = json_encode($data);
        elseif(($store = Util::setObjectFromSchema($data,$schema,'settings')) == null) $this->attributes['settings'] = json_encode($data);
        else $this->attributes['settings'] = json_encode($store);
        
    }






    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/

    /* get discounts filter list data */
    public static function _getFilterList($rel='all'){

        return self::select('id','name','discount_code','descriptor','is_active','is_exclusive','scheduled_at','created_at')->withCount('sales')->ofHasFilter($rel)->orderBy('name')->ofActive()->get()->map(function($item,$key){
            $sched = ($item->scheduled_at ? $item->scheduled_at->timestamp : Carbon::now()->timestamp);
            return (object)[
                'id'        => $item->id,
                'name'      => $item->name.' '.($item->discount_code ? '['.$item->discount_code.']' : '').(($item->is_active===false || $sched>Carbon::now()->timestamp) ? ' **[NOT YET ACTIVE]**' : ''),
                'nickname'  => $item->descriptor,
                'is_active' => (($item->is_active===false || $sched>Carbon::now()->timestamp) ? false : true),
                'is_exclusive'  => $item->is_exclusive,
                'discount_code' => $item->discount_code,
                'count'         => $item->sales_count,
                'scheduled_at'  => ($item->scheduled_at ? $item->scheduled_at->toDateTimeString() : null),
                'created_at'    => $item->created_at->toDateTimeString()
            ];
        });

    }


    /* get and dynamically inject ui schema for this models frontend vue filtering */
    public static function _getSchema(){
        
        $user = Auth::guard('api')->user();
        $schema = AppSchema::getSchema('discount_schema');

        data_set($schema,'form.group_ids.values',Group::query()->select('id','name')->orderBy('name')->get(),true);
        data_set($schema,'form.location_ids.values',Location::query()->select('id','name')->where('status','activated')->where('type',$user->location->type)->orderBy('name')->get(),true);
        data_set($schema,'form.category_ids.values',Category::query()->withoutGlobalScope(LocationScope::class)->whereIn('location_id',$user->locationsAssigned->pluck('id')->toArray())->select('id','name','location_id')->ofActive()->orderBy('name')->get(),true);
        data_set($schema,'form.priceset_ids.values',Priceset::query()->withoutGlobalScope(LocationScope::class)->whereIn('location_id',$user->locationsAssigned->pluck('id')->toArray())->select('id','name_grade as name','location_id')->ofActive()->orderBy('name_grade')->get(),true);
        data_set($schema,'form.product_ids.values',Product::query()->withoutGlobalScope(LocationScope::class)->whereIn('location_id',$user->locationsAssigned->pluck('id')->toArray())->select('id','name','location_id')->ofActive()->orderBy('name')->get(),true);
        
        //data_set($schema,'filters.type.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'type'),true);
        //data_set($schema,'filters.priceset_id.values',Priceset::select('id','name_grade as name')->whereHas('discounts')->get(),true);
        data_set($schema,'filters.group_id.values',Group::select('id','name')->whereHas('discounts')->orderBy('name')->get(),true);
        data_set($schema,'filters.category_id.values',Category::select('id','name')->whereHas('discounts')->orderBy('name')->get(),true);
        data_set($schema,'filters.location_id.values',Location::select('id','name')->whereHas('discounts')->orderBy('name')->get(),true);
        

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