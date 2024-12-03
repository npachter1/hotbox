<?php

namespace App\Jobs\Metrc\Plant;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Models\METRC\MetrcPlantApi;
use App\Models\METRC\MetrcHarvestApi;
use App\Models\Grow\Plant;
use App\Models\Grow\Harvest;
use App\Models\Grow\Room;
use App\Services\Grow\HarvestService;
use Exception;

class MetrcPlantHarvest extends MetrcBaseJob
{
    protected $unitOfWeight;
    protected $dryingRoomName;
    protected $harvestName;
    protected $patientLicenseNumber;
    protected $actualDate;
    protected $plantIds;
    protected $weights;
    protected $harvest_metrc_id;

    public function __construct($items, $user, $unitOfWeight, $dryingRoomName, $harvestName, $patientLicenseNumber, $actualDate, $plantIds, $weights)
    {
        $this->unitOfWeight = $unitOfWeight;
        $this->dryingRoomName = $dryingRoomName;
        $this->harvestName = $harvestName;
        $this->patientLicenseNumber = $patientLicenseNumber;
        $this->actualDate = $actualDate;
        $this->plantIds = $plantIds;
        $this->weights = $weights;
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
                    'Plant' => $plant->label,
                    'Weight' => $this->weights[$plant->id],
                    'UnitOfWeight' => $this->unitOfWeight,
                    'DryingRoom' => $this->dryingRoomName,
                    'HarvestName' => $this->harvestName,
                    'PatientLicenseNumber' => $this->patientLicenseNumber,
                    'ActualDate' => $this->actualDate
                ];
            }

            $http_code = $api->harvestPlants($this->user, $jsonItems);

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

            // check that new harvest exists in Metrc
            $api2 = new MetrcHarvestApi;
            $metrc_harvests = $api2->getActiveHarvests($this->user);
            $foundMetrc = false;
            foreach($metrc_harvests as $harvest) {
                if ($harvest->Name == $this->harvestName) {
                    $this->harvest_metrc_id = $harvest->Id;
                    $foundMetrc = true;
                    break;
                }
            }
            if ($foundMetrc) {
                foreach($this->items as $item) {
                    $item->metrc_status = 'synced';
                    $item->save();
                }
            }
            else {
                throw new Exception('Harvest Plants Not Successful');
            }
            
            // store the new harvest in hotbox
            $metrc_item = $api2->getHarvests($this->user, $this->harvest_metrc_id);
            $hs = new HarvestService;
            foreach ($metrc_item as $key => $value)
            {
                $metrc_item_array[$key] = $value;
            }
            $hs->storeMetrc($metrc_item_array);
            $new_harvest = Harvest::where('metrc_id',$this->harvest_metrc_id)->first();
            $new_harvest->source_plant_ids = json_encode($this->plantIds);
            $new_harvest->save();
        }
        // test mode - no interaction with Metrc
        else {
            $strain_ids = [];
            $current_weight = 0;
            foreach($this->plantIds as $id) {
                $plant = Plant::find($id);
                $plant->metrc_status = 'synced';
                $plant->save();
                $current_weight += $this->weights[$id];
                if (!in_array($plant->strain_id, $strain_ids))
                    $strain_ids[] = $plant->strain_id;
            }
            
            $room = Room::where('name', $this->dryingRoomName)->where('location_id', $this->user->location_id)->first();
            
            // store the new harvest in hotbox
            $harvest = new Harvest();
            $harvest->name = $this->harvestName;
            $harvest->location_id = $this->user->location_id;
            $harvest->harvest_type = 'Product';
            $harvest->source_strain_count = count($strain_ids);
            $harvest->drying_room_id = $room ? $room->id : null;
            $harvest->patient_license_number = $this->patientLicenseNumber;
            $harvest->current_weight = $current_weight;
            $harvest->total_waste_weight = 0;
            $harvest->plant_count = count($this->plantIds);
            $harvest->source_plant_ids = json_encode($this->plantIds);
            $harvest->total_packaged_weight = 0;
            $harvest->unit_of_weight = $this->getWeightAbbr($this->unitOfWeight);
            $harvest->harvest_start_at = $this->actualDate;
            $harvest->metrc_status = 'synced';
            $harvest->created_by = $this->user->id;
            $harvest->updated_by = $this->user->id;
            $harvest->save();
            $new_harvest = Harvest::where('name', $this->harvestName)->where('location_id', $this->user->location_id)->first();
        }
        
        
        foreach($this->plantIds as $plant_id) {
            $plant = Plant::find($plant_id);
            $plant->harvest_id = $new_harvest->id;
            $plant->save();
        }
        
        return $this->items;
    }
    
    private function getWeightAbbr($weight_uom) {
        switch ($weight_uom) {
            case 'Grams':
                $weight_abbr = 'g';
                break;
            case 'Ounces':
                $weight_abbr = 'oz';
                break;
            case 'Milligrams':
                $weight_abbr = 'mg';
                break;
            case 'Kilograms':
                $weight_abbr = 'kg';
                break;
            case 'Pounds':
                $weight_abbr = 'lb';
                break;
        }
        return $weight_abbr;
    }
}
