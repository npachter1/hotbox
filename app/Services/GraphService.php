<?php

namespace App\Services;

use App\Models\Auth\User;
use App\Models\Dispensary\Product;
use App\Models\Dispensary\Customer;
use App\Models\Dispensary\Sale;
use App\Models\Dispensary\Inventory;
Use App\Models\Dispensary\Campaign;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Auth;
use Carbon\CarbonPeriod;
use Exception;
use DB;
use Carbon\Carbon;
use stdClass;
use Illuminate\Support\Facades\Validator;

class GraphService extends BaseService
{

    public static $TIME_PERIOD_DAILY = 'DATE';
    public static $TIME_PERIOD_WEEKLY = 'WEEK';
    public static $TIME_PERIOD_MONTHLY = 'MONTH';
    public static $TIME_PERIOD_YEARLY = 'YEAR';
    

    /* Top Sellers By Price Pie Chart Data */
    public function TopSellersByPrice(array $filters,$user){
        
        // TODO - this works as MVP (only so many products a dispensary can have in the field) however,
        // we should create a saleitem - product pivot table with the date and amount indexed for quick and memory efficient lookup.

        $data = collect([]);
        $start = data_get($filters,'start',Carbon::now()->subdays(365)->toDateTimeString());
        $end = data_get($filters,'end',Carbon::now()->toDateTimeString());

        foreach(Product::select('id','name')->with('inventory.sales.sale')->whereHas('inventory')->ofActive()->get() as $prod){
                
                $sales = collect([]);
                foreach($prod->inventory as $inv)
                    foreach($inv->sales
                        ->whereBetween('sale.settled_at',[$start,$end])
                        ->whereIn('sale.status', ['settled','refunded']) as $sale) // ->where('sale.status','settled') as $sale)
                            $sales->push($sale);                                // new collection of filtered sales from all inventory items received against products

                $total_sales = $sales->sum('price');
                if ($total_sales>0) {
                    $data->push((object)[                                           // inv property used for meta.
                        'total_sale_price'  => number_format($total_sales,2),
                        'product_name'     => $prod->name,
                        'product_id'        => $prod->id
                    ]);
                }
        }
        
        return $data->sortByDesc('total_sale_price')->slice(0,data_get($filters,'limit',10))->values()->toArray();
        
    }
    
    
    /* get Sales by Age Chart Data */
    public function SalesByAge(array $filters,$user){
        
        /* Query customers, while eager loading their sales*, by created_at date and location, and mapping the laravel collection */
        // *HINT: this method was chosen to limit queries/joins, as assumed only so much data (per client group customers/sales) hence prirotizing memory over resource to keep it fast and efficient..
        // TODO: this assumes the date and location filter on the customers created on and sales, perhaps it better to choose one?..
        $start = data_get($filters,'start',Carbon::now()->subdays(365)->toDateTimeString());
        $end = data_get($filters,'end',Carbon::now()->toDateTimeString());

        return Customer::query()->with('sales')
            ->select(DB::raw('GROUP_CONCAT(DISTINCT id SEPARATOR ",") AS customer_ids,SUM(total_queue_count) as visits,YEAR(birthdate) as born'))
            ->ofMatchFilter(data_get($filters,'location',null),'location_id')
            ->whereBetween('created_at',[$start,$end])
            ->groupBy('born')
            ->get()->map(function($group,$key)use($filters,$start,$end){
              return (object)[
                  'age'     => Carbon::parse($group->born.'-01-01')->age,
                  'visits'  => (int)$group->visits,
                  'amount'  => (float)Sale::query()                             // get an aggregate count (theres only 100 potential queries here..)
                    ->whereIn('customer_id',explode(',',$group->customer_ids))
                    ->ofMatchFilter(data_get($filters,'location',null),'location_id')
                    // ->whereRaw('((status="refunded" AND created_at BETWEEN ? AND ?) OR (status="settled" AND settled_at BETWEEN ? AND ?))',[$start,$end,$start,$end])
                    ->whereBetween('settled_at',[$start,$end])
                    ->where('status','settled')
                    ->sum('sale_price')
                  ];
            })->where('amount','>',0)->sortByDesc('amount')->values()->toArray();
                
    }

