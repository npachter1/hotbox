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


trait MetrcHarvestApi
{

    
    /**
     * Return a harvest (Bud harvested from a group of plants on the same day) by $id
     *
     * @param $id
     * @param $user
     *
     * Example response:
     * {
     *  "Id": 1,
     *  "Name": "2014-11-19-Harvest Room-M",
     *  "HarvestType": "Product",
     *  "SourceStrainCount": 0,
     *  "SourceStrainNames": null,
     *  "Strains": [],
     *  "DryingRoomId": 1,
     *  "DryingRoomName": "Harvest Room",
     *  "PatientLicenseNumber": null,
     *  "CurrentWeight": 0.0,
     *  "TotalWasteWeight": 0.0,
     *  "PlantCount": 70,
     *  "TotalWetWeight": 40.0,
     *  "TotalRestoredWeight": 0.0,
     *  "PackageCount": 5,
     *  "TotalPackagedWeight": 0.0,
     *  "UnitOfWeightName": "Ounces",
     *  "LabTestingState": null,
     *  "LabTestingStateDate": null,
     *  "IsOnHold": false,
     *  "HarvestStartDate": "2014-11-19",
     *  "FinishedDate": null,
     *  "ArchivedDate": null,
     *  "LastModified": "0001-01-01T00:00:00+00:00"
     * }
     */
    public function getHarvests($user, $id) {
        $result = $this->get($user, "/harvests/v1/{$id}");
           
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }
    /**
     * Return all active harvests for a licensed facility
     *
     * @param $user
     * @param $lastModifiedStart (optional)
     * @param $lastModifiedEnd (optional)
     *
     * Response is an array of harvests. See getHarvests($id) function for example of data returned for a single harvest.
     */
    public function getActiveHarvests($user, $lastModifiedStart=0, $lastModifiedEnd=0) {
        $result = $this->get($user, "/harvests/v1/active?lastModifiedStart={$lastModifiedStart}&lastModifiedEnd={$lastModifiedEnd}");
            
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }
    
    /**
     * Return all harvests that have been placed on Hold by the Marijiuana Enforcement Division for a licensed facility
     *
     * @param $user
     * @param $lastModifiedStart (optional)
     * @param $lastModifiedEnd (optional)
     *
     * Response is an array of harvests. See getHarvests($id) function for example of data returned for a single harvest.
     */
    public function getOnHoldHarvests($user, $lastModifiedStart=0, $lastModifiedEnd=0) {
        $result = $this->get($user, "/harvests/v1/onhold?lastModifiedStart={$lastModifiedStart}&lastModifiedEnd={$lastModifiedEnd}");
            
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }
    
    /**
     * Return all inactive harvests for a licensed facility
     *
     * @param $user
     * @param $lastModifiedStart (optional)
     * @param $lastModifiedEnd (optional)
     *
     * Response is an array of harvests. See getHarvests($id) function for example of data returned for a single harvest.
     */
    public function getInactiveHarvests($user, $lastModifiedStart=0, $lastModifiedEnd=0) {
        $result = $this->get($user, "/harvests/v1/inactive?lastModifiedStart={$lastModifiedStart}&lastModifiedEnd={$lastModifiedEnd}");
            
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }
    
    /**
     * Create packages from harvests
     *
     * @param $user
     * @param $data
     *
     * Example request
     * [
     *  {
     *   "Tag": "ABCDEF012345670000020201",
     *   "Room": null,
     *   "Item": "Buds",
     *   "UnitOfWeight": "Grams",
     *   "PatientLicenseNumber": "X00001",
     *   "IsProductionBatch": false,
     *   "ProductionBatchNumber": null,
     *   "ProductRequiresRemediation": false,
     *   "RemediateProduct": false,
     *   "RemediationMethodId": null,
     *   "RemediationDate": null,
     *   "RemediationSteps": null,
     *   "ActualDate": "2015-12-15",
     *   "Ingredients": [
     *    {
     *     "HarvestId": 2,
     *     "HarvestName": null,
     *     "Weight": 100.23,
     *     "UnitOfWeight": "Grams"
     *    },
     *    {
     *     "HarvestId": null,
     *     "HarvestName": "2018-04-03-Harvest Room-M",
     *     "Weight": 25.1,
     *     "UnitOfWeight": "Grams"
     *    }
     *   ]
     *  },
     * ]
     */
    public function createHarvestPackages($user, $data) {
        $result = $this->post($user, "/harvests/v1/create/packages", $data);
        
        if ($result->http_code == 200)
            return $result->http_code;
        else
            return $result->data;
        
    }
    
    /**
     * Remove waste (anything coming off the plants that will be thrown away) from harvests
     *
     * @param $user
     * @param $data
     *
     * Example request
     * [
     *  {
     *   "Id": 1,
     *   "UnitOfWeight": "Grams",
     *   "WasteWeight": 10.05,
     *   "ActualDate": "2015-12-15"
     *   },
     * ]
     */
    public function removeWaste($user, $data) {
        $result = $this->post($user, "/harvests/v1/removewaste", $data);
            
        if ($result->http_code == 200)
            return $result->http_code;
        else
            return $result->data;
    }
    
    /**
     * Finish out harvests after they have been packaged
     *
     * @param $user
     * @param $data
     *
     * Example request
     * [
     *  {
     *   "Id": 1,
     *   "ActualDate": "2015-12-15"
     *  },
     * ]
     */
    public function finishHarvest($user, $data) {
        $result = $this->post($user, "/harvests/v1/finish", $data);
            
        // Metrc doesn't return a response, just an HTTP code
        return $result->http_code;
    }
    
    /**
     * Unfinish harvests, make harvests active again
     *
     * @param $user
     * @param $data
     *
     * Example request
     * [
     *  {
     *   "Id": 1,
     *  },
     * ]
     */
    public function unfinishHarvest($user, $data) {
        $result = $this->post($user, "/harvests/v1/unfinish", $data);
            
        // Metrc doesn't return a response, just an HTTP code
        return $result->http_code;
    }
}
