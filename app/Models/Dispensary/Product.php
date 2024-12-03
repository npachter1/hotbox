<?php

namespace App\Models\Dispensary;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

use App\Models\AppSchema;
use App\Models\Auth\Location;
use App\Models\Auth\Addressbook;

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
class Product extends Eloquent
{

    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/

    protected $table = 'dispensary_products';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify

	protected $fillable = ['name','sku','description','category_id','public_img','type','cbd_percentage','thc_percentage','terpene_percentage'];
	protected $dates = ['archived_at','created_at','updated_at','deleted_at'];
    protected $casts = ['inv_meta' => 'object'];



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	public function location(){
		return $this->belongsTo(Location::class, 'location_id', 'id');
	}

	public function category(){
		return $this->belongsTo(Category::class,'category_id','id');
	}

	public function inventory(){

		return $this->hasMany(Inventory::class, 'product_id', 'id')->orderBy('created_at','desc');
	}

	public function globalinventory(){

		return $this->hasMany(Inventory::class, 'product_id', 'id')->withoutGlobalScope(LocationScope::class)->orderBy('created_at','desc');
	}

    public function pricing(){
	    return $this->belongsToMany(Priceset::class,'dispensary_priceset_dispensary_product','product_id','priceset_id');
	}

    public function grouped(){
	    return $this->belongsToMany(Inventory::class,'disp_inventory_disp_product','disp_product_id','disp_inventory_id')->withPivot('quantity','policy');
	}

    /* functions on Product Object */
    public function activeInventory(){
        return $this->hasMany(Inventory::class, 'product_id', 'id')->where('quantity_on_hand','>',0)->whereNull('archived_at');
    }