    /* get Sales by Location Pie Chart Data */
    public function SalesByLocation(array $filters,$user){
        $start = data_get($filters,'start',Carbon::now()->subdays(365)->toDateTimeString());
        $end = data_get($filters,'end',Carbon::now()->toDateTimeString());

        return DB::table('dispensary_sales as s')
            ->join('auth_locations as l','s.location_id','=','l.id')
            ->select(DB::raw('l.name as location_name, SUM(sale_price) as total_sales'))
            // ->whereRaw('((s.status="refunded" AND s.created_at BETWEEN ? AND ?) OR (s.status="settled" AND s.settled_at BETWEEN ? AND ?))',[$start,$end,$start,$end])
            ->whereBetween('s.settled_at',[$start,$end])
            ->whereIn('s.status', ['settled','refunded']) //->where('s.status','settled')
            ->where('sale_price','>',0)
            ->groupBy('location_id')
            ->groupBy('l.name')
            ->orderBy('total_sales','DESC')
            ->get()
            ->toArray();

    }

    public function DashboardMetricsLoyalty() {
        return DB::table('dispensary_campaigns')
            ->select(DB::raw('COALESCE(SUM(notified_count),0) as notified_count'))
            ->whereRaw('scheduled_at BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW()')
            ->get()
            ->toArray();
    }
    public function DashboardMetricsSales(array $filters) {
        return DB::table('dispensary_sales as s')
            ->select(DB::raw('IF (settled_at BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW(),\'last30\',\'previous30\') as datePeriod,SUM(sale_price) as total_sales'))
//            ->select(DB::raw('
//                IF((
//                   (s.status = "refunded" AND s.created_at BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW())
//                   OR
//                   (s.status = "settled" AND s.settled_at BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW())
//                ),\'last30\',\'previous30\') as datePeriod,
//                SUM(sale_price) as total_sales'))
//            ->whereRaw('((s.status="refunded" AND s.created_at BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND NOW()) OR (s.status="settled" AND s.settled_at BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND NOW()))')
            ->whereRaw('s.settled_at BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND NOW()')
            ->whereIn('s.status', ['settled','refunded']) //->where('s.status','settled')
            ->where('sale_price','>',0)
            ->groupBy('datePeriod')
            ->orderBy('datePeriod')
            ->get()
            ->toArray();
    }

    public function SalesByCustomerCity(array $filters,$user){
        $start = data_get($filters,'start',Carbon::now()->subdays(365)->toDateTimeString());
        $end = data_get($filters,'end',Carbon::now()->toDateTimeString());
        $location = data_get($filters,'location_id',0);

        return DB::table('dispensary_sales as s')
            ->join('dispensary_customers as c','s.customer_id','=','c.id')
            ->join('auth_addressbook as a','c.addressbook_id','=','a.id')
            ->select(DB::raw('IFNULL(a.city,\'No City Specified\') as city,SUM(s.sale_price) as total_sales'))
            ->whereBetween('s.settled_at',[$start,$end])
            ->whereIn('s.status', ['settled','refunded'])
            //->whereRaw('((s.status="refunded" AND s.created_at BETWEEN ? AND ?) OR (s.status="settled" AND s.settled_at BETWEEN ? AND ?))',[$start,$end,$start,$end])
            ->where('sale_price','>',0)
            ->when(intval($location)>0, function($query, $location) {
                return $query->where('s.location_id',$location.'');
            })
            ->groupBy('a.city')
            ->orderBy('total_sales','DESC')
            ->get()
            ->toArray();

    }
    
