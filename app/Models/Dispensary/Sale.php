<?php

namespace App\Models\Dispensary;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

use App\Models\AppSchema;
use App\Models\Auth\Location;
use App\Models\Auth\User;

use App\Services\Dispensary\InventoryService;
use App\Services\Dispensary\RewardService;
use App\Services\Dispensary\CampaignService;
use App\Services\Dispensary\SaleService;

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
class Sale extends Eloquent
{

    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/

    protected $table = 'dispensary_sales';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify

	protected $fillable = ['customer_id','price','discount','tax','sale_price','thc_equivalent_grams','discount_code'];
	protected $dates = ['archived_at','migrated_at','settled_at','created_at','updated_at','deleted_at'];
    protected $casts = ['price'=>'float','discount'=>'float','tax'=>'float','sale_price'=>'float','thc_equivalent_grams'=>'float'];

    public static $abbv_fields = ['id','location_id','user_id','customer_id','order_number','status','settled_at'];


    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	/*  location this sale belongs to */
	public function location(){
		return $this->belongsTo(Location::class,'location_id','id');
	}

    public function customer(){
        return $this->hasOne(Customer::class,'id','customer_id');
    }

    public function customerAbbv(){
        return $this->hasOne(Customer::class,'id','customer_id')->select(Customer::$abbv_fields);
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function items(){
        return $this->hasMany(SaleItem::class,'sale_id','id')->withoutGlobalScope(LocationScope::class);
    }

    public function discounts(){
        return $this->belongsToMany(Discount::class,'dispensary_discount_dispensary_sale','sale_id','discount_id')->as('applied')->withoutGlobalScope(LocationScope::class)->withPivot('is_active', 'rejection_code','rejection_reason','is_forced')->orderBy('rank');
    }

    public function payments(){
        return $this->hasMany(SalePayment::class,'sale_id','id')->withoutGlobalScope(LocationScope::class);
    }

    public function drawer(){
        return $this->hasOne(Drawer::class, 'id', 'drawer_id')->withoutGlobalScope(LocationScope::class);
    }



    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/


    /* scope text match */
    public function scopeOfTextFilter($query, $text){

        if (!$text) return $query;

        return $query->where(function ($query) use ($text) {
            $query->where('order_number', 'like', '%'.$text.'%')
                ->orWhereHas('customer',function($q)use($text){
                    $q->where('first_name','like', '%'.$text.'%')
                        ->orWhere('last_name', 'like', '%'.$text.'%')
                        ->orWhere('type', 'like', '%'.$text.'%');
                    })
                ->orWhereHas('items.inventory',function($q)use($text){
                    $q->where('metrc_tag','like', '%'.$text.'%')
                        ->orWhere('item_barcode', 'like', '%'.$text.'%')
                        ->orWhereHas('product',function($p)use($text){
                            $p->where('name','like','%'.$text.'%');
                        });
                    });
        });

    }


    /* scope by unpaid account payment */
    public function scopeOfAccountUnpaidFilter($query){

        return $query->whereHas('payments',function($q){
            $q->where('payment_method','account')
                ->where('amount_owed','>',0);
        });

    }


    /* filter by frontend query orderBy filter */
    public function scopeOfActive($query){

        return $query->whereNull('archived_at');

    }


    /* filter by frontend query orderBy filter */
    public function scopeOfSettled($query){

        return $query->where('status','settled');

    }




    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/








    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/

    /* update Sale and items data with pricing, tax and discounting */
    // this is called upon each change to a POS checkout, to ensure persistant pricing
    // this also holds the logic of discount rules, taxation, and thc equiv ratio - as the carts contents updating update all of this!
    public function _updateWithDiscountAndTaxPricing($data,$user){
        //Allow for changing customer
        $cid = data_get($data, 'customer_id', null);
        if( $cid !== $this->customer->id) {
            $customer = Customer::findOrFail($cid);
            $this->customer_id = $cid;
            $this->customer->save();
        }
        // Reset this sale order's values:
        $this->price = 0;
        $this->discount = 0;
        $this->tax = 0;
        $this->sale_price = 0;
        $this->discount_descriptor = null;
        $this->thc_equivalent_grams = 0;

        // Added to handle the way vuestorefront does cart updates. Probably will remove once storefront is refactored to send updated cart instead of one item at a time.
        $addItem = data_get($data, 'add_item', false);
        $removeItem = data_get($data, 'remove_item', false);
        $newItems = data_get($data,'items',[]);
        if($addItem)
        {
            $existingItems = SaleItem::where('sale_id', $this->id)->whereNotIn('inventory_id',array_column($newItems, 'inventory_id'))->select('id','inventory_id','quantity','is_confirmed')->get()->toArray();
            $existingItems[] = $newItems[0];
            data_set($data,'items',$existingItems);
        }
        if($removeItem)
        {
            $existingItems = SaleItem::where('sale_id', $this->id)->whereNotIn('inventory_id',array_column($newItems, 'inventory_id'))->select('id','inventory_id','quantity','is_confirmed')->get()->toArray();
            data_set($data,'items',$existingItems);
        }

        $invService = new InventoryService();

        if(!$data || !$user) abort(422,'Unable to parse data or incomplete');   // Ensure we have request data array sent

        elseif(empty(($items = data_get($data,'items',[])))) {                  // It's possible that an open sale order doesn't have any items

            $updPendingInv = SaleItem::where('sale_id', $this->id)->whereNotIn('id',array_column($items, 'id'))->select('inventory_id')->pluck('inventory_id')->toArray();

            SaleItem::where('sale_id', $this->id)->delete();

            $this->archived_at = Carbon::now()->toDateTimeString();
            $this->save();

            foreach(Inventory::whereIn('id',$updPendingInv)->get() as $upd)
                $upd->save();                                                   // this will update all old/new inventory items pending count
                                                                                // update lineitems preious inventory pending count

            return $this;
        }
        elseif(($customer = Customer::with('groups')->find(data_get($data,'customer_id',null))) == null) throw new Exception('No customer found'); // need to load customer with groups relation for retrieving associaed discounts

        $_discountCode = data_get($data,'discount_code',null);                  // apply inputted discount code
        $_omitRuleIds = data_get($data,'omit_rule_ids',[]);                     // omit certain discounts at POS


        /* get settings filter data */
        $_discounts = [];                                                       // associated pivot table (discount_rule_sale) array
        $_rules = [];                                                           // applicable rules that are active for this saleorder
        $_locationIds = [$user->location->id];                                  // associations (categories, groups, locations)
        $_categoryIds = [];
        $_productIds = [];
        $_groupIds = [];
        $_pricesetIds = [];
        foreach(($customer->groups ?: []) as $group)
            $_groupIds[] = $group->id;

        $_prevSalesCount = Sale::where('customer_id','!=',$customer->id)->count();        // sales/revenu filters for discount_rule addl settings
        $_prevSalesAmount = Sale::where('customer_id','!=',$customer->id)->sum('sale_price');
        $_prevDiscounts = Sale::with('discounts')->where('customer_id',$customer->id)->get()->flatMap(function($item,$key){
            return $item->discounts->where('applied.is_active',true)->values()->toArray();
        });

        $_price_agg = [];                                                       // aggregate counts for porportionate and BOGO calcs
        $_dicount_agg = [];
        $_tax_agg = [];
        $_sale_price_agg = [];
        $_quantity_agg = [];


        $saleService = new SaleService();

        /* 1. parse and hash items data */
        foreach($items as &$item){

            $item = (object)$item;

            if(($inv = Inventory::with('product.category','taxes.rates')->find($item->inventory_id)) == null) abort(422,'an item in this order cannot be found in inventory');
            $item->data = $inv;                                                 // hash inventory data for discount and tax application loop

            if(!in_array($inv->product->category_id,$_categoryIds))
                $_categoryIds[] = $inv->product->category_id;                   // list categoriy ids for all items in order
            if(!in_array($inv->product->id,$_productIds))
                $_productIds[] = $inv->product->id;                   // list product ids for all items in order
            if($inv->priceset_id && !in_array($inv->priceset_id,$_pricesetIds))
                $_pricesetIds[] = $inv->priceset_id;


            //if(!$item->price)                                                 // TODO if POS forces a price w adminpin override
                if(is_array($getPricing = $invService->_getCurrentPrice(['quantity'=>$item->quantity,'quantity_priced_at'=>data_get($item,'quantity_priced_at',0)],$item->inventory_id,$user)))
                    $item->price = data_get($getPricing,'current_price',0);

            if(!$item->price)
                abort(422,'could not assess base price');                       // failsafe: if price was not provided, or could not be calculated based on inventory data, fail

            // DEREK: round the price now, before other calculations & storage in db.
            // Jesse: backend to calc in 1000s - resource class to round
            //$item->price = round($item->price, 2);

            $_price_agg[] = $item->price;
            $_quantity_agg[] = $item->quantity;

            // Calculate THC Equivalent grams
            $item->thc_equivalent_grams = $this->_getSaleItemThcAmount($item->data, $item->quantity);
            $item->applied_discount_rule_ids = [];
        }

        /* 2. filter through List of potential discount_rules for this order */
        $_hasExclusive = false;
        foreach(Discount::query()->with('categories','campaigns')
            ->where(function($q)use($_discountCode,$_groupIds,$_categoryIds,$_productIds,$_locationIds,$_pricesetIds){
                $q->whereRaw('LOWER(`discount_code`)="'.strtolower($_discountCode).'"')
                    ->orWhereHas('campaigns',function($s)use($_discountCode){   // or if it is a campaign code and campaign is linked to this discount
                        $s->whereRaw('LOWER(`campaign_code`)="'.strtolower($_discountCode).'"')
                            ->whereIn('status',['working','completed','pending']);
                    })
                    ->orWhere(function($a)use($_groupIds,$_categoryIds,$_productIds,$_locationIds,$_pricesetIds){
                        $a->ofAnyAssociation('groups',$_groupIds)
                            ->ofAnyAssociation('categories',$_categoryIds)
                            ->ofAnyAssociation('products',$_productIds)
                            ->ofAnyAssociation('locations',$_locationIds)
                            ->ofAnyAssociation('pricesets',$_pricesetIds);
                    });
            })
            ->ofActive()
            ->orderBy('rank')
            ->get() as $rule){

                /* 2.0 see if it is scheduled for later */
                if(!$rule->is_active){
                    $_discounts[$rule->id] = [
                        'is_active'         => 0,
                        'rejection_code'    => 'INACTIVE',
                        'rejection_reason'  => 'This Discount Rule has been Inactivated by Admin'
                    ];
                    continue;
                }elseif($rule->scheduled_at){
                    if($rule->scheduled_at->startOfDay()->timestamp > Carbon::now()->startOfDay()->timestamp){
                        $_discounts[$rule->id] = [
                            'is_active'         => 0,
                            'rejection_code'    => 'SCHEDULED_FOR_LATER',
                            'rejection_reason'  => 'Not yet Active - This Discount has been Scheduled to start on '.$rule->scheduled_at->format('m/d/y')
                        ];
                        continue;
                    }
                }


                /* 2.1 filter by settings */
                foreach((array)data_get($rule,'settings',[]) as $key => $val){
                    switch($key){
                        case 'pointsToRedeem':
                            if($val>$customer->total_reward_points){
                                $_discounts[$rule->id] = [
                                    'is_active'         => 0,
                                    'rejection_code'    => 'INSUFFICIENT_POINTS',
                                    'rejection_reason'  => 'This Discount requires '.$val.' points and customer only has '.$customer->total_reward_points
                                ];
                                continue 3;
                            }
                            break;
                        case 'minSales':
                            if($val>$_prevSalesCount){
                                $_discounts[$rule->id] = [
                                    'is_active'         => 0,
                                    'rejection_code'    => 'INSUFFICIENT_SALES',
                                    'rejection_reason'  => 'This Discount requires '.$val.' previous sales and customer only has '.$_prevSalesCount
                                ];
                                continue 3;
                            }
                            break;
                        case 'minSpend':
                            if($val>array_sum($_price_agg)){
                                $_discounts[$rule->id] = [
                                    'is_active'         => 0,
                                    'rejection_code'    => 'INSUFFICIENT_SUBTOTAL',
                                    'rejection_reason'  => 'This Discount requires a subtotal (before tax) of '.$val.' or more'
                                ];
                                continue 3;
                            }
                            break;
                        case 'hourStart':                                       // if settings start hour (converted to servers UTC time via location timezone) is greater then current time
                            if(!$val) break;                                 // TODO, this rounds to the hour (ie: 1630 is 4pm) - convert 1600 to 16:00
                            if(date("Hi",strtotime((string)$val)) > Carbon::parse(date('Hi'))->setTimezone(data_get($user->location,'settings.communication_timezone','UTC'))->format('Hi')){
                            //if(Carbon::parse(date('m/d/Y H:i:s'),data_get($user->location,'settings.communication_timezone','UTC'))->timestamp < Carbon::parse(date('m/d/Y '.$time.':00'),data_get($user->location,'settings.communication_timezone','UTC'))->timestamp){
                                $_discounts[$rule->id] = [
                                    'is_active'         => 0,
                                    'rejection_code'    => 'NOT_IN_TIME_RANGE',
                                    'rejection_reason'  => 'This Discount is Only Available between '.date("g:ia", strtotime((string)$val)).' and '.date("g:ia", strtotime((string)data_get($rule,'settings.hourEnd',2100)))
                                ];
                                continue 3;
                            }
                            break;
                        case 'hourEnd':                                         // if settings end hour (converted to servers UTC time via location timezone) is less then current time
                            if(!$val) break;                                 // TODO, this rounds to the hour (ie: 1630 is 4pm) - convert 1600 to 16:00
                            if(Carbon::parse(date('Hi'))->setTimezone(data_get($user->location,'settings.communication_timezone','UTC'))->format('Hi') > date("Hi",strtotime((string)$val))){
                            //if(Carbon::now()->timestamp > Carbon::parse(date('m/d/Y '.rtrim($val,'0').':00:00'),data_get($user->location,'settings.communication_timezone','UTC'))->setTimezone('UTC')->timestamp){
                                $_discounts[$rule->id] = [
                                    'is_active'         => 0,
                                    'rejection_code'    => 'NOT_IN_TIME_RANGE',
                                    'rejection_reason'  => 'This Discount is Only Available between '.date("g:ia", strtotime((string)data_get($rule,'settings.hourStart',900))).' and '.date("g:ia", strtotime((string)$val))
                                ];
                                continue 3;
                            }
                            break;
                        case 'daysOfWeek':
                            if($rule->type!='time' || empty($val)) break;
                            $isToday = false;

                            foreach($val as $day)
                                if(date('N',time())==$day) $isToday = true; // TODO refactor this!
                            if($isToday==false){
                                $_discounts[$rule->id] = [
                                    'is_active'         => 0,
                                    'rejection_code'    => 'NOT_ON_DAY',
                                    'rejection_reason'  => 'This Discount does not apply today'
                                ];
                                continue 3;
                            }
                            break;
                        case 'customerType':
                            if(!is_array($val)) break;
                            elseif(empty($val)) break;
                            elseif(!in_array($customer->type,$val)){
                                $_discounts[$rule->id] = [
                                    'is_active'         => 0,
                                    'rejection_code'    => 'NOT_FOR_CUSTOMER_TYPE',
                                    'rejection_reason'  => 'This Discount is Only Available for '.ucwords(join(', ',$val)).' Customers'
                                ];
                                continue 3;
                            }
                            break;
                        default: //TODO add more setting filters in schema.json, and process them here!
                    }
                }

                /* 2.3 check if max_per_customer is reached */
                $_prevUsed = $_prevDiscounts->where('id',$rule->id)->count();
                if($_prevUsed>$rule->max_per_customer){
                    $_discounts[$rule->id] = [
                        'is_active'         => 0,
                        'rejection_code'    => 'CUSTOMER_MAX_EXCEEDED',
                        'rejection_reason'  => 'Customer used this Discount '.$_prevUsed.' Times of '.$rule->max_per_customer.' allowed'
                    ];
                    continue;
                }


                /* 2.1 If discount_code is provided, and we don't match or associate, reject */
                if(strtolower($rule->discount_code)!=strtolower($_discountCode) && $rule->discount_code && $rule->campaigns->filter(function($row,$ky)use($_discountCode){
                    return (strtolower(data_get($row,'campaign_code',''))==strtolower($_discountCode) ? true : false);
                })->count()==0){
                    $_discounts[$rule->id] = [
                        'is_active'         => 0,
                        'rejection_code'    => 'CODE_NOT_SUPPLIED',
                        'rejection_reason'  => 'This Discount requires the input of a specific Discount/Campaign Code'
                    ];
                    continue;
                }elseif($rule->discount_code){                                  // check matched discount by code for all associations
                    if($rule->locations->count()>0 && $rule->locations->whereIn('id',$_locationIds)->count()==0){
                        $_discounts[$rule->id] = [
                            'is_active'         => 0,
                            'rejection_code'    => 'NOT_IN_LOCATION',
                            'rejection_reason'  => 'This Discount is not assigned to this Location'
                        ];
                        continue;
                    }elseif($rule->groups->count()>0 && $rule->groups->whereIn('id',$_groupIds)->count()==0){
                        $_discounts[$rule->id] = [
                            'is_active'         => 0,
                            'rejection_code'    => 'NOT_IN_GROUP',
                            'rejection_reason'  => 'This Discount is not assigned to this customers groups'
                        ];
                        continue;
                    }elseif($rule->categories->count()>0 && $rule->categories->whereIn('id',$_categoryIds)->count()==0){
                        $_discounts[$rule->id] = [
                            'is_active'         => 0,
                            'rejection_code'    => 'NOT_IN_CATEGORY',
                            'rejection_reason'  => 'This Discount does not apply to any categories in this Order'
                        ];
                        continue;
                    }elseif($rule->products->count()>0 && $rule->products->whereIn('id',$_productIds)->count()==0){
                        $_discounts[$rule->id] = [
                            'is_active'         => 0,
                            'rejection_code'    => 'NOT_IN_PRODUCT',
                            'rejection_reason'  => 'This Discount does not apply to any products in this Order'
                        ];
                        continue;
                    }elseif($rule->pricesets->count()>0 && $rule->pricesets->whereIn('id',$_pricesetIds)->count()==0){
                        $_discounts[$rule->id] = [
                            'is_active'         => 0,
                            'rejection_code'    => 'NOT_IN_PRICESET',
                            'rejection_reason'  => 'This Discount does not apply to any pricing groups/sets in this Order'
                        ];
                        continue;
                    }
                }


                /* any omissions from terminal */
                if(in_array($rule->id,$_omitRuleIds)){
                    $_discounts[$rule->id] = [
                        'is_active'         => 0,
                        'rejection_code'    => 'OFFER_OMITTED',
                        'rejection_reason'  => 'This Discount offer has been omitted by you.'
                    ];
                    continue;
                }


                /* 2.4 If exclusive, save as (1) and exit as it is the highest ranked one, else add to list */
                if($rule->is_exclusive && $_hasExclusive==false){
                    foreach($_rules as $rkey => $unrule){
                            $_discounts[$unrule->id] = [
                                'is_active'         => 0,
                                'rejection_code'    => 'OTHER_EXCLUSIVE_IN_ORDER',
                                'rejection_reason'  => 'Another Discount for this order has Exclusivity'
                            ];
                        unset($_rules[$rkey]);
                    }

                    $_discounts[$rule->id] = ['is_active' => 1,'is_forced' => (count($_omitRuleIds)>=1 ? 1 : 0)]; // mark discount as active
                    $_rules[] = $rule;                                          // add active rule to rules array
                    $_hasExclusive = true;
                }elseif($_hasExclusive==true){
                    $_discounts[$rule->id] = [
                        'is_active'         => 0,
                        'rejection_code'    => 'OTHER_EXCLUSIVE_IN_ORDER',
                        'rejection_reason'  => 'Another Discount for this order has Exclusivity'
                    ];
                }else{
                    $_discounts[$rule->id] = ['is_active' => 1,'is_forced' => (count($_omitRuleIds)>=1 ? 1 : 0)]; // mark discount as active
                    $_rules[] = $rule;                                          // add active rule to rules array
                }

            }


        /* 3. loop through each line item and apply discount, based on distribution type */
        foreach($items as &$item){

            $item->discount = 0;
            $item->tax = 0;
            $item->sale_price = 0;

            /* 3.1 distribute discounts amount to this lineitem */
            foreach($_rules as $rule){                                          // each discount applies on top of previous, so 10% off 100 = 90, then $5 off = 85
                switch($rule->distribution_type){
                    case 'equal':                                               // apply discount to total price of all items divided by the amount of items
                        // NOTE: if rule associates with categories, but is set to equally disctribute, then all items get distributed.
                        $ration = (1/count($_price_agg));
                        $item->discount = $item->discount + ($rule->discount_type=='pct' ? ((array_sum($_price_agg)*($rule->discount_amount/100))*$ration) : ($rule->discount_amount*$ration));
                        break;
                    case 'proportionate':                                       // apply discount based on the total quantity / quantity of this item
                        // NOTE: if rule associates with categories, but is set to equally disctribute, then all items get distributed.
                        $ration = ($item->price/array_sum($_price_agg));
                        $item->discount = $item->discount + ($rule->discount_type=='pct' ? ((array_sum($_price_agg)*($rule->discount_amount/100))*$ration) : ($rule->discount_amount*$ration));
                        break;
                    case 'quantity':
                        $ration = ($item->quantity/array_sum($_quantity_agg));
                        $item->discount = $item->discount + ($rule->discount_type=='pct' ? ((array_sum($_price_agg)*($rule->discount_amount/100))*$ration) : ($rule->discount_amount*$ration));
                        break;
                    case 'line':
                        // If $rule->categories associations, and is set to discount line only, ensure theres a match or continue to next
                        if($rule->categories->count()>0 && !in_array(data_get($item,'data.product.category_id',null),$rule->categories->pluck('id')->toArray()))
                            continue 2;
                        // NOTE this basically applies the discount amount to each line item that applies!
                        $item->discount = $item->discount + ($rule->discount_type=='pct' ? ($item->price*($rule->discount_amount/100)) : $rule->discount_amount);
                        break;
                    default: //TODO other types..
                }

                $item->discount_descriptor = (isset($item->discount_descriptor) ? $item->discount_descriptor.' - ' : '').$rule->descriptor;
                $item->discount_code = (isset($item->discount_code) ? $item->discount_descriptor.', ' : '').$rule->descripor;
                $item->applied_discount_rule_ids[] = $rule->id;

            }

            // Derek: Round prices here, then calculate final sale_price
            // Jesse: backend to calc in 1000s - resource class to round
            //$item->discount = round($item->discount, 2);

            if($item->price <= $item->discount)
                $item->discount = ($item->price - 0.01);                     // Ensure no lineitem is free or (-)

            $_discount_agg[] = $item->discount;


            /* 3.2 tax */
            //if($customer->type=='wholesale' || $customer->tax_exempt===true){ // TODO customer tax_exempt?
            if($customer->type=='wholesale'){                                   // no tax for wholesale sale
                $item->tax = 0;
                $item->tax_id = null; // for accountign purposes
            }elseif(($tax = data_get($item->data,'taxes',collect([]))->firstWhere('nature_type',data_get($item->data,'product.nature_type','noncannabis')))!=null){                                 // if inventory.tax group has rates..

                $item->tax = (($item->price - $item->discount)*($tax->rates->sum('rate_percent')/100)); // TODO is this a sum or the highest of..  do we tax the discounted amount or base price..
                $item->tax_id = $tax->id; // for accountign purposes

                // Derek: Round prices here, then calculate final sale_price
                // Jesse: backend to calc in 1000s - resource class to round
                //$item->tax = round($item->tax, 2);

                $_tax_agg[] = $item->tax;
            }

            $item->sale_price = $item->price - $item->discount + $item->tax;
            //$item->sale_price = ($item->price - $item->discount + $item->tax)  * $item->quantity;

            $_sale_price_agg[] = $item->sale_price;

            unset($item->data);

        }


        /* 4. add totals and discounts data and save */
        $this->price = array_sum($_price_agg);
        $this->discount = array_sum($_discount_agg);
        $this->tax = array_sum($_tax_agg);
        $this->sale_price = array_sum($_sale_price_agg);
        $this->discount_descriptor = count($_rules).' Discounts Applied, '.(count($_discounts) - count($_rules)).' Discounts Rejected';

        $this->thc_equivalent_grams = collect($items)->sum('thc_equivalent_grams');
        $this->archived_at = null;

        if ($_discountCode) {
            // Derek: Save discount_code in the sale order
            // TODO: Verify that this discount code is actually used.
            $this->discount_code = $_discountCode;
        }

        // Jeff: Update quantity to 0 on any sale item not in items because it was removed
        // Jesse the inentory recon service is for settled sales; the pending count is hooked on inventory save..
        $updPendingInv = array_merge(array_column($items,'inventory_id'),SaleItem::where('sale_id', $this->id)->whereNotIn('id',array_column($items, 'id'))->select('inventory_id')->pluck('inventory_id')->toArray());


        // Jeff: If you save it before you update the items current inventory will be off. So, I commented this out and moved to after the item updates.
        //$this->save();

        SaleItem::where('sale_id', $this->id)->delete();
        $saleService->_addSaleOrderItems($this, $items);

        $this->save();

        $this->discounts()->sync($_discounts);                                  // sync active and inactive associative discount pivot table for this Sale

        foreach(Inventory::whereIn('id',$updPendingInv)->get() as $upd)         // this will update all old/new inventory items pending count
            $upd->save();


        return $this;

    }


    /* THC equavalency amount calculations.. */
    protected function _getSaleItemThcAmount ($item, $quantity) {

        $schema = collect(data_get(AppSchema::getSchema('category_schema'),'form.equivalency_type.values',[]))->firstWhere('id',data_get($item,'product.category.equivalency_type','noncannabis'));
        $ratio = data_get($schema,'value',0);
        $type = data_get($schema,'prompt','amount');

        switch($type){
            case 'amount':
                return ($item->amount_unit * $quantity) * $ratio;
                break;
            case 'potency':
                return (($item->weight_potency * $quantity) * $ratio);          // the weight_potency (in grams) times quantity, then ratio
                break;
            default:                                                            // weight.  the quantity is weighed in grams, so simply multiply by the metrc category ratio
                if(data_get($item,'unit_of_measure','ea')=='g') return ($quantity * $ratio);
                else return ($item->amount_unit * $quantity * $ratio);          // if selling as ea, and the equivalency type prompt is weight based, then use the amount_unit of grams x quantity
        }

    }




    protected static function _getActiveProductsList(){

        return Product::select('id','name')->whereHas('inventory',function($q){
            $q->whereIn('id',array_unique(SaleItem::query()
                ->ofSaleStatus('settled')
                ->pluck('inventory_id')
                ->all()));
        })->orderBy('name')->get();

    }




    /* get and dynamically inject ui schema for this models frontend vue filtering */
    public static function _getSchema(){

        $schema = AppSchema::getSchema('sale_schema');


        data_set($schema,'form.drawer_id.values',Drawer::_getStaffDrawers(),true);
        data_set($schema,'form.lookup_categories',Category::query()->select('id','name')->whereHas('products')->orderBy('name')->ofActive()->get()->push((object)['id'=>0,'name'=>'all']),true);
        data_set($schema,'form.lookup_types',Category::_getFilterTypesList('all')->push((object)['id'=>0,'name'=>'all']),true);

        data_set($schema,'filters.drawer_id.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'drawer_id'),true);
        data_set($schema,'filters.status.values',Util::getActiveFilterList(self::query(),$schema,'status'),true);
        data_set($schema,'filters.product_id.values',self::_getActiveProductsList(),true);
        data_set($schema,'filters.discount_id.values',Discount::_getFilterList('sales'),true);
        data_set($schema,'filters.created_at.values',Util::getActiveFilterDateRange('INIT'),true);

        $location = Auth::guard('api')->user()->location_id;

        /* agg data */
        $todayAgo = Carbon::now()->subDays(1)->toDateTimeString();
        $weekAgo = Carbon::now()->subDays(7)->toDateTimeString();
        $startOfMonth = Carbon::now()->startOfMonth()->toDateTimeString();
        $daysAgo30 = Carbon::now()->subDays(30)->toDateTimeString();
        $daysAgo60 = Carbon::now()->subDays(60)->toDateTimeString();


        data_set($schema,'agg',self::select(DB::raw('
            COUNT(DISTINCT(id)) as `all`,
            COUNT(DISTINCT(CASE WHEN created_at >= "'.$todayAgo.'" THEN id ELSE NULL END)) as all_today,
            SUM(CASE WHEN created_at >= "'.$weekAgo.'" THEN sale_price ELSE NULL END) as total_week,
            SUM(CASE WHEN created_at >= "'.$startOfMonth.'" THEN sale_price ELSE NULL END) as total_mtd,
            
            SUM(CASE WHEN created_at >= "'.$daysAgo30.'" THEN sale_price ELSE NULL END) as total_last30,
            SUM(CASE WHEN created_at <= "'.$daysAgo30.'" AND created_at >= "'.$daysAgo60.'" THEN sale_price ELSE NULL END) as total_previous30,
            
            COUNT(DISTINCT(CASE WHEN status = "pending" THEN id ELSE NULL END)) as pending,
            COUNT(DISTINCT(CASE WHEN created_at >= "'.$startOfMonth.'" AND customer_id IN (SELECT customer_id FROM dispensary_sales ds where ds.location_id='.$location.' and ds.status="settled" GROUP BY customer_id HAVING COUNT(customer_id)>1) THEN customer_id ELSE NULL END)) as repeat_customer_mtd,
            
            COUNT(DISTINCT(CASE WHEN created_at >= "'.$daysAgo30.'" AND customer_id IN (SELECT customer_id FROM dispensary_sales ds where ds.location_id='.$location.' and ds.status="settled" GROUP BY customer_id HAVING COUNT(customer_id)>1) THEN customer_id ELSE NULL END)) as repeat_customer_last30,
            COUNT(DISTINCT(CASE WHEN created_at <= "'.$daysAgo30.'" AND created_at >= "'.$daysAgo60.'" AND customer_id IN (SELECT customer_id FROM dispensary_sales ds where ds.location_id='.$location.' and ds.status="settled" GROUP BY customer_id HAVING COUNT(customer_id)>1) THEN customer_id ELSE NULL END)) as repeat_customer_previous30
            
        '))->ofActive()->first(),true);


        return $schema;

    }



    /* model boot util - generate a unique suite integer upon creation */
    protected static function boot(){

        $user = Auth::guard('api')->user();
        parent::boot();

        static::addGlobalScope(new LocationScope);                              // add location_id tenant scope
        static::creating(function ($model)use($user) {
            $model->location_id =$user->location_id;
            $model->created_at = $model->created_at ?: Carbon::now()->toDateTimeString();
            $model->order_number = $model->order_number ?: data_get($user->location,'settings.sales_prefix','ORD').'-'.Carbon::now()->format('mdyHis');
        });
        static::saved(function ($model)use($user) {

            $invService = new InventoryService();
            $invService->_syncSaleOrder($model->id);                            // this service will reconcille received - sold + adjusted for all inventory_items in order

            $campaignService = new CampaignService();
            $campaignService->_logPosVisit($model);                             // this service will log a visit (non settled sale) or purhase or campaign code use is customer belongs to campaigns!

            if(in_array($model->status,['settled','refunded'])){
                $rewardService = new RewardService();
                $rewardService->_transactReward('sale',$model,$user);           // this service will transact rewards for type sale (if status is complete or returned) - returns true or false
            }

            if($model->customer) $model->customer->save();                      // by saving customer (hint touch wont work for this) it re-aggregates the customers aggregate data as it is updating with this sale.

        });

    }

}
