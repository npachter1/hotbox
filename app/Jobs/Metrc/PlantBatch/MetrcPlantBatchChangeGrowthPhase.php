<?php

namespace App\Jobs\Metrc\PlantBatch;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Services\Metrc\MetrcRequestService;
use App\Jobs\Metrc\Plant\MetrcPlantCreateByPlantBatch;
use App\Models\Grow\Room;
use App\Models\Grow\Strain;
use App\Models\Grow\Plant;
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
class MetrcPlantBatchChangeGrowthPhase extends MetrcBaseJob {

    protected $changeDate;
    protected $changeCount;
    protected $roomId;
    protected $growthPhase;
    protected $startingTag;
    protected $patientLicenseNumber;
    protected $regulatory_agent;

    public function __construct($items, $user, $changeCount, $changeDate, $roomId,
                                $growthPhase, $startingTag, $patientLicenseNumber = null)
    {
        $this->changeCount = $changeCount;
        $this->changeDate = $changeDate;
        $this->growthPhase = $growthPhase;
        $this->roomId = $roomId;
        $this->startingTag = $startingTag;
        $this->patientLicenseNumber;
        $location = \App\Models\Auth\Location::_getSchema();
        $this->regulatory_agent = $location->model->settings->regulatory_agent;
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
            $room = Room::findOrFail($this->roomId);

            $jsonItems = [];
            foreach($this->items as $item) {
                $jsonItems[] =
                    [
                        'Name' => $item->name,
                        'Count' => $this->changeCount,
                        'StartingTag' => $this->startingTag,
                        'GrowthPhase' => $this->growthPhase,
                        'NewRoom' => $room->name,
                        'GrowthDate' => Carbon::parse($item->changeDate)->toIso8601String(),
                        'PatientLicenseNumber' => $this->patientLicenseNumber
                    ];
            }

            $http_code = $api->changeGrowthPhase($this->user, $jsonItems);

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

            $metrc_items = [];
            //The difference between and active and inactive batch is a 'Count' of 0 or >0
            //Playing it safe and getting all recently modified batches
            foreach (($api->getActivePlantBatches($this->user) ?: []) as $batchdata) {
                $metrc_items[] = $batchdata;
            }

            foreach (($api->getInactivePlantBatches($this->user) ?: []) as $batchdata) {
                $metrc_items[] = $batchdata;
            }


            if (!$metrc_items || !count($metrc_items)) {
                foreach($this->items as $item) {
                    $item->metrc_status = 'error';
                    $item->save();
                }
                throw new Exception('No recently modified plant batches found in Metrc');
            }

            foreach($this->items as $item) {
                $foundMetrc = false;
                foreach ($metrc_items as $metrc_item) {
                    //Skip any non-matching batches
                    if ($metrc_item->Name === $item->name) {
                        if ($metrc_item->LiveCount !== $item->live_count) {
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
                        MetrcPlantCreateByPlantBatch::dispatch($item,$this->user);
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
                // $strain = Strain::where('id', $item->strain_id)->first();
                // $room = Room::where('id', $this->roomId)->first();
                // $tag = $this->startingTag;
                
                // for($i=0;$i<$this->changeCount;$i++) {
                //     $plant = new Plant([$this->user]);
                //     $plant->label = $tag;
                //     $plant->growth_phase = $this->growthPhase;
                //     $plant->state = 'Tracked';
                //     $plant->plant_batch_id = $item->id;
                //     $plant->location_id = $this->user->location_id;
                //     $plant->planted_at = $item->planted_at;
                //     $plant->vegetative_at = date("Y-m-d");
                //     $plant->patient_license_number = $this->patientLicenseNumber; //TODO update to ID
                //     $plant->strain_id = $strain ? $strain->id : null;
                //     $plant->room_id = $room ? $room->id : null;
                //     $plant->metrc_status = 'synced';
                //     $plant->created_by = $this->user->id;
                //     $plant->save();
                //     $tag++;
                // }
            }
        }
        
        return $this->items;
    }
}