    /* an alternate to sales by city - with eager loading, model application scoping/filters, and collection mapping */
    public function AltSalesByCustomerCity(array $filters,$user){
        return Sale::query()->with('customerAbbv.address')
            ->wherebetween('created_at',[data_get($filters,'start',Carbon::now()->subdays(365)->toDateTimeString()),data_get($filters,'end',Carbon::now()->toDateTimeString())])
            ->ofMatchFilter('location_id',data_get($filters,'location_id','all'))
            ->ofActive()
            ->get()
            ->groupBy('customerAbbv.address.city')
            ->map(function($sales,$city)use($filters){
                return (object)[
                    'city'              => $city ?: 'No City Specified',
                    'total_sales'       => $sales->sum('sale_price')
                ];
            })->sortByDesc('total_sales')
            ->values()
            ->toArray();
            
    }
    
    
    
    /* get sales/visits by demographic segments */
    public function SalesBySegments(array $filters, $user){
        
        switch(data_get($filters,'period','month')){
            case 'year':
                $format = '%Y';
                break;
            case 'month':
                $format = '%Y-%m'; //'%Y-%c'
                break;
            case 'day':
                $format = '%Y-%m-%d'; //'%Y-%c-%e'
                break;
            default: $format = '%Y-%c';
        }
        
        return Sale::query()->with('customer')
            ->select(DB::raw('id,location_id,customer_id,sale_price,status,DATE_FORMAT(created_at,"'.$format.'") as period'))
            ->wherebetween('created_at',[
                Carbon::create(data_get($filters,'start',Carbon::now()->subdays(365)->toDateTimeString()))->startOfDay()->toDateTimeString(),
                Carbon::create(data_get($filters,'end',Carbon::now()->toDateTimeString()))->endOfDay()->toDateTimeString()
            ])
            ->ofMatchFilter('location_id',data_get($filters,'location_id','all'))
            ->ofActive()
            ->get()
            ->groupBy('period')
            ->map(function($items,$period)use($filters,$user){
                return (object)[
                    'period'    => $period,
                    'data'      => (object)[
                        'all'       => (object)[
                            'sales_amount'    => $items->sum('sale_price'),
                            'sales_count'     => $items->count(),
                            'customer_visits'    => $items->sum('customer.total_queue_count')
                        ],
                        'recreational'   => (object)[    // TODO map current locations actual state
                            'sales_amount'    => $items->where('customer.type','recreational')->sum('sale_price'),
                            'sales_count'     => $items->where('customer.type','recreational')->count(),
                            'customer_visits'    => $items->where('customer.type','recreational')->sum('customer.total_queue_count')
                        ],
                        'patient'   => (object)[    // TODO map current locations actual state
                            'sales_amount'    => $items->where('customer.type','patient')->sum('sale_price'),
                            'sales_count'     => $items->where('customer.type','patient')->count(),
                            'customer_visits'    => $items->where('customer.type','patient')->sum('customer.total_queue_count')
                        ],
                        'optin'   => (object)[    // TODO map current locations actual state
                            'sales_amount'    => $items->where('customer.sms_optin',true)->sum('sale_price'),
                            'sales_count'     => $items->where('customer.sms_optin',true)->count(),
                            'customer_visits'    => $items->where('customer.sms_optin',true)->sum('customer.total_queue_count')
                        ]
                    ]
                ];
            })->sortKeys()->values()->toArray();
            
    }

