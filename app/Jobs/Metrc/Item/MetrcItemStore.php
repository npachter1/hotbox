<?php

namespace App\Jobs\Metrc\Item;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Services\Metrc\MetrcRequestService;
use Exception;
use App\Models\Grow\Strain;
use App\Models\Dispensary\CategoryMetrc;
use App\Helpers\Conversions;

class MetrcItemStore extends MetrcBaseJob
{

    protected $metrc_category;
    protected $strain;

    public function __construct($items, $user, $metrc_category, $strain)
    {
        $this->metrc_category = $metrc_category;
        $this->strain = $strain;
        parent::__construct($items, $user);
    }
    /**
     * Execute the job.
     */
    public function handle(MetrcRequestService $api)
    {
        if (!$this->user->location || !$this->user->location->licensenum) {
            throw new Exception('User with ID '.$this->user->id.' does not have a location licensenum.');
        }

        if (!$this->test_mode) {
            $jsonItems = [];
            foreach($this->items as $item) {
                
                $unit_of_measure = Conversions::uomAbbreviationToName($item->unit_of_measure);
                $unit_cbd_content_unit_of_measure = Conversions::uomAbbreviationToName($item->unit_cbd_content_unit_of_measure);
                $unit_thc_content_unit_of_measure = Conversions::uomAbbreviationToName($item->unit_thc_content_unit_of_measure);
                $unit_volume_unit_of_measure = Conversions::uomAbbreviationToName($item->unit_volume_unit_of_measure);
                $unit_weight_unit_of_measure = Conversions::uomAbbreviationToName($item->unit_weight_unit_of_measure);
                
                $jsonItems[] = [
                    'ItemCategory' => $this->metrc_category ? $this->metrc_category->name : null,
                    'Name' => $item->name,
                    'UnitOfMeasure' => $unit_of_measure,
                    'Strain' => $this->strain ? $this->strain->name : null,
                    'ItemBrand' => $item->item_brand,
                    'AdministrationMethod' => $item->administration_method,
                    'UnitCbdPercent' => $item->unit_cbd_percent,
                    'UnitCbdContent' => $item->unit_cbd_content,
                    'UnitCbdContentUnitOfMeasureName' => $unit_cbd_content_unit_of_measure,
                    'UnitThcPercent' => $item->unit_thc_percent,
                    'UnitThcContent' => $item->unit_thc_content,
                    'UnitThcContentUnitOfMeasureName' => $unit_thc_content_unit_of_measure,
                    'UnitVolume' => $item->unit_volume,
                    'UnitVolumeUnitOfMeasureName' => $unit_volume_unit_of_measure,
                    'UnitWeight' => $item->unit_weight,
                    'UnitWeightUnitOfMeasureName' => $unit_weight_unit_of_measure,
                    'ServingSize' => $item->serving_size,
                    'SupplyDurationDays' => $item->supply_duration_days,
                    'Ingredients' => $item->ingredients
                ];
            }

            $http_code = $api->createItems($this->user, $jsonItems);

            foreach($this->items as &$item) {
                $item->metrc_create_transaction_id = $api->metrcTransactionId;
            }

            if ($http_code !== 200) {
                foreach($this->items as $item) {
                    $item->metrc_status = 'error';
                    $item->save();
                }
                throw new Exception('Create Items Not Successful, returned HTTP code '.$http_code);
            }

            $metrc_items = $api->getActiveItems($this->user);
            if (!$metrc_items || !count($metrc_items)) {
                foreach($this->items as $item) {
                    $item->metrc_status = 'error';
                    $item->save();
                }
                throw new Exception('No active items found in Metrc');
            }

            foreach($this->items as $item) {
                $foundMetrc = false;
                foreach ($metrc_items as $metrc_item) {
                    if ($metrc_item->Name === $item->name) {
                        $item->metrc_id = $metrc_item->Id;
                        $item->metrc_status = 'synced';
                        $item->save();
                        $foundMetrc = true;
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
            }
        }

        return $this->items;
    }
    
}
