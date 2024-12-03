<?php

namespace App\Services\Dispensary;

use App\Services\BaseService;

use App\Http\Resources\Dispensary\SaleCollectionExport;

use App\Models\Dispensary\Customer;
use App\Models\Dispensary\Sale;
use App\Models\Dispensary\SaleItem;
use App\Models\Dispensary\SalePayment;

use App\Services\Dispensary\DrawerService;
use App\Events\CustomerUpdated;
use App\Helpers\LocationScope;


use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use Excel;
use Storage;
use PDF;


class SaleService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;
    protected static $DEFAULT_CUSTOMER_TYPE = 'Rec';


    public function search(array $data){

        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);

        $discountIds = data_get($data,'filter.discount_id',[]);
        $productIds = data_get($data,'filter.product_id',[]);


        $query = Sale::query()
            ->with('items.inventory','customer.address','drawer.user','payments')
            ->ofListFilters($filters)
            ->ofTextFilter($search)
            ->ofAssociationFilter('discounts',$discountIds)
            ->ofAssociationFilter('items.inventory.product',$productIds)
            ->ofActive();

        if(data_get($data,'onlyUnpaid','false')=='true')
            $query->ofAccountUnpaidFilter();                                    // if only unpaid boolean then filter by payments.type=account amount_owed

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('updated_at', 'desc');


        return $query->paginate($limit);

    }


    /* get Sale record */
    public function show(array $data,$id){

        return Sale::query()
            ->withoutGlobalScope(LocationScope::class)
            ->with('items.inventory.product.category','items.inventory.pricing','payments','customer.address','user','drawer','discounts.campaigncodes')
            ->where('id',$id)
            ->first();

    }


    /* create Sale record  */
    public function create($data,$user=null){

        $sale = new sale();
        $sale->customer_id = data_get($data, 'customer_id', null);
        $sale->user_id = $user->id;

        $service = new DrawerService;
        $sale->drawer_id = (($draw = $service->_currentDrawer([],$user))!=null ? $draw->id : null);
        // $sale->cash_drawer_id = array_get($data, 'cash_drawer_id', null);

        $sale->status = 'pending';
        $sale->save();

        // save to get imit data and id
        $sale->_updateWithDiscountAndTaxPricing($data,$user);                   // calculate pricing, discounting, tax, and persist sale with relations

        $sale = $this->show([],$sale->id);                                      // requery sale after calculations and persistance
        return $sale;

    }


    /* update Sale */
    public function update($data,$id,$user=null){

        if(($sale = Sale::find($id)) == null) abort(422,'Couldnt locate sales record');
        elseif(in_array($sale->status,['settled','voided'])) abort(422,'This Order has already been settled');

        // Derek:  Allow a SaleOrder to be "switched" between users.  For example, if user Bob
        // starts a SaleOrder for Customer Sue, then Bob takes a break, user Jane can open the POS
        // and resume the SaleOrder for Customer Sue.  This entails the Sale Order switching user id's.
        // elseif(($user = Auth::guard()->user()) == null) throw new Exception('Not Authenticated');
        // elseif(($order = SaleOrder::where('user_id', $user->id)->where('id', $id)->first()) == null) throw new Exception('SaleOrder not found.');

        // Jesse: this service is passed an authenticated user object, and the Sale modal is scoped by users logged in location
        // If Bob comes back to his terminal with Customer Sue, he will have friction; if sale is still pending, he can switch, or cancel his action.  If sale is settled, we error above..

        $service = new DrawerService;

        $sale->user_id = $user->id;                                             // Sale user id is always that of the current user
        $sale->drawer_id = (($draw = $service->_currentDrawer([],$user))!=null ? $draw->id : null);

        $sale->_updateWithDiscountAndTaxPricing($data,$user);                   // calculate pricing, discounting, and persist SaleOrder with relations

        $sale = $this->show([],$id);                                            // requery sale after calculations and persistance
        return $sale;

    }

    public function payAccount($data,$id,$user=null){

        if(($payment = SalePayment::find($id)) == null) abort(422,'Whoops, no payment record found - aborting.');
        elseif(($amt = (float)data_get($data,'amount',0))==0) abort(412,'Whoops, no amount to post - aborting.');

        $payment->amount_owed = ($payment->amount_owed - $amt);
        $payment->save();

        return [
            'status'    => 200,
            'message'   => 'A Payment of $'.number_format($amt,2).' has been applied to the account',
            'schema'    => Customer::_getSchema()                               // include schema..
        ];

    }


    /* process an export */
    public function exportCollection($data,$typ,$file){

        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Sale_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all')
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;


        return $this->export(new SaleCollectionExport($data),$file,$name,$typ,[]);

    }


    public function exportPdf($data,$id,$file){

        $name = Carbon::now()->timestamp.'_receipt.pdf';
        //data_set($data,'foo','bar',true);

        $pdf = PDF::loadView('print.test', $data);
        //$pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
        // https://github.com/barryvdh/laravel-dompdf


        return $this->export($pdf->output(),$file,$name,'pdf',[]);

    }


    /* Removal service */
    public function archive($data,$id,$user){

        abort(422,'Not able to destroy a sales record');

    }




    /**                                             **/
	/**            Sales Service Actions            **/
	/**                                             **/

    public function _addSaleOrderItems($order, $items) {                    // Derek, had to change to public for SaleOrder model to call, and data_get cause the $items could be rows of objects now.

        $user = Auth::guard('api')->user();

        if($items){

            foreach($items as $item) {
                if(is_object($item)) $item = (array)$item;
                if (data_get($item, 'inventory_id',null)!=null){
                    $orderItem = new SaleItem();
                    $orderItem->sale_id = $order->id;
                    $orderItem->inventory_id = data_get($item,'inventory_id',null);
                    $orderItem->quantity = data_get($item, 'quantity', 0);
                    $orderItem->tax_id = data_get($item, 'tax_id', null);
                    $orderItem->group_ref = data_get($item, 'group_ref', null);
                    $orderItem->price = data_get($item, 'price', 0);
                    $orderItem->discount = data_get($item, 'discount', 0);
                    $orderItem->tax = data_get($item, 'tax', 0);
                    $orderItem->sale_price = data_get($item, 'sale_price', 0);
                    $orderItem->thc_equivalent_grams = data_get($item, 'thc_equivalent_grams', 0);
                    $orderItem->quantity_priced_at = data_get($item, 'quantity_priced_at', 0);
                    $orderItem->is_confirmed = (data_get($user->location,'settings.use_confirm',false)===true ? data_get($item, 'is_confirmed', false) : true);
                    $orderItem->discount_rule_ids = join(',',data_get($item, 'applied_discount_rule_ids', []));
                    $orderItem->save();
                }
            }
        }
    }


    public function storeOrderPayments(array $data, $id) {
        // Add the payments
        // TODO: refactor, possibly add unique ID for gift cards
        // TODO: Should the sale order be 'settled' after payment?
        // TODO: Should we check if the payment is enough to cover the entire sale order cost?
        // Or perhaps throw an Exception?
        $payment_cash = data_get($data, 'cash', 0);
        $payment_credit = data_get($data, 'credit', 0);
        $payment_gift = data_get($data, 'gift', 0);
        $payment_account = data_get($data, 'account', 0);

        if (!$payment_cash && !$payment_credit && !$payment_gift && !$payment_account) return null;  // No payments to process

        if ($payment_cash) {
            // create a new payment record of type cash
            $orderPayment = new SalePayment();
            $orderPayment->sale_id = $id;
            $orderPayment->payment_method = 'cash';
            $orderPayment->amount = $payment_cash;
            $orderPayment->save();
        }
        if ($payment_credit) {
            // create a new payment record of type cash
            $orderPayment = new SalePayment();
            $orderPayment->sale_id = $id;
            $orderPayment->payment_method = 'credit';
            $orderPayment->amount = $payment_credit;
            $orderPayment->save();
        }
        if ($payment_gift) {
            // create a new payment record of type cash
            $orderPayment = new SalePayment();
            $orderPayment->sale_id = $id;
            $orderPayment->payment_method = 'gift card';
            $orderPayment->amount = $payment_gift;
            $orderPayment->save();
        }
        if ($payment_account) {
            // create a new payment record of type cash
            $orderPayment = new SalePayment();
            $orderPayment->sale_id = $id;
            $orderPayment->payment_method = 'account';
            $orderPayment->amount = $payment_account;
            $orderPayment->amount_owed = $payment_account;
            $orderPayment->save();
        }


        // Only now is the SaleOrder actually settled
        $order = $this->show([],$id);
        $order->status = 'settled';
        $order->settled_at = Carbon::now()->toDateTimeString();
        $order->save();

        // Add the sale order amounts to the Cash Drawer
        $drawer = $order->drawer;
        $drawer->total_sale_price += $order->sale_price;
        $drawer->total_thc_equivalent_grams += $order->thc_equivalent_grams;
        $drawer->total_sales += 1;
        $drawer->current_balance = ($drawer->current_balance + $payment_cash - (($payment_cash + $payment_credit + $payment_gift + $payment_account) - $order->sale_price)); // cash payment minus change due to customer
        $drawer->save();

        $order->customer()->save($order->customer);                             // save hooked agg attributes for customer upon settling order
        return $order;

    }


    public function voidOrder($data,$id,$user=null){

        if(($order = Sale::find($id)) == null) abort(422,'Could not find the Sale Record to Void');
        elseif($order->status == 'voided') abort(409,'Sale has already been voided.  Aborting.');

        $order->status = 'voided';
        $order->save();

        $sale = $this->show([],$id);                                            // requery sale after calculations and persistance
        return $sale;
    }


    public function returnOrder($data,$id,$user){

        $service = new DrawerService;

        if(($orig = Sale::with('items')->find($id)) == null) abort(422,'Could not find the Sale Record to Modify');
        elseif($orig->status != 'settled') abort(409,'Whoops - We can Only Modify Settled Sales - Aborting.');
        elseif(($amt = data_get($data,'refund_total',0))<=0) abort(409,'Cannont Return/Modify Order with 0 Amount - Aborting.');
        elseif(($drawer = $service->_currentDrawer([],$user))==null) abort(409,'No Drawer is Open for the current user - aborting.');

        $hash = collect((array)data_get($data,'returned_items',[]))->pluck('quantity_returning','id')->toArray();
        $hashRestock = collect((array)data_get($data,'returned_items',[]))->pluck('is_restock','id')->toArray();

        $ritems = $orig->items->map(function($item,$key)use($hash,$hashRestock){
            if(($retrn = data_get($hash,$item->id,0))>0){                       // collect and parse neg. sale price, qty and thc equiv for each item were returning
                $item->sale_price = (($item->price * $retrn) * -1);
                $item->tax = ($item->tax * -1);
                $item->thc_equivalent_grams = ((($item->thc_equivalent_grams/($item->quantity || 1))*$retrn)*-1);
                $item->quantity = (data_get($hashRestock,$item->id,true)===true ? ($retrn * -1) : 0);
                $item->discount = 0;

                return $item;
            }
        })->filter()->values();


        /* create a negative ticket */
        $return = $orig->replicate();
        $return->user_id = $user->id;                                           // reassign user from orig sale for return
        $return->drawer_id = $drawer->id;                                       // also reassign new drawer of current user for this return ticket
        $return->order_number = 'RETURN-'.$return->order_number;                // accession prefix - this is a return of the X ticket..

        $return->sale_price = ($amt*-1);
        $return->tax = (data_get($data,'refund_tax',0)*-1);
        $return->discount = 0;

        $return->thc_equivalent_grams = $ritems->sum('thc_equivalent_grams');
        $return->discount_code = null;
        $return->discount_descriptor = null;

        $return->status = 'refunded';
        $return->settled_at = Carbon::now()->toDateTimeString();
        $return->save();
        $this->_addSaleOrderItems($return,$ritems->toArray());                  // store the returning items


        /* Add reduction to users Drawer (were assuming its a cash for now..) */
        $drawer->total_sale_price += $return->sale_price;
        $drawer->total_thc_equivalent_grams += $return->thc_equivalent_grams;
        $drawer->total_sales += 1; // I guess it still counts as a sale count?
        $drawer->current_balance = ($drawer->current_balance - $amt);           // At moment, all returns are recorded as a cash
        $drawer->save();


        $return->save();                                                        // save return sale again, after (-) items are added to update invnetory / customer / rewards aggregates
        return $return;

    }


    /**
     * Send Customer Updated Event to keep pos customer list updated
     */
    public function customerUpdated($user, $id){

        try{
            $item = Customer::query()
            ->withCount('pendingSales','settledSales')
            ->where('id',$id)
            ->first();

            event(new CustomerUpdated($user, $item->toJson()));

        }catch(\Exception $e){
            //TODO: handle error because the socket service is temporarily down
        }

    }

    /**
     * Get Discount and Tax data into Sale endpoint
     */
    public function getDiscountAndTaxPricing($data,$id){

        if(($user = Auth::guard('api')->user()) == null) abort(403,'You are not authenticated');
        elseif(!$data) abort(422,'Unable to parse data');
        elseif(($sale = Sale::where('user_id', $user->id)->where('id', $id)->first()) == null) abort(409,'Item not found or assigned to your user account.');

        $sale->_updateWithDiscountAndTaxPricing($data);                         // calculate pricing, discounting, and persist SaleOrder with relations

        $sale = $this->show([],$id);                                            // requery sale after calculations and persistance
        return $sale;

    }


}
