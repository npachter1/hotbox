<?php

namespace App\Services\Grow;

use App\Services\BaseService;

use App\Http\Resources\Grow\PlantBatchCollectionExport;

use App\Models\Auth\User;
use App\Models\Grow\PlantBatch;
use App\Models\Grow\Strain;
use App\Models\Grow\Room;
use App\Models\Grow\Plant;
use App\Models\Grow\Package;

use App\Services\Metrc\MetrcRequestService;
use App\Services\Grow\StrainService;

use App\Jobs\Metrc\PlantBatch\MetrcPlantBatchChangeGrowthPhase;
use App\Jobs\Metrc\Plant\MetrcPlantCreateByPlantBatch;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use Excel;
use Storage;
use DB;


class PlantBatchService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;


    public function search(array $data){

        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);

        $query = PlantBatch::query()
            ->with('strain')
            ->with('room')
            ->with('source_package')
            ->with('source_plant')
            ->ofTextFilter($search)
            ->ofListFilters($filters)
            ->ofActive();

        if(empty($search))
        {
            $query->ofHasCount();
        }        

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('updated_at', 'desc');


        return $query->paginate($limit);

    }


    /* get plant batch record */
    public function show(array $data,$id){

        return PlantBatch::query()
            ->where('id',$id)
            ->with('strain')
            ->with('room')
            ->with('location')
            ->with('source_package')
            ->with('source_plant')
            ->first();

    }


    public function destroy($data,$id,$user){

        if(($plantbatch = PlantBatch::find($id)) == null) abort(422,'Could not find Plant Batch to destroy - aborting');

        $plantbatch->metrc_status = 'synced';
        if ($data['destroy_count'] > $plantbatch->count) { throw new Exception('Cannot destroy more plants than available.');}
        $plantbatch->destroyed_count = $plantbatch->destroyed_count + $data['destroy_count'];
        $plantbatch->count = $plantbatch->count - $data['destroy_count'];
        $plantbatch->updated_by = Auth::user()->id;
        $plantbatch->save();

        return $plantbatch;

    }

    /* update single batch edit data */
    public function update(array $data, $id)
    {
        if (!$id) { throw new Exception('Item ID not provided.'); }

        $user = Auth::guard()->user();

        $plantBatch = PlantBatch::find($id);
        if (!$plantBatch) { throw new Exception('Item not found.'); }

        if ($plantBatch->label !== $data['label']) {
            $item2 = PlantBatch::where('label', $data['label'])->where('location_id', $user->location_id)->get();
            if (count($item2) > 0)
                throw new Exception('Plant Batch with tag '.$data['label'].' already exists');
        }

        $plantBatch->fill($data);
        $plantBatch->updated_by = Auth::user()->id;
        $plantBatch->metrc_status = 'synced';

        $plantBatch->save();
        
        return $plantBatch;
    }

    public function changeGrowthPhase(array $data,$id,$user) {

        $plantBatch = PlantBatch::find($id);
        if(!$plantBatch) { throw new Exception('Plant Batch not found.'); }

        if ($data['count'] > $plantBatch->count) { throw new Exception('Cannot move more plants than available.');}
        
        // can't move an untagged plant to Flowering
        if (($data['growth_phase'] == 'Flowering') && (!$data['starting_tag']))
            throw new exception ('Plants require a Tag before being moved into the Flowering phase.');

        //Check to make sure tags aren't reused - if any exception rollback everything
        $strain = Strain::where('id', $plantBatch->strain_id)->first();
        $room = Room::where('id', $data['new_room'])->first();
        $tag = $data['starting_tag'];
        DB::beginTransaction();
        try{
            for($i=0;$i<$data['count'];$i++) {
                $plant = new Plant();
                $plant->label = $tag;
                $plant->growth_phase = $data['growth_phase'];
                $plant->state = 'Tracked';
                $plant->plant_batch_id = $plantBatch->id;
                $plant->location_id = $user->location_id;
                $plant->planted_at = $plantBatch->planted_at;
                if($data['growth_phase'] === 'Vegetative')
                {
                    $plant->vegetative_at = Carbon::parse($data['growth_date'])->toDateTimeString(); 
                }
                if($data['growth_phase'] === 'Flowering')
                {
                    $plant->flowering_at = Carbon::parse($data['growth_date'])->toDateTimeString(); 
                }
                $plant->patient_license_number = $data['patient_license_number']; //TODO update to ID
                $plant->strain_id = $strain ? $strain->id : null;
                $plant->room_id = $room ? $room->id : null;
                $plant->metrc_status = 'synced';
                $plant->created_by = $user->id;
                $plant->save();
                $tag++;
            }

            $plantBatch->metrc_status = 'synced';
            $plantBatch->live_count = $plantBatch->live_count + $data['count'];
            $plantBatch->count = $plantBatch->count - $data['count'];
            $plantBatch->updated_by = $user->id;
            $plantBatch->save();
        }
        catch(\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            //TODO: Maybe write a QueryException parser so we can return sensible error messages
            throw $e;
        }
        catch(\Exception $e)
        {
            DB::rollback();
            throw $e;
        }

        DB::commit();

        // MetrcPlantBatchChangeGrowthPhase::withChain([
        //     new MetrcPlantCreateByPlantBatch($plants,$user)])->dispatch($plantBatch,$user,$data['count'], $data['growth_date'], $data['new_room'],
        //     $data['growth_phase'], $data['starting_tag'], (isset($data['patient_license_number']) ? $data['patient_license_number'] : null));


        return $plantBatch;
    }

    public function createPackage(array $data) {
        if(($user = Auth::guard()->user()) == null) return 'USER_NOT_AUTHENTICATED';
        elseif(!$user->location) return 'NO_LOCATION_ASSIGNED';

        $id = array_get($data, 'batch_id', null);
        $label = array_get($data, 'label', null);
        $count = array_get($data, 'count', null);
        $patient_license_number = array_get($data, 'patient_license_number', null);
        $item_id = array_get($data, 'item_id', null);
        $packaged_date = array_get($data, 'packaged_date', null);

        $plantBatch = PlantBatch::find($id);
        if(!$plantBatch) { throw new Exception('Plant Batch not found.'); }

        if ($data['count'] > $plantBatch->count) { throw new Exception('Cannot package more plants than available.');}
        
        DB::beginTransaction();

        try {

            // create the new package in hotbox
            $package = new Package();
            $package->label = $label;
            $package->plant_batch_id = $id;
            $package->location_id = $user->location_id;
            $package->quantity = $count;
            $package->unit_of_measure = 'ea';
            $package->patient_license_number = $patient_license_number;
            $package->product_id = $item_id;
            $package->packaged_at = Carbon::parse($packaged_date)->toDateTimeString();
            $package->metrc_status = 'synced';
            $package->created_by = $user->id;
            $package->updated_by = $user->id;
            $package->save();

            $plantBatch->metrc_status = 'synced';
            $plantBatch->packaged_count = $plantBatch->packaged_count + $count;
            $plantBatch->count = $plantBatch->count - $count;
            $plantBatch->updated_by = $user->id;
            $plantBatch->save();
        }
        catch(\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            //TODO: Maybe write a QueryException parser so we can return sensible error messages
            throw $e;
        }
        catch(\Exception $e)
        {
            DB::rollback();
            throw $e;
        }

        DB::commit();

        // MetrcPlantBatchCreatePackage::dispatch($plantBatch,$user,$data['count'], $data['item_id'], $data['label'],
        //     $data['patient_license_number'], $data['packaged_date']);

        return $plantBatch;
    }

    public function splitBatch(array $data)
    {
        if(($user = Auth::guard()->user()) == null) return 'USER_NOT_AUTHENTICATED';
        elseif(!$user->location) return 'NO_LOCATION_ASSIGNED';
        
        $id = array_get($data, 'source_batch_id', null);
        $destroyed_count = array_get($data, 'destroyed_count', null);

        $sourceBatch = PlantBatch::find($id);
        if (!$sourceBatch) { throw new Exception('Source Batch not found.'); }


        $section = array_get($data, 'item_sections', null);

        $total_count = 0;
        foreach($section as $row){

            $plant_batch_label = array_get($row, 'plant_batch_label', null);
            $plant_count = array_get($row, 'plant_count', null);

            $total_count +=  $plant_count;

            $batch = PlantBatch::where('label',$plant_batch_label)->first();
            if ($batch) { throw new Exception('Plant Batch Label already assigned.'); }

        }
        if (($total_count + $destroyed_count) > $sourceBatch->count){ throw new Exception('Plantings count exceeds Source Batch Count'); }


        foreach($section as $row){

            $plant_batch_name = array_get($row, 'plant_batch_name', null);
            $plant_batch_type = array_get($row, 'plant_batch_type', null);
            $plant_batch_label = array_get($row, 'plant_batch_label', null);
            $plant_count = array_get($row, 'plant_count', null);
            $patient_license_number = array_get($row, 'patient_license_number', null);
            $room_id = array_get($row, 'room_id', null);
            $strain_id = array_get($row, 'strain_id', null);
            $actual_date = array_get($row, 'actual_date', null);


            
            $room = Room::where('id',$room_id)->first();
            if ($room)
                $room_name = $room->name;
            else
                $room_name = null;

            $batch = PlantBatch::where('label',$plant_batch_label)->first();
            if ($batch) { throw new Exception('Plant Batch Label already assigned.'); }
    
            $strain = Strain::where('id',$strain_id)->first();
            $strain_name = $strain->name;
           
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
            $plantbatch->source_plant_id = $sourceBatch->source_plant_id;
            $plantbatch->planted_at = Carbon::parse($actual_date)->toDateTimeString();
            $plantbatch->metrc_status = 'synced';
            $plantbatch->created_by = $user->id;
            $plantbatch->save();
        }

        $sourceBatch->destroyed_count += $destroyed_count;
        $sourceBatch->count -= ($total_count + $destroyed_count);
        $sourceBatch->updated_by = $user->id;
        $sourceBatch->save();

        // MetrcPlantBatchStore::withChain([
        //     new MetrcPlantCreatePlantings($plant, $user, $plant_batch_name, $plant_batch_type, $plant_count, $patient_license_number, $room_name, $strain_name, $actual_date)
        // ])->dispatch($plantbatch,$user, $plant_batch_name, $plant_batch_type, $plant_count, $patient_license_number, $room_name, $strain_name, $actual_date);

        return $sourceBatch;
    }

    /* update multiple batch edit data */
    public function updateBatchData($data){

        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no Plant Batch batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach($batch as $row){

            if(!isset($row['id'])) continue;
            elseif(($store = PlantBatch::find($row['id'])) == null) continue;

            $store->fill($row);
            $store->save();
            $saved++;

        }


        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'Updated '.$saved.' of '.count($batch).' Records - Please check updates below..' : 'Successfully Updated '.$saved.' of '.count($batch).' Records!'),
            'schema'    => PlantBatch::_getSchema()                            // include schema as these batch changes may update the scope of some filters.
        ];

    }

    /* archive batch list */
    public function updateBatchArchive($data){

        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no Plant Batch batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach(PlantBatch::whereIn('id',$batch)->get() as $plantbatch){

            $plantbatch->archived_at = Carbon::now()->toDateTimeString();
            $plantbatch->save();
            $saved++;

        }


        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'Archived '.$saved.' of '.count($batch).' Records - Please review updates below..' : 'Successfully Updated '.$saved.' of '.count($batch).' Records!'),
            'schema'    => PlantBatch::_getSchema()                            // include schema as these batch changes may update the scope of some filters.
        ];

    }


    /* process an export */
    public function exportCollection($data,$typ,$file){

        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_PlantBatch_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all')
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;


        return $this->export(new PlantBatchCollectionExport($data),$file,$name,$typ,[]);

    }

    public function storeMetrc(array $data, $user=null)
    {
        if (!$user)
            $user = Auth::guard()->user();

        $plantBatch = (PlantBatch::where('metrc_id',$data['Id'])->first() ?: new PlantBatch());

        $plantBatch->metrc_id = $data['Id'];
        $plantBatch->name = $data['Name'];
        $plantBatch->planted_at = $data['PlantedDate'];

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
                    $plantBatch->strain_id = $new_strain->id;
                }
                // if Strain not in Metrc or Hotbox
                else {
                    $plantBatch->strain_id = null;
                }
            } else
                $plantBatch->strain_id = $strain->id;
        }

        $plantBatch->type = $data['Type'];

        if ($data['RoomId']) {
            $room = Room::findByMetrcId($data['RoomId']);
            if (!$room) {
                throw new Exception('Cannot find room for this plant batch.');
            }
            $plantBatch->room_id = $room->id;
        }

        $plantBatch->count = $data['Count'];
        $plantBatch->patient_license_number = $data['PatientLicenseNumber'];
        $plantBatch->live_count = $data['LiveCount'];
        $plantBatch->packaged_count = $data['PackagedCount'];
        $plantBatch->harvested_count = $data['HarvestedCount'];
        $plantBatch->destroyed_count = $data['DestroyedCount'];

        if ($data['SourcePlantId']) {
            $plant = Plant::findByMetrcId($data['SourcePlantId']);
            if (!$plant) {
                $plantBatch->source_plant_id = null;
            } else
                $plantBatch->source_plant_id = $plant->id;
        }

  /*      if ($data['SourcePackageId']) {
            $package = Package::findByMetrcId($data['SourcePackageId']);
            if (!$package) {
                throw new Exception('Cannot find source package for this plant batch.');
            } else
                $plantBatch->source_package_id = $package->id;
        }       */

        $plantBatch->metrc_status = 'synced';
        $plantBatch->location_id = $user->location_id;
        $plantBatch->created_by = $user->id;
        $plantBatch->updated_by = $user->id;
        $plantBatch->save();

        return $plantBatch;
    }

}
