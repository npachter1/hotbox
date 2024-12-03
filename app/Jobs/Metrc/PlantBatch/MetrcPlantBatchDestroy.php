<?php

namespace App\Jobs\Metrc\PlantBatch;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Services\Metrc\MetrcRequestService;
use App\User;
use Exception;
use Carbon\Carbon;


/**
 * Class MetrcPlantBatchDestroy
 * @package App\Jobs\Metrc\PlantBatch
 *
 * POST /plantbatches/v1/destroy?licenseNumber=123-ABC
 *       [
 *       {
 *       "PlantBatch": "AK-47 Clone 1/31/2017",
 *       "Count": 25,
 *       "ReasonNote": "",
 *       "ActualDate": "2015-12-15"
 *       },
 *       {
 *       "PlantBatch": "AK-47 Clone 1/31/2017",
 *       "Count": 10,
 *       "ReasonNote": "McQueen ran over the plants. Poor little plants. =(",
 *       "ActualDate": "2015-12-15"
 *       }
 *       ]
 */
class MetrcPlantBatchDestroy extends MetrcBaseJob {

    protected $destroyDate; // The date of the destroy
    protected $destroyReason; // The reason of the destroy
    protected $destroyCount; // The number of immature plants to destroy

    public function __construct($items, $user, $destroyCount, $destroyDate, $destroyReason)
    {
        $this->destroyCount = $destroyCount;
        $this->destroyDate = $destroyDate;
        $this->destroyReason = $destroyReason;
        parent::__construct($items, $user);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(MetrcRequestService $api)
    {
        if (!$this->user->location || !$this->user->location->licensenum) {
            throw new Exception('User with ID '.$this->user->id.' does not have a location licensenum.');
        }
        
        if (!$this->test_mode) {
            $jsonItems = [];
            $this->items->load('room');
            foreach($this->items as $item) {
                $jsonItems[] =
                    [
                        'PlantBatch' => $item->name,
                        'Count' => $this->destroyCount,
                        'ReasonNote' => $this->destroyReason,
                        'ActualDate' => Carbon::parse($this->destroyDate)->toIso8601String()
                    ];
            }

            $http_code = $api->destroyPlantBatch($this->user, $jsonItems);

            if ($http_code !== 200) {
                foreach($this->items as $item) {
                    $item->metrc_status = 'error';
                    $item->save();
                }
                throw new Exception('Destroy Plant Batch Not Successful, returned HTTP code '.$http_code);
            }

            foreach($this->items as $item) {
                $foundMetrc = false;
                $metrc_item = $api->getPlantBatches($this->user,$item->metrc_id);
                if ($metrc_item->Name === $item->name) {
                    if ($metrc_item->DestroyedCount !== $item->destroyed_count) {
                        // For some reason, Metrc is out of sync with Hotbox
                        $item->metrc_status = 'error';
                    }elseif($metrc_item->Count !== $item->count) {
                        // For some reason, Metrc is out of sync with Hotbox
                        $item->metrc_status = 'error';
                    }
                    else {
                        $item->metrc_status = 'synced';
                    }
                    $item->save();
                    $foundMetrc = true;
                }
                if (!$foundMetrc) {
                    $item->metrc_status = 'error';
                    $item->save();
                    throw new Exception('Destroy Plant Batch Not Successful');
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