    public function SalesProductPerformance(array $filters): array {
        $start = (new Carbon(data_get($filters, 'start', Carbon::now()->subdays(365)->toDateTimeString())))->startOfDay();
        $end = (new Carbon(data_get($filters, 'end', Carbon::now()->toDateTimeString())))->endOfDay();
        $locationId = data_get($filters, 'location_id', 'all');

        $filterSales = function ($query) use ($locationId, $start, $end) {
            $r = $query
                ->wherebetween('settled_at', [$start->toDateTimeString(), $end->toDateTimeString()])
                ->whereIn('dispensary_sales.status', ['settled','refunded'])
                //->whereRaw('((dispensary_sales.status="refunded" AND created_at BETWEEN ? AND ?) OR (dispensary_sales.status="settled" AND settled_at BETWEEN ? AND ?))',[$start,$end,$start,$end])
                ->ofMatchFilter($locationId, 'location_id');
            return $r;
        };

        $products = Product::with(['inventory.sales.sale' => $filterSales, 'inventory.adjustments'])
            ->select(DB::raw('*'))
            ->ofMatchFilter($locationId, 'location_id')
            ->ofActive()
            ->whereHas('inventory.sales.sale', $filterSales)
            ->get();

        $productSales = $products->map(function (Product $product) use ($start, $end, $locationId) {
            $productInventorySales = $product->inventory->map(function (Inventory $inventory) use ($start, $end, $locationId) {
                $date_in_stock = ($start->greaterThan($inventory->created_at->startOfDay())) ? $start : $inventory->created_at->startOfDay();
                $date_out_stock = $this->getDateOutOfStock($inventory, $end);
                $days_in_stock = $date_out_stock->diffInDays($date_in_stock) + 1; //add one to make the count inclusive
                if ($days_in_stock < 1) $days_in_stock = 1; // should not happen but just in case db data is screwy

                $sales = $inventory
                    ->sales
                    ->wherebetween('created_at', [$start->toDateTimeString(), $end->toDateTimeString()]) //inventory created_at
                    ->whereIn('sale.status',['settled','refunded'])
                    ->when($locationId != 'all', function ($query) use ($locationId) {
                        return $query->where('sale.location_id', $locationId);
                    });
//                    ->filter(function($saleItem, $key) use ($start,$end) {
//                        $status = $saleItem->sale()->first()->status;
//                        if ($status==='settled' || $status==='refunded') {
//                            if ($status==='settled') $saleDate = $saleItem->sale()->first()->settled_at;
//                            if ($status==='refunded') $saleDate = $saleItem->sale()->first()->created_at;
//                            return $saleDate->isBetween($start,$end);
//                        }
//                        return false; //$saleItem->where('sale.status','settled');
//                    });

                if ($sales->count() == 0) return false; //bail if nothing to report

                $sale_price = $sales->sum('sale_price');
                $tax = $sales->sum('tax');
                $quantity_sold = $sales->SUM('quantity');
                $cogs = $inventory->cost_unit * $quantity_sold;
                $period = CarbonPeriod::create($date_in_stock, '1 day', $date_out_stock);
                return [
                    "revenue" => $sale_price - $tax,
                    "cogs" => $cogs,
                    "quantity_sold" => $quantity_sold,
                    "days_in_stock" => $days_in_stock,
                    "stock_period" => $period,
                    "profit" => $sale_price - $tax - $cogs
                ]; //$inventorySales;
            });
            $productInventorySales = $productInventorySales->filter(); //remove false items

            if ($productInventorySales->count() == 0) return false;
            return $this->getProductInventorySalesArray($productInventorySales, $product);
        });

        $productSales = $productSales->filter(); //remove false items

        return $productSales
            ->sortKeys()
            ->values()
            ->toArray();
    }

    private function getDateOutOfStock(Inventory $inventory, Carbon $end): Carbon {
        if (($inventory->quantity_on_hand > 0) || ($inventory->adjustments->count() == 0)) {
            $date_out_stock = Carbon::now()->startOfDay(); //still in stock, set out of stock date to latest possible (today)
        } else {
            $date_out_stock = $inventory->adjustments->max('created_at')->startOfDay(); //last date of adjustment
        }
        if ($end->lessThan($date_out_stock)) $date_out_stock = $end; // if out of stock date is past the filter end date, just use filter end date

        return $date_out_stock;
    }

