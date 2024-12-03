<?php

namespace App\Services\Grow;

use App\Models\Grow\Room;
use App\Models\Grow\Strain;
use App\Models\Grow\Item;
use App\Models\Grow\PlantBatch;
use App\Models\Grow\Plant;
use App\Models\Grow\Harvest;
use App\Models\Grow\Package;
use App\Models\Auth\User;
use App\Models\Dispensary\CategoryMetrc;
use App\Services\Dispensary\CategoryService;
use App\Services\Metrc\MetrcRequestService;
use App\Services\Metrc\Traits\MetrcRoomApi;
use App\Services\Metrc\Traits\MetrcStrainApi;
use App\Services\Metrc\Traits\MetrcItemApi;
use App\Services\Metrc\Traits\MetrcPlantBatchesApi;
use App\Services\Metrc\Traits\MetrcPlantApi;
use App\Services\Metrc\Traits\MetrcHarvestApi;
use App\Services\Metrc\Traits\MetrcPackageApi;
use Exception;
use App\Services\BaseService;
use Auth;
use Illuminate\Database\Eloquent\Collection;
use DateTime;
use Carbon\Carbon;
use App\Services\Grow\RoomService;
use App\Services\Grow\StrainService;
use App\Services\Grow\ItemService;
use App\Services\Grow\PlantBatchService;
use App\Services\Grow\PlantService;
use App\Services\Grow\HarvestService;
use App\Services\Grow\PackageService;
use App\Jobs\Metrc\Room\MetrcRoomStore;
use App\Jobs\Metrc\Room\MetrcRoomUpdate;
use App\Jobs\Metrc\Strain\MetrcStrainStore;
use App\Jobs\Metrc\Strain\MetrcStrainUpdate;
use App\Jobs\Metrc\Item\MetrcItemStore;
use App\Jobs\Metrc\Item\MetrcItemUpdate;
use App\Jobs\Metrc\PlantBatch\MetrcPlantBatchStore;
use Illuminate\Pagination\LengthAwarePaginator;


class SyncService extends BaseService
{
    use MetrcStrainApi;


    /*public function syncMetrc(array $data)
    {
        $locationId = Auth::user()->location_id;
        $sectionName = $data['sectionName'];
        $resultsArray = [];
        $startDate = null;
        $endDate = null;
        $page = 1;
        if (isset($data['page']))
            $page = $data['page'];
        $perPage = 50;
        if ($data['startDate'] > 0 && $data['endDate'] > 0) {
            $startDate = new DateTime($data['startDate']);
            $endDate = new DateTime($data['endDate']);
        }

        switch ($sectionName) {
            case 'Rooms':
                $results = $this->syncRooms(Auth::user());
                break;
            case 'Strains':
                $results = $this->syncStrains(Auth::user());
                break;
            case 'Items':
                $results = $this->syncItems(Auth::user());
                break;
            case 'PlantBatches':
                $results = $this->syncPlantBatches($startDate, $endDate, Auth::user());
                break;
            case 'Plants':
                $results = $this->syncPlants($startDate, $endDate, Auth::user());
                break;
            case 'Harvests':
                $results = $this->syncHarvests($startDate, $endDate, Auth::user());
                break;
            case 'Packages':
                $results = $this->syncPackages($startDate, $endDate, Auth::user());
                break;
            default:
                throw new Exception('No Grow Section Specified');
        }

        foreach ($results as $result)
            $resultsArray[] = $result;

        // paginate results
        $collection = collect($resultsArray);
        $paginatedResults = new LengthAwarePaginator($collection->forPage($page, $perPage), $collection->count(), $perPage, $page);

        return ['data' => $paginatedResults];
    }*/

    public function importMetrc(array $data)
    {
        $sectionName = $data['sectionName'];
        $user = Auth::guard('api')->user();

        switch ($sectionName) {
            case 'Rooms':
                $results = $this->importMetrcRoom($data, $user);
                break;
            case 'Strains':
                $results = $this->importMetrcStrain($data, $user);
                break;
            case 'Items':
                $results = $this->importMetrcItem($data, $user);
                break;
            case 'PlantBatches':
                $results = $this->importMetrcPlantBatch($data, $user);
                break;
            case 'Plants':
                $results = $this->importMetrcPlant($data, $user);
                break;
            case 'Harvests':
                $results = $this->importMetrcHarvest($data, $user);
                break;
            case 'Packages':
                $results = $this->importMetrcPackage($data, $user);
                break;
            default:
                throw new Exception('No Grow Section Specified');
        }

        return ['data' => 'true'];
    }

    public function exportHotbox(array $data)
    {
        $sectionName = $data['sectionName'];

        switch ($sectionName) {
            case 'Rooms':
                $results = $this->exportHotboxRoom($data);
                break;
            case 'Strains':
                $results = $this->exportHotboxStrain($data);
                break;
            case 'Items':
                $results = $this->exportHotboxItem($data);
                break;
            case 'PlantBatches':
                $results = $this->exportHotboxPlantBatch($data);
                break;
            case 'Plants':
                $results = $this->exportHotboxPlant($data);
                break;
            case 'Harvests':
                $results = $this->exportHotboxHarvest($data);
                break;
            case 'Packages':
                $results = $this->exportHotboxPackage($data);
                break;
            default:
                throw new Exception('No Grow Section Specified');
        }

        return ['data' => 'true'];
    }

