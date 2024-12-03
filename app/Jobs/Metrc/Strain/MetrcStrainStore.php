<?php

namespace App\Jobs\Metrc\Strain;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Services\Metrc\MetrcRequestService;
use Exception;

class MetrcStrainStore extends MetrcBaseJob
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
                    'Name' => $item->name,
                    'TestingStatus' => $item->testing_status,
                    'ThcLevel' => $item->thc_level,
                    'CbdLevel' => $item->cbd_level,
                    'IndicaPercentage' => $item->indica_percentage,
                    'SativaPercentage' => $item->sativa_percentage

                ];
            }

            $http_code = $api->createStrains($this->user, $jsonItems);

            foreach($this->items as &$item) {
                $item->metrc_create_transaction_id = $api->metrcTransactionId;
            }

            if ($http_code !== 200) {
                foreach($this->items as $item) {
                    $item->metrc_status = 'error';
                    $item->save();
                }
                throw new Exception('Create Strains Not Successful, returned HTTP code '.$http_code);
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
                    if ($metrc_item->Name === $item->name) {
                        $item->metrc_id = $metrc_item->Id;
                        $item->metrc_status = 'synced';
                        $item->save();
                        $foundMetrc = true;
                        break;
                    }
                }
                if (!$foundMetrc) {
                    $item->metrc_status = 'error';
                    $item->save();
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
