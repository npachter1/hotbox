<?php

namespace App\Models\Dispensary;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

use App\Models\AppSchema;
use App\Models\Auth\Location;
use App\Models\Auth\Addressbook;

use App\Services\Dispensary\RewardService;

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
class Customer extends Eloquent
{

    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/

    protected $table = 'dispensary_customers';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify

	protected $fillable = ['type','first_name','middle_name','last_name','alias','birthdate','drivers_license_state','drivers_license_expiry_date','mmj_card_state','mmj_card_expiry_date','email_optin','sms_optin','tax_exempt','document_image','settings','preferences','comments','gender','referred_by','address.address1','address.address2','contact_name','receiving_party_name','employee_position','tax_exempt_number','license_type','online_login_email'];
	protected $dates = ['archived_at','created_at','updated_at','deleted_at','birthdate','drivers_license_expiry_date','mmj_card_expiry_date'];
    protected $casts = ['email_optin'=>'boolean','sms_optin'=>'boolean','settings'=>'object','total_reward_points'=>'float','total_spent'=>'float','tax_exempt'=>'boolean'];
    protected $appends = ['age','payments'];

    public static $abbv_fields = ['id','addressbook_id','first_name','last_name','alias','birthdate','type','drivers_license_state','total_reward_points','total_spent','created_at','updated_at'];


    const HASH_ALGORITHM = 'sha256';
    const HASH_SECRET_CONFIG_KEY = 'hotbox.hash_secret';



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	/*  location this customer belongs to */
	public function location(){
		return $this->belongsTo(Location::class,'location_id','id');
	}


	/* get prime user of this location */
	public function address(){
		return $this->hasOne(Addressbook::class,'id','addressbook_id');
	}


    /* customer groups */
    public function groups(){
        return $this->belongsToMany(Group::class,'dispensary_customer_dispensary_group','group_id','customer_id');
    }


    public function sales(){
        return $this->hasMany(Sale::class, 'customer_id', 'id')->withoutGlobalScope(LocationScope::class)->orderBy('created_at','desc')->ofActive();
    }

    public function settledSales(){
        return $this->hasMany(Sale::class, 'customer_id', 'id')->withoutGlobalScope(LocationScope::class)->orderBy('created_at','desc')->where('status','settled')->ofActive();
    }
    public function settledOrRefundedSales(){
        return $this->hasMany(Sale::class, 'customer_id', 'id')->withoutGlobalScope(LocationScope::class)->orderBy('created_at','desc')->whereIn('status',['settled','refunded'])->ofActive();
    }
    public function pendingSales(){
        return $this->hasMany(Sale::class, 'customer_id', 'id')->withoutGlobalScope(LocationScope::class)->orderBy('created_at','desc')->where('status','pending')->ofActive();
    }


    public function queues(){
        return $this->hasMany(CustomerQueue::class, 'customer_id', 'id')->withoutGlobalScope(LocationScope::class);
    }