    public function deleteHotbox(array $data)
    {
        $sectionName = $data['sectionName'];

        switch ($sectionName) {
            case 'Rooms':
                $results = $this->deleteHotboxRoom($data);
                break;
            case 'Strains':
                $results = $this->deleteHotboxStrain($data);
                break;
            case 'Items':
                $results = $this->deleteHotboxItem($data);
                break;
            case 'PlantBatches':
                $results = $this->deleteHotboxPlantBatch($data);
                break;
            case 'Plants':
                $results = $this->deleteHotboxPlant($data);
                break;
            case 'Harvests':
                $results = $this->deleteHotboxHarvest($data);
                break;
            case 'Packages':
                $results = $this->deleteHotboxPackage($data);
                break;
            default:
                throw new Exception('No Grow Section Specified');
        }

        return ['data' => 'true'];
    }

    public function matchHotboxToMetrc(array $data)
    {
        $sectionName = $data['sectionName'];
        $user = Auth::guard('api')->user();

        switch ($sectionName) {
            case 'Rooms':
                $results = $this->matchHotboxToMetrcRoom($data, $user);
                break;
            case 'Strains':
                $results = $this->matchHotboxToMetrcStrain($data, $user);
                break;
            case 'Items':
                $results = $this->matchHotboxToMetrcItem($data, $user);
                break;
            case 'PlantBatches':
                $results = $this->matchHotboxToMetrcPlantBatch($data, $user);
                break;
            case 'Plants':
                $results = $this->matchHotboxToMetrcPlant($data, $user);
                break;
            case 'Harvests':
                $results = $this->matchHotboxToMetrcHarvest($data, $user);
                break;
            case 'Packages':
                $results = $this->matchHotboxToMetrcPackage($data, $user);
                break;
            default:
                throw new Exception('No Grow Section Specified');
        }

        return ['data' => 'true'];
    }

    public function matchMetrcToHotbox(array $data)
    {
        $sectionName = $data['sectionName'];

        switch ($sectionName) {
            case 'Rooms':
                $results = $this->matchMetrcToHotboxRoom($data);
                break;
            case 'Strains':
                $results = $this->matchMetrcToHotboxStrain($data);
                break;
            case 'Items':
                $results = $this->matchMetrcToHotboxItem($data);
                break;
            case 'PlantBatches':
                $results = $this->matchMetrcToHotboxPlantBatch($data);
                break;
            case 'Plants':
                $results = $this->matchMetrcToHotboxPlant($data);
                break;
            case 'Harvests':
                $results = $this->matchMetrcToHotboxHarvest($data);
                break;
            case 'Packages':
                $results = $this->matchMetrcToHotboxPackage($data);
                break;
            default:
                throw new Exception('No Grow Section Specified');
        }

        return ['data' => 'true'];
    }

    public function deleteMetrc(array $data)
    {
        $sectionName = $data['sectionName'];

        switch ($sectionName) {
            case 'Rooms':
                $results = $this->deleteMetrcRoom($data);
                break;
            case 'Strains':
                $results = $this->deleteMetrcStrain($data);
                break;
            case 'Items':
                $results = $this->deleteMetrcItem($data);
                break;
            case 'PlantBatches':
                $results = $this->deleteMetrcPlantBatch($data);
                break;
            case 'Plants':
                $results = $this->deleteMetrcPlant($data);
                break;
            case 'Harvests':
                $results = $this->deleteMetrcHarvest($data);
                break;
            case 'Packages':
                $results = $this->deleteMetrcPackage($data);
                break;
            default:
                throw new Exception('No Grow Section Specified');
        }

        return ['data' => 'true'];
    }

    public function syncRoomsFromMetrc($user)
    {
        $results = [];
        $metrcIds = [];  // id's of all rooms found in metrc
        $hotboxMetrcIds = [];
        $foundMetrc = [];  // metrc rooms also found in hotbox

        if (!$user)
            $user = Auth::guard('api')->user();

        // get Hotbox roomss
        $hotboxRooms = Room::where('location_id', $user->location_id)
        ->whereNull('deleted_at')->get();
        foreach ($hotboxRooms as $room) {
            $hotboxMetrcIds[] = $room->metrc_id;
        }    

        $api = new MetrcRequestService();
        $metrcRooms = $api->getActiveRooms($user);
        foreach ($metrcRooms as $metrcRoom) {
            $metrcIds[] = $metrcRoom->Id;
            if (!in_array($metrcRoom->Id, $hotboxMetrcIds))    // if in Metrc but not Hotbox
                $results[$metrcRoom->Id]['metrc'] = $metrcRoom;
            else
                $foundMetrc[] = $metrcRoom;
        }

        foreach ($hotboxRooms as $hotboxRoom) {
            if (!in_array($hotboxRoom->metrc_id, $metrcIds))    // if in Hotbox but not Metrc
                $results[$hotboxRoom->metrc_id]['hotbox'] = $hotboxRoom;
        }

        // check that the names of found rooms match
        foreach ($foundMetrc as $metrcItem) {
            $hotboxRoom = $hotboxRooms[array_search($metrcItem->Id, $hotboxMetrcIds)];
            if ($hotboxRoom->name != $metrcItem->Name) {
                $results[$metrcItem->Id]['metrc'] = $metrcItem;
                $results[$metrcItem->Id]['hotbox'] = $hotboxRoom;
            }
        }
        return $results;
    }

