<?php

namespace App\Jobs\Metrc\Harvest;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Models\METRC\MetrcHarvestApi;
use App\Models\Grow\Harvest;
use Exception;

class MetrcHarvestUnfinish extends MetrcBaseJob
{
    /**
     * Execute the job.
     */
    public function handle(MetrcHarvestApi $api)
    {
        if (!$this->user->location || !$this->user->location->licensenum) {
            throw new Exception('User with ID '.$this->user->id.' does not have a location licensenum.');
        }

        if (!$this->test_mode) {
            $jsonItems = [];
            foreach($this->items as $harvest) {
                $jsonItems[] = [
                    'Id' => $harvest->metrc_id
                ];
            }

            $http_code = $api->unfinishHarvest($this->user, $jsonItems);

            foreach($this->items as $item) {
                $item->metrc_update_transaction_id = $api->metrcTransactionId;
            }

            if ($http_code !== 200) {
                foreach($this->items as $item) {
                    $item->metrc_status = 'error';
                    $item->save();
                }
                throw new Exception('Update Harvests Not Successful, returned HTTP code '.$http_code);
            }
            // check that the harvest now has NO FinishedDate in Metrc
            foreach($this->items as $harvest) {
                $metrc_item = $api->getHarvests($this->user, $harvest->metrc_id);
                if ($metrc_item->FinishedDate) {
                    foreach($this->items as $item) {
                        $item->metrc_status = 'error';
                        $item->save();
                    }
                    throw new Exception('Harvest Not Unfinished Successfully');
                }
            }
        }
        foreach($this->items as $item) {
            $item->metrc_status = 'synced';
            $item->save();
        }

        return $this->items;
    }
}
