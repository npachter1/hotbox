<?php

namespace App\Observers;

use App\Jobs\Financial\SubmitSaleToHotboxFinancial;
use App\Models\Dispensary\Sale;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class SaleObserver
{
    /**
     * Handle the sale "created" event.
     *
     * @param  \App\Sale  $sale
     * @return void
     */
    public function created(Sale $sale)
    {
        if($sale->status == 'refunded') {
            if(data_get($sale, 'location.financial_settings.financial_enabled')) {
                SubmitSaleToHotboxFinancial::dispatch($sale);
            }
        }
    }

    /**
     * Handle the sale "updated" event.
     *
     * @param  \App\Sale  $sale
     * @return void
     */
    public function updated(Sale $sale)
    {
        if($sale->isDirty('status')){
           if($sale->status == 'settled' && $sale->getOriginal('status') == 'pending') {
                if(data_get($sale, 'location.financial_settings.financial_enabled')) {
                    SubmitSaleToHotboxFinancial::dispatch($sale);
                }
            }
        }
    }

    /**
     * Handle the sale "deleted" event.
     *
     * @param  \App\Sale  $sale
     * @return void
     */
    public function deleted(Sale $sale)
    {
        //
    }

    /**
     * Handle the sale "restored" event.
     *
     * @param  \App\Sale  $sale
     * @return void
     */
    public function restored(Sale $sale)
    {
        //
    }

    /**
     * Handle the sale "force deleted" event.
     *
     * @param  \App\Sale  $sale
     * @return void
     */
    public function forceDeleted(Sale $sale)
    {
        //
    }
}