    public function syncStrains($user)
    {
        $results = [];
        $metrcIds = [];  // id's of all strains found in metrc
        $hotboxMetrcIds = [];
        $foundMetrc = [];  // metrc strains also found in hotbox

        if (!$user)
            $user = Auth::guard('api')->user();

        // get Hotbox strains
        $hotboxStrains = Strain::where('location_id', $user->location_id)
            ->whereNull('deleted_at')->get();
        foreach ($hotboxStrains as $strain) {
            $hotboxMetrcIds[] = $strain->metrc_id;
        }

        $api  = new MetrcRequestService();
        $metrcStrains = $api->getActiveStrains($user);
        foreach ($metrcStrains as $metrcStrain) {
            $metrcIds[] = $metrcStrain->Id;
            if (!in_array($metrcStrain->Id, $hotboxMetrcIds))    // if in Metrc but not Hotbox
                $results[$metrcStrain->Id]['metrc'] = $metrcStrain;
            else
                $foundMetrc[] = $metrcStrain;
        }

        foreach ($hotboxStrains as $hotboxStrain) {
            if (!in_array($hotboxStrain->metrc_id, $metrcIds))    // if in Hotbox but not Metrc
                $results[$hotboxStrain->metrc_id]['hotbox'] = $hotboxStrain;
        }

        // check that the info in found strains match
        foreach ($foundMetrc as $metrcItem) {
            $hotboxStrain = $hotboxStrains[array_search($metrcItem->Id, $hotboxMetrcIds)];
            if ($hotboxStrain->name != $metrcItem->Name || $hotboxStrain->testing_status != $metrcItem->TestingStatus || $hotboxStrain->thc_level * 1 != $metrcItem->ThcLevel * 1 || $hotboxStrain->cbd_level * 1 != $metrcItem->CbdLevel * 1 || $hotboxStrain->indica_percentage * 1 != $metrcItem->IndicaPercentage * 1 || $hotboxStrain->sativa_percentage * 1 != $metrcItem->SativaPercentage * 1) {
                $results[$metrcItem->Id]['metrc'] = $metrcItem;
                $results[$metrcItem->Id]['hotbox'] = $hotboxStrain;
            }
        }
        return $results;
    }

    public function syncItems($user)
    {
        $results = [];
        $metrcIds = [];  // id's of all strains found in metrc
        $hotboxMetrcIds = [];
        $foundMetrc = [];  // metrc strains also found in hotbox

        if (!$user)
            $user = Auth::guard('api')->user();

        $hasMetrcCats = CategoryMetrc::where('location_id', $user->location_id)->get();
        if ($hasMetrcCats) {
            $categoryService = new CategoryService;
            try {
                $categoryService->syncMetrcCategories($user);
            } catch(\Exception $e) {
                throw new \Exception('Could not establish Metrc connection');
            }
        }

        // get Hotbox items
        $hotboxItems = Item::where('location_id', $user->location_id)
            ->whereNull('deleted_at')->get();
        foreach ($hotboxItems as $item) {
            $hotboxMetrcIds[] = $item->metrc_id;
        }

        $api = new MetrcRequestService();
        $metrcItems = $api->getActiveItems($user);
        foreach ($metrcItems as $metrcItem) {
            $metrcIds[] = $metrcItem->Id;
            if (!in_array($metrcItem->Id, $hotboxMetrcIds))    // if in Metrc but not Hotbox
                $results[$metrcItem->Id]['metrc'] = $metrcItem;
            else
                $foundMetrc[] = $metrcItem;
        }

        foreach ($hotboxItems as $hotboxItem) {
            if (!in_array($hotboxItem->metrc_id, $metrcIds))    // if in Hotbox but not Metrc
                $results[$hotboxItem->metrc_id]['hotbox'] = $hotboxItem;
        }

        // check that the info in found items match
        foreach ($foundMetrc as $metrcItem) {
            $hotboxItem = $hotboxItems[array_search($metrcItem->Id, $hotboxMetrcIds)];
            $metrcCategory = CategoryMetrc::where('id', $hotboxItem->metrc_category_id)->first();
            $strain = Strain::where('id', $hotboxItem->strain_id)->first();
            if ($strain) $strainMetrcId = $strain->metrc_id;
            else $strainMetrcId = null;
            $unitOfMeasure = $this->getWeightAbbr($metrcItem->UnitOfMeasureName);
            $unitCbdContentUnitOfMeasure = $this->getWeightAbbr($metrcItem->UnitCbdContentUnitOfMeasureName);
            $unitThcContentUnitOfMeasure = $this->getWeightAbbr($metrcItem->UnitThcContentUnitOfMeasureName);
            $unitVolumeUnitOfMeasure = $this->getWeightAbbr($metrcItem->UnitVolumeUnitOfMeasureName);
            $unitWeightUnitOfMeasure = $this->getWeightAbbr($metrcItem->UnitWeightUnitOfMeasureName);
            if (trim($hotboxItem->name) != trim($metrcItem->Name) || $metrcCategory->product_category_type != $metrcItem->ProductCategoryType || $hotboxItem->unit_of_measure != $unitOfMeasure || $strainMetrcId != $metrcItem->StrainId || $hotboxItem->unit_cbd_content_unit_of_measure != $unitCbdContentUnitOfMeasure || $hotboxItem->unit_thc_content_unit_of_measure != $unitThcContentUnitOfMeasure || $hotboxItem->unit_volume_unit_of_measure != $unitVolumeUnitOfMeasure || $hotboxItem->unit_weight_unit_of_measure != $unitWeightUnitOfMeasure || $hotboxItem->administration_method != $metrcItem->AdministrationMethod || $hotboxItem->unit_cbd_percent * 1 != $metrcItem->UnitCbdPercent * 1 || $hotboxItem->unit_cbd_content * 1 != $metrcItem->UnitCbdContent * 1 || $hotboxItem->unit_thc_percent * 1 != $metrcItem->UnitThcPercent * 1 || $hotboxItem->unit_thc_content * 1 != $metrcItem->UnitThcContent * 1) {
                $results[$metrcItem->Id]['metrc'] = $metrcItem;
                $results[$metrcItem->Id]['hotbox'] = $hotboxItem;
            }
        }
        return $results;
    }

    public function syncPlantBatches($startDate, $endDate, $user)
    {
        $results = [];
        $metrcIds = [];  // id's of all strains found in metrc
        $hotboxMetrcIds = [];
        $foundMetrc = [];  // metrc strains also found in hotbox

        if (!$user)
            $user = Auth::guard('api')->user();

        // get Hotbox plant batches
        $hotboxPlantBatches = PlantBatch::where('location_id', $user->location_id)
            ->whereNull('deleted_at')->get();
        foreach ($hotboxPlantBatches as $plantBatch) {
            $hotboxMetrcIds[] = $plantBatch->metrc_id;
        }

        $plusOneDay = null;
        if ($startDate) {
            $plusOneDay = new DateTime($startDate->format('Y-m-d\TH:i:s\Z'));
            $plusOneDay->modify('+1 day');
        }
        $formattedStartDate = null;
        $formattedPlusOneDay = null;
        $api = new MetrcRequestService();
        // loop through plant batches one day at a time
        while ($plusOneDay <= $endDate) {
            if ($startDate && $endDate) {
                $formattedStartDate = $startDate->format('Y-m-d\TH:i:s\Z');
                $formattedPlusOneDay = $plusOneDay->format('Y-m-d\TH:i:s\Z');
            }
            $metrcPlantBatches = $api->getActivePlantBatches($user, $formattedStartDate, $formattedPlusOneDay);
            if ($metrcPlantBatches) {
                foreach ($metrcPlantBatches as $metrcPlantBatch) {
                    $metrcIds[] = $metrcPlantBatch->Id;
                    if (!in_array($metrcPlantBatch->Id, $hotboxMetrcIds))    // if in Metrc but not Hotbox
                        $results[$metrcPlantBatch->Id]['metrc'] = $metrcPlantBatch;
                    else
                        $foundMetrc[] = $metrcPlantBatch;
                }
            }
            $metrcPlantBatches = $api->getInactivePlantBatches($user, $formattedStartDate, $formattedPlusOneDay);
            if ($metrcPlantBatches) {
                foreach ($metrcPlantBatches as $metrcPlantBatch) {
                    $metrcIds[] = $metrcPlantBatch->Id;
                    if (!in_array($metrcPlantBatch->Id, $hotboxMetrcIds))    // if in Metrc but not Hotbox
                        $results[$metrcPlantBatch->Id]['metrc'] = $metrcPlantBatch;
                    else
                        $foundMetrc[] = $metrcPlantBatch;
                }
            }
            if ($startDate) {
                $startDate = clone $plusOneDay;
                $plusOneDay->modify('+1 day');
            } else
                break;
        }

        foreach ($hotboxPlantBatches as $hotboxPlantBatch) {
            // if in Hotbox but not found in Metrc withind specified dates
            if (!in_array($hotboxPlantBatch->metrc_id, $metrcIds)) {
                // look for the batch in Metrc by Id
                $metrcPlantBatch = $api->getPlantBatches($user, $hotboxPlantBatch->metrc_id);
                if (!$metrcPlantBatch)
                    $results[$hotboxPlantBatch->metrc_id]['hotbox'] = $hotboxPlantBatch;
                else
                    $foundMetrc[] = $metrcPlantBatch;
            }
        }

        // check that the info in found plant batches match
        foreach ($foundMetrc as $metrcItem) {
            $hotboxPlantBatch = $hotboxPlantBatches[array_search($metrcItem->Id, $hotboxMetrcIds)];
            $room = Room::where('id', $hotboxPlantBatch->room_id)->first();
            if ($room) $roomMetrcId = $room->metrc_id;
            else $roomMetrcId = null;
            $strain = Strain::where('id', $hotboxPlantBatch->strain_id)->first();
            if ($strain) $strainMetrcId = $strain->metrc_id;
            else $strainMetrcId = null;
            if ($hotboxPlantBatch->name != $metrcItem->Name || $hotboxPlantBatch->type != $metrcItem->Type || $roomMetrcId != $metrcItem->RoomId || $strainMetrcId != $metrcItem->StrainId || $hotboxPlantBatch->patient_license_number != $metrcItem->PatientLicenseNumber || $hotboxPlantBatch->count != $metrcItem->Count || $hotboxPlantBatch->live_count != $metrcItem->LiveCount || $hotboxPlantBatch->packaged_count != $metrcItem->PackagedCount || $hotboxPlantBatch->harvested_count != $metrcItem->HarvestedCount || $hotboxPlantBatch->destroyed_count != $metrcItem->DestroyedCount) {
                $results[$metrcItem->Id]['metrc'] = $metrcItem;
                $results[$metrcItem->Id]['hotbox'] = $hotboxPlantBatch;
            }
        }
        return $results;
    }

    public function syncPlants(Carbon $startDate, Carbon $endDate, $user)
    {
        $results = [];
        $metrcIds = [];  // id's of all strains found in metrc
        $hotboxMetrcIds = [];
        $foundMetrc = [];  // metrc strains also found in hotbox

        if (!$user)
            $user = Auth::guard('api')->user();

        // get Hotbox plants
        $hotboxPlants = Plant::where('location_id', $user->location_id)
            ->whereNull('deleted_at')->get();
        foreach ($hotboxPlants as $plant) {
            $hotboxMetrcIds[] = $plant->metrc_id;
        }

        $plusOneDay = null;
        if ($startDate) {
            $plusOneDay = new DateTime($startDate->format('Y-m-d\TH:i:s\Z'));
            $plusOneDay->modify('+1 day');
        }
        $formattedStartDate = null;
        $formattedPlusOneDay = null;
        $api = new MetrcRequestService();
        // loop through plants one day at a time
        while ($plusOneDay <= $endDate) {
            if ($startDate && $endDate) {
                $formattedStartDate = $startDate->format('Y-m-d\TH:i:s\Z');
                $formattedPlusOneDay = $plusOneDay->format('Y-m-d\TH:i:s\Z');
            }
            // get vegetative plants
            $metrcPlants = $api->getVegetativePlants($user, $formattedStartDate, $formattedPlusOneDay);
            if ($metrcPlants) {
                foreach ($metrcPlants as $metrcPlant) {
                    $metrcIds[] = $metrcPlant->Id;
                    if (!in_array($metrcPlant->Id, $hotboxMetrcIds))   // if in Metrc but not Hotbox
                        $results[$metrcPlant->Id]['metrc'] = $metrcPlant;
                    else
                        $foundMetrc[] = $metrcPlant;
                }
            }
            // get flowering plants
            $metrcPlants = $api->getFloweringPlants($user, $formattedStartDate, $formattedPlusOneDay);
            if ($metrcPlants) {
                foreach ($metrcPlants as $metrcPlant) {
                    $metrcIds[] = $metrcPlant->Id;
                    if (!in_array($metrcPlant->Id, $hotboxMetrcIds))   // if in Metrc but not Hotbox
                        $results[$metrcPlant->Id]['metrc'] = $metrcPlant;
                    else
                        $foundMetrc[] = $metrcPlant;
                }
            }
            // get inactive plants
            $metrcPlants = $api->getInactivePlants($user, $formattedStartDate, $formattedPlusOneDay);
            if ($metrcPlants) {
                foreach ($metrcPlants as $metrcPlant) {
                    $metrcIds[] = $metrcPlant->Id;
                    if (!in_array($metrcPlant->Id, $hotboxMetrcIds))   // if in Metrc but not Hotbox
                        $results[$metrcPlant->Id]['metrc'] = $metrcPlant;
                    else
                        $foundMetrc[] = $metrcPlant;
                }
            }
            if ($startDate) {
                $startDate = clone $plusOneDay;
                $plusOneDay->modify('+1 day');
            } else
                break;
        }

        foreach ($hotboxPlants as $hotboxPlant) {
            // if in Hotbox but not found in Metrc withind specified dates

            if (!in_array($hotboxPlant->metrc_id, $metrcIds)) {
                // look for the plant in Metrc by Id
                $metrcPlant = $api->getPlants($user, $hotboxPlant->metrc_id);
                if (!$metrcPlant)
                    $results[$hotboxPlant->metrc_id]['hotbox'] = $hotboxPlant;
                else
                    $foundMetrc[] = $metrcPlant;
            }
        }

        // check that the info in found plants match
        foreach ($foundMetrc as $metrcItem) {
            $hotboxPlant = $hotboxPlants[array_search($metrcItem->Id, $hotboxMetrcIds)];
            $room = Room::where('id', $hotboxPlant->room_id)->first();
            if ($room) $roomMetrcId = $room->metrc_id;
            else $roomMetrcId = null;
            $strain = Strain::where('id', $hotboxPlant->strain_id)->first();
            if ($strain) $strainMetrcId = $strain->metrc_id;
            else $strainMetrcId = null;
            $harvest = Harvest::where('id', $hotboxPlant->harvest_id)->first();
            if ($harvest) $harvestMetrcId = $harvest->metrc_id;
            else $harvestMetrcId = null;
            if ($hotboxPlant->growth_phase != $metrcItem->GrowthPhase || $strainMetrcId != $metrcItem->StrainId || $roomMetrcId != $metrcItem->RoomId || $hotboxPlant->patient_license_number != $metrcItem->PatientLicenseNumber || $harvestMetrcId != $metrcItem->HarvestId || $hotboxPlant->harvested_wet_weight != $metrcItem->HarvestedWetWeight || $hotboxPlant->harvest_count != $metrcItem->HarvestCount) {
                $results[$metrcItem->Id]['metrc'] = $metrcItem;
                $results[$metrcItem->Id]['hotbox'] = $hotboxPlant;
            }
        }
        return $results;
    }

