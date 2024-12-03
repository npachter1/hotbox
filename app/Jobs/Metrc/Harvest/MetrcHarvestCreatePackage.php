<?php

namespace App\Jobs\Metrc\Harvest;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Models\METRC\MetrcHarvestApi;
use App\Models\METRC\MetrcPackageApi;
use App\Models\Grow\Harvest;
use App\Models\Grow\Item;
use App\Models\Grow\Package;
use App\Services\Grow\PackageService;
use Carbon\Carbon;
use Exception;

class MetrcHarvestCreatePackage extends MetrcBaseJob
{
    protected $label;
    protected $package_uom;
    protected $package_date;
    protected $item_id;
    protected $patient_license_number;
    protected $package_weights;

    public function __construct($items, $user, $label, $package_uom, $package_date, $item_id, $patient_license_number, $package_weights)
    {
        $this->label = $label;
        $this->package_uom = $package_uom;
        $this->package_date = $package_date;
        $this->item_id = $item_id;
        $this->patient_license_number = $patient_license_number;
        $this->package_weights = $package_weights;
        parent::__construct($items, $user);
    }
    /**
     * Execute the job.
     */
    public function handle(MetrcHarvestApi $api)
    {
        if (!$this->user->location || !$this->user->location->licensenum) {
            throw new Exception('User with ID '.$this->user->id.' does not have a location licensenum.');
        }

        $grow_item = Item::where('id',$this->item_id)->first();

        if (!$this->test_mode) {
            // check that package tag has not been used
            $api2 = new MetrcPackageApi;
            $package = $api2->getPackagesByLabel($this->user, $this->label);
            if ($package)
                throw new Exception('Package tag has already been used');

            $jsonItems = [];
            $jsonItems[] = [
                    'Tag' => $this->label,
                    'Room' => null,
                    'Item' => $grow_item->name,
                    'UnitOfWeight' => $this->package_uom,
                    'PatientLicenseNumber' => $this->patient_license_number,
                    'IsProductionBatch' => 'false',
                    'ProductionBatchNumber' => null,
                    'ProductRequiresRemediation' => 'false',
                    'RemediateProduct' => 'false',
                    'RemediationMethodId' => null,
                    'RemediationSteps' => null,
                    'ActualDate' => $this->package_date
            ];
            $ingredientsList = [];
            foreach($this->items as $harvest) {
                $ingredients = [
                    'HarvestId' => $harvest->metrc_id,
                    'HarvestName' => $harvest->name,
                    'Weight' => $this->package_weights[$harvest->id],
                    'UnitOfWeight' => $this->package_uom
                ];
                $ingredientsList[] = $ingredients;
            }
            $jsonItems[0]['Ingredients'] = $ingredientsList;

            $http_code = $api->createHarvestPackages($this->user, $jsonItems);

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
            // check that the new package label exists in Metrc
            $metrc_item = $api2->getPackagesByLabel($this->user, $this->label);
            if (!$metrc_item) {
                foreach($this->items as $item) {
                    $item->metrc_status = 'error';
                    $item->save();
                }
                throw new Exception('Package Not Created Successfully');
            }

            // check that the harvests have been updated in Metrc
            foreach($this->items as $harvest) {
                $metrc_item2 = $api->getHarvests($this->user, $harvest->metrc_id);
                if ($metrc_item2->CurrentWeight != $harvest->current_weight || $metrc_item2->TotalPackagedWeight != $harvest->total_packaged_weight || $metrc_item2->PackageCount != $harvest->package_count) {
                    foreach($this->items as $item) {
                        $item->metrc_status = 'error';
                        $item->save();
                    }
                    throw new Exception('Harvests Not Updated Successfully');
                }
            }
            // store the new package in hotbox
            $ps = new PackageService;
            foreach ($metrc_item as $key => $value)
            {
                $metrc_item_array[$key] = $value;
            }
            $ps->storeMetrc($metrc_item_array);

            foreach($this->items as $item) {
                $item->metrc_status = 'synced';
                $item->save();
            }
        }

        // test mode - no interaction with Metrc
        else {
            $source_harvest_names = [];
            $quantity = 0;
            foreach($this->items as $harvest) {
                $harvest->metrc_status = 'synced';
                $harvest->save();
                $source_harvest_names[] = $harvest->name;
                $quantity += $this->package_weights[$harvest->id];
            }

            // store the new package in hotbox
            $package = new Package();
            $package->label = $this->label;
            $package->location_id = $this->user->location_id;
            $package->package_type = 'Product';
            $package->source_harvest_names = json_encode($source_harvest_names);
            $package->quantity = $quantity;
            $package->unit_of_measure = $this->getWeightAbbr($this->package_uom);
            $package->patient_license_number = $this->patient_license_number;
            $package->product_id = $grow_item->id;
            $package->packaged_at = Carbon::parse($this->package_date)->toDateTimeString();
            $package->metrc_status = 'synced';
            $package->created_by = $this->user->id;
            $package->save();
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
