<?php

namespace App\Jobs\Metrc\Plant;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Models\METRC\MetrcPlantApi;
use App\Models\Grow\Plant;
use Exception;

class MetrcPlantDestroy extends MetrcBaseJob
{
    protected $reasonNote;
    protected $destroyDate;
    protected $plantIds;

    public function __construct($items, $user, $reasonNote, $destroyDate, $plantIds)
    {
        $this->reasonNote = $reasonNote;
        $this->destroyDate = $destroyDate;
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

        if (!$this->test_mode) {
            $jsonItems = [];
            foreach($this->plantIds as $id) {
                $plant = Plant::find($id);
                $jsonItems[] = [
                    'Id' => $plant->metrc_id,
                    'Label' => $plant->label,
                    'ReasonNote' => $this->reasonNote,
                    'ActualDate' => $this->destroyDate
                ];
            }

            $http_code = $api->destroyPlants($this->user, $jsonItems);

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
                $plant->plant_batch->destroyed_count += 1;
                $plant->plant_batch->count -= 1;
                $plant->plant_batch->save();
                $metrc_plant = $api->getPlants($this->user, $plant->metrc_id);
                if ($metrc_plant->DestroyedNote == $this->reasonNote) {
                    $plant->metrc_status = 'synced';
                    $plant->save();
                    break;
                }
                else {
                    throw new Exception('Destroy Plants Not Successful');
                }
            }
        }
        // test mode - no interaction with Metrc
        else {
            foreach($this->plantIds as $id) {
                $plant = Plant::find($id);
                $plant->plant_batch->destroyed_count += 1;
                $plant->plant_batch->count -=1;
                $plant->plant_batch->save();
                $plant->metrc_status = 'synced';
                $plant->save();
            }
        }

        $all_plants = Plant::all();
        return $all_plants;
    }
}
