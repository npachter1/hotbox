<?php

namespace App\Jobs\Metrc\Plant;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Services\Metrc\MetrcRequestService;
use App\Models\Grow\Plant;
use App\Models\Auth\User;
use App\Models\Grow\PlantBatch;
use App\Models\Grow\Strain;
use App\Models\Grow\Room;
use App\Services\Grow\PlantBatchService;
use Exception;
use Carbon\Carbon;
use Auth;

class MetrcPlantCreatePlantings extends MetrcBaseJob
{
    protected $plantBatchName;
    protected $plantBatchType;
    protected $plantCount;
    protected $patientLicenseNumber;
    protected $roomName;
    protected $strainName;
    protected $actualDate;
    protected $metrc_id;

    public function __construct($items, User $user, $plantBatchName, $plantBatchType, $plantCount, $patientLicenseNumber, $roomName, $strainName, $actualDate)
    {
        $this->plantBatchName = $plantBatchName;
        $this->plantBatchType = $plantBatchType;
        $this->plantCount = $plantCount;
        $this->patientLicenseNumber = $patientLicenseNumber;
        $this->roomName = $roomName;
        $this->strainName = $strainName;
        $this->actualDate = $actualDate;
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

        if (!$this->test_mode && $this->regulatory_agent === 'metrc') {
            $jsonItems = [];
            foreach($this->items as $plant) {
                $jsonItems[] = [
                    'PlantLabel' => $plant->label,
                    'PlantBatchName' => $this->plantBatchName,
                    'PlantBatchType' => $this->plantBatchType,
                    'PlantCount' => $this->plantCount,
                    'RoomName' => $this->roomName,
                    'StrainName' => $this->strainName,
                    'PatientLicenseNumber' => $this->patientLicenseNumber,
                    'ActualDate' => $this->actualDate
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
            }

               
        }

        return $this->items;
    }
}
