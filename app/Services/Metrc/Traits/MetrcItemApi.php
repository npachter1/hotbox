<?php

namespace App\Services\Metrc\Traits;

use App\Models\Auth\Suite;
use App\Models\AppSchema;

use Auth;
use Log;
use stdClass;
use Exception;
use Carbon\Carbon;
use Storage;


trait MetrcItemApi{

    /**
     * Return an item by $id
     *
     * @param $id
     *
     * Example response:
     * {
     *   "Id": 1,
     *   "Name": "Buds",
     *   "ProductCategoryName": "Buds",
     *   "ProductCategoryType": "Buds",
     *   "QuantityType": "WeightBased",
     *   "DefaultLabTestingState": 0,
     *   "UnitOfMeasureName": "Ounces",
     *   "ApprovalStatus": 0,
     *   "AdministrationMethod": null,
     *   "StrainId": 1,
     *   "StrainName": "Spring Hill Kush",
     *   "UnitCbdPercent": null,
     *   "UnitCbdContent": null,
     *   "UnitCbdContentUnitOfMeasureName": null,
     *   "UnitThcPercent": null,
     *   "UnitThcContent": null,
     *   "UnitThcContentUnitOfMeasureName": null,
     *   "UnitVolume": null,
     *   "UnitVolumeUnitOfMeasureName": null,
     *   "UnitWeight": null,
     *   "UnitWeightUnitOfMeasureName": null,
     *   "ServingSize": null,
     *   "SupplyDurationDays": null,
     *   "UnitQuantity": null,
     *   "UnitQuantityUnitOfMeasureName": null,
     *   "Ingredients": null
     * }
     */
    public function getItems($user,$id) {
        $result = $this->get($user, "/items/v1/{$id}");
        
        if ($result->http_code == 200)
            return $result->data;
        else return null;
            
    }
    
    
    /**
     * Return all active items for a licensed facility
     *
     *
     * Returns an array of items. See getItems($id) for example of data returned for a single item.
     */
    public function getActiveItems($user) {
        $result = $this->get($user, "/items/v1/active");
            
        if ($result->http_code == 200)
            return $result->data;
        else return null;

    }
   
   
    /**
     * Return all Metrc item categories
     *
     * Example response:
     * [
     *  {
     *   "Name": "Buds",
     *   "ProductCategoryType": "Buds",
     *   "QuantityType": "WeightBased",
     *   "DefaultLabTestingState": "NotSubmitted",
     *   "RequiresApproval": false,
     *   "RequiresAdministrationMethod": false,
     *   "RequiresStrain": true,
     *   "RequiresUnitCbdPercent": false,
     *   "RequiresUnitCbdContent": false,
     *   "RequiresUnitThcPercent": false,
     *   "RequiresUnitThcContent": false,
     *   "RequiresUnitVolume": false,
     *   "RequiresUnitWeight": false,
     *   "RequiresServingSize": false,
     *   "RequiresSupplyDurationDays": false,
     *   "UnitQuantityMultiplier": null,
     *   "UnitQuantityUnitOfMeasureName": null,
     *   "RequiresIngredients": false,
     *   "RequiresProductPhoto": false,
     *   "CanContainSeeds": true,
     *   "CanBeRemediated": true
     *   },
     * ]
     */
    public function getItemCategories($user) {
        
        $result = $this->get($user, "/items/v1/categories");
            
        if ($result->http_code == 200)
            return $result->data;
        else return null;

    }
    
    
    /**
     * Create items
     *
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "ItemCategory": "Buds",
     *   "Name": "Buds Item",
     *   "UnitOfMeasure": "Ounces",
     *   "AdministrationMethod": null,
     *   "Strain": "Spring Hill Kush",
     *   "UnitCbdPercent": null,
     *   "UnitCbdContent": null,
     *   "UnitCbdContentUnitOfMeasure": null,
     *   "UnitThcPercent": null,
     *   "UnitThcContent": null,
     *   "UnitThcContentUnitOfMeasure": null,
     *   "UnitVolume": null,
     *   "UnitVolumeUnitOfMeasure": null,
     *   "UnitWeight": null,
     *   "UnitWeightUnitOfMeasure": null,
     *   "ServingSize": null,
     *   "SupplyDurationDays": null,
     *   "Ingredients": null
     *  },
     * ]
     */
    public function createItems($user,$data) {
        $result = $this->post($user, "/items/v1/create", $data);
            
        // Metrc doesn't return a response, just an HTTP code
        return $result->http_code;
    }
    
    
    /**
     * Update items
     *
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "Id": 1,
     *   "Name": "Buds Item",
     *   "ItemCategory": "Buds",
     *   "UnitOfMeasure": "Ounces",
     *   "AdministrationMethod": null,
     *   "Strain": "String Hill Kush",
     *   "UnitCbdPercent": null,
     *   "UnitCbdContent": null,
     *   "UnitCbdContentUnitOfMeasure": null,
     *   "UnitThcPercent": null,
     *   "UnitThcContent": null,
     *   "UnitThcContentUnitOfMeasure": null,
     *   "UnitVolume": null,
     *   "UnitVolumeUnitOfMeasure": null,
     *   "UnitWeight": null,
     *   "UnitWeightUnitOfMeasure": null,
     *   "ServingSize": null,
     *   "SupplyDurationDays": null,
     *   "Ingredients": null
     *  },
     * ]
     */
    public function updateItems($user,$data) {
        $result = $this->post($user, "/items/v1/update", $data);
            
        if ($result->http_code == 200)
            return $result->http_code;
        else
            return $result->data;
    }
    
    
    /**
     * Delete an item by $id
     *
     * @param $id
     */
    public function deleteItems($user,$id) {
        $result = $this->delete($user, "/items/v1/{$id}");
            
        // Metrc doesn't return a response, just an HTTP code
        return $result->http_code;
    }
}