    public function syncHarvests($startDate, $endDate, $user)
    {
        $results = [];
        $metrcIds = [];  // id's of all strains found in metrc
        $hotboxMetrcIds = [];
        $foundMetrc = [];  // metrc strains also found in hotbox

        if (!$user)
            $user = Auth::guard('api')->user();

        // get Hotbox harvests
        $hotboxHarvests = Harvest::where('location_id', $user->location_id)
            ->whereNull('deleted_at')->get();
        foreach ($hotboxHarvests as $harvest) {
            $hotboxMetrcIds[] = $harvest->metrc_id;
        }

        $plusOneDay = null;
        if ($startDate) {
            $plusOneDay = new DateTime($startDate->format('Y-m-d\TH:i:s\Z'));
            $plusOneDay->modify('+1 day');
        }
        $formattedStartDate = null;
        $formattedPlusOneDay = null;
        $api = new MetrcRequestService();
        // loop through harvests one day at a time
        while ($plusOneDay <= $endDate) {
            if ($startDate && $endDate) {
                $formattedStartDate = $startDate->format('Y-m-d\TH:i:s\Z');
                $formattedPlusOneDay = $plusOneDay->format('Y-m-d\TH:i:s\Z');
            }
            $metrcHarvests = $api->getActiveHarvests($user, $formattedStartDate, $formattedPlusOneDay);
            if ($metrcHarvests) {
                foreach ($metrcHarvests as $metrcHarvest) {
                    $metrcIds[] = $metrcHarvest->Id;
                    if (!in_array($metrcHarvest->Id, $hotboxMetrcIds))   // if in Metrc but not Hotbox
                        $results[$metrcHarvest->Id]['metrc'] = $metrcHarvest;
                    else
                        $foundMetrc[] = $metrcHarvest;
                }
            }
            $metrcHarvests = $api->getInactiveHarvests($user, $formattedStartDate, $formattedPlusOneDay);
            if ($metrcHarvests) {
                foreach ($metrcHarvests as $metrcHarvest) {
                    $metrcIds[] = $metrcHarvest->Id;
                    if (!in_array($metrcHarvest->Id, $hotboxMetrcIds)) // if in Metrc but not Hotbox
                        $results[$metrcHarvest->Id]['metrc'] = $metrcHarvest;
                    else
                        $foundMetrc[] = $metrcHarvest;
                }
            }
            if ($startDate) {
                $startDate = clone $plusOneDay;
                $plusOneDay->modify('+1 day');
            } else
                break;
        }

        foreach ($hotboxHarvests as $hotboxHarvest) {
            // if in Hotbox but not found in Metrc withind specified dates
            if (!in_array($hotboxHarvest->metrc_id, $metrcIds)) {
                // look for the harvest in Metrc by Id
                $metrcHarvest = $api->getHarvests($user, $hotboxHarvest->metrc_id);
                if (!$metrcHarvests)
                    $results[$hotboxHarvest->metrc_id]['hotbox'] = $hotboxHarvest;
            }
        }

        // check that the info in found harvests match
        // foreach ($foundMetrc as $metrcItem) {
        //     $hotboxHarvest = $hotboxHarvests[array_search($metrcItem->Id, $hotboxMetrcIds)];
        //     $room = Room::where('id', $hotboxHarvest->drying_room_id)->first();
        //     if ($room) $roomMetrcId = $room->metrc_id;
        //     else $roomMetrcId = null;
        //     if ($hotboxHarvest->name != $metrcItem->Name || $hotboxHarvest->harvest_type != $metrcItem->HarvestType || $roomMetrcId != $metrcItem->DryingRoomId || $hotboxHarvest->patient_license_number != $metrcItem->PatientLicenseNumber || $hotboxHarvest->current_weight != $metrcItem->CurrentWeight || $hotboxHarvest->total_waste_weight != $metrcItem->TotalWasteWeight || $hotboxHarvest->total_restored_weight != $metrcItem->TotalRestoredWeight || $hotboxHarvest->package_count != $metrcItem->PackageCount || $hotboxHarvest->total_packaged_weight != $metrcItem->TotalPackagedWeight || $hotboxHarvest->lab_testing_state != $metrcItem->LabTestingState) {
        //         $results[$metrcItem->Id]['metrc'] = $metrcItem;
        //         $results[$metrcItem->Id]['hotbox'] = $hotboxHarvest;
        //     }
        // }

        return $results;
    }

    public function syncPackages($startDate, $endDate, $user)
    {
        $results = [];
        $metrcIds = [];  // id's of all strains found in metrc
        $hotboxMetrcIds = [];
        $foundMetrc = [];  // metrc strains also found in hotbox

        if (!$user)
            $user = Auth::guard('api')->user();

        // get Hotbox packages
        $hotboxPackages = Package::where('location_id', $user->location_id)
            ->whereNull('deleted_at')->get();
        foreach ($hotboxPackages as $package) {
            $hotboxMetrcIds[] = $package->metrc_id;
        }

        $plusOneDay = null;
        if ($startDate) {
            $plusOneDay = new DateTime($startDate->format('Y-m-d\TH:i:s\Z'));
            $plusOneDay->modify('+1 day');
        }
        $carbonStartDate = null;
        $carbonPlusOneDay = null;
        $api = new MetrcRequestService();
        // loop through harvests one day at a time
        while ($plusOneDay <= $endDate) {
            if ($startDate && $endDate) {
                $formattedStartDate = $startDate->format('Y-m-d\TH:i:s\Z');
                $formattedPlusOneDay = $plusOneDay->format('Y-m-d\TH:i:s\Z');
            }
            $metrcPackages = $api->getActivePackages($user, $formattedStartDate, $formattedPlusOneDay);
            if ($metrcPackages) {
                foreach ($metrcPackages as $metrcPackage) {
                    $metrcIds[] = $metrcPackage->Id;
                    if (!in_array($metrcPackage->Id, $hotboxMetrcIds))   // if in Metrc but not Hotbox
                        $results[$metrcPackage->Id]['metrc'] = $metrcPackage;
                    else
                        $foundMetrc[] = $metrcPackage;
                }
            }
            $metrcPackages = $api->getInactivePackages($user, $formattedStartDate, $formattedPlusOneDay);
            if ($metrcPackages) {
                foreach ($metrcPackages as $metrcPackage) {
                    $metrcIds[] = $metrcPackage->Id;
                    if (!in_array($metrcPackage->Id, $hotboxMetrcIds))      // if in Metrc but not Hotbox
                        $results[$metrcPackage->Id]['metrc'] = $metrcPackage;
                    else
                        $foundMetrc[] = $metrcPackage;
                }
            }
            if ($startDate) {
                $startDate = clone $plusOneDay;
                $plusOneDay->modify('+1 day');
            } else
                break;
        }

        $hotboxPackages = Package::where('location_id', $user->location_id)
            ->whereNull('deleted_at')->get();
        foreach ($hotboxPackages as $hotboxPackage) {
            // if in Hotbox but not found in Metrc withind specified dates
            if (!in_array($hotboxPackage->metrc_id, $metrcIds)) {
                // look for the package in Metrc by Id
                $metrcPackage = $api->getPackages($user, $hotboxPackage->metrc_id);
                if (!$metrcPackages)
                    $results[$hotboxPackage->metrc_id]['hotbox'] = $hotboxPackage;
            }
        }

        // check that the info in found packages match
        foreach ($foundMetrc as $metrcItem) {
            $hotboxPackage = $hotboxPackages[array_search($metrcItem->Id, $hotboxMetrcIds)];
            $room = Room::where('id', $hotboxPackage->room_id)->first();
            if ($room) $roomMetrcId = $room->metrc_id;
            else $roomMetrcId = null;
            $product = Item::where('id', $hotboxPackage->product_id)->first();
            if ($product) $productMetrcId = $product->metrc_id;
            else $productMetrcId = null;
            if ($hotboxPackage->label != $metrcItem->Label || $hotboxPackage->package_type != $metrcItem->PackageType || $roomMetrcId != $metrcItem->RoomId || $hotboxPackage->quantity != $metrcItem->Quantity || $hotboxPackage->patient_license_number != $metrcItem->PatientLicenseNumber || $productMetrcId != $metrcItem->ProductId || $hotboxPackage->lab_testing_state != $metrcItem->LabTestingState) {
                $results[$metrcItem->Id]['metrc'] = $metrcItem;
                $results[$metrcItem->Id]['hotbox'] = $hotboxPackage;
            }
        }

        return $results;
    }

    //************************* Import from Metrc *************************//

    public function importMetrcRoom($data, $user)
    {
        $service = new RoomService();
        return $service->storeMetrc($data, $user);
    }

    public function importMetrcStrain($data, $user)
    {
        $service = new StrainService();
        return $service->storeMetrc($data, $user);
    }

    public function importMetrcItem($data, $user)
    {
        $service = new ItemService();
        return $service->storeMetrc($data, $user);
    }

    public function importMetrcPlantBatch($data, $user)
    {
        $service = new PlantBatchService();
        return $service->storeMetrc($data, $user);
    }

    public function importMetrcPlant($data, $user)
    {
        $service = new PlantService();
        return $service->storeMetrc($data, $user);
    }

    public function importMetrcHarvest($data, $user)
    {
        $service = new HarvestService();
        return $service->storeMetrc($data, $user);
    }

    public function importMetrcPackage($data, $user)
    {
        $service = new PackageService();
        return $service->storeMetrc($data, $user);
    }

    //************************* Export to Metrc *************************//

    public function exportHotboxRoom($data)
    {
        $metrc_id = $data['metrc_id'];
        $room = Room::where('metrc_id', $metrc_id)->first();
        MetrcRoomStore::dispatch($room, Auth::user());
    }

    public function exportHotboxStrain($data)
    {
        $metrc_id = $data['metrc_id'];
        $strain = Strain::where('metrc_id', $metrc_id)->first();
        MetrcStrainStore::dispatch($strain, Auth::user());
    }

    public function exportHotboxItem($data)
    {
        $metrc_id = $data['metrc_id'];
        $item = Item::where('metrc_id', $metrc_id)->first();
        MetrcItemStore::dispatch($item, Auth::user());
    }

    public function exportHotboxPlantBatch($data)
    {
        $metrc_id = $data['metrc_id'];
        $plantBatch = PlantBatch::where('metrc_id', $metrc_id)->first();
        MetrcPlantBatchStore::dispatch($plantBatch, Auth::user());
    }

    public function exportHotboxPlant($data)
    {
        $api = new MetrcPlantBatchesApi();
        $plantBatch = PlantBatch::where('id', $data['plant_batch_id'])->first();
        $room = Room::where('id', $data['room_id'])->first();
        $jsonItem = [];
        $jsonItem[] = [
            'Name' => $plantBatch->name,
            'Count' => 1,
            'StartingTag' => $data['label'],
            'GrowthPhase' => $data['growth_phase'],
            'NewRoom' => $room->name ? $room->name : null,
            'GrowthDate' => Carbon::parse($data['vegetative_at'])->toIso8601String(),
            'PatientLicenseNumber' => $data['patient_license_number']
        ];
        $api->changeGrowthPhase(Auth::user(), $jsonItem);
    }

    public function exportHotboxHarvest($data)
    { }

    public function exportHotboxPackage($data)
    { }

    //************************* Delete From Hotbox *************************//

    public function deleteHotboxRoom($data)
    {
        $room = Room::where('metrc_id', $data['metrc_id'])->first();
        $room->deleted_by = Auth::user()->id;
        $room->metrc_status = 'synced';
        $room->save();
        $room->delete();
    }

    public function deleteHotboxStrain($data)
    {
        $strain = Strain::where('metrc_id', $data['metrc_id'])->first();
        $strain->deleted_by = Auth::user()->id;
        $strain->metrc_status = 'synced';
        $strain->save();
        $strain->delete();
    }

    public function deleteHotboxItem($data)
    {
        $item = Item::where('metrc_id', $data['metrc_id'])->first();
        $item->deleted_by = Auth::user()->id;
        $item->metrc_status = 'synced';
        $item->save();
        $item->delete();
    }

    public function deleteHotboxPlantBatch($data)
    {
        $plantBatch = PlantBatch::where('metrc_id', $data['metrc_id'])->first();
        $plantBatch->deleted_by = Auth::user()->id;
        $plantBatch->metrc_status = 'synced';
        $plantBatch->save();
        $plantBatch->delete();
    }

    public function deleteHotboxPlant($data)
    {
        $plant = Plant::where('metrc_id', $data['metrc_id'])->first();
        $plant->deleted_by = Auth::user()->id;
        $plant->metrc_status = 'synced';
        $plant->save();
        $plant->delete();
    }

    public function deleteHotboxHarvest($data)
    {
        $harvest = Harvest::where('metrc_id', $data['metrc_id'])->first();
        $harvest->deleted_by = Auth::user()->id;
        $harvest->metrc_status = 'synced';
        $harvest->save();
        $harvest->delete();
    }

    public function deleteHotboxPackage($data)
    {
        $package = Package::where('metrc_id', $data['metrc_id'])->first();
        $package->deleted_by = Auth::user()->id;
        $package->metrc_status = 'synced';
        $package->save();
        $package->delete();
    }

    //************************* Update Hotbox Item to Match Metrc *************************//

    public function matchHotboxToMetrcRoom($data, $user)
    {
        $hotboxRoom = $data['hotbox'];
        $metrcRoom = $data['metrc'];
        $service = new RoomService();
        return $service->matchHotboxToMetrc($metrcRoom, $hotboxRoom['id'], $user);
    }

    public function matchHotboxToMetrcStrain($data, $user)
    {
        $hotboxStrain = $data['hotbox'];
        $metrcStrain = $data['metrc'];
        $service = new StrainService();
        return $service->matchHotboxToMetrc($metrcStrain, $hotboxStrain['id'], $user);
    }

    public function matchHotboxToMetrcItem($data, $user)
    {
        $hotboxItem = $data['hotbox'];
        $metrcItem = $data['metrc'];
        $service = new ItemService();
        return $service->matchHotboxToMetrc($metrcItem, $hotboxItem['id'], $user);
    }

    public function matchHotboxToMetrcPlantBatch($data, $user)
    {
        $hotboxPlantBatch = $data['hotbox'];
        $metrcPlantBatch = $data['metrc'];
        $service = new PlantBatchService();
        return $service->matchHotboxToMetrc($metrcPlantBatch, $hotboxPlantBatch['id'], $user);
    }

    public function matchHotboxToMetrcPlant($data, $user)
    {
        $hotboxPlant = $data['hotbox'];
        $metrcPlant = $data['metrc'];
        $service = new PlantService();
        return $service->matchHotboxToMetrc($metrcPlant, $hotboxPlant['id'], $user);
    }

    public function matchHotboxToMetrcHarvest($data, $user)
    {
        $hotboxHarvest = $data['hotbox'];
        $metrcHarvest = $data['metrc'];
        $service = new HarvestService();
        return $service->matchHotboxToMetrc($metrcHarvest, $hotboxHarvest['id'], $user);
    }

    public function matchHotboxToMetrcPackage($data, $user)
    {
        $hotboxPackage = $data['hotbox'];
        $metrcPackage = $data['metrc'];
        $service = new PackageService();
        return $service->matchHotboxToMetrc($metrcPackage, $hotboxPackage['id'], $user);
    }

    //************************* Update Metrc Item to Match Hotbox *************************//

    public function matchMetrcToHotboxRoom($data)
    {
        $hotboxRoom = Room::find($data['hotbox']['id']);
        MetrcRoomUpdate::dispatch($hotboxRoom, Auth::user());
    }

    public function matchMetrcToHotboxStrain($data)
    {
        $hotboxStrain = Strain::find($data['hotbox']['id']);
        MetrcStrainUpdate::dispatch($hotboxStrain, Auth::user());
    }

    public function matchMetrcToHotboxItem($data)
    {
        $hotboxItem = Item::find($data['hotbox']['id']);
        MetrcItemUpdate::dispatch($hotboxItem, Auth::user());
    }

    //************************* Delete From Metrc *************************//

    public function deleteMetrcRoom($data)
    {
        $api = new MetrcRoomApi();
        $api->deleteRooms(Auth::user(), $data['Id']);
    }

    public function deleteMetrcStrain($data)
    {
        $api = new MetrcStrainApi();
        $api->deleteStrains(Auth::user(), $data['Id']);
    }

    public function deleteMetrcItem($data)
    {
        $api = new MetrcItemApi();
        $api->deleteItems(Auth::user(), $data['Id']);
    }


    public function importAll(array $data)
    {
        $locationId = Auth::user()->location_id;
        $sectionName = $data['sectionName'];
        $startDate = null;
        $endDate = null;
        if ($data['startDate'] > 0 && $data['endDate'] > 0) {
            $startDate = $data['startDate'];
            $endDate = $data['endDate'];
        }

        switch ($sectionName) {
            case 'Rooms':
                \Artisan::call('SyncRooms ' . Auth::user()->id);
                break;
            case 'Strains':
                \Artisan::call('SyncStrains ' . Auth::user()->id);
                break;
            case 'Items':
                \Artisan::call('SyncItems ' . Auth::user()->id);
                break;
            case 'PlantBatches':
                \Artisan::call('SyncPlantBatches ' . Auth::user()->id . ' ' . $startDate . ' ' . $endDate);
                break;
            case 'Plants':
                \Artisan::call('SyncPlants ' . Auth::user()->id . ' ' . $startDate . ' ' . $endDate);
                break;
            case 'Harvests':
                \Artisan::call('SyncHarvests ' . Auth::user()->id . ' ' . $startDate . ' ' . $endDate);
                break;
            case 'Packages':
                \Artisan::call('SyncPackages ' . Auth::user()->id . ' ' . $startDate . ' ' . $endDate);
                break;
            default:
                throw new Exception('No Grow Section Specified');
        }

        return ['data' => 'true'];
    }


    public function getWeightAbbr($weightName)
    {
        switch ($weightName) {
            case 'Milligrams':
                return 'mg';
            case 'Grams':
                return 'g';
            case 'Ounces':
                return 'oz';
            case 'Kilograms':
                return 'kg';
            case 'Pounds':
                return 'lb';
            case 'Each':
                return 'ea';
            default:
                return false;
        }
    }
}