    private function getProductInventorySalesArray(Collection $productInventorySales, Product $product): array {
        $revenue = ($productInventorySales->count() > 0) ? $productInventorySales->sum("revenue") : 0;
        $quantity_sold = ($productInventorySales->count() > 0) ? $productInventorySales->sum("quantity_sold") : 0;
        $cogs = ($productInventorySales->count() > 0) ? $productInventorySales->sum("cogs") : 0;
        $profit = ($productInventorySales->count() > 0) ? $productInventorySales->sum("profit") : 0;
        $periods = $productInventorySales->map(function ($item, $key) {
            return $item['stock_period']->toArray(); // take all the periods and make a standard array so we can more easily de-dupe & count
        });
        $days_in_stock = count(array_unique(Arr::collapse($periods->all()))); //combine and de-dupe
        $margin = $revenue == 0 ? 0 : (($revenue - $cogs) / $revenue) * 100;
        $quantity_on_hand = $product->inventory->sum('quantity_on_hand');
        $days_until_gone = ($quantity_sold==0 || $days_in_stock==0) ? 9999 : floor($quantity_on_hand/($quantity_sold / $days_in_stock));
        $quantity_sold_per_day = $days_in_stock > 0 ? $quantity_sold / $days_in_stock : $quantity_sold;

        return [
            "product_name" => $product->name,
            "product_id" => $product->id,

            "revenue" => round($revenue, 2),
            "revenue_per_day" => $days_in_stock > 0 ? round($revenue / $days_in_stock, 2) : round($revenue, 2),

            "quantity_sold" => round($quantity_sold, 2),
            "quantity_sold_per_day" => round($quantity_sold_per_day, 2),

            "cogs" => round($cogs, 2),
            "cogs_per_day" => $days_in_stock > 0 ? round($cogs / $days_in_stock, 2) : round($cogs, 2),

            "margin" => $revenue == 0 ? 0 : round($margin, 0),

            "profit" => round($profit, 2),
            "profit_per_day" => $days_in_stock > 0 ? round($profit / $days_in_stock, 2) : round($profit, 2),

            "quantity_on_hand" => round($quantity_on_hand,2),
            "days_until_gone" => max(0, $days_until_gone),

            "reorder_30" => max(0, ceil(($quantity_sold_per_day*30) - $quantity_on_hand)), //ideally would factor in expiration date
            "reorder_60" => max(0, ceil(($quantity_sold_per_day*60) - $quantity_on_hand)),
            "reorder_90" => max(0, ceil(($quantity_sold_per_day*90) - $quantity_on_hand)),

            //"days_in_stock"=>$days_in_stock
        ];
    }

