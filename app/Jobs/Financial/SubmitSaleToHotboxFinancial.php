<?php

namespace App\Jobs\Financial;

use App\Models\Dispensary\Sale;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SubmitSaleToHotboxFinancial implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sale;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = [
            'batch' =>
                [
                    'type_id' => '1',
                    'description' => 'batch',
                ],
            'header' =>
                [
                    'currency_rate' => '1',
                    "description" => $this->sale->order_number,
                    'currency_id' => data_get($this->sale, 'location.financial_settings.currency_id'),
                    'company_id' => data_get($this->sale, 'location.financial_settings.company_id'),
                    'location_id' => data_get($this->sale, 'location.financial_settings.location_id'),
                    'header_date' => Carbon::now()->format('m/d/Y')
                ],
            'detail' =>
                [
                    0 =>
                        [
                            'quantity' => 1,
                            'account_id' => data_get($this->sale, 'location.financial_settings.undeposited_funds_account_id'),
                            'debit_credit' => $this->sale->sale_price,
                            'dc' => '1',
                        ],
                    1 =>
                        [
                            'quantity' => 1,
                            'debit_credit' => $this->sale->price * -1,
                            'account_id' => data_get($this->sale, 'location.financial_settings.sales_account_id'),
                            'dc' => '-1',
                        ],
                    2 =>
                        [
                            'quantity' => 1,
                            'debit_credit' => $this->sale->tax * -1,
                            'account_id' => data_get($this->sale, 'location.financial_settings.sales_tax_account_id'),
                            'dc' => '-1',
                        ],
                    3 =>
                        [
                            'quantity' => 1,
                            'debit_credit' => $this->sale->discount,
                            'account_id' => data_get($this->sale, 'location.financial_settings.discount_account_id'),
                            'dc' => '1',
                        ],
                ],
        ];



        $client = new Client();

        $request = new Request('POST', data_get($this->sale, 'location.financial_settings.base_url').'/services/API/entry',
            [
                'Authorization' => 'Bearer '.data_get($this->sale, 'location.financial_settings.api_key'),
                'Content-Type' => 'application/json',
                'Accept' => '*/*'
            ],

            json_encode($data)
        );


        $client->send($request);

    }
}
