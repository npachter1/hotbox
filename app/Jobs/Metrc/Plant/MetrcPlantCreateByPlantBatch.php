<?php

namespace App\Jobs\Metrc\Plant;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Services\Metrc\MetrcRequestService;

use App\Models\Grow\PlantBatch;
use App\Models\Grow\Room;
use App\Models\Grow\Strain;
use App\Models\Grow\Plant;
use Exception;

class MetrcPlantCreateByPlantBatch extends MetrcBaseJob
{
    /**
     * Execute the job.
     */
    public function handle(MetrcRequestService $api)
    {
        if (!$this->user->location || !$this->user->location->licensenum) {
            throw new Exception('User with ID '.$this->user->id.' does not have a location licensenum.');
        }


        $metrcPlants = [];
        foreach (($api->getVegetativePlants($this->user) ?: []) as $plantdata) {
            $metrcPlants[] = $plantdata;
        }
        foreach (($api->getFloweringPlants($this->user) ?: []) as $plantdata) {
            $metrcPlants[] = $plantdata;
        }

        foreach($this->items as $plantBatch) {
            foreach($metrcPlants as $metrcPlant) {
                if($metrcPlant->PlantBatchId === $plantBatch->metrc_id) {
                    //Found a plant in the batch - make sure it doesn't exist yet
                    $foundPlant = Plant::findByMetrcId($metrcPlant->Id);
                    if(!$foundPlant) {
                        //We didn't find the plant (which is correct) so create one
                        $plant = new Plant();
                        $plant->metrc_id = $metrcPlant->Id;
                        $plant->label = $metrcPlant->Label;
                        $plant->growth_phase = $metrcPlant->GrowthPhase;
                        $plant->state = $metrcPlant->State;
                        $plant->plant_batch_id = $plantBatch->id;
                        $plant->location_id = $plantBatch->location_id;
                        $plant->planted_at = $metrcPlant->PlantedDate;
                        $plant->vegetative_at = $metrcPlant->VegetativeDate;
                        $plant->flowering_at = $metrcPlant->FloweringDate;
                        $plant->harvested_at = $metrcPlant->HarvestedDate;
                        $plant->patient_license_number = $metrcPlant->PatientLicenseNumber; //TODO update to ID
                        //This is redundant for each plant, but the overhead is low.
                        $strain = Strain::findByMetrcId($metrcPlant->StrainId);
                        $plant->strain_id = $strain->id;
                        $room = Room::findByMetrcId($metrcPlant->RoomId);
                        $plant->room_id = $room->id;
                        $plant->metrc_status = 'synced';
                        $plant->save();
                    }
                }
            }

        }
        $plantBatchPlants = Plant::where('plant_batch_id','=',$plantBatch->id)->get();
        return $plantBatchPlants;

    }
}
