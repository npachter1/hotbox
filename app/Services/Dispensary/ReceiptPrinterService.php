<?php

namespace App\Services\Dispensary;

use App\Services\BaseService;
use Illuminate\Support\Arr;
use App\Models\Dispensary\Sale;
use App\Models\Dispensary\SaleItem;
use App\Models\Dispensary\SalePayment;
use App\Models\Dispensary\Inventory;

use App\Services\Dispensary\SaleService;

use Mike42\Escpos;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use App\Helpers\ReceiptToHTML;
use App\Mail\CustomerReceipt;

use Auth;
use Exception;
use Carbon\Carbon;
use Mail;
use PDF;



class ReceiptPrinterService extends BaseService
{

    public function generateReceipt(array $data,$id) {

        $saleOrder = Sale::query()
        ->with('items.inventory.product.category.type','items.inventory.pricing','payments','customer.address','user','drawer','discounts','location.address')
        ->where('id',$id)
        ->first();

        $taxes = $this->getTaxes($saleOrder);

        /* Information for the receipt */
        $saleOrderItems = $saleOrder->items;
        $saleOrderItems = $saleOrderItems->sortBy('inventory.product.category.type.name');
        $items = array();
        $lastCategory = "";
        foreach ($saleOrderItems as $orderitem) {
            if($orderitem->inventory->unit_of_measure === "ea")
            {
                $title = number_format((float)$orderitem->quantity, 0, '', '')." (".$orderitem->inventory->unit_of_measure.") ".$orderitem->inventory->product->name." ";
            
            }
            else
            {
                $title = number_format((float)$orderitem->quantity, 2, '.', '')." (".$orderitem->inventory->unit_of_measure.") ".$orderitem->inventory->product->name." ";
            
            }
            if($lastCategory !== $orderitem->inventory->product->category->name)
            {
                $item = new item(strtoupper($orderitem->inventory->product->category->name),'', false, true);
                $items[] = $item;
                $lastCategory = $orderitem->inventory->product->category->name;
            }

            $title = explode( "\n", wordwrap( $title, 38));
            $itemTitle = reset($title);
            array_shift($title);
            $item = new item($itemTitle, number_format((float)$orderitem->price, 2, '.', ''));
            $items[] = $item;
            if(sizeof($title))
            {
                $title = implode("\n",$title);
                $items[] = $title."\n";
            }
            if($orderitem->thc_equivalent_grams > 0)
            {
                $item = new item("Equivalent to ".number_format((float)$orderitem->thc_equivalent_grams, 2, '.', '')."g THC");
                $items[] = $item;
            }
            $item = "\n";
            $items[] = $item;
        }
        $payments = $saleOrder->payments;
        $paymentItems = array();
        $paymentTotal = 0;
        foreach($payments as $pay)
        {
            $paymentTotal += $pay->amount;
            $payment = new item(ucwords($pay->payment_method), number_format((float)$pay->amount, 2, '.', ''));
            $paymentItems[] = $payment;
            if($pay->payment_method === "credit")
            {
                //TODO: needs real approval code ...
                $dummyCode = random_int(1000000000, 99999999999);
                $payment = new item("  FSwipe"."\n"."  APPROVED ".$dummyCode);
                $paymentItems[] = $payment;
            }
        }
        $subtotal = new item('Subtotal', number_format((float)$saleOrder->price, 2, '.', ''));
        $tax = new item('Tax', number_format((float)$saleOrder->tax, 2, '.', ''));
        $total = new item('Total', number_format((float)$saleOrder->price + $saleOrder->tax - $saleOrder->discount, 2, '.', ''), true);
        $changeDue = ($saleOrder->price + $saleOrder->tax - $saleOrder->discount - $paymentTotal) * -1;
        /* Fill in your own connector here */
        $connector = new DummyPrintConnector();
        $logo = EscposImage::load(base_path()."/resources/images/logo.png", false);
        /* Start the printer */
        $printer = new Printer($connector);
        /* Print top logo */
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> graphics($logo);
        /* Name of shop */
        // $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> feed();
        $printer -> selectPrintMode(Printer::MODE_EMPHASIZED);
        $printer -> text($saleOrder->location->name."\n");
        $printer -> text("License Number: ".$saleOrder->location->licensenum."\n");
        $printer -> selectPrintMode();
        $printer -> text($saleOrder->location->address->address1."\n");
        $printer -> text($saleOrder->location->address->city.",".$saleOrder->location->address->region." ".$saleOrder->location->address->zip."\n");
        $printer -> text($saleOrder->location->address->phone."\n");
        $printer -> feed();
         /* Cashier and Customer */
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> text("Cashier: ".$saleOrder->user->name."\n");
        /* Date is kept the same for testing */
        // $date = date('l jS \of F Y h:i:s A');
        // $date = $saleOrder->updated_at;
        $date = $saleOrder->updated_at->timezone(data_get($saleOrder->location,'settings.communication_timezone','UTC'))->format('m/d/y g:i:s a T');
        $printer -> text($date . "\n");
        $printer -> feed();
        if($saleOrder->customer)
        {
            $customerName = $saleOrder->customer->first_name.$saleOrder->customer->last_name;
            $customerName = $customerName ? $saleOrder->customer->first_name." ".$saleOrder->customer->last_name : $saleOrder->customer_temp_uid ;
            $printer -> text("Customer: ".$customerName."\n");
            $printer -> feed();
        }
        $printer -> text("Order ");
        $printer -> setEmphasis(true);
        $printer -> text($saleOrder->order_number."\n");
        $printer -> text(new item('', '$'));
        $printer -> setEmphasis(false);
        /* Items */
        foreach ($items as $item) {
    
            if(!is_string($item))
            {
                if($item->emphasize())
                    $printer -> setEmphasis(true); 
            }
            $printer -> text($item);
            $printer -> setEmphasis(false); 
        }
        $printer -> setEmphasis(true);
        $printer -> text($subtotal);
        $printer -> setEmphasis(false);
        $printer -> feed();
        /* Tax and total and discount and payments and change */
        $taxes = Arr::sort($taxes, function($tax_item)
        {
            return $tax_item->getName();
        });
        $last_level = '';
        $taxes_aggr = [];
        foreach($taxes as $tax_item)
        {
            if($tax_item->getName() !== $last_level)
            {
                $taxes_aggr[$tax_item->getName()] = $tax_item;
            }
            else
            {
                $taxes_aggr[$tax_item->getName()]->addAmount($tax_item->getAmount());
            }
            $last_level = $tax_item->getName();

        }
        $printer -> text($tax);
        foreach($taxes_aggr as $tax_item)
        {
            $printer -> text($tax_item->getTaxLine());           
        }

        if($saleOrder->discount > 0)
        {
            $discountCode = "";
            if($saleOrder->discount_code)
            {
                $discountCode = " (".$saleOrder->discount_code.")";
            }
            $discount = new item("Discount".$discountCode ,number_format((float)$saleOrder->discount * -1, 2, '.', ''));
            $printer -> text($discount);
            $discounts = $saleOrder->discounts;
            foreach($discounts as $discountItem)
            {
                if($discountItem->applied->is_active)
                {
                    $descriptor = implode("\n ",explode( "\n", wordwrap( $discountItem->descriptor, 37)));
                    $printer -> text(" ".$descriptor."\n");
                }
            }
            $printer -> feed();
        }
        // $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> setEmphasis(true);
        $printer -> text($total);
        $printer -> setEmphasis(false);
        // $printer -> selectPrintMode();
        $formattedOunces = $this->getFormattedOuncesFromGrams((float)$saleOrder->thc_equivalent_grams,true);
        $totalTHC = "Total THC Equivalent ".number_format((float)$saleOrder->thc_equivalent_grams, 2, '.', '')."g ";
        $printer -> text($totalTHC);
        $printer -> setFont(Printer::FONT_B);
        $printer -> text($formattedOunces."\n");
        $printer -> feed(2);
        $printer -> setFont(Printer::FONT_A);
        /* payments and change */
        // $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> setEmphasis(true);
        $printer -> text("Tendered"."\n");
        // $printer -> selectPrintMode();
        $printer -> setEmphasis(false);
        foreach ($paymentItems as $item) {
            $printer -> text($item);
        }
        $printer -> setEmphasis(true);
        // $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $change = new item("Change Due",number_format((float)$changeDue, 2, '.', ''),true);
        $printer -> text($change);
        // $printer -> selectPrintMode();
        $printer -> setEmphasis(false);
        /* Footer */
        $printer -> feed(2);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("Thank you for shopping with ".$saleOrder->location->name."\n");
        $printer -> text("Please retain your receipt"."\n");
        $printer -> text("For more info, please visit ".$saleOrder->location->address->website."\n");
        $printer -> feed();
        $printer -> setBarcodeHeight(80);
        $printer -> setBarcodeWidth(2);
        $printer -> setBarcodeTextPosition(Printer::BARCODE_TEXT_BELOW);
        $printer -> barcode($saleOrder->order_number);
        $printer -> feed(2);
        /* Cut the receipt and open the cash drawer */
        $printer -> cut();
        $printer -> pulse();
        // Get the data out as a string before closing it
        $receiptFile = $connector -> getData();
        $printer -> close();

        // NOTE: use this method if you are using the FilePrintConnector after you close the printer
        // $receiptFile = file_get_contents($dest);

        $returnData = new receipt($receiptFile, $changeDue);
        
        return $returnData;
    }

    public function generateReceiptHTML(array $data,$id)
    {
        $returnData = $this->generateReceipt($data,$id);
        $receiptFile = $returnData->file();
        $temp = tmpfile();
        fwrite($temp, $receiptFile);
        $path = stream_get_meta_data($temp)['uri'];
        $receiptHTML = ReceiptToHTML::render($path);
        fclose($temp);
        return $receiptHTML;
    }

    public function generateReceiptEmail(array $data,$id)
    {
        $saleOrder = Sale::query()
        ->with('items.inventory.product.category.type','items.inventory.pricing','payments','customer.address','user','drawer','discounts','location.address')
        ->where('id',$id)
        ->first();

        $receiptHTML = $this->generateReceiptHTML($data,$id);
        
        $name = Carbon::now()->timestamp.'_receipt.pdf';
        
        $pdf = PDF::loadHTML($receiptHTML);

        $email_address = $saleOrder->customer->address->email;
        if($email_address == null)abort(422,'No email address found for the customer');
        
        Mail::to($email_address)
        ->send(new CustomerReceipt($saleOrder, $pdf->output(), $name));

        return $saleOrder;

    }

    public function exportPdf($data,$id,$file){

        $receiptHTML = $this->generateReceiptHTML($data,$id);
        
        $name = Carbon::now()->timestamp.'_receipt.pdf';
        
        $pdf = PDF::loadHTML($receiptHTML);
        
        return $this->export($pdf->output(),$file,$name,'pdf',[]);

    }

    public function getTaxes($data) {
        $_tax_rates = [];
        $items = data_get($data,'items',null);
        foreach($items as &$item){
            if(($inv = Inventory::with('product.category.type','taxes.rates')->find($item->inventory_id)) == null) throw new Exception('an item in this order cannot be found in inventory');
            $item->data = $inv; 

            if(($tax = data_get($item->data,'taxes',collect([]))->firstWhere('nature_type',data_get($item->data,'product.nature_type','noncannabis')))!=null){                                 // if inventory.tax group has rates..
                foreach($tax->rates as $rate){
                    $_tax_rates[] = new item_tax($rate->tax_rate_level, $rate->name, $rate->state_code, $rate->county_name, $rate->city_name,(float)$rate->rate_percent, ($item->price - $item->discount)*((float)$rate->rate_percent/100));
                }
            }
        }
        return $_tax_rates;
    }

    protected static function getFormattedOuncesFromGrams ($grams, $withParens) {

        //TODO: Fix this so that less than a whole ounce will be returned as it's fractional value.
        $ounces = floor($grams / 28);
        $rem = $grams % 28;

        $formatted = '';

        if ($rem >= 24.5) { $formatted = $formatted.' 7/8'; }
        else if ($rem >= 21) { $formatted = $formatted.' 3/4'; }
        else if ($rem >= 17.5) { $formatted = $formatted.' 5/8'; }
        else if ($rem >= 14) { $formatted = $formatted.' 1/2'; }
        else if ($rem >= 10.5) { $formatted = $formatted.' 3/8'; }
        else if ($rem >= 7) { $formatted = $formatted.' 1/4'; }
        else if ($rem >= 3.5) { $formatted = $formatted.' 1/8'; }

        if (!$ounces && !$formatted) {
          return '';
        }

        // if (ounces > 1 || (ounces === 1 && formatted)) { formatted += ' ounces'; }
        // else { formatted += ' ounce'; }
        $formatted = $formatted.' oz';

        if ($ounces > 0) { $formatted = $ounces.$formatted; }

        if ($withParens) { $formatted = '('.trim($formatted).')'; }

        return $formatted;
      }


}

class receipt
{
    private $file;
    private $changedue;

    public function __construct($file, $changedue)
    {
        $this -> file = $file;
        $this -> changedue = $changedue;
    }

    public function file (){
        return $this->file;
    }

    public function changedue (){
        return $this->changedue;
    }

}

/* A wrapper to do organize item names & prices into columns */
class item
{
    private $name;
    private $price;
    private $dollarSign;
    private $emphasize;
    public function __construct($name = '', $price = '', $dollarSign = false, $emphasize = false)
    {
        $this -> name = $name;
        $this -> price = $price;
        $this -> dollarSign = $dollarSign;
        $this -> emphasize = $emphasize;
    }
    
    public function __toString()
    {
        $rightCols = 10;
        $leftCols = 38;
        if ($this -> dollarSign) {
            // $leftCols = $leftCols / 2 - $rightCols / 2;
        }
        $left = str_pad($this -> name, $leftCols) ;
        
        $sign = ($this -> dollarSign ? '$ ' : '');
        $right = str_pad($sign . $this -> price, $rightCols, ' ', STR_PAD_LEFT);
        return "$left$right\n";
    }

    public function emphasize()
    {
        return $this->emphasize;
    }
}

class item_tax
{
    private $name;
    private $rate;
    private $amount;
    private $county_name;
    private $city_name;
    private $state_name;
    private $tax_name;

    public function __construct($name = '', $tax_name = '', $state_name = '', $county_name = '', $city_name = '', $rate = 0, $amount = 0)
    {
        $this -> name = $name;
        $this -> state_name = $state_name;
        $this -> county_name = $county_name;
        $this -> city_name = $city_name;
        $this -> tax_name = $tax_name;
        $this -> rate = $rate;
        $this -> amount = $amount;
    }

    public function getTaxLine()
    {
        if($this->name === "state")
            return new item(" ".$this->state_name." ".ucwords($this->name)." ".$this->rate."%");
        if($this->name === "county")
            return new item(" ".$this->county_name." ".ucwords($this->name)." ".$this->rate."%");
        if($this->name === "city")
            return new item(" ".$this->city_name." ".ucwords($this->name)." ".$this->rate."%");
        if($this->name === "local")
            return new item(" ".$this->tax_name." ".$this->rate."%");
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRate()
    {
        return $this->rate;
    }

    public function getAmount()
    {
        return number_format((float)$this->amount, 2, '.', '');
    }

    public function addAmount($amount)
    {
        return $this->amount += $amount;
    }
}