    public function InventoryInsights(array $filters): array {
        $start = Carbon::now()->subdays(365)->startOfDay();
        $end = Carbon::now()->endOfDay();

        $locationId = data_get($filters, 'location_id', 'all');

        $filterSales = function ($query) use ($locationId, $start, $end) {
            $r = $query
                // ->whereRaw('((dispensary_sales.status="refunded" AND created_at BETWEEN ? AND ?) OR (dispensary_sales.status="settled" AND settled_at BETWEEN ? AND ?))',[$start,$end,$start,$end])
                ->wherebetween('settled_at', [$start->toDateTimeString(), $end->toDateTimeString()])
                ->whereIn('dispensary_sales.status', ['settled','refunded'])
                ->ofMatchFilter($locationId, 'location_id');
            return $r;
        };

        $products = Product::with(['inventory.sales.sale' => $filterSales, 'inventory.adjustments'])
            ->select(DB::raw('*'))
            ->ofMatchFilter($locationId, 'location_id')
            ->ofActive()
            ->whereHas('inventory.sales.sale', $filterSales)
            ->get();

        $productSales = $products->map(function (Product $product) use ($start, $end, $locationId) {
            $productInventorySales = $product->inventory->map(function (Inventory $inventory) use ($start, $end, $locationId) {
                $date_in_stock = ($start->greaterThan($inventory->created_at->startOfDay())) ? $start : $inventory->created_at->startOfDay();
                $date_out_stock = $this->getDateOutOfStock($inventory, $end);
                $days_in_stock = $date_out_stock->diffInDays($date_in_stock) + 1; //add one to make the count inclusive
                if ($days_in_stock < 1) $days_in_stock = 1; // should not happen but just in case db data is screwy

                $sales = $inventory
                    ->sales
                    ->wherebetween('created_at', [$start->toDateTimeString(), $end->toDateTimeString()]) //inventory created_at
                    ->whereIn('sale.status', ['settled','refunded'])
                    ->when($locationId != 'all', function ($query) use ($locationId) {
                        return $query->where('sale.location_id', $locationId);
                    });
//                    ->filter(function($saleItem, $key) use ($start,$end) {
//                        $status = $saleItem->sale()->first()->status;
//                        if ($status==='settled' || $status==='refunded') {
//                            if ($status==='settled') $saleDate = $saleItem->sale()->first()->settled_at;
//                            if ($status==='refunded') $saleDate = $saleItem->sale()->first()->created_at;
//                            return $saleDate->isBetween($start,$end);
//                        }
//                        return false; //$saleItem->where('sale.status','settled');
//                    });

                if ($sales->count() == 0) return false; //bail if nothing to report

                $sale_price = $sales->sum('sale_price');
                $tax = $sales->sum('tax');
                $quantity_sold = $sales->SUM('quantity');
                $cogs = $inventory->cost_unit * $quantity_sold;
                $period = CarbonPeriod::create($date_in_stock, '1 day', $date_out_stock);

                return [
                    "revenue" => $sale_price - $tax,
                    "cogs" => $cogs,
                    "quantity_sold" => $quantity_sold,
                    "days_in_stock" => $days_in_stock,
                    "stock_period" => $period,
                    "profit" => $sale_price - $tax - $cogs
                ]; //$inventorySales;
            });
            $productInventorySales = $productInventorySales->filter(); //remove false items

            if ($productInventorySales->count() == 0) return false;

            return $this->getProductInventorySalesArray($productInventorySales, $product);
        });

        $productSales = $productSales->filter(); //remove false items

        return $productSales
            ->sortKeys()
            ->values()
            ->toArray();
    }

    public function ProductInventoryInsights(array $filters): array {
        $productId = data_get($filters, 'productId', '0');
        $start = Carbon::now()->subweek(52)->startOfDay();
        $end = Carbon::now()->endOfDay();
        $currentDate = $start;
        //loop through each week and for the $currentDate, get
        //  inventory count
        //  sales rate for previous 7 days

        $period = CarbonPeriod::create($start, '1 week', $end);
        $returnArray = [];

        $product = Product::with(['inventory.sales.sale', 'inventory.adjustments'])
            ->select(DB::raw('*'))
            ->where('id', $productId)
            ->get()->first();

        foreach ($period as $key) {
            $startWeek = (new Carbon($key->format('Y-m-d')))->subWeek()->addDay()->startOfDay(); //we don't count sat->sat, it's more like sun->sat (inclusive)
            $endWeek = (new Carbon($key->format('Y-m-d')))->endOfDay();

            $productInventoryData = $product->inventory->map(function (Inventory $inventory) use ($startWeek, $endWeek) {
                $date_in_stock = ($startWeek->greaterThan($inventory->created_at->startOfDay())) ? $startWeek : $inventory->created_at->startOfDay();
                $date_out_stock = $this->getDateOutOfStock($inventory, $endWeek);
                $stock_period = CarbonPeriod::create($date_in_stock, '1 day', $date_out_stock);

                if ($date_out_stock>=$endWeek) {
                    $inventory_on_hand=$this->getInventoryOnHandAtDate($inventory, $endWeek);
                } else {
                    $inventory_on_hand=0;
                }

                $sales = $inventory
                    ->sales
                    ->wherebetween('created_at', [$startWeek->toDateTimeString(), $endWeek->toDateTimeString()])
                    ->where('sale.status', 'settled');

                return [
                    'stock_period'=>$stock_period,
                    'quantity_sold'=>$sales->SUM('quantity'),
                    'inventory_on_hand'=>$inventory_on_hand
                ];
            });

            $productSales = $productInventoryData->sum('quantity_sold');

            $periods = $productInventoryData->map(function ($item) {
                return $item['stock_period']->toArray(); // take all the periods and make a standard array so we can more easily de-dupe & count
            });
            $days_in_stock = count(array_unique(Arr::collapse($periods->all()))); //combine and de-dupe

            $inventory_on_hand = $productInventoryData->sum('inventory_on_hand');

            array_push($returnArray, (object)[
                "date"=>$endWeek->startOfDay()->toDateString(),
                "product_sales"=>$productSales,
                "days_in_stock"=>$days_in_stock,
                "quantity_sold_per_day"=>$days_in_stock==0 ? 0 : $productSales/$days_in_stock,
                "quantity_on_hand"=>$inventory_on_hand,
            ]);
        }

        return $returnArray;
    }

