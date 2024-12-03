<?php

namespace App\Mail;

use App\Models\Dispensary\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomerRegistered extends Mailable
{
    use Queueable, SerializesModels;

    protected $customer;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.customer-registered')
            ->subject('Welcome to Hotbox ERP')
            ->onQueue('email')
            ->with([
                'customer' => $this->customer
            ]);
    }
}
