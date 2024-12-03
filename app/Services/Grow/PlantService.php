<?php

namespace App\Services\Grow;

use App\Services\BaseService;

use App\Http\Resources\Grow\PlantCollectionExport;

use App\Models\Auth\User;
use App\Models\Grow\Plant;
use App\Models\Grow\PlantBatch;
use App\Models\Grow\Strain;
use App\Models\Grow\Room;
use App\Models\Grow\Harvest;

use App\Services\Metrc\MetrcRequestService;
use App\Services\Grow\StrainService;
use App\Jobs\Metrc\Plant\MetrcPlantCreatePlantings;
use App\Jobs\Metrc\PlantBatch\MetrcPlantBatchStore;
use App\Helpers\Conversions;
use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use Excel;
use Storage;


class PlantService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;


    public function search(array $data){

        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);

        if(!isset($filters['state']))
        {
            $filters['state'] = 'Tracked';
        }
        if($filters['state'] == 'all')
        {
            $filters['state'] = 'Tracked';
        }

        $query = Plant::query()
            ->with('strain')
            ->with('room')
            ->with('plant_batch')
            ->with('location')
            ->ofTextFilter($search)
            ->ofListFilters($filters)
            ->ofActive();

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('updated_at', 'desc');


        return $query->paginate($limit);

    }


    /* get plant record */
    public function show(array $data,$id){

        return Plant::query()
            ->where('id',$id)
            ->with('strain')
            ->with('room')
            ->with('plant_batch')
            ->with('location')
            ->first();

    }

    public function update($data,$id){
        
        if(($upd = Plant::find($id)) == null) abort(400,'Whoops, Plant Data is temporarially disonnected - please try again later');

        $user = Auth::guard()->user();

        $item = Plant::find($id);
        if (!$item) { throw new Exception('Plant not found.'); }
        
        // if Plant label updated, check if new label already exists
        if ($item->label !== $data['label']) {
            $item2 = Plant::where('label', $data['label'])->where('location_id', $user->location_id)->get();
            if (count($item2) > 0)
                throw new Exception('Plant with tag '.$data['label'].' already exists');
        }
        
        $item->fill($data);
        
        $item->metrc_status = 'synced';
        $item->updated_by = $user->id;
        $item->save();
        
        return $item;
        
    }

    public function createPlantings(array $data)
    {
        if(($user = Auth::guard()->user()) == null) return 'USER_NOT_AUTHENTICATED';
        elseif(!$user->location) return 'NO_LOCATION_ASSIGNED';
        
        $id = array_get($data, 'source_plant_id', null);
        $plant_batch_name = array_get($data, 'plant_batch_name', null);
        $plant_batch_type = array_get($data, 'plant_batch_type', null);
        $plant_batch_label = array_get($data, 'plant_batch_label', null);
        $plant_count = array_get($data, 'plant_count', null);
        $patient_license_number = array_get($data, 'patient_license_number', null);
        $room_id = array_get($data, 'new_room', null);
        $strain_id = array_get($data, 'strain_id', null);
        $actual_date = array_get($data, 'actual_date', null);

        if(!empty($id))
        {
           $plant = Plant::find($id);
           if (!$plant) { throw new Exception('Plant not found.'); }
        }
        
        $room = Room::where('id',$room_id)->first();
        if ($room)
            $room_name = $room->name;
        else
            $room_name = null;

        $batch = PlantBatch::where('label',$plant_batch_label)->first();
        if ($batch) { throw new Exception('Plant Batch Label already assigned.'); }
 
        $strain = Strain::where('id',$strain_id)->first();
        $strain_name = $strain->name;
        
        if(!empty($id))
        {
            $plant->metrc_status = 'synced';
            $plant->save();
        }

        
        // create the new plant batch in hotbox
        $plantbatch = new PlantBatch();
        $plantbatch->name = $plant_batch_name;
        $plantbatch->label = $plant_batch_label;
        $plantbatch->type = $plant_batch_type;
        $plantbatch->location_id = $user->location_id;
        $plantbatch->room_id = $room ? $room->id : null;
        $plantbatch->strain_id = $strain ? $strain->id : null;
        $plantbatch->patient_license_number = $patient_license_number;
        $plantbatch->count = $plant_count;
        $plantbatch->live_count = 0;
        $plantbatch->packaged_count = 0;
        $plantbatch->harvested_count = 0;
        $plantbatch->destroyed_count = 0;
        $plantbatch->source_plant_id = !empty($id) ? $plant->id : null;
        $plantbatch->planted_at = Carbon::parse($actual_date)->toDateTimeString();
        $plantbatch->metrc_status = 'synced';
        $plantbatch->created_by = $user->id;
        $plantbatch->save();

        // MetrcPlantBatchStore::withChain([
        //     new MetrcPlantCreatePlantings($plant, $user, $plant_batch_name, $plant_batch_type, $plant_count, $patient_license_number, $room_name, $strain_name, $actual_date)
        // ])->dispatch($plantbatch,$user, $plant_batch_name, $plant_batch_type, $plant_count, $patient_license_number, $room_name, $strain_name, $actual_date);

        return $plantbatch;
    }

    public function destroy($data, $user){

        $reason_note = array_get($data, 'reason_note', null);
        $destroy_date = array_get($data, 'destroy_date', null);
        $plant_ids = array_get($data,'plant_ids',null);
        $saved = 0;
        foreach(Plant::whereIn('id',$plant_ids)->get() as $plant){

            $plant->metrc_status = 'synced';
            $plant->destroyed_at = Carbon::parse($destroy_date)->toDateTimeString();
            $plant->destroyed_by = $user->id;
            $plant->destroyed_note = $reason_note;
            $plant->state = "Destroyed";
            $plant->save();
            $saved++;

        }

        // $plants = Plant::whereIn('id',$plant_ids)->get();
        return [
            'status'    => ($saved==count($plant_ids) ? 200 : 207),
            'message'   => ($saved!=count($plant_ids) ? 'Updated '.$saved.' of '.count($plant_ids).' Plants - Please review updates below..' : 'Successfully Updated '.$saved.' of '.count($plant_ids).' Plants!'),
            'schema'    => Plant::_getSchema()                            // include schema as these changes may update the scope of some filters.
        ];

    }

    public function movePlants(array $data, $user)
    {

        $room_id = array_get($data, 'new_room', null); // id of the room plants are being moved to
        $moved_at = array_get($data, 'moved_at', null); // date plants are being moved
        $plant_ids = array_get($data, 'plant_ids', null); // an array of plant_id's to be moved

        if(!$plant_ids || !count($plant_ids)) {
            return;
        }

        $plants = Plant::whereIn('id', $plant_ids)->get();

        $plantMoves = [];
        $saved = 0;

        foreach($plants as $plant) {
            $plant->updated_by = $user->id;
            $plant->updated_at = Carbon::parse($moved_at)->toDateTimeString();;
            $plant->room_id = $room_id;
            $plant->save();
            $saved++;
        }

        return [
            'status'    => ($saved==count($plant_ids) ? 200 : 207),
            'message'   => ($saved!=count($plant_ids) ? 'Moved '.$saved.' of '.count($plant_ids).' Plants - Please review updates below..' : 'Successfully Updated '.$saved.' of '.count($plant_ids).' Plants!'),
            'schema'    => Plant::_getSchema()                            // include schema as these changes may update the scope of some filters.
        ];
    }

    public function changeGrowthPhase(array $data, $user)
    {

        $growth_phase = array_get($data, 'growth_phase', null); // growth phase eg. 'Vegetative', 'Flowering'
        $room_id = array_get($data, 'room_id', null); // id of the room plants are in or being moved to
        $growth_date = array_get($data, 'growth_date', null); // date plants are being changed
        $plant_ids = array_get($data, 'plant_ids', null); // an array of plant_id's to be changed
        // Don't allow new label assignment. Client prefers to destroy plant and create new if plant created without tag
 //       $new_labels = array_get($data, 'new_labels', null); // an array of new labels to assign to plants

        $plants = Plant::whereIn('id', $plant_ids)->get();

        // check if trying to move untagged plant to Flowering
        if ($growth_phase == 'Flowering') {
            foreach($plants as $plant) {
                if ((!$plant->label)) {
//                    if (isset($new_labels[$plant->id])) {
//                        if ($new_labels[$plant->id] == 0 || $new_labels[$plant->id] == '')
//                            throw new Exception('Plant with Metrc ID '.$plant->metrc_id.' requires a tag before being moved to Flowering phase.');
//                    }
//                    else {
                        throw new Exception('Plant with Metrc ID '.$plant->metrc_id.' requires a tag before being moved to Flowering phase.');
//                    }
                }
                $plant->flowering_at = Carbon::parse($growth_date)->toDateTimeString();
            }
        }

        $saved = 0;
        foreach($plants as $plant) {
            $plant->growth_phase = $growth_phase;
            $plant->room_id = $room_id;
//            if (isset($new_labels[$plant->id]))
//                if ($new_labels[$plant->id] !== 0 && $new_labels[$plant->id] !== '')
//                    $plant->label = $new_labels[$plant->id];
            $plant->updated_by = $user->id;
            $plant->metrc_status = 'synced';
            $plant->save();
            $saved++;
        }
        // MetrcPlantChangeGrowthPhase::dispatch($plants, $user, $growth_phase, $room_id, $growth_date, $plant_ids);
        return [
            'status'    => ($saved==count($plant_ids) ? 200 : 207),
            'message'   => ($saved!=count($plant_ids) ? 'Updated '.$saved.' of '.count($plant_ids).' Plants - Please review updates below..' : 'Successfully Updated '.$saved.' of '.count($plant_ids).' Plants!'),
            'schema'    => Plant::_getSchema()                            // include schema as these changes may update the scope of some filters.
        ];
    }
   
    public function manicure(array $data, $user) 
    {
        $plant_ids = array_get($data, 'plant_ids', null);
        $weights = array_get($data, 'weights', null);
        $unit_of_weight = array_get($data, 'unit_of_weight', null);
        $drying_room_id = array_get($data, 'drying_room_id', null);
        $harvest_name = array_get($data, 'harvest_name', null);
        $patient_license_number = array_get($data, 'patient_license_number', null);
        $actual_date = array_get($data, 'actual_date', null);

        $conv = new Conversions;

        $plants = Plant::whereIn('id', $plant_ids)->get();

        // check that the harvest name has not already been used
        $harvest = Harvest::where('name', $harvest_name)->where('location_id', $user->location_id)->get();
        if (count($harvest) > 0)
            throw new Exception('A Harvest with name '.$harvest_name.' already exists and cannot be used again');

        $strain_ids = [];
        $current_weight = 0;
        foreach($plants as $plant) {
            $plant->updated_by = $user->id;
            $plant->metrc_status = 'synced';
            $plant->save();
            $current_weight += $weights[$plant->id];
            if (!in_array($plant->strain_id, $strain_ids))
                $strain_ids[] = $plant->strain_id;
        }
        
        // store the new harvest in hotbox
        $harvest = new Harvest();
        $harvest->name = $harvest_name;
        $harvest->location_id = $user->location_id;
        $harvest->harvest_type = 'Product';
        $harvest->source_strain_count = count($strain_ids);
        $harvest->drying_room_id = $drying_room_id ? $drying_room_id : null;
        $harvest->patient_license_number = $patient_license_number;
        $harvest->current_weight = $current_weight;
        $harvest->total_wet_weight = $current_weight;
        $harvest->total_restored_weight = 0;
        $harvest->total_waste_weight = 0;
        $harvest->total_packaged_weight = 0;
        $harvest->package_count = 0;
        $harvest->plant_count = count($plant_ids);
        $harvest->unit_of_weight = $conv->uomNameToAbbreviation($unit_of_weight);
        $harvest->harvest_start_at = Carbon::parse($actual_date)->toDateTimeString();
        // TODO: Need way to track each harvest or manicure to the plant. Current model does not support multiple harvests or manicures
        // $harvest->source_plant_ids = json_encode($plant_ids);
        $harvest->metrc_status = 'synced';
        $harvest->created_by = $user->id;
        $harvest->updated_by = $user->id;
        $harvest->save();

        return $plants;
    }

    public function harvest(array $data, $user)
    {

        $plant_ids = array_get($data, 'plant_ids', null);
        $weights = array_get($data, 'weights', null);
        $trim_weights = array_get($data, 'trim_weights', null);
        $waste_weights = array_get($data, 'waste_weights', null);
        $unit_of_weight = array_get($data, 'unit_of_weight', null);
        $drying_room_id = array_get($data, 'drying_room_id', null);
        $harvest_name = array_get($data, 'harvest_name', null);
        $patient_license_number = array_get($data, 'patient_license_number', null);
        $actual_date = array_get($data, 'actual_date', null);

        $plants = Plant::whereIn('id', $plant_ids)->get();
        $conv = new Conversions;

        // check that the harvest name has not already been used
        $harvest = Harvest::where('name', $harvest_name)->where('location_id', $user->location_id)->get();
        if (count($harvest) > 0)
            throw new Exception('A Harvest with name '.$harvest_name.' already exists and cannot be used again');

        foreach($plants as $plant)
        {
            $plant->harvested_unit_of_weight = $conv->uomNameToAbbreviation($unit_of_weight);
            $plant->harvested_wet_weight = $weights[$plant->id];
            $plant->harvested_at = Carbon::parse($actual_date)->toDateTimeString();
            $plant->updated_by = $user->id;
            $plant->metrc_status = 'synced';
            $plant->save();
        }

        $room = Room::where('id',$drying_room_id)->first();
        if ($room)
            $drying_room_name = $room->name;
        else
            $drying_room_name = null;

            $strain_ids = [];
            $current_weight = 0;
            $trim_weight = 0;
            $waste_weight = 0;
            foreach($plant_ids as $id) {
                $plant = Plant::find($id);
                $plant->metrc_status = 'synced';
                $plant->save();
                $current_weight += $weights[$id];
                $trim_weight += $trim_weights[$id];
                $waste_weight += $waste_weights[$id];
                if (!in_array($plant->strain_id, $strain_ids))
                    $strain_ids[] = $plant->strain_id;
            }
            
            $room = Room::where('name', $drying_room_name)->where('location_id', $user->location_id)->first();
            
            // store the new harvest in hotbox
            $harvest = new Harvest();
            $harvest->name = $harvest_name;
            $harvest->location_id = $user->location_id;
            $harvest->harvest_type = 'Product';
            $harvest->source_strain_count = count($strain_ids);
            $harvest->drying_room_id = $room ? $room->id : null;
            $harvest->patient_license_number = $patient_license_number;
            $harvest->current_weight = $current_weight;
            $harvest->total_wet_weight = $current_weight + $trim_weight + $waste_weight;
            $harvest->total_restored_weight = 0;
            $harvest->total_trim_weight = $trim_weight;
            $harvest->total_waste_weight = $waste_weight;
            $harvest->total_packaged_weight = 0;
            $harvest->package_count = 0;
            $harvest->plant_count = count($plant_ids);
            // TODO: Does this need to be done?
            // $harvest->source_plant_ids = json_encode($plant_ids);
            $harvest->total_packaged_weight = 0;
            $harvest->unit_of_weight = $conv->uomNameToAbbreviation($unit_of_weight);
            $harvest->harvest_start_at = Carbon::parse($actual_date)->toDateTimeString();
            $harvest->metrc_status = 'synced';
            $harvest->created_by = $user->id;
            $harvest->updated_by = $user->id;
            $harvest->save();
            $new_harvest = Harvest::where('name', $harvest_name)->where('location_id', $user->location_id)->first();
            
            $saved = 0;
            foreach($plant_ids as $plant_id) {
                $plant = Plant::find($plant_id);
                $plant->state = 'Harvested';
                $plant->harvest_id = $new_harvest->id;
                $plant->save();
                $saved++;
            }

            return [
                'status'    => ($saved==count($plant_ids) ? 200 : 207),
                'message'   => ($saved!=count($plant_ids) ? 'Updated '.$saved.' of '.count($plant_ids).' Plants - Please review updates below..' : 'Successfully Updated '.$saved.' of '.count($plant_ids).' Plants!'),
                'schema'    => Plant::_getSchema()                            // include schema as these changes may update the scope of some filters.
            ];
    }

    /* get a batch (abbreviated) collection */
    public function getBatch(array $data){

        $ids = explode(',',data_get($data,'batch_ids',''));

        $query = Plant::query()
            ->whereIn('id',$ids)
            ->orderBy('updated_at')
            ->ofActive();


        return $query->get();

    }


    /* process an export */
    public function exportCollection($data,$typ,$file){

        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Plant_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all')
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;


        return $this->export(new PlantCollectionExport($data),$file,$name,$typ,[]);

    }

    public function storeMetrc(array $data, $user=null)
    {
        if (!$user)
            $user = Auth::guard()->user();

        $plant = (Plant::where('metrc_id',$data['Id'])->first() ?: new Plant()) ;

        $plant->metrc_id = $data['Id'];
        $plant->label = $data['Label'];
        $plant->state = $data['State'];
        $plant->growth_phase = $data['GrowthPhase'];

        if ($data['PlantBatchId']) {
            $plantBatch = PlantBatch::findByMetrcId($data['PlantBatchId']);
            if (!$plantBatch) {
                $plant->plant_batch_id = null;
            } else
                $plant->plant_batch_id = $plantBatch->id;
        }
        if ($data['StrainId']) {
            $strain = Strain::findByMetrcId($data['StrainId']);
            if (!$strain) {
                $strain_api  = new MetrcRequestService();
                $metrc_strain = $strain_api->getStrains($user, $data['StrainId']);
                if ($metrc_strain) {
                    // add Metrc Strain to Hotbox
                    $strain_service = new StrainService();
                    $strain_array = json_decode(json_encode($metrc_strain), true);
                    $strain_service->storeMetrc($strain_array, $user);
                    $new_strain = Strain::where('metrc_id', $data['StrainId'])->first();
                    $plant->strain_id = $new_strain->id;
                }
                // if Strain not in Metrc or Hotbox
                else {
                    $plant->strain_id = null;
                }
            } else
                $plant->strain_id = $strain->id;
        }
        if ($data['RoomId']) {
            $room = Room::findByMetrcId($data['RoomId']);
            if (!$room) {
                $plant->room_id = null;
            } else
                $plant->room_id = $room->id;
        }
        if ($data['HarvestId']) {
            $harvest = Harvest::findByMetrcId($data['HarvestId']);
            if (!$harvest) {  // TODO: import Harvest from metrc
                $plant->harvest_id = null;
            } else
                $plant->harvest_id = $harvest->id;
        }
        $plant->patient_license_number = $data['PatientLicenseNumber'];
        $plant->harvested_unit_of_weight = $data['HarvestedUnitOfWeightAbbreviation'] ? $data['HarvestedUnitOfWeightAbbreviation'] : 'g';
        $plant->harvested_wet_weight = $data['HarvestedWetWeight'];
        $plant->is_on_hold = $data['IsOnHold'];
        $plant->planted_at = $data['PlantedDate'];
        $plant->vegetative_at = $data['VegetativeDate'];
        $plant->flowering_at = $data['FloweringDate'];
        $plant->harvested_at = $data['HarvestedDate'];
        $plant->destroyed_at = date('Y-m-d h:i:s', strtotime($data['DestroyedDate']));
        $plant->last_modified = date('Y-m-d h:i:s', strtotime($data['LastModified']));

        $plant->metrc_status = 'synced';
        $plant->location_id = $user->location_id;
        $plant->created_by = $user->id;
        $plant->updated_by = $user->id;
        $plant->save();

        return $plant;
    }

}
