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
 * Class Campaign.
 */
class Campaign extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'dispensary_campaigns';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['name','group_id','discount_id','type','campaign_code','subject','message','sms_test_number','scheduled_at'];
	protected $dates = ['archived_at','scheduled_at','created_at','updated_at','deleted_at'];
    protected $casts = [];



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

    /* relations */
    public function group(){
        return $this->belongsTo(Group::class,'group_id','id');
    }
    
    public function discount(){
        return $this->hasOne(Discount::class,'id','discount_id');
    }
    
    public function customerLog(){
        return $this->hasMany(CampaignLog::class,'campaign_id','id');
    }

    public function sales(){
        return $this->hasMany(Sale::class,'discount_code','campaign_code');
    }    
    
    
    

    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){
        
        if (!$text) return $query;

        return $query->where(function ($query) use ($text) {
            $query->where('name', 'like', '%'.$text.'%')
                ->orWhere('subject', 'like', '%'.$text.'%')
                ->orWhere('message', 'like', '%'.$text.'%');
        });

    }
    
    
    /* filter by frontend query orderBy filter */
    public function scopeOfActive($query){
        
        return $query->whereNull('archived_at');

    }
    




    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/

    /* set aggregate attributes (upon saving..) based on the campaignlog */
    public function setNotifiedCountAttribute(){
        $this->attributes['notified_count'] = CampaignLog::where('campaign_id',$this->id)->whereNotNull('notified_at')->count();
    } 

    public function setVisitedCountAttribute(){
        $this->attributes['visited_count'] = CampaignLog::where('campaign_id',$this->id)->whereNotNull('visited_at')->count();
    }
    
    public function setPurchasedCountAttribute(){
        $this->attributes['purchased_count'] = CampaignLog::where('campaign_id',$this->id)->whereNotNull('purchased_at')->count();
    }
    public function setCodeusedCountAttribute(){
        $this->attributes['codeused_count'] = CampaignLog::where('campaign_id',$this->id)->whereNotNull('code_used_at')->count();
    }





    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/

    /* get discounts filter list data */
    public static function _getFilterList($rel='all'){

        return self::select('id','name','subject','type','status')->withCount('customer_log')->orderBy('name')->ofHasFilter($rel)->ofActive()->get()->map(function($item,$key){
            return (object)[
                'id'        => $item->id,
                'name'      => $item->name.' '.$item->customer_log_count.' Customers'
            ];
        });

    }




    /* get and dynamically inject ui schema for this models frontend vue filtering */
    public static function _getSchema(){
        
        $schema = AppSchema::getSchema('campaign_schema');
        $user = Auth::guard('api')->user();

        data_set($schema,'form.group_id.values',Group::_getFilterList(),true);
        data_set($schema,'form.discount_id.values',Discount::_getFilterList(),true);

        data_set($schema,'filters.group_id.values',Group::_getFilterList('campaigns'),true);
        data_set($schema,'filters.discount_id.values',Discount::_getFilterList('campaigns'),true);
        data_set($schema,'filters.type.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'type'),true);
        

        /* agg data */
        $todayAgo = Carbon::now()->subDays(1)->toDateTimeString();
        data_set($schema,'agg',self::select(DB::raw('
            COUNT(DISTINCT(id)) as `all`,
            COUNT(DISTINCT(CASE WHEN created_at >= "'.$todayAgo.'" THEN id ELSE NULL END)) as all_today
        '))->ofActive()->first(),true);

        data_set($schema,'agg.campaign_sms_limit',data_get($user->location,'settings.campaign_sms_limit',200),true);
        data_set($schema,'agg.campaign_sms_used',Campaign::ofActive()->sum('notified_count'),true);


        return $schema;
        
    }



    /* model boot util - generate a unique suite integer upon creation */
    protected static function boot(){
        
        parent::boot();
        
        static::creating(function ($model) {
            $model->created_at = Carbon::now()->toDateTimeString();
        });
        static::saving(function ($model) {
            $model->setAttribute('notified_count',0);
            $model->setAttribute('visited_count',0);
            $model->setAttribute('purchased_count',0);
            $model->setAttribute('codeused_count',0);
        });
        
    }

}