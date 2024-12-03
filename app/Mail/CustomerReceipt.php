<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Auth\User;
use App\Models\Dispensary\Sale;


class CustomerReceipt extends Mailable
{
    
    use Queueable, SerializesModels;

    protected $saleOrder;
    protected $pdf;
    protected $name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Sale $saleOrder, $pdf, $name){
        $this->saleOrder = $saleOrder;
        $this->pdf = $pdf;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(){

        $first_name = (isset($this->saleOrder->customer->first_name)) ? $this->saleOrder->customer->first_name : $this->saleOrder->customer->alias;

        return $this->view('email.tocustomer')->attachData($this->pdf, $this->name, [
            'mime' => 'application/pdf',
            ])
            ->subject('Hi '.$first_name.' here is your receipt.')
            ->onQueue('email')
            ->with([
                'saleOrder'      => $this->saleOrder,
                'mes'       => 'Your receipt from '.$this->saleOrder->location->name.' is attached.',
                'title' => $this->saleOrder->location->name
            ]);
            
    }

    /* the queing tag for */
    public function tags(){
        return ['CustomerReceipt - '.$this->saleOrder->customer->address->email];
    }
    
}