    public function rewards(){
        return $this->hasMany(Reward::class, 'customer_id', 'id')->withoutGlobalScope(LocationScope::class);
    }




    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){

        if (!$text) return $query;

        $hash = hash_hmac(
            self::HASH_ALGORITHM,
            $text,
            \Config::get(self::HASH_SECRET_CONFIG_KEY));

        return $query->where(function ($query) use ($text,$hash) {
            $query->where('first_name', 'like', '%'.$text.'%')
                ->orWhere('last_name', 'like', '%'.$text.'%')
                ->orWhere('type', 'like', '%'.$text.'%')
                ->orWhere('mmj_card',$hash)
                ->orWhere('drivers_license',$hash)
                ->orWhereHas('address',function($q)use($text,$hash){
                    $q->where('name', 'like', '%'.$text.'%')
                        ->orWhere('address1', 'like', '%'.$text.'%')
                        ->orWhere('city', 'like', '%'.$text.'%')
                        ->orWhere('email', 'like', '%'.$text.'%')
                        ->orWhere('phone', 'like', '%'.$text.'%');
                });
        });

    }


    /* filter by frontend query orderBy filter */
    public function scopeOfActive($query){

        return $query->whereNull('archived_at');

    }



    /* location associations (for customer group filters) */
    public function scopeOfLocations($query, $locations, $matchType='and'){

        if(empty($locations) || !$locations) return $query;
        else{

            $match = ($matchType=='and' ? 'where' : 'orWhere');
            $query->where(function ($query) use ($locations,$match) {
                foreach ($locations as $location)
                    $query->$match('location_id', '=', $location);
            });

            return $query;
        }

    }

    /* product associations (for customer group filters) */
    public function scopeOfProducts($query, $products, $matchType='and'){

        if(empty($products) || !$products) return $query;
        else{

            $match = ($matchType=='and' ? 'whereRaw' : 'orWhereRaw');
            $query->where(function ($query) use ($products,$match){
                foreach ($products as $product)
                    $query->$match('FIND_IN_SET(?,product_ids_purchased)', [$product]);
            });

            return $query;
        }

    }


    /* categories associations (for customer group filters) */
    public function scopeOfCategories($query, $categories, $matchType='and'){

        if(empty($categories) || !$categories) return $query;
        else{

            $match = ($matchType=='and' ? 'whereRaw' : 'orWhereRaw');
            $query->where(function ($query) use ($categories,$match){
                foreach ($categories as $category)
                    $query->$match('FIND_IN_SET(?,category_ids_purchased)', [$category]);
            });

            return $query;
        }

    }


    /* preferences associations (for customer group filters) */
    public function scopeOfPreferences($query, $prefs, $matchType='and'){

        if(empty($prefs) || !$prefs) return $query;
        else{

            $match = ($matchType=='and' ? 'whereRaw' : 'orWhereRaw');
            $query->where(function ($query) use ($prefs,$match){
                foreach ($prefs as $pref)
                    $query->$match('FIND_IN_SET(?,preferences)', [$pref]);
            });

            return $query;
        }

    }


    /* aggregate loyalty (reward) filters (for customer group filters) */
    public function scopeOfMinPoints($query, $point){

        if(!$point || !is_integer($point) || $point==0) return $query;
        else{
            return $query->where('total_reward_points','>=',$point);
        }

    }

    public function scopeOfMinSpent($query, $amt){

        if(!$amt || !is_integer($amt) || $amt==0) return $query;
        else{
            return $query->where('total_spent','>=',$amt);
        }

    }

    //






    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/

    public function getAgeAttribute(){
        if (!$this->birthdate) { return null; } // What should we return here? null? zero?
        return Carbon::parse($this->birthdate)->age;
    }

    public function getPaymentsAttribute(){

        if(!$this->relationLoaded('sales')) return [];

        $payments = [];
        foreach($this->sales as $sale)
            foreach($sale->payments as $pay)
                if($pay->payment_method=='account')
                    $payments[] = (object)[
                        'sale_id'       => $sale->id,
                        'payment_id'    => $pay->id,
                        'order_number'  => $sale->order_number,
                        'amount'        => $pay->amount,
                        'amount_owed'   => $pay->amount_owed,
                        'created_at'    => $pay->created_at->toDateTimeString(),
                        'updated_at'    => $pay->updated_at->toDateTimeString()
                    ];

        return $payments;

    }


    /* get the customers thc equiv limit of grams of cannabis, and the used from todays sales */
    public function getThcLimitGramsAttribute(){
        $user = Auth::guard('api')->user();
        $grams_per_ounce = data_get($user->location,'settings.grams_per_ounce',28);
        return ($this->type=='recreational' ? data_get(AppSchema::getSchema('services_registrar'),'legal.rec_daily_limit',$grams_per_ounce) : (float)data_get($this->settings,'med_carry_weight',1)*$grams_per_ounce);
    }

    public function getThcLimitGramsUsedAttribute(){

        return Sale::query()->where('status','settled')->where('customer_id',$this->id)->where('created_at','>=',Carbon::now()->startOfDay()->toDateTimeString())->sum('thc_equivalent_grams');

    }


    /*public function setMmjCardAttribute($value){
        $this->attributes['mmj_card'] = hash_hmac(
            self::HASH_ALGORITHM,
            $value,
            \Config::get(self::HASH_SECRET_CONFIG_KEY));
    }


    public function setDriversLicenseAttribute($value){
        $this->attributes['drivers_license'] = hash_hmac(
            self::HASH_ALGORITHM,
            $value,
            \Config::get(self::HASH_SECRET_CONFIG_KEY));
    }*/


    /* set customer aggregate data */
    public function setTotalRewardPointsAttribute(){

        $this->attributes['total_reward_points'] = $this->rewards->sum('points_transacted');
    }

    public function setTotalSpentAttribute(){

        //$this->attributes['total_spent'] = $this->settledSales->sum('sale_price');
        $this->attributes['total_spent'] = $this->settledOrRefundedSales->sum('sale_price');

    }

    public function setReferralCountAttribute(){

        if($this->id) $this->attributes['referral_count'] = self::where('referred_by',$this->id)->count();

    }

    public function setTotalQueueCountAttribute(){

        if($this->id) $this->attributes['total_queue_count'] = CustomerQueue::where('customer_id',$this->id)->count();

    }



    /* get preferences comma seperated into array, and set back as comma seperated for concat search in groups filters */
    public function getPreferencesAttribute($val){

        return ($val ? explode(',',$val) : []);

    }

    public function setPreferencesAttribute($data){

        $this->attributes['preferences'] = ($data ? join(',',$data) : null);

    }


    public function setProductIdsPurchasedAttribute(){

        $productIds = [];
        $categoryIds = [];
        $metrcCategoryIds = [];

        foreach(SaleItem::query()
            ->with('inventory.product','inventory.product.category','inventory.product.category.type')
            ->ofCustomerSettledSale($this->id)
            ->get() as $line){

                if(!$line->inventory) continue;
                elseif(!$line->inventory->product) continue;                // must have a linked inventory and product data

                $productIds[data_get($line,'inventory.product.id','XXXXX')] = 1;
                $categoryIds[data_get($line,'inventory.product.category.id','XXXXX')] = 1;
                $metrcCategoryIds[data_get($line,'inventory.product.category.type.id','XXXXX')] = 1;

        }

        $this->attributes['product_ids_purchased'] = substr(join(',',array_keys($productIds)),0,175);
        $this->attributes['category_ids_purchased'] = substr(join(',',array_keys($categoryIds)),0,175);
        $this->attributes['metrc_category_ids_purchased'] = substr(join(',',array_keys($metrcCategoryIds)),0,175);

    }


    /* Cast properties and/or assume defaults of this settings array by schema.form. */
    public function setSettingsAttribute($data){

        if(($schema = self::_getSchema()) == null) $this->attributes['settings'] = json_encode($data);
        elseif(($store = Util::setObjectFromSchema($data,$schema,'settings')) == null) $this->attributes['settings'] = json_encode($data);
        else $this->attributes['settings'] = json_encode($store);

    }


    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/

    /* get categories scoped by product_items data */
    public static function _getFilterList(){

        return self::with('address')->select(self::$abbv_fields)->ofActive()->get()->map(function($item,$key){
            return (object)[
                'id'        => $item->id,
                'name'      => $item->first_name.' '.$item->last_name.' '.data_get($item,'address.city',null).', '.data_get($item,'address.region',null)
            ];
        });

    }


    /* get categories scoped by product_items data */
    public static function _getPreferenceFilterList(){

        $hash = [];
        foreach(data_get(AppSchema::getSchema('customer_schema'),'form.preferences.values',[]) as $val)
            $data[$val->id] = $val->name;

        $count = [];
        foreach(self::select('id','preferences')->whereNotNull('preferences')->ofActive()->get() as $custPref)
            foreach($custPref->preferences as $pref)
                data_set($count,$pref,(data_get($count,$pref,0)+1),true);


        return collect($data)->map(function($item,$key)use($count){
            return (object)[
                'id'        => $key,
                'name'      => $item.' ('.data_get($count,$key,0).' Customers)'
            ];
        })->values();

    }





    /* get ui schema for this model */
    public static function _getSchema(){

        $user = Auth::guard('api')->user();
        $schema = AppSchema::getSchema('customer_schema');
        $states = Util::getStates('US');

        data_set($schema,'form.location_id.values',Location::query()->select('id','name')->where('status','activated')->orderBy('name')->get(),true); // current locations for default location
        data_set($schema,'filters.location_id.values',Util::getActiveFilterList(self::query()->ofActive()->whereIn('location_id',$user->locationsAssigned()->where('type',$user->location->type)->pluck('id')->toArray()),$schema,'location_id'),true);
        data_set($schema,'filters.location_id.focus',($user ? $user->location->id : null)); // focus the location id filter tabs to the users current location id
        data_set($schema,'form.drivers_license_state.values',$states,true);
        data_set($schema,'form.mmj_card_state.values',$states,true);
        //data_set($schema,'form.referred_by.values',Customer::query()->select(DB::raw('id,CONCAT_WS(" ",first_name,middle_name,last_name) as name'))->ofActive()->where('location_id',$user->location_id)->whereRaw('CONCAT_WS(" ",first_name,middle_name,last_name)!=""')->orderBy('last_name','asc')->get(),true);
        data_set($schema,'form.country.values',Util::getCountries(),true);

        data_set($schema,'filters.type.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'type'),true);
        data_set($schema,'filters.group_id.values',Group::withCount('customers')->whereHas('customers')->orderBy('name')->get()->map(function($item,$key){
            return (object)[
                'id'    => $item->id,
                'name'  => $item->name.' ('.$item->customers_count.' Customers)',
                'type'  => $item->type
            ];
        }),true);
        data_set($schema,'filters.created_at.values',Util::getActiveFilterDateRange('INIT'),true);


        /* agg data */
        $todayAgo = Carbon::now()->subDays(1)->toDateTimeString();
        $daysAgo30 = Carbon::now()->subDays(30)->toDateTimeString();
        $daysAgo60 = Carbon::now()->subDays(60)->toDateTimeString();
        data_set($schema,'agg',self::select(DB::raw('
            COUNT(DISTINCT(id)) as `all`,
            COUNT(DISTINCT(CASE WHEN created_at >= "'.$todayAgo.'" THEN id ELSE NULL END)) as all_today,
            COUNT(DISTINCT(CASE WHEN created_at >= "'.$daysAgo30.'" THEN id ELSE NULL END)) as new_customers_last30,
            COUNT(DISTINCT(CASE WHEN created_at <= "'.$daysAgo30.'" AND created_at >= "'.$daysAgo60.'" THEN id ELSE NULL END)) as new_customers_previous30
        '))->ofActive()->first(),true);


        return $schema;

    }



    /* model boot util - generate a unique suite integer upon creation */
    protected static function boot(){
        $user = Auth::guard('api')->user();

        parent::boot();

        static::creating(function ($model)use($user) {
            $model->created_at = $model->created_at ?: Carbon::now()->toDateTimeString();
            $model->created_by = ($user ? $user->id : null);

            $rewardService = new RewardService();
            if($model->referred_by && ($ref = self::where('id',$model->referred_by)->orderBy('created_at','desc')->first()))
                $rewardService->_transactReward('referral',$ref,$user);         // this service will transact rewards for type referral - returns true or false
        });
        static::updated(function ($model)use($user) {
            $model->updated_by = ($user ? $user->id : null);
        });
        static::saving(function ($model) {
            $model->setAttribute('total_reward_points',null);
            $model->setAttribute('total_spent',null);
            $model->setAttribute('referral_count',null);
            $model->setAttribute('product_ids_purchased',null);
            $model->setAttribute('total_queue_count',null);
        });
    }

}
