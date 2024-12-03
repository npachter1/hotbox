<?php

namespace App\Jobs\Metrc\Package;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Models\METRC\MetrcPackageApi;
use App\Models\Grow\Package;
use App\Models\Grow\Item;
use App\Services\Grow\PackageService;
use Exception;
use Carbon\Carbon;

class MetrcPackageCreatePackages extends MetrcBaseJob
{
    protected $itemSections;
    protected $packageUom;
    protected $packageDate;
    protected $patientLicenseNumber;
    protected $productRequiresRemediation;
    
    public function __construct($items, $user, $itemSections, $packageUom, $packageDate, $patientLicenseNumber, $productRequiresRemediation)
    {
        $this->itemSections = $itemSections;
        $this->packageUom = $packageUom;
        $this->packageDate = $packageDate;
        $this->patientLicenseNumber = $patientLicenseNumber;
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
        
        foreach($this->items as $item) {
            $source_package_label = $item->label;
            $source_package_quantity = $item->quantity;
        }
        
        if (!$this->test_mode) {
            $jsonItems = [];
            $i = 0;
            foreach($this->itemSections as $section) {
                $package_item = Item::where('id', $section['item_id'])->first();
                foreach($section['labels'] as $label) {
                    $jsonItems[] = [
                        'Tag' => $label,
                        'Room' => null,
                        'Item' => $package_item->name,
                        'Quantity' => $section['package_weights'],
                        'UnitOfMeasure' => $this->packageUom,
                        'PatientLicenseNumber' => $this->patientLicenseNumber,
                        'IsProductionBatch' => 'false',
                        'ProductionBatchNumber' => null,
                        'ProductRequiresRemediation' => $this->productRequiresRemediation,
                        'ActualDate' => $this->packageDate
                    ];
                    $ingredientsList = [];
                    $ingredients = [
                        'Package' => $source_package_label,
                        'Quantity' => $section['package_weights'],
                        'UnitOfMeasure' => $this->packageUom
                    ];
                    $ingredientsList[] = $ingredients;
                    $jsonItems[$i]['Ingredients'] = $ingredientsList;
                    $i++;
                }
            }

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
            // check that the new package labels exist in Metrc
            $package_not_found = 0;
            foreach($this->itemSections as $section) {
                foreach($section['labels'] as $label) {
                    $metrc_item = $api->getPackagesByLabel($this->user, $label);
                    if (!$metrc_item) {
                        foreach($this->items as $item) {
                            $item->metrc_status = 'error';
                            $item->save();
                        }
                        throw new Exception('Package '.$label. 'Not Created Successfully');
                    }
                }
            }

            // check that the source package quantity has been updated in Metrc
            $metrc_item2 = $api->getPackagesByLabel($this->user, $source_package_label);
            if ($metrc_item2->Quantity != $source_package_quantity) {
                foreach($this->items as $item) {
                    $item->metrc_status = 'error';
                    $item->save();
                }
                throw new Exception('Packages Not Updated Successfully');
            }
            // store the new packages in hotbox
            $ps = new PackageService;
            foreach($this->itemSections as $section) {
                foreach($section['labels'] as $label) {
                    $metrc_item = $api->getPackagesByLabel($this->user, $label);
                    foreach ($metrc_item as $key => $value)
                    {
                        $metrc_item_array[$key] = $value;
                    }
                    $ps->storeMetrc($metrc_item_array);
                }
            }
            foreach($this->items as $item) {
                $item->metrc_status = 'synced';
                $item->save();
            }
        }
        // test mode - no interaction with Metrc
        else {
            foreach($this->items as $item) {
                $source_harvest_names = $item->source_harvest_names;
                $item->metrc_status = 'synced';
                $item->save();
            }

            // store the new packages in hotbox
            foreach($this->itemSections as $section) {
                $package_item = Item::where('id', $section['item_id'])->first();
                foreach($section['labels'] as $label) {
                    $package = new Package();
                    $package->label = $label;
                    $package->location_id = $this->user->location_id;
                    $package->package_type = 'Product';
                    $package->source_harvest_names = $source_harvest_names;
                    $package->quantity = $section['package_weights'];
                    $package->unit_of_measure = $this->getWeightAbbr($this->packageUom);
                    $package->patient_license_number = $this->patientLicenseNumber;
                    $package->product_id = $package_item->id;
                    $package->packaged_at = Carbon::parse($this->packageDate)->toDateTimeString();
                    $package->metrc_status = 'synced';
                    $package->created_by = $this->user->id;
                    $package->save();
                }
            }
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
