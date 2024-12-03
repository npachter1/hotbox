<?php

namespace App\Jobs\Metrc\Plant;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Models\METRC\MetrcPlantApi;
use App\Models\Grow\Plant;
use App\Models\Grow\Room;
use Carbon\Carbon;
use Exception;

/**
 * Class MetrcPlantMove
 * @package App\Jobs\Metrc\Plant
 *
 * POST /plants/v1/create/plantings?licenseNumber=123-ABC
 * [
 * {
 * "PlantLabel": "ABCDEF012345670000010011",
 * "PlantBatchName": "Demo Plant Batch 1",
 * "PlantBatchType": "Clone",
 * "PlantCount": 3,
 * "RoomName": null,
 * "StrainName": "Spring Hill Kush",
 * "PatientLicenseNumber": "X00001",
 * "ActualDate": "2016-10-18T13:11:03Z"
 * },
 * {
 * "PlantLabel": "ABCDEF012345670000010012",
 * "PlantBatchName": "Demo Plant Batch 2",
 * "PlantBatchType": "Seed",
 * "PlantCount": 2,
 * "RoomName": null,
 * "StrainName": "Spring Hill Kush",
 * "PatientLicenseNumber": "X00002",
 * "ActualDate": "2016-10-18T13:11:03Z"
 * }
 * ]
 */
class MetrcPlantMove extends MetrcBaseJob
{
    /**
     * Execute the job.
     */
    public function handle(MetrcPlantApi $api)
    {
        if (!$this->user->location || !$this->user->location->licensenum) {
            throw new Exception('User with ID '.$this->user->id.' does not have a location licensenum.');
        }
        
        if (!$this->test_mode) {
            $jsonItems = [];
            $this->items->load('plant', 'toRoom');
            foreach($this->items as $item) {
                $jsonItems[] =
                    [
                        'Id' => $item->plant->metrc_id,
                        'Room' => $item->toRoom->name,
                        'ActualDate' => $api->getMetrcDateString(Carbon::parse($item->moved_at))
                    ];
            }

            $http_code = $api->movePlants($this->user, $jsonItems);

            foreach($this->items as $item) {
                $item->metrc_transaction_id = $api->metrcTransactionId;
            }

            if ($http_code !== 200) {
                foreach($this->items as $item) {
                    $item->metrc_status = 'error';
                    $item->save();
                    $item->plant->metrc_status = 'error';
                    $item->plant->save();
                }
                if (is_array($http_code))
                    throw new Exception($http_code[0]->message);
                else
                    throw new Exception($http_code->Message);
            }

            // Do we need to double check that the plants were actually moved?
            // For now, assume that the return code of 200 means we were successful (as it should be).
            foreach($this->items as $item) {
                $item->metrc_status = 'synced';
                $item->save();
                $item->plant->room_id = $item->to_room_id;
                $item->plant->metrc_status = 'synced';
                $item->plant->save();
            }
        }
        // test mode - no interaction with Metrc
        else {
            foreach($this->items as $item) {
                $item->metrc_status = 'synced';
                $item->save();
                $item->plant->room_id = $item->to_room_id;
                $item->plant->metrc_status = 'synced';
                $item->plant->save();
            }
        }

        return $this->items;
    }
}
