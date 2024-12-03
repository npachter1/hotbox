<?php

namespace App\Jobs\Metrc\PlantBatch;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Services\Metrc\MetrcRequestService;
use App\Models\Auth\User;
use App\Models\Grow\PlantBatch;
use Carbon\Carbon;
use Exception;
use Auth;


class MetrcPlantBatchStore extends MetrcBaseJob
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
     *
     * @return void
     */
    public function handle(MetrcRequestService $api)
    {
        if (!$this->user->location || !$this->user->location->licensenum) {
            throw new Exception('User with ID '.$this->user->id.' does not have a location licensenum.');
        }
        if (!$this->test_mode && $this->regulatory_agent === 'metrc') {
            $jsonItems = [];
            foreach($this->items as $item) {
                $batch = PlantBatch::with('sourcePlant')->with('room')->with('strain')->findOrFail($item->id);
                if (!$batch->sourcePlant)
                    throw new Exception('Source Plant Not Found');
                $jsonItems[] =
                    [
                        'PlantLabel' => $batch->sourcePlant->label, //required
                        'PlantBatchName' => $batch->name,
                        'PlantBatchType' => $batch->type,
                        'PlantCount' => $batch->count,
                        'RoomName' => ($batch->room ? $batch->room->name : null), //optional, room could not be an object
                        'StrainName' => $batch->strain->name,
                        'PatientLicenseNumber' => $batch->patient_license_number,
                        'ActualDate' => Carbon::parse($batch->planted_at)->toIso8601String(),
                ];
            }

            if (!$jsonItems || !count($jsonItems)) {
                // No items to process. It's possible this job already ran.
                return;
            }

            $http_code = $api->createPlantings($this->user, $jsonItems);

            foreach($this->items as $item) {
                $item->metrc_create_transaction_id = $api->metrcTransactionId;
            }

            if ($http_code !== 200) {
                foreach($this->items as $item) {
                    $item->metrc_status = 'error';
                    $item->save();
                }
                throw new Exception('Create Plant Batch Not Successful, returned HTTP code '.$http_code);
            }

            $metrc_items = $api->getActivePlantBatches($this->user);
            if (!$metrc_items || !count($metrc_items)) {
                foreach($this->items as $item) {
                    $item->metrc_status = 'error';
                    $item->save();
                }
                throw new Exception('No active plant batches found in Metrc');
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
