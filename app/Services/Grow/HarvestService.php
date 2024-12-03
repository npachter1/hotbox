<?php

namespace App\Services\Grow;

use App\Services\BaseService;

use App\Http\Resources\Grow\HarvestCollectionExport;

use App\Models\Auth\User;
use App\Models\Grow\Harvest;
use App\Models\Grow\Package;
use App\Models\Grow\Room;
use App\Helpers\Conversions;

use App\Services\Metrc\MetrcRequestService;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use Excel;
use Storage;


class HarvestService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;


    public function search(array $data){

        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);

        $query = Harvest::query()
            ->with('strain')
            ->with('room')
            ->with('location')
            ->with('plant','plant.strain','plant.plant_batch','plant.room')
            ->ofTextFilter($search)
            ->ofListFilters($filters)
            ->ofActive();
        
        if(empty($search)){
            $query->ofIsActive();
        }   
      

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('updated_at', 'desc');


        return $query->paginate($limit);

    }


    /* get harvest record */
    public function show(array $data,$id){

        return Harvest::query()
            ->where('id',$id)
            ->first();

    }


    public function destroy($data,$id,$user){

        if(($harvest = Harvest::find($id)) == null) abort(422,'Could not find Harvest to destroy - aborting');

        $harvest->metrc_status = 'synced';
        $harvest->save();

        return $harvest->name;

    }

    public function removeWaste(array $data)
    {
        if(($user = Auth::guard()->user()) == null) return 'USER_NOT_AUTHENTICATED';
        elseif(!$user->location) return 'NO_LOCATION_ASSIGNED';
        
        $remove_waste_date = array_get($data, 'remove_waste_date', null);  // date waste was removed
        $remove_waste_uom = array_get($data, 'remove_waste_uom', null);  // unit of measure for waste weights
        $harvest_ids = array_get($data, 'harvest_ids', null);   // array of ids of harvests having waste removed from them
        $waste_weights = array_get($data, 'waste_weights', null);   // array of weights to be removed from harvests
        $waste_types = array_get($data, 'waste_types', null);   // array of waste types to be removed from harvests
    
        if(!$harvest_ids || !count($harvest_ids)) {
            return;
        }
        
        $harvests = Harvest::whereIn('id', $harvest_ids)->get();
        $conv = new Conversions;
        $remove_waste_abbr = $conv->uomNameToAbbreviation($remove_waste_uom);
        
        // check that waste weight is not more than current weight
        foreach($harvests as $harvest) {
            $converted_waste_weight = $conv->convertWeight($waste_weights[$harvest->id], $remove_waste_abbr, $harvest->unit_of_weight, $user);
            if ($converted_waste_weight > $harvest->current_weight)
                throw new Exception('Waste weight cannot exceed current weight of harvest');
        }
        
        $saved = 0;
        foreach($harvests as $harvest) {
            $converted_waste_weight = $conv->convertWeight($waste_weights[$harvest->id], $remove_waste_abbr, $harvest->unit_of_weight, $user);
            $harvest->current_weight = $harvest->current_weight - $converted_waste_weight;
            $harvest->total_waste_weight = $converted_waste_weight + $harvest->total_waste_weight;
            $harvest->metrc_status = 'synced';
            $harvest->updated_by = $user->id;
            $harvest->save();
            $saved++;
        }
       
        // MetrcHarvestRemoveWaste::dispatch($harvests, $user, $remove_waste_date, $remove_waste_uom, $waste_weights, $waste_types);

        return [
            'status'    => ($saved==count($harvest_ids) ? 200 : 207),
            'message'   => ($saved!=count($harvest_ids) ? 'Updated '.$saved.' of '.count($harvest_ids).' Harvests - Please review updates below..' : 'Successfully Updated '.$saved.' of '.count($harvest_ids).' Harvests!'),
            'schema'    => Harvest::_getSchema()                            // include schema as these changes may update the scope of some filters.
        ];
    }
    
    public function finish(array $data, $user)
    {
        $finish_date = array_get($data, 'finish_date', null);   // date harvest was finished
        $harvest_ids = array_get($data, 'harvest_ids', null);   // array of ids of harvests to be finished
        
        if(!$harvest_ids || !count($harvest_ids)) {
            return;
        }
        
        $harvests = Harvest::whereIn('id', $harvest_ids)->get();
        $saved = 0;
        //TODO: Add waste_type to grow_harvest and build history table. Waste Type will be 'Water Evaporation'
        foreach($harvests as $harvest) {
            $harvest->finished_at = Carbon::parse($finish_date)->toDateTimeString();
            $harvest->total_waste_weight = $harvest->total_waste_weight + $harvest->current_weight;
            $harvest->current_weight = 0;
            $harvest->metrc_status = 'synced';
            $harvest->updated_by = $user->id;
            $harvest->save();
            $saved++;
        }

        return [
            'status'    => ($saved==count($harvest_ids) ? 200 : 207),
            'message'   => ($saved!=count($harvest_ids) ? 'Updated '.$saved.' of '.count($harvest_ids).' Harvests - Please review updates below..' : 'Successfully Updated '.$saved.' of '.count($harvest_ids).' Harvests!'),
            'schema'    => Harvest::_getSchema()                            // include schema as these changes may update the scope of some filters.
        ];
    }
    
    public function unfinish(array $data, $user)
    {
      
        $harvest_ids = array_get($data, 'harvest_ids', null);   // array of ids of harvests to be unfinished
        
        if(!$harvest_ids || !count($harvest_ids)) {
            return;
        }
        
        $harvests = Harvest::whereIn('id', $harvest_ids)->get();
        $saved = 0;
        foreach($harvests as $harvest) {
            $harvest->finished_at = null;
            $harvest->metrc_status = 'synced';
            $harvest->updated_by = $user->id;
            $harvest->save();
            $saved++;
        }

        return [
            'status'    => ($saved==count($harvest_ids) ? 200 : 207),
            'message'   => ($saved!=count($harvest_ids) ? 'Updated '.$saved.' of '.count($harvest_ids).' Harvests - Please review updates below..' : 'Successfully Updated '.$saved.' of '.count($harvest_ids).' Harvests!'),
            'schema'    => Harvest::_getSchema()                            // include schema as these changes may update the scope of some filters.
        ];
    }
    
    public function createPackage(array $data, $user)
    {
        
        $label = array_get($data, 'label', null);
        $package_uom = array_get($data, 'package_uom', null);
        $package_date = array_get($data, 'package_date', null);
        $item_id = array_get($data, 'item_id', null);
        $patient_license_number = array_get($data, 'patient_license_number', null);
        $harvest_ids = array_get($data, 'harvest_ids', null);
        $package_weights = array_get($data, 'package_weights');
        
        if(!$harvest_ids || !count($harvest_ids)) {
            return;
        }
        
        $harvests = Harvest::whereIn('id', $harvest_ids)->get();
        $conv = new Conversions;
        $package_weight_abbr = $conv->uomNameToAbbreviation($package_uom);

        $converted_package_weight = 0;
        $package_weight = 0;

        $source_harvest_names = array();
        // check that packaged weight is not more than current weight of harvest
        foreach($harvests as $harvest) {
            $package_weight += $package_weights[$harvest->id];
            $converted_weight = $conv->convertWeight($package_weights[$harvest->id], $package_weight_abbr, $harvest->unit_of_weight, $user);
            $source_harvest_names[] = $harvest->name;
            $converted_package_weight += $converted_weight;
            if ($converted_weight > $harvest->current_weight)
                throw new Exception('Packaged weight cannot exceed current weight of harvest');
        }


        $package = new Package();
        $package->label = $label;
        $package->location_id = $user->location_id;
        $package->package_type = 'Product';
        $package->source_harvest_names = rtrim(implode(',', $source_harvest_names), ',');
        $package->quantity = $package_weight;
        $package->unit_of_measure = $package_weight_abbr;
        $package->patient_license_number = $patient_license_number;
        $package->product_id = $item_id;
        $package->packaged_at = Carbon::parse($package_date)->toDateTimeString();
        $package->metrc_status = 'synced';
        $package->created_by = $user->id;
        $package->save();

        $saved = 0;
        foreach($harvests as $harvest) {
            $converted_package_weight = $conv->convertWeight($package_weights[$harvest->id], $package_weight_abbr, $harvest->unit_of_weight, $user);
            $harvest->current_weight = $harvest->current_weight - $converted_package_weight;
            $harvest->total_packaged_weight = $harvest->total_packaged_weight + $converted_package_weight;
            $harvest->package_count = $harvest->package_count + 1;
            $harvest->metrc_status = 'synced';
            $harvest->updated_by = $user->id;
            $harvest->save();
            $saved++;
        }
        
        return [
            'status'    => ($saved==count($harvest_ids) ? 200 : 207),
            'message'   => ($saved!=count($harvest_ids) ? 'Created '.$saved.' of '.count($harvest_ids).' Harvests - Please review updates below..' : 'Successfully Creaated '.$saved.' of '.count($harvest_ids).' Harvests!'),
            'schema'    => Harvest::_getSchema()                            // include schema as these changes may update the scope of some filters.
        ];
    }
    
    public function createPackages(array $data, $user) {
        
        $labels = array_get($data, 'labels', null);
        $package_uom = array_get($data, 'package_uom', null);
        $package_date = array_get($data, 'package_date', null);
        $item_id = array_get($data, 'item_id', null);
        $patient_license_number = array_get($data, 'patient_license_number', null);
        $harvest_id = array_get($data, 'harvest_id', null);
        $package_weights = array_get($data, 'package_weights');
        $package_count = array_get($data, 'package_count');
        
        $harvest = Harvest::where('id', $harvest_id)->first();
        $conv = new Conversions;
        $package_weight_abbr = $conv->uomNameToAbbreviation($package_uom);
        $converted_package_weight = ($package_count) * ($conv->convertWeight($package_weights, $package_weight_abbr, $harvest->unit_of_weight, $user));
        if ($converted_package_weight > $harvest->current_weight)
            throw new Exception('Packaged weight cannot exceed current weight of harvest');
        
        $harvest->current_weight = $harvest->current_weight - $converted_package_weight;
        $harvest->total_packaged_weight = $harvest->total_packaged_weight + $converted_package_weight;
        $harvest->package_count = $harvest->package_count + $package_count;
        $harvest->patient_license_number = $patient_license_number;
        $harvest->metrc_status = 'synced';

        $harvest->updated_by = $user->id;
        $harvest->save();

        // store the new packages in hotbox
        $saved = 0;
        $packages_ids = array();
        foreach($labels as $label) {
            $package = new Package();
            $package->label = $label;
            $package->location_id = $user->location_id;
            $package->package_type = 'Product';
            $package->source_harvest_names = $harvest->name;
            $package->quantity = $package_weights;
            $package->unit_of_measure = $package_weight_abbr;
            $package->patient_license_number = $patient_license_number;
            $package->product_id = $item_id;
            $package->packaged_at = Carbon::parse($package_date)->toDateTimeString();
            $package->metrc_status = 'synced';
            $package->created_by = $user->id;
            $package->save();
            $saved++;

            $packages_ids[] = $package->id;
        }
        
        return [
            'status'    => ($saved==count($packages_ids) ? 200 : 207),
            'message'   => ($saved!=count($packages_ids) ? 'Created '.$saved.' of '.count($packages_ids).' Packages - Please review updates below..' : 'Successfully Created '.$saved.' of '.count($packages_ids).' Packages!'),
            'schema'    => Harvest::_getSchema()                            // include schema as these changes may update the scope of some filters.
        ];
    }

    public function addMaterial(array $data,$id) {
        $user = Auth::guard()->user();
        $harvestMaterialRecord = new HarvestMaterialRecord();
        $harvestMaterialRecord->fill($data);
        $harvestMaterialRecord->harvest_id = $id;
        $harvestMaterialRecord->added_at = Carbon::parse($data['material_date'])->toDateTimeString();
        $harvestMaterialRecord->created_by = $user->id;
        $harvestMaterialRecord->save();

        return $harvestMaterialRecord;
    }

    /* update batch edit data */
    public function updateBatchData($data){

        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no Harvest batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach($batch as $row){

            if(!isset($row['id'])) continue;
            elseif(($store = Harvest::find($row['id'])) == null) continue;

            $store->fill($row);
            $store->save();
            $saved++;

        }


        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'Updated '.$saved.' of '.count($batch).' Records - Please check updates below..' : 'Successfully Updated '.$saved.' of '.count($batch).' Records!'),
            'schema'    => Harvest::_getSchema()                            // include schema as these batch changes may update the scope of some filters.
        ];

    }

    /* archive batch list */
    public function updateBatchArchive($data){

        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no Harvest batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach(Harvest::whereIn('id',$batch)->get() as $harvest){

            $harvest->archived_at = Carbon::now()->toDateTimeString();
            $harvest->save();
            $saved++;

        }


        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'Archived '.$saved.' of '.count($batch).' Records - Please review updates below..' : 'Successfully Updated '.$saved.' of '.count($batch).' Records!'),
            'schema'    => Harvest::_getSchema()                            // include schema as these batch changes may update the scope of some filters.
        ];

    }

        /* get a batch (abbreviated) collection */
    public function getBatch(array $data){

        $ids = explode(',',data_get($data,'batch_ids',''));

        $query = Harvest::query()
            ->whereIn('id',$ids)
            ->orderBy('updated_at')
            ->ofActive();


        return $query->get();

    }

    /* process an export */
    public function exportCollection($data,$typ,$file){

        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Harvest_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all')
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;


        return $this->export(new HarvestCollectionExport($data),$file,$name,$typ,[]);

    }

    public function storeMetrc(array $data, $user=null)
    {
        if (!$user)
            $user = Auth::guard()->user();

        $harvest = (Harvest::where('metrc_id',$data['Id'])->first() ?: new Harvest());
        $harvest->fill($data);
        $harvest->metrc_id = $data['Id'];
        $harvest->name = $data['Name'];
        $harvest->harvest_type = $data['HarvestType'];
        $harvest->source_strain_count = $data['SourceStrainCount'];
        $harvest->patient_license_number = $data['PatientLicenseNumber'];
        $harvest->current_weight = $data['CurrentWeight'];
        $harvest->total_waste_weight = $data['TotalWasteWeight'];
        $harvest->plant_count = $data['PlantCount'];
        $harvest->total_wet_weight = $data['TotalWetWeight'];
        $harvest->total_restored_weight = $data['TotalRestoredWeight'];
        $harvest->package_count = $data['PackageCount'];
        $harvest->total_packaged_weight = $data['TotalPackagedWeight'];
        $harvest->lab_testing_state = $data['LabTestingState'];
        $harvest->lab_testing_state_date = $data['LabTestingStateDate'];
        $harvest->is_on_hold = $data['IsOnHold'];
        $harvest->harvest_start_at = $data['HarvestStartDate'];
        $harvest->finished_at = $data['FinishedDate'];
        $harvest->archived_at = $data['ArchivedDate'];


        // get Hotbox Room id from metrc_id
        $drying_room_metrc_id = $data['DryingRoomId'];
        $drying_room_hotbox = Room::where('metrc_id', $drying_room_metrc_id)
                                    ->first();
        if (!$drying_room_hotbox) { throw new Exception('Cannot find room for this Harvest.'); }
        $harvest->drying_room_id = $drying_room_hotbox->id;

        $conv = new Conversions;
        $harvest->unit_of_weight = $conv->uomNameToAbbreviation($data['UnitOfWeightName']);

        $harvest->metrc_status = 'synced';
        $harvest->location_id = $user->location_id;
        $harvest->created_by = $user->id;
        $harvest->updated_by = $user->id;
        $harvest->save();

        return $harvest;
    }

}
