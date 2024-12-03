<?php

namespace App\Jobs\Metrc\Plant;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Models\METRC\MetrcPlantApi;
use App\Models\Grow\Plant;
use App\Models\Grow\Room;
use Exception;

class MetrcPlantChangeGrowthPhase extends MetrcBaseJob
{
    protected $growthPhase;
    protected $roomId;
    protected $growthDate;
    protected $plantIds;

    public function __construct($items, $user, $growthPhase, $roomId, $growthDate, $plantIds)
    {
        $this->growthPhase = $growthPhase;
        $this->roomId = $roomId;
        $this->growthDate = $growthDate;
        $this->plantIds = $plantIds;
        parent::__construct($items, $user);
    }
    /**
     * Execute the job.
     */
    public function handle(MetrcPlantApi $api)
    {
        if (!$this->user->location || !$this->user->location->licensenum) {
            throw new Exception('User with ID '.$this->user->id.' does not have a location licensenum.');
        }
        
        $room = Room::where('id', $this->roomId)->first();

        if (!$this->test_mode) {
            $jsonItems = [];
            foreach($this->plantIds as $id) {
                $plant = Plant::find($id);
                $jsonItems[] = [
                    'Id' => $plant->metrc_id,
                    'Label' => null,
                    'NewTag' => null,  // no new tag option, client prefers to destroy plant & start over if plant created without tag
                    'GrowthPhase' => $this->growthPhase,
                    'NewRoom' => $room->name,
                    'GrowthDate' => $this->growthDate
                ];
            }

            $http_code = $api->changeGrowthPhases($this->user, $jsonItems);

            if ($http_code !== 200) {
                foreach($this->plantIds as $plant_id) {
                    $plant = Plant::find($plant_id);
                    $plant->metrc_status = 'error';
                    $plant->save();
                }
                if (is_array($http_code))
                    throw new Exception($http_code[0]->message);
                else
                    throw new Exception($http_code->Message);
            }

            foreach($this->plantIds as $id) {
                $plant = Plant::find($id);
                $metrc_plant = $api->getPlants($this->user, $plant->metrc_id);
                if ($metrc_plant->GrowthPhase == $this->growthPhase) {
                    $plant->metrc_status = 'synced';
                    $plant->save();
                }
                else {
                    throw new Exception('Update Plants Not Successful');
                }
            }
        }
        // test mode - no interaction with Metrc
        else {
            foreach($this->plantIds as $id) {
                $plant = Plant::find($id);
                $plant->metrc_status = 'synced';
                $plant->save();
            }
        }

        $all_plants = Plant::all();
        return $all_plants;
    }
}
