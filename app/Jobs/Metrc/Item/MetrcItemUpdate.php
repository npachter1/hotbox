<?php

namespace App\Jobs\Metrc\Item;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Services\Metrc\MetrcRequestService;
use Exception;
use App\Models\Grow\Strain;
use App\Models\Dispensary\CategoryMetrc;

class MetrcItemUpdate extends MetrcBaseJob
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
                
                $unit_of_measure = $this->uomAbbreviationToName($item->unit_of_measure);
                $unit_cbd_content_unit_of_measure = $this->uomAbbreviationToName($item->unit_cbd_content_unit_of_measure);
                $unit_thc_content_unit_of_measure = $this->uomAbbreviationToName($item->unit_thc_content_unit_of_measure);
                $unit_volume_unit_of_measure = $this->uomAbbreviationToName($item->unit_volume_unit_of_measure);
                $unit_weight_unit_of_measure = $this->uomAbbreviationToName($item->unit_weight_unit_of_measure);
                
                $jsonItems[] = [
                    'Id' => $item->metrc_id,
                    'Name' => $item->name,
                    'ItemCategory' => $this->metrc_category ? $this->metrc_category->name : null,
                    'UnitOfMeasure' => $unit_of_measure,
                    'Strain' => $this->strain ? $this->strain->name : null,
                    'ItemBrand' => $item->item_brand,
                    'AdministrationMethod' => $item->administration_method,
                    'UnitCbdPercent' => $item->unit_cbd_percent,
                    'UnitCbdContent' => $item->unit_cbd_content,
                    'UnitCbdContentUnitOfMeasure' => $unit_cbd_content_unit_of_measure,
                    'UnitThcPercent' => $item->unit_thc_percent,
                    'UnitThcContent' => $item->unit_thc_content,
                    'UnitThcContentUnitOfMeasure' => $unit_thc_content_unit_of_measure,
                    'UnitVolume' => $item->unit_volume,
                    'UnitVolumeUnitOfMeasure' => $unit_volume_unit_of_measure,
                    'UnitWeight' => $item->unit_weight,
                    'UnitWeightUnitOfMeasure' => $unit_weight_unit_of_measure,
                    'ServingSize' => $item->serving_size,
                    'SupplyDurationDays' => $item->supply_duration_days,
                    'Ingredients' => $item->ingredients
                ];
            }

            $http_code = $api->updateItems($this->user, $jsonItems);

            foreach($this->items as &$item) {
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
            

            foreach($this->items as $item) {
                    $item->metrc_status = 'synced';
                    $item->save();
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
    
    public function uomAbbreviationToName($abbr) {
        $name = null;
        switch ($abbr) {
            case 'g':
                $name = 'Grams';
                break;
            case 'oz':
                $name = 'Ounces';
                break;
            case 'mg':
                $name = 'Milligrams';
                break;
            case 'kg':
                $name = 'Kilograms';
                break;
            case 'lb':
                $name = 'Pounds';
                break;
            case 'ea':
                $name = 'Each';
        }
        return $name;
    }
}