    private function getInventoryOnHandAtDate(Inventory $inventory, Carbon $date): Float {
        $quantity_on_hand = $inventory->quantity_received;

        $adjustments = $inventory->adjustments->where('created_at','<=',$date);

        foreach ($adjustments as $adjustment) {
            if ($adjustment->created_at<=$date) {
                $quantity_on_hand+=$adjustment->value;
            }
        }

        return $quantity_on_hand;
    }

    public function DispensaryLoyaltyCampaign(array $filters): array {
        $start = (new Carbon(data_get($filters, 'start', Carbon::now()->subdays(365)->toDateTimeString())))->startOfDay();
        $end = (new Carbon(data_get($filters, 'end', Carbon::now()->toDateTimeString())))->endOfDay();
        $status = data_get($filters, 'status', null);

        $campaigns = DB::table('dispensary_campaigns as c')
            ->select(DB::raw("id,name,campaign_code,notified_count,visited_count,purchased_count,codeused_count,(SELECT IFNULL(SUM(s.price-s.discount),0) FROM dispensary_sales s WHERE s.discount_code=c.campaign_code AND status IN ('settled','refunded')) as totalSales"))
            ->wherebetween('created_at', [$start->toDateTimeString(), $end->toDateTimeString()]);

        if ($status) $campaigns->whereIn('status',$status);

        return $campaigns->get()->toArray();
    }

    private function getFilterValues(array $filters): array {
        $start = (new Carbon(data_get($filters, 'start', Carbon::now()->subdays(365)->toDateTimeString())))->startOfDay();
        $end = (new Carbon(data_get($filters, 'end', Carbon::now()->toDateTimeString())))->endOfDay();
        $locationId = data_get($filters, 'location_id', 'all');

        return [$start,$end,$locationId];
    }
    /*
 * Customer By Loyalty Points
SELECT c.id,SUM(r.points_transacted) as total_points,DATE_FORMAT(r.created_at,"%Y-%m") as period
FROM dispensary_customers as c
INNER JOIN dispensary_rewards as r ON r.customer_id=c.id
GROUP BY period, id
ORDER BY period DESC;
   */
    public function DispensaryLoyaltyCustomersByRewards(array $filters): array {
        list($start,$end,$locationId) = $this->getFilterValues($filters);

        return DB::table('dispensary_customers as c')
            ->select(DB::raw('c.id,c.first_name,c.last_name,SUM(r.points_transacted) as total_points,DATE_FORMAT(r.created_at,"%Y-%m") as period'))
            ->join('dispensary_rewards as r','r.customer_id','=','c.id')
            ->when($locationId != 'all', function ($query) use ($locationId) {
                return $query->where('c.location_id', $locationId);
            })
            ->whereBetween('r.created_at',[$start->toDateTimeString(),$end->toDateTimeString()])
            ->groupBy('period','id')
            ->orderBy('period','DESC')
            ->get()
            ->groupBy('period')
            ->toArray();
    }

