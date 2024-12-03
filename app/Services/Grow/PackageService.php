<?php

namespace App\Services\Grow;

use App\Services\BaseService;

use App\Http\Resources\Grow\PackageCollectionExport;

use App\Models\Auth\User;
use App\Models\Grow\Package;
use App\Models\Grow\Room;
use App\Models\Grow\Item;
use App\Models\Dispensary\CategoryMetrc;
use App\Helpers\Conversions;

use App\Services\Metrc\MetrcRequestService;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use Excel;
use Storage;


class PackageService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;


    public function search(array $data){

        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);

        $query = Package::query()
            ->with('item')
            ->with('item.metrc_category')
            ->with('room')
            ->with('location')
            ->with('harvest')
            ->with('plant_batch')
            ->ofTextFilter($search)
            ->ofListFilters($filters)
            ->ofActive();

        if(empty($search))
        {
            $query->ofHasQuantity();
        }    

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('updated_at', 'desc');


        return $query->paginate($limit);

    }


    /* get package record */
    public function show(array $data,$id){

        return Package::query()
            ->with('item')
            ->with('plant_batch')
            ->where('id',$id)
            ->first();

    }

    /* update single batch edit data */
    public function update(array $data, $id)
    {
        if (!$id) { throw new Exception('Item ID not provided.'); }

        $user = Auth::guard()->user();

        $package = Package::find($id);
        if (!$package) { throw new Exception('Item not found.'); }

        if ($package->label !== $data['label']) {
            $item2 = Package::where('label', $data['label'])->where('location_id', $user->location_id)->get();
            if (count($item2) > 0)
                throw new Exception('Package with label '.$data['label'].' already exists');
        }

        $package->fill($data);
        $package->updated_by = Auth::user()->id;
        $package->metrc_status = 'synced';

        $package->save();
        
        return $package;
    }

    public function destroy($data,$id,$user){

        if(($package = Package::find($id)) == null) abort(422,'Could not find Package to destroy - aborting');

        $package->metrc_status = 'deleting';
        $package->save();

        return $package->name;

    }

    public function changeItem($data, $user){

        $id = array_get($data, 'package_id', null);
        $product_id = array_get($data, 'product_id', null);

        if(($package = Package::find($id)) == null) abort(422,'Could not find Package - aborting');
        if(($product = Item::find($product_id)) == null) abort(422,'Could not find Product - aborting');

        $package->product_id = $product->id;
        $package->updated_by = $user->id;
        $package->metrc_status = 'synced';
        $package->save();

        return $package;

    }

    public function remediate(array $data, $user)
    {
        $id = array_get($data, 'package_id', null);
        $remediation_at = array_get($data, 'remediation_at', null);

        $package = Package::find($id);
        if (!$package) { throw new Exception('Package not found.'); }

        $package->remediation_at = Carbon::parse($remediation_at)->toDateTimeString();
        $package->contains_remediated_product = 1;
        $package->metrc_status = 'synced';
        $package->save();
        // MetrcPackageRemediate::dispatch($package, $user, $data['remediation_method_name'], $data['remediation_at'], $data['remediation_steps']);

        return $package;
    }
    
    public function adjust(array $data, $user)
    {
        $id = array_get($data, 'package_id', null);
        $unit_of_measure = array_get($data, 'unit_of_measure', null);
        $quantity = array_get($data, 'quantity', null);
        $package = Package::find($id);
        if (!$package) { throw new Exception('Package not found.'); }
        

        $conv = new Conversions;
        $adjust_uom_abbr = $conv->uomNameToAbbreviation($unit_of_measure);
        $converted_adjust_qty = $conv->convertWeight($quantity, $adjust_uom_abbr, $package->unit_of_measure,$user);
        
        // check that adjustment quantity is not more than total package quantity
        if (($converted_adjust_qty + $package->quantity) < 0)
            throw new Exception('Cannot adjust package quantity below 0');

        $package->quantity += $converted_adjust_qty;
        $package->metrc_status = 'synced';
        $package->save();
        // MetrcPackageAdjust::dispatch($package, $user, $data['quantity'], $data['unit_of_measure'], $data['adjustment_date'], $data['adjustment_reason'], $data['reason_note']);

        return $package;
    }
    
    public function createPackage(array $data, $user) {
        
        $package_ids = array_get($data, 'package_ids', null);   // array of ids of source packages
        $package_weights = array_get($data, 'package_weights', null);   // array of weights added to new package
        $is_testing_sample = array_get($data, 'is_testing_sample', 0);
        
        if(!$package_ids || !count($package_ids)) {
            return;
        }
        
        $total_package_weight = 0;
        $source_harvest_names = array();
        $source_packages = Package::whereIn('id', $package_ids)->get();

        $conv = new Conversions;
        $package_weight_abbr = $conv->uomNameToAbbreviation($data['package_uom']);
        
        // check that package tag has not been used
        foreach($source_packages as $package) {
                $item2 = Package::where('label', $data['label'])->where('location_id', $user->location_id)->get();
                if (count($item2) > 0)
                    throw new Exception('Package with tag '.$data['label'].' already exists');

        }


        // unit of measure must match the category (each for CountBased, weights for WeightBased)
        $item = Item::find($data['item_id']);

        if(data_get($user->location,'settings.regulatory_agent','metrc')=='metrc'){
            $metrc_category = CategoryMetrc::find($item->metrc_category_id);
            if (($metrc_category->quantity_type == 'CountBased') && ($package_weight_abbr !== 'ea'))
                throw new Exception('Unit of Measure must match category type');
            if (($metrc_category->quantity_type == 'WeightBased') && ($package_weight_abbr == 'ea'))
                throw new Exception('Unit of Measure must match category type');
        }
        
        // check that packaged weights are not more than package quantites
        foreach($source_packages as $package) {
            $converted_package_weight = $conv->convertWeight($package_weights[$package->id], $package_weight_abbr, $package->unit_of_measure, $user);
            if ($converted_package_weight > $package->quantity)
                throw new Exception('Quantity packaged cannot exceed total package amount. Source package - '.$package->label);
        }
        
        foreach($source_packages as $package) {
            $converted_package_weight = $conv->convertWeight($package_weights[$package->id], $package_weight_abbr, $package->unit_of_measure,$user);
            $total_package_weight += $converted_package_weight;
            $source_harvest_names[] = $package->source_harvest_names;
            $package->quantity = $package->quantity - $converted_package_weight;
            $package->metrc_status = 'synced';
            $package->updated_by = $user->id;
            $package->save();
        }

        // store the new package in hotbox
        $package = new Package();
        $package->label = $data['label'];
        $package->location_id = $user->location_id;
        $package->package_type = 'Product';
        $package->is_testing_sample = $is_testing_sample;
        $package->source_harvest_names = rtrim(implode(',', $source_harvest_names), ',');
        $package->quantity = $total_package_weight;
        $package->unit_of_measure = $package_weight_abbr;
        $package->patient_license_number = $data['patient_license_number'];
        $package->product_id = $data['item_id'];
        $package->packaged_at = Carbon::parse($data['package_date'])->toDateTimeString();
        $package->metrc_status = 'synced';
        $package->created_by = $user->id;
        $package->save();
       
        // MetrcPackageCreatePackage::dispatch($source_packages, $user, $data['package_weights'], $data['label'], $data['package_uom'], $data['package_date'], $data['item_id'], $data['patient_license_number'], $data['is_production_batch'], $data['production_batch_number'], $data['product_requires_remediation']);
        // MetrcPackageCreateTestingPackage::dispatch($source_packages, $user, $data['package_weights'], $data['label'], $data['package_uom'], $data['package_date'], $data['item_id'], $data['patient_license_number'], $data['is_production_batch'], $data['production_batch_number'], $data['product_requires_remediation']);
        return $package;
    }
    
    // public function createTestingPackage(array $data, $user) {
        
    //     $package_ids = array_get($data, 'package_ids', null);   // array of ids of source packages
    //     $package_weights = array_get($data, 'package_weights', null);   // array of weights added to new package
        
    //     if(!$package_ids || !count($package_ids)) {
    //         return;
    //     }
        
    //     $source_packages = Package::whereIn('id', $package_ids)->get();
    //     $package_weight_abbr = $this->getWeightAbbr($data['package_uom']);
        
    //     // check that package tag has not been used
    //     $api = new MetrcPackageApi;
    //     $package = $api->getPackagesByLabel($user,$data['label']);
    //     if ($package)
    //         throw new Exception('Package tag has already been used');
        
    //     // unit of measure must match the category (each for CountBased, weights for WeightBased)
    //     $item = Item::find($data['item_id']);
    //     $metrc_category = MetrcCategory::find($item->metrc_category_id);
    //     if (($metrc_category->quantity_type == 'CountBased') && ($package_weight_abbr !== 'ea'))
    //         throw new Exception('Unit of Measure must match category type');
    //     if (($metrc_category->quantity_type == 'WeightBased') && ($package_weight_abbr == 'ea'))
    //         throw new Exception('Unit of Measure must match category type');
        
    //     // check that packaged weights are not more than package quantites
    //     foreach($source_packages as $package) {
    //         $converted_package_weight = $conv->convertWeight($package_weights[$package->id], $package_weight_abbr, $package->unit_of_measure);
    //         if ($converted_package_weight > $package->quantity)
    //             throw new Exception('Quantity packaged cannot exceed total package amount. Source package - '.$package->label);
    //     }
        
    //     foreach($source_packages as $package) {
    //         $converted_package_weight = $conv->convertWeight($package_weights[$package->id], $package_weight_abbr, $package->unit_of_measure);
    //         $package->quantity = $package->quantity - $converted_package_weight;
    //         $package->metrc_status = 'updating';
    //         $package->updated_by = $user->id;
    //         $package->save();
    //     }

    //     MetrcPackageCreateTestingPackage::dispatch($source_packages, $user, $data['package_weights'], $data['label'], $data['package_uom'], $data['package_date'], $data['item_id'], $data['patient_license_number'], $data['is_production_batch'], $data['production_batch_number'], $data['product_requires_remediation']);
    //     return $this->search($data);
    // }
    
    public function createPlantings(array $data, $id)
    {
        $user = Auth::guard()->user();
        $package = Package::find($id);
        if (!$package) { throw new Exception('Package not found.'); }
        
        // check that plant batch name hasn't been used
        $plantbatch = PlantBatch::where('name', $data['plant_batch_name'])->where('location_id',$user->location_id)->get();
        if (count($plantbatch) > 0)
            throw new Exception('Plant Batch '.$data['plant_batch_name'].' already exists');
        
        // check that package is of type Plants
        $product = Item::find($package->product_id);
        $metrc_category = MetrcCategory::find($product->metrc_category_id);
        $metrc_category_type = $metrc_category->product_category_type;
        if ($metrc_category_type !== 'Plants')
            throw new Exception('Package must contain plants or seeds to create plantings');
        
        // check that package adjustment amount won't result in quantity less than 0
        $adjustment_weight_abbr = $this->getWeightAbbr($data['unit_of_measure']);
        $converted_adjustment_amount = $conv->convertWeight($data['package_adjustment_amount'], $adjustment_weight_abbr, $package->unit_of_measure);
        if ($converted_adjustment_amount > $package->quantity)
            throw new Exception('Cannot adjust package quantity below 0');
        else
            $package->quantity = $package->quantity - $converted_adjustment_amount;

        $package->metrc_status = 'updating';
        $package->save();
        MetrcPackageCreatePlantings::dispatch($package, $user, $data['package_adjustment_amount'], $data['unit_of_measure'], $data['plant_batch_name'], $data['plant_batch_type'], $data['plant_count'], $data['room_name'], $data['strain_name'], $data['patient_license_number'], $data['planted_date'], $data['unpackaged_date']);

        return $package;
    }
    
    public function createPackages(array $data) {
        if(($user = Auth::guard()->user()) == null) return 'USER_NOT_AUTHENTICATED';
        elseif(!$user->location) return 'NO_LOCATION_ASSIGNED';
        
        $package_id = array_get($data, 'package_id', null);   // id of source package
        $item_sections = array_get($data, 'item_sections', null);
        $total_weight = 0;
        
        $source_package = Package::where('id', $package_id)->first();
        $conv = new Conversions;
        $package_weight_abbr = $conv->uomNameToAbbreviation($data['package_uom']);
        
        // check that package tags have not been used
        foreach($item_sections as $section) {
            foreach($section['labels'] as $label) {
  
                    $package = Package::where('label', $label)->where('location_id', $user->location_id)->first();
                    if ($package)
                        throw new Exception('Package with tag '.$label.' already exists');
                
            }
            if(data_get($user->location,'settings.regulatory_agent','metrc')=='metrc'){
                // unit of measure must match the category (each for CountBased, weights for WeightBased)
                $item = Item::find($section['item_id']);
                $metrc_category = CategoryMetrc::find($item->metrc_category_id);
                if (($metrc_category->quantity_type == 'CountBased') && ($package_weight_abbr !== 'ea'))
                    throw new Exception('Unit of Measure must match category type');
                if (($metrc_category->quantity_type == 'WeightBased') && ($package_weight_abbr == 'ea'))
                    throw new Exception('Unit of Measure must match category type');
            }
            
            $total_weight += $section['package_weights'] * $section['package_count'];
        }
        
        // check that packaged weights are not more than source package quantity
        $converted_package_weight = $conv->convertWeight($total_weight, $package_weight_abbr, $source_package->unit_of_measure,$user);
        if ($converted_package_weight > $source_package->quantity)
            throw new Exception('Quantity packaged cannot exceed total package amount. Source package - '.$source_package->label);
        
        $source_package->quantity = $source_package->quantity - $converted_package_weight;
        $source_package->metrc_status = 'synced';
        $source_package->updated_by = $user->id;
        $source_package->save();

        // store the new packages in hotbox
        foreach($item_sections as $section) {
            $package_item = Item::where('id', $section['item_id'])->first();
            foreach($section['labels'] as $label) {
                $package = new Package();
                $package->label = $label;
                $package->location_id = $user->location_id;
                $package->package_type = 'Product';
                $package->source_harvest_names = $source_package->source_harvest_names;
                $package->quantity = $section['package_weights'];
                $package->unit_of_measure = $package_weight_abbr;
                $package->patient_license_number = $data['patient_license_number'];
                $package->product_id = $package_item->id;
                $package->packaged_at = Carbon::parse($data['package_date'])->toDateTimeString();
                $package->metrc_status = 'synced';
                $package->created_by = $user->id;
                $package->save();
            }
        }
        
        // MetrcPackageCreatePackages::dispatch($source_package, $user, $item_sections, $data['package_uom'], $data['package_date'], $data['patient_license_number'], $data['product_requires_remediation']);
        return $this->search($data);
    }


    /* update batch edit data */
    public function updateBatchData($data){

        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no Packages batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach($batch as $row){

            if(!isset($row['id'])) continue;
            elseif(($store = Package::find($row['id'])) == null) continue;

            $store->fill($row);
            $store->save();
            $saved++;

        }


        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'Updated '.$saved.' of '.count($batch).' Records - Please check updates below..' : 'Successfully Updated '.$saved.' of '.count($batch).' Records!'),
            'schema'    => Package::_getSchema()                            // include schema as these batch changes may update the scope of some filters.
        ];

    }

    /* archive batch list */
    public function updateBatchArchive($data){

        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no Packages batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach(Package::whereIn('id',$batch)->get() as $package){

            $package->archived_at = Carbon::now()->toDateTimeString();
            $package->save();
            $saved++;

        }


        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'Archived '.$saved.' of '.count($batch).' Records - Please review updates below..' : 'Successfully Updated '.$saved.' of '.count($batch).' Records!'),
            'schema'    => Package::_getSchema()                            // include schema as these batch changes may update the scope of some filters.
        ];

    }

        /* get a batch (abbreviated) collection */
    public function getBatch(array $data){

        $ids = explode(',',data_get($data,'batch_ids',''));

        $query = Package::query()
            ->whereIn('id',$ids)
            ->orderBy('updated_at')
            ->ofActive();


        return $query->get();

    }

    /* process an export */
    public function exportCollection($data,$typ,$file){

        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Package_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all')
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;


        return $this->export(new PackageCollectionExport($data),$file,$name,$typ,[]);

    }

    public function storeMetrc(array $data, $user=null)
    {
        if (!$user)
            $user = Auth::user();

        $package = (Package::where('metrc_id',$data['Id'])->first() ?: new Package());
        $package->fill($data);
        $package->metrc_id = $data['Id'];
        $package->label = $data['Label'];
        $package->package_type = $data['PackageType'];
        $package->source_harvest_names = $data['SourceHarvestNames'];
        $package->quantity = $data['Quantity'];
        $package->unit_of_measure = $data['UnitOfMeasureAbbreviation'];
        $package->patient_license_number = $data['PatientLicenseNumber'];
        $package->packaged_at = $data['PackagedDate'];
        $package->is_production_batch = $data['IsProductionBatch'];
        $package->production_batch_number = $data['ProductionBatchNumber'];
        $package->is_testing_sample = $data['IsTestingSample'];
        $package->is_process_validation_testing_sample = $data['IsProcessValidationTestingSample'];
        $package->contains_remediated_product = $data['ContainsRemediatedProduct'];
        $package->remediation_at = $data['RemediationDate'];
        $package->received_from_manifest_number = $data['ReceivedFromManifestNumber'];
        $package->received_from_facility_license_number = $data['ReceivedFromFacilityLicenseNumber'];
        $package->received_from_facility_name = $data['ReceivedFromFacilityName'];
        $package->received_at = $data['ReceivedDateTime'];
        $package->is_on_hold = $data['IsOnHold'];
        $package->archived_at = $data['ArchivedDate'];
        $package->finished_at = $data['FinishedDate'];

        // get Hotbox Room id from metrc_id
        if ($data['RoomId']) {
            $room_metrc_id = $data['RoomId'];
            $room_hotbox = Room::where('metrc_id', $room_metrc_id)->first();
            if (!$room_hotbox) { throw new Exception('Cannot find room for this package.'); }
            $package->room_id = $room_hotbox->id;
        }

        // get Hotbox Item id from metrc_id
        $item_metrc_id = $data['ProductId'];
        $item_hotbox = Item::where('metrc_id', $item_metrc_id)->first();
        if (!$item_hotbox) {throw new Exception('Cannot find item for this package.'); }
        $package->product_id = $item_hotbox->id;

        $package->metrc_status = 'synced';
        $package->location_id = $user->location_id;
        $package->created_by = $user->id;
        $package->updated_by = $user->id;
        $package->save();

        return $package;
    }

}
