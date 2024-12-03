<?php

namespace App\Jobs\Metrc\Strain;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Services\Metrc\MetrcRequestService;
use Exception;

class MetrcStrainUpdate extends MetrcBaseJob
{
    /**
     * Execute the job.
     */
    public function handle(MetrcRequestService $api)
    {
        if (!$this->user->location || !$this->user->location->licensenum) {
            throw new Exception('User with ID '.$this->user->id.' does not have a location licensenum.');
        }

        if (!$this->test_mode) {
            $jsonItems = [];
            foreach($this->items as $item) {
                $jsonItems[] = [
                    'Id' => $item->metrc_id,
                    'Name' => $item->name,
                    'TestingStatus' => $item->testing_status,
                    'ThcLevel' => $item->thc_level,
                    'CbdLevel' => $item->cbd_level,
                    'IndicaPercentage' => $item->indica_percentage,
                    'SativaPercentage' => $item->sativa_percentage
                ];
            }

            $http_code = $api->updateStrains($this->user, $jsonItems);

            foreach($this->items as &$item) {
                $item->metrc_update_transaction_id = $api->metrcTransactionId;
            }

            if ($http_code !== 200) {
                foreach($this->items as $item) {
                    $item->metrc_status = 'error';
                    $item->save();
                }
                throw new Exception('Update Strains Not Successful, returned HTTP code '.$http_code);
            }

            $metrc_items = $api->getActiveStrains($this->user);
            if (!$metrc_items || !count($metrc_items)) {
                foreach($this->items as $item) {
                    $item->metrc_status = 'error';
                    $item->save();
                }
                throw new Exception('No active strains found in Metrc');
            }

            foreach($this->items as $item) {
                $foundMetrc = false;
                foreach ($metrc_items as $metrc_item) {
                    if (
                        $metrc_item->Id == $item->metrc_id &&
                        $metrc_item->Name == $item->name &&
                        $metrc_item->TestingStatus == $item->testing_status &&
                        $metrc_item->ThcLevel == $item->thc_level &&
                        $metrc_item->CbdLevel == $item->cbd_level &&
                        $metrc_item->IndicaPercentage == $item->indica_percentage &&
                        $metrc_item->SativaPercentage == $item->sativa_percentage
                    ) {

                        $item->metrc_status = 'synced';
                        $item->save();
                        $foundMetrc = true;
                        break;
                    }
                }
               
                if (!$foundMetrc) {
                    $item->metrc_status = 'error';
                    $item->save();
                    throw new Exception('Update Strains Not Successful');
                }
            }
        }
        // test mode - no interaction with Metrc
        else {
            foreach($this->items as $item) {
                $item->metrc_status = 'synced';
                $item->save();
            }
        }

        return $this->items;
    }
}