	/* get last item received against product */
    public function lastReceived(){
        return $this->hasOne(Inventory::class, 'product_id', 'id')->orderBy('created_at','desc');
    }



    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text, $cnt=5){

        if (!$text) return $query;

        $_words = ($text ? explode(' ',$text) : []);
        for($i=0; $i<=($cnt-1); $i++)
            if(isset($_words[$i]))
                $query->where(function($q) use($_words,$i){
                    $q->where('name', 'like', '%'.$_words[$i].'%')
                        ->orWhere('sku','like', '%'.$_words[$i].'%')
                        ->orWhereHas('category',function($c)use($_words,$i){
                            $c->where('name', 'like', '%'.$_words[$i].'%');
                        })
                        ->orWhereHas('inventory',function($inv)use($_words,$i){
                            $inv->where('item_barcode', 'like', '%'.$_words[$i].'%')
                                ->orWhere('item_strain','like','%'.$_words[$i].'%');
                        });
                });


        return $query;

    }


    /* filter by items vendor */
    public function scopeOfVendors($query,$filter){

        if(!$filter || in_array(strtolower($filter),['','null','undefined',null,'all'])) return $query;
        elseif(empty(($ids = explode(',',$filter)))) return $query;
        else{

            $ids = array_diff($ids,['all']); // if "all" is in the list of ids

            return $query->whereHas('inventory',function($q)use($ids){
                $q->whereIn('addressbook_id', $ids);
            });

        }

    }

    /* filter based on a belongsToMany association with ids passed */
    public function scopeOfPricingFilter($query,$filter){

        if(!$filter || in_array(strtolower($filter),['','null','undefined',null,'all'])) return $query;
        elseif(empty(($ids = explode(',',$filter)))) return $query;
        else{

            $ids = array_diff($ids,['all']); // if "all" is in the list of ids

            return $query->whereHas('inventory',function($q)use($ids){
                $q->whereIn('priceset_id', $ids);
            });

        }

    }


    /* filter by frontend query orderBy filter */
    public function scopeOfActive($query){

        return $query->whereNull('archived_at');

    }




    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/



    /* add inventory meta info to a querys results */
    public function setInvMetaAttribute(){

        $data = null;
        if(($inventory = Inventory::with('sales.sale','vendor')->where('product_id',$this->id)->orderBy('created_at','desc')->get())->count()>0 && $this->type=='single'){

            $sales = collect([]);
            foreach($inventory as $inv)
                foreach($inv->sales->where('sale.status','settled') as $sale)
                    $sales->push($sale);                                        // new collection of sales from all inventory items received against product

            $this->attributes['inv_meta'] = json_encode((object)[               // inv property used for meta.
                'received'  => $inventory->sum('quantity_received'),
                'onhand'    => $inventory->sum('quantity_on_hand'),
                'uom'       => $inventory[0]->unit_of_measure,                  // first inventory record is latest received
                'priceset'  => $inventory[0]->priceset_id,                      // first inventory record is latest received
                'amount_unit'=> $inventory[0]->amount_unit,
                'weight_potency'=> $inventory[0]->weight_potency,
                'cost'      => $inventory[0]->cost_unit ?: 0.01,
                'retail'    => $inventory[0]->retail_unit ?: 0.01,
                'first'     => $inventory->min('created_at'),
                'last'      => $inventory->max('created_at'),
                'expiring'  => $inventory->max('expires_at'),
                'sold'      => $sales->sum('quantity'),
                'revenue'   => $sales->sum('sale_price'),
                'vendor'    => data_get($inventory[0],'vendor.name','Misc'),
                'licensenum'    => data_get($inventory[0],'vendor.licensenum',null),
                'strain'    => data_get($inventory[0],'item_strain',null),
                'received_at' => data_get($inventory[0],'created_at',null),
                'first_sale'=> (($first = $sales->min('created_at')) ? $first->toDateTimeString() : null),
                'last_sale' => (($last = $sales->max('created_at')) ? $last->toDateTimeString() : null),
            ]);

            if($inventory->sum('quantity_on_hand')>0)
                $this->attributes['archived_at'] = null;                        // if we do have qty on hand, then, lets ensure it is not archived.
        }elseif(in_array($this->type,['grouping','kit']) && $this->grouped->count()>0){

            $invService = new InventoryService;
            $user = Auth::guard('api')->user();

            $gqty = $this->grouped->map(function($itm,$ky)use($invService,$user){
                return (object)[
                    'id'        => $itm->id,
                    'received'  => ($itm->quantity_received / ($itm->pivot->quantity ?: 1)),
                    'onhand'    => ($itm->quantity_on_hand / ($itm->pivot->quantity ?: 1)),
                    'cost'      => ($itm->cost_unit * $itm->pivot->quantity),
                    'retail'    => (data_get($invService->_getCurrentPrice(['quantity'=>$itm->pivot->quantity,'quantity_priced_at'=>$itm->pivot->quantity],$itm->id,$user),'current_price',0) * $itm->pivot->quantity)
                ];
            });

            $this->attributes['inv_meta'] = json_encode((object)[               // inv property used for meta on a grouping type.
                'received'  => $gqty->min('received'),
                'onhand'    => $gqty->min('onhand'),
                'cost'      => $gqty->sum('cost'),
                'retail'    => $gqty->sum('retail'),
                'uom'       => 'ea',                                            // since it is a grouping - it is each.
                'first'     => $this->created_at,                               // the first receiving is when the grouping was created - as all grouped had to be received before assgining,
                'grouped'   => $gqty->toArray()
            ]);

        }else $this->attributes['inv_meta'] = null;

    }


    /* update nature_type index (rec, med, non) */
    public function setNatureTypeAttribute(){

        if(!$this->category) $this->attributes['nature_type'] = $this->nature_type;
        elseif(!$this->location) $this->attributes['nature_type'] = $this->nature_type;
        else{

            if($this->category->contains_thc===false) $this->attributes['nature_type'] = 'noncannabis';
            elseif(data_get($this->location,'settings.is_medical',false)===true) $this->attributes['nature_type'] = 'medical';
            elseif($this->location->type=='dispensary') $this->attributes['nature_type'] = 'recreational';

            // HINT: conditionally set others as needed here for use in app with tax calc, limits, etc..




            else $this->attributes['nature_type'] = 'misc';

        }

    }





    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/

    /* get aggregated data for customer group filters */
    public static function _getCustomerFilterListData(){

        $hash = [];                                                        // load an associated arrar of product's customer purchased counts
        foreach(Customer::select('id','product_ids_purchased')->whereNotNull('product_ids_purchased')->get() as $cust)
            foreach(explode(',',$cust->product_ids_purchased) as $item)
                data_set($hash,$item,data_get($hash,$item,0)+1,true);

        return self::withoutGlobalScope(LocationScope::class)->select('id','name','location_id')
                ->with('globalinventory.globalsales')
                ->whereIn('id',array_keys($hash))
                ->get()
                ->map(function($item,$key)use($hash){                           // custom load aggregates from lazy-loaded relations (vs appended mutators)

                    $sales = collect([]);
                    foreach($item->globalinventory as $inv)
                        foreach($inv->globalsales as $sale) $sales->push($sale);      // new collection of sales from all inventory items received against product

                    $item->instock_count = $item->globalinventory->sum('quantity_on_hand');
                    $item->soonest_expiry = $item->globalinventory->max('expires_at');
                    $item->last_received = $item->globalinventory->max('created_at');

                    // TODO test/demo sample data doesnt have sales last 30 days! - so I did 360 for now..
                    $item->mtd_sold = $sales->whereBetween('created_at',[Carbon::now()->subDays(360)->startOfDay()->toDateTimeString(),Carbon::now()->toDateTimeString()])->sum('quantity');
                    $item->mtd_revenue = $sales->whereBetween('created_at',[Carbon::now()->subDays(360)->startOfDay()->toDateTimeString(),Carbon::now()->toDateTimeString()])->sum('sale_price');
                    $item->mtd_trend = round(((($sales->whereBetween('created_at',[Carbon::now()->subDays(360)->startOfDay()->toDateTimeString(),Carbon::now()->subDays(181)->endOfDay()->toDateTimeString()])->sum('quantity') ?: 1)/($item->mtd_sold ?: 1))*100),1);


                    $item->customer_count = data_get($hash,$item->id,0);

                    unset($item->globalinventory);                              // reduce meta info for frontend
                    return $item;
                });
    }


    /* parse best 3 words from ie (metrc supplied) name */
    public static function _getBestSearchFromName($search){

        if(!is_string($search)) return null;

        return join(' ',collect((explode(' ',$search) ?: []))->map(function($item,$key){
            return (object)['name'=>strtolower(str_replace('/[0-99999]mg|[0-9]pc|\.[0-999]g/gi','',$item))];
        })->filter(function($item,$key){
            return (strlen($item->name)>2 ? true : false);
        })->pluck('name')->slice(0,3)->values()->toArray());

    }


    /* get product filters from inventory */
    public static function _getPricesetFilters(){

        return self::select('id','name')->with('inventory')->whereHas('inventory',function($q){
            $q->whereNotNull('priceset_id');
        })->orderBy('name')->ofActive()->get()->map(function($item,$key){
            return (object)[
                'id'        => $item->id,
                'name'      => $item->name,
                'count'     => $item->inventory->unique('priceset_id')->count()
            ];
        });

    }




    /* get ui schema for this model */
    public static function _getSchema(){

        $schema = AppSchema::getSchema('product_schema');

        data_set($schema,'form.category_id.values',Category::query()->select('id','name')->orderBy('name')->ofActive()->get(),true); // all categories registered
        data_set($schema,'form.priceset_id.values',Priceset::select('id','name_grade as name')->orderBy('name_grade')->get(),true);
        data_set($schema,'form.vendor_id.values',Addressbook::where('type','vendor')->whereNull('archived_at')->select('id','name')->orderBy('name')->get(),true);

        data_set($schema,'filters.type.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'type'),true);
        data_set($schema,'filters.category_id.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'category_id'),true); // will map used category ids to their names
        data_set($schema,'filters.priceset_id.values',Priceset::_getProductSetFilters(),true);
        data_set($schema,'filters.vendor_id.values',Addressbook::_getProductVendorFilters(),true);

        data_set($schema,'form.product_search.categories',Category::query()->select('id','name','contains_thc')->whereHas('products')->orderBy('name')->ofActive()->get(),true); // all categories registered
        data_set($schema,'form.product_search.types',Category::_getFilterTypesList('all'),true); // all categories registered


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

        static::addGlobalScope(new LocationScope);                              // add location_id tenant scope
        static::creating(function ($model) {
            $model->location_id = $model->location_id ?: Auth::guard('api')->user()->location_id;      // inventory is scoped to location
            $model->created_at = $model->created_at ?: Carbon::now()->toDateTimeString();
        });
        static::saving(function ($model) {
            $model->setAttribute('nature_type',null);
            $model->setAttribute('inv_meta',null);
        });

    }

}
