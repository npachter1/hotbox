<?php

namespace App\Jobs\Metrc\PlantBatch;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Services\Metrc\MetrcRequestService;
use App\User;
use Exception;


/**
 * Class MetrcPlantBatchMove
 * @package App\Jobs\Metrc\PlantBatch
 *
 * POST /plants/v1/moveplants
 * /plantbatches/v1/moveplantbatches?licenseNumber=123-ABC
 * [
 * {
 * "Name": "AK-47 Clone 1/31/2017",
 * "Room": "Plants Room",
 * "MoveDate": "2015-12-15"
 * },
 * {
 * "Name": "Metrc Bliss 5/30/2018",
 * "Room": "Plants Room",
 * "MoveDate": "2018-01-05"
 * }
 * ]
 */
class MetrcPlantBatchMove extends MetrcBaseJob {

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
            $this->items->load('plantBatch', 'toRoom');
            foreach($this->items as $item) {
                $jsonItems[] =
                    [
                        'Name' => $item->plantBatch->name,
                        'Room' => $item->toRoom->name,
                        'MoveDate' => $item->moved_at
                    ];
            }

            $http_code = $api->movePlantBatches($this->user, $jsonItems);

            foreach($this->items as $item) {
                $item->metrc_transaction_id = $api->metrcTransactionId;
            }

            if ($http_code !== 200) {
                foreach($this->items as $item) {
                    $item->metrc_status = 'error';
                    $item->save();
                    $item->plantBatch->metrc_status = 'error';
                    $item->plantBatch->save();
                }
                throw new Exception('Move Plant Batch Not Successful, returned HTTP code '.$http_code);
            }

            $metrc_items = $api->getActivePlantBatches($this->user);
            if (!$metrc_items || !count($metrc_items)) {
                foreach($this->items as $item) {
                    $item->metrc_status = 'error';
                    $item->save();
                    $item->plantBatch->metrc_status = 'error';
                    $item->plantBatch->save();
                }
                throw new Exception('No active plant batches found in Metrc');
            }

            foreach($this->items as $item) {
                $foundMetrc = false;
                foreach ($metrc_items as $metrc_item) {
                    if ($metrc_item->Name === $item->plantBatch->name) {
                        if ($metrc_item->RoomName !== $item->toRoom->name) {
                            // For some reason, Metrc is out of sync with Hotbox
                            $item->metrc_status = 'error';
                            $item->save();
                            $item->plantBatch->metrc_status = 'error';
                            $item->plantBatch->save();
                        }
                        else {
                            // This is the only success branch.  The PlantBatch has been moved.
                            // Update both the PlantBatchMove and PlantBatch:
                            $item->metrc_status = 'synced';
                            $item->save();
                            $item->plantBatch->room_id = $item->to_room_id;
                            $item->plantBatch->metrc_status = 'synced';
                            $item->plantBatch->save();
                        }
                        $foundMetrc = true;
                        break;
                    }
                }
                if (!$foundMetrc) {
                    $item->metrc_status = 'error';
                    $item->save();
                    $item->plantBatch->metrc_status = 'error';
                    $item->plantBatch->save();
                }
            }
        }
        // test mode - no interaction with Metrc
        else {
            foreach($this->items as $item) {
                $item->metrc_status = 'synced';
                $item->save();
                $item->plantBatch->room_id = $item->to_room_id;
                $item->plantBatch->metrc_status = 'synced';
                $item->plantBatch->save();
            }
        }
        
        return $this->items;
    }
}
