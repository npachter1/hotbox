<?php

namespace App\Jobs\Metrc\PlantBatch;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Services\Metrc\MetrcRequestService;
use App\Models\Grow\Item;
use App\Models\Grow\Package;
use App\User;
use App\Services\Grow\PackageService;
use Exception;
use Carbon\Carbon;


class MetrcPlantBatchCreatePackage extends MetrcBaseJob {

    protected $count;
    protected $item_id;
    protected $package_tag;
    protected $patient_license_number;
    protected $packaged_date;

    public function __construct($items, $user, $count, $item_id, $package_tag,
                                $patient_license_number, $packaged_date)
    {
        $this->count = $count;
        $this->item_id = $item_id;
        $this->package_tag = $package_tag;
        $this->patient_license_number = $patient_license_number;
        $this->packaged_date = $packaged_date;
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

        $product_item = Item::findOrFail($this->item_id);

        if (!$this->test_mode) {
            $jsonItems = [];
            foreach($this->items as $item) {
                $jsonItems[] =
                    [
                        'Id' => $item->metrc_id,
                        'PlantBatch' => $item->name,
                        'Count' => $this->count,
                        'Room' => null,
                        'Item' => $product_item->name,
                        'Tag' => $this->package_tag,
                        'PatientLicenseNumber' => $this->patient_license_number,
                        'ActualDate' => Carbon::parse($this->packaged_date)->toIso8601String()
                    ];
            }

            $http_code = $api->createPackages($this->user, $jsonItems);

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
        
            // $api2 = new MetrcPackagesApi;
            foreach($this->items as $item) {
                // check that the new package label exists in Metrc
                $metrc_item = $api->getPackagesByLabel($this->user, $this->package_tag);
                if (!$metrc_item || !count($metrc_item)) {
                    foreach($this->items as $item) {
                        $item->metrc_status = 'error';
                        $item->save();
                    }
                    throw new Exception('Package Not Created Successfully');
                }
                // check that the plantbatch counts have been updated in Metrc
                $foundMetrc = false;
                $metrc_item = $api->getPlantBatches($this->user, $item->metrc_id);
                if ($metrc_item->Name === $item->name) {
                    if ($metrc_item->PackagedCount !== $item->packaged_count) {
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
                }
                if (!$foundMetrc) {
                    $item->metrc_status = 'error';
                    $item->save();
                    throw new Exception('Package Not Created Successfully');
                }
                // store the new package in hotbox
                $ps = new PackageService;
                $ps->storeMetrc($metrc_item);
                foreach($this->items as $item) {
                    $item->metrc_status = 'synced';
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
            // create the new package in hotbox
            $package = new Package();
            $package->label = $this->package_tag;
            $package->location_id = $this->user->location_id;
            $package->quantity = $this->count;
            $package->unit_of_measure = 'ea';
            $package->patient_license_number = $this->patient_license_number;
            $package->product_id = $this->item_id;
            $package->packaged_at = Carbon::parse($this->packaged_date)->toDateTimeString();
            $package->metrc_status = 'synced';
            $package->created_by = $this->user->id;
            $package->updated_by = $this->user->id;
            $package->save();
        }
        
        return $this->items;
    }
}
