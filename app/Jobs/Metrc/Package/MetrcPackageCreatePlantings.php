<?php

namespace App\Jobs\Metrc\Package;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Services\Metrc\MetrcRequestService;
use App\Models\Grow\Package;
use App\Models\Grow\Room;
use App\Models\Grow\Strain;
use App\Models\Grow\PlantBatch;
use App\Services\Grow\PlantBatchService;
use Exception;
use Carbon\Carbon;

class MetrcPackageCreatePlantings extends MetrcBaseJob
{
    protected $packageAdjustmentAmount;
    protected $packageAdjustmentUnitOfMeasure;
    protected $plantBatchName;
    protected $plantBatchType;
    protected $plantCount;
    protected $roomName;
    protected $strainName;
    protected $patientLicenseNumber;
    protected $plantedDate;
    protected $unpackagedDate;

    public function __construct($items, $user, $packageAdjustmentAmount, $packageAdjustmentUnitOfMeasure, $plantBatchName, $plantBatchType, $plantCount, $roomName, $strainName, $patientLicenseNumber, $plantedDate, $unpackagedDate)
    {
        $this->packageAdjustmentAmount = $packageAdjustmentAmount;
        $this->packageAdjustmentUnitOfMeasure = $packageAdjustmentUnitOfMeasure;
        $this->plantBatchName = $plantBatchName;
        $this->plantBatchType = $plantBatchType;
        $this->plantCount = $plantCount;
        $this->roomName = $roomName;
        $this->strainName = $strainName;
        $this->patientLicenseNumber = $patientLicenseNumber;
        $this->plantedDate = $plantedDate;
        $this->unpackagedDate = $unpackagedDate;
        parent::__construct($items, $user);
    }
    
    /**
     * Execute the job.
     */
    public function handle(MetrcRequestService $api)
    {
        if (!$this->user->location || !$this->user->location->licensenum) {
            throw new Exception('User with ID '.$this->user->id.' does not have a location licensenum.');
        }
        
        $room = Room::where('name', $this->roomName)->where('location_id', $this->user->location_id)->first();
        $strain = Strain::where('name', $this->strainName)->where('location_id', $this->user->location_id)->first();
        
        if (!$this->test_mode) {
            $jsonItems = [];
            foreach($this->items as $package) {
                $jsonItems[] = [
                    'PackageLabel' => $package->label,
                    'PackageAdjustmentAmount' => $this->packageAdjustmentAmount,
                    'PackageAdjustmentUnitOfMeasureName' => $this->packageAdjustmentUnitOfMeasure,
                    'PlantBatchName' => $this->plantBatchName,
                    'PlantBatchType' => $this->plantBatchType,
                    'PlantCount' => $this->plantCount,
                    'RoomName' => $this->roomName,
                    'StrainName' => $this->strainName,
                    'PatientLicenseNumber' => $this->patientLicenseNumber,
                    'PlantedDate' => $this->plantedDate,
                    'UnpackageDate' => $this->unpackagedDate
                ];
            }
            $http_code = $api->createPlantings($this->user, $jsonItems);

            foreach($this->items as $item) {
                $item->metrc_update_transaction_id = $api->metrcTransactionId;
            }

            if ($http_code !== 200) {
                foreach($this->items as $item) {
                    $item->metrc_status = 'error';
                    $item->save();
                }
                if (is_array($http_code))
                    throw new Exception($http_code[0]->message);
                else
                    throw new Exception($http_code->Message);
            }
            // check that the new plant batch exists in Metrc
            // $api2 = new MetrcPlantBatchesApi;
            $metrc_items = $api->getActivePlantBatches($this->user);
            $foundMetrc = false;
            foreach($metrc_items as $metrc_item) {
                if ($metrc_item->Name === $this->plantBatchName) {
                    $this->metrc_id = $metrc_item->Id;
                    $foundMetrc = true;
                    break;
                }
            }
            if (!$foundMetrc) {
                foreach($this->items as $item) {
                    $item->metrc_status = 'error';
                    $item->save();
                }
                throw new Exception('Plant Batch Not Created Successfully');
            }
            // store the new plant batch in hotbox
            // $api2 = new MetrcPlantBatchesApi;
            $metrc_item = $api->getPlantBatches($this->user, $this->metrc_id);
            $pbs = new PlantBatchService;
            foreach ($metrc_item as $key => $value)
            {
                $metrc_item_array[$key] = $value;
            }
            $pbs->storeMetrc($metrc_item_array);
            foreach($this->items as $item) {
                $item->metrc_status = 'synced';
                $item->save();
            }
        }
        // test mode - no interaction with Metrc
        else {
            foreach($this->items as $item) {
                $item->metrc_status = 'synced';
                $item->save();
                // create the new plant batch in hotbox
                $plantbatch = new PlantBatch();
                $plantbatch->name = $this->plantBatchName;
                $plantbatch->type = $this->plantBatchType;
                $plantbatch->location_id = $this->user->location_id;
                $plantbatch->room_id = $room ? $room->id : null;
                $plantbatch->strain_id = $strain ? $strain->id : null;
                $plantbatch->patient_license_number = $this->patientLicenseNumber;
                $plantbatch->count = $this->plantCount;
                $plantbatch->live_count = 0;
                $plantbatch->packaged_count = 0;
                $plantbatch->harvested_count = 0;
                $plantbatch->destroyed_count = 0;
                $plantbatch->source_package_id = $item->id;
                $plantbatch->planted_at = Carbon::parse($this->plantedDate)->toDateTimeString();
                $plantbatch->metrc_status = 'synced';
                $plantbatch->created_by = $this->user->id;
                $plantbatch->save();
            }
        }

        return $this->items;
    }
}