    public function DispensaryLoyaltyCustomersBySales(array $filters): array {
        list($start,$end,$locationId) = $this->getFilterValues($filters);

        return DB::table('dispensary_sales as s')
            ->select(DB::raw('c.id,c.first_name,c.last_name,SUM(s.sale_price) as total_sales,DATE_FORMAT(s.created_at,"%Y-%m") as period'))
            ->join('dispensary_customers as c','s.customer_id','=','c.id')
            ->when($locationId != 'all', function ($query) use ($locationId) {
                return $query->where('c.location_id', $locationId);
            })
            ->whereBetween('s.settled_at',[$start->toDateTimeString(),$end->toDateTimeString()])
            ->whereIn('s.status', ['settled','refunded'])
            //->whereRaw('((s.status="refunded" AND s.created_at BETWEEN ? AND ?) OR (s.status="settled" AND s.settled_at BETWEEN ? AND ?))',[$start,$end,$start,$end])
            ->groupBy('period','id')
            ->orderBy('period','DESC')
            ->get()
            ->groupBy('period')
            ->toArray();
    }

    public function DispensaryLoyaltyDiscountsUsed(array $filters): array {
        list($start,$end,$locationId) = $this->getFilterValues($filters);

        return DB::table('dispensary_discount_dispensary_sale as ddds')
            ->select(DB::raw('ddds.discount_id as discountId,dd.name as discountName,COUNT(ddds.discount_id) as discountUsedCount'))
            ->join('dispensary_sales as ds','ddds.sale_id','=','ds.id')
            ->join('dispensary_discounts as dd','ddds.discount_id','=','dd.id')
            ->when($locationId != 'all', function ($query) use ($locationId) {
                return $query->where('ds.location_id', $locationId);
            })
            ->whereBetween('ds.settled_at',[$start->toDateTimeString(),$end->toDateTimeString()])
            ->where('ds.status','settled')
            ->where('ddds.is_active',1)
            ->groupBy('ddds.discount_id')
            ->orderBy('discountUsedCount','DESC')
            ->get()
            ->toArray();
    }

    public function DispensaryLoyaltyCustomersRepeat(array $filters): array {
        list($start,$end,$locationId) = $this->getFilterValues($filters);

        return DB::table('dispensary_sales as ds')
            ->select(DB::raw('DATE_FORMAT(ds.created_at,\'%Y-%m\') as period,
                                (SELECT IF(COUNT(*)>0,true,false) FROM dispensary_sales s WHERE s.customer_id=ds.customer_id AND s.status=\'settled\' AND s.settled_at<ds.settled_at) as previousCustomer,
                                SUM(ds.sale_price) as totalSales'))
            ->join('dispensary_customers as dc','ds.customer_id','=','dc.id')
            ->when($locationId != 'all', function ($query) use ($locationId) {
                return $query->where('ds.location_id', $locationId);
            })
            // ->whereRaw('((ds.status="refunded" AND ds.created_at BETWEEN ? AND ?) OR (ds.status="settled" AND ds.settled_at BETWEEN ? AND ?))',[$start,$end,$start,$end])
            ->whereBetween('ds.settled_at',[$start->toDateTimeString(),$end->toDateTimeString()])
            // ->where('ds.status','settled')
            ->whereIn('s.status', ['settled','refunded'])
            ->groupBy('period','previousCustomer')
            ->orderBy('period','ASC')
            ->orderBy('previousCustomer','ASC')
            ->get()
            ->toArray();
    }

    public function BudtenderStats(array $filters): array {
        $start = (new Carbon(data_get($filters, 'start', Carbon::now()->subdays(365)->toDateTimeString())))->startOfDay();
        $end = (new Carbon(data_get($filters, 'end', Carbon::now()->toDateTimeString())))->endOfDay();
        $location = data_get($filters,'location_id',0);

        return DB::select("
            SELECT
                u.id,
                u.name,
                SUM(sale_price) as totalSales,
                COUNT(sale_price) as saleCount,
                COUNT(DISTINCT customer_id) as customerCount
            FROM dispensary_sales s
            INNER JOIN users u ON s.user_id=u.id
            WHERE s.location_id=? AND s.status IN ('settled','refunded')
                    AND s.settled_at BETWEEN ? AND ?
            GROUP BY u.id,u.name
            ORDER BY u.name
            ",[$location,$start,$end]);
    }
}