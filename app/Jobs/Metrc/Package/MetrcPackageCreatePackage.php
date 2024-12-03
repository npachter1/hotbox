<?php

namespace App\Jobs\Metrc\Package;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Models\METRC\MetrcPackageApi;
use App\Models\Grow\Package;
use App\Models\Grow\Item;
use App\Services\Grow\PackageService;
use Exception;
use Carbon\Carbon;

class MetrcPackageCreatePackage extends MetrcBaseJob
{
    protected $packageWeights;
    protected $label;
    protected $packageUom;
    protected $packageDate;
    protected $itemId;
    protected $patientLicenseNumber;
    protected $isProductionBatch;
    protected $productionBatchNumber;
    protected $productRequiresRemediation;

    public function __construct($items, $user, $packageWeights, $label, $packageUom, $packageDate, $itemId, $patientLicenseNumber, $isProductionBatch, $productionBatchNumber, $productRequiresRemediation)
    {
        $this->packageWeights = $packageWeights;
        $this->label = $label;
        $this->packageUom = $packageUom;
        $this->packageDate = $packageDate;
        $this->itemId = $itemId;
        $this->patientLicenseNumber = $patientLicenseNumber;
        if ($isProductionBatch == 1)
            $this->isProductionBatch = true;
        else
            $this->isProductionBatch = false;
        $this->productionBatchNumber = $productionBatchNumber;
        if ($productRequiresRemediation == 1)
            $this->productRequiresRemediation = true;
        else
            $this->productRequiresRemediation = false;
        parent::__construct($items, $user);
    }
    /**
     * Execute the job.
     */
    public function handle(MetrcPackageApi $api)
    {
        if (!$this->user->location || !$this->user->location->licensenum) {
            throw new Exception('User with ID '.$this->user->id.' does not have a location licensenum.');
        }
        
        $package_item = Item::where('id', $this->itemId)->first();
        $quantity = 0;
        foreach($this->items as $package) {
            $quantity += $this->packageWeights[$package->id];
        }
        
        if (!$this->test_mode) {
            $jsonItems = [];
            $jsonItems[] = [
                'Tag' => $this->label,
                'Room' => null,
                'Item' => $package_item->name,
                'Quantity' => $quantity,
                'UnitOfMeasure' => $this->packageUom,
                'PatientLicenseNumber' => $this->patientLicenseNumber,
                'IsProductionBatch' => $this->isProductionBatch,
                'ProductionBatchNumber' => $this->productionBatchNumber,
                'ProductRequiresRemediation' => $this->productRequiresRemediation,
                'ActualDate' => $this->packageDate
            ];
            $ingredientsList = [];
            foreach($this->items as $package) {
                $ingredients = [
                    'Package' => $package->label,
                    'Quantity' => $this->packageWeights[$package->id]+0,
                    'UnitOfMeasure' => $this->packageUom
                ];
                $ingredientsList[] = $ingredients;
            }
            $jsonItems[0]['Ingredients'] = $ingredientsList;

            $http_code = $api->createPackages($this->user, $jsonItems);

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
            $metrc_item = $api->getPackagesByLabel($this->user, $this->label);
            if (!$metrc_item) {
                foreach($this->items as $item) {
                    $item->metrc_status = 'error';
                    $item->save();
                }
                throw new Exception('Package Not Created Successfully');
            }
            // check that the source package quantities have been updated in Metrc
            foreach($this->items as $package) {
                $metrc_item2 = $api->getPackagesByLabel($this->user, $package->label);
                if ($metrc_item2->Quantity != $package->quantity) {
                    foreach($this->items as $item) {
                        $item->metrc_status = 'error';
                        $item->save();
                    }
                    throw new Exception('Packages Not Updated Successfully');
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
            foreach($this->items as $item) {
                $item->metrc_status = 'synced';
                $item->save();
                $source_harvest_names[] = $item->source_harvest_names;
            }
            
            // store the new package in hotbox
            $package = new Package();
            $package->label = $this->label;
            $package->location_id = $this->user->location_id;
            $package->package_type = 'Product';
            $package->source_harvest_names = json_encode($source_harvest_names);
            $package->quantity = $quantity;
            $package->unit_of_measure = $this->getWeightAbbr($this->packageUom);
            $package->patient_license_number = $this->patientLicenseNumber;
            $package->product_id = $package_item->id;
            $package->packaged_at = Carbon::parse($this->packageDate)->toDateTimeString();
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
