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


trait MetrcPlantBatchesApi
{
    
    /**
     * Return a plant batch by $id
     *
     * @param $id
     *
     * Example response:
     * {
     *   "Id": 5,
     *   "Name": "Demo Plant Batch 1",
     *   "Type": "Seed",
     *   "RoomId": null,
     *   "RoomName": null,
     *   "StrainId": 1,
     *   "StrainName": "Spring Hill Kush",
     *   "PatientLicenseNumber": null,
     *   "Count": 80,
     *   "LiveCount": 10,
     *   "PackagedCount": 0,
     *   "HarvestedCount": 0,
     *   "DestroyedCount": 40,
     *   "SourcePackageId": 0,
     *   "SourcePackageLabel": null,
     *   "SourcePlantId": 0,
     *   "SourcePlantLabel": null,
     *   "PlantedDate": "2014-10-10",
     *   "LastModified": "0001-01-01T00:00:00+00:00"
     * }
     */
    public function getPlantBatches($user,$id) {
        $result = $this->get($user,"/plantbatches/v1/{$id}",[],false);
           
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }
    
    /**
     * Returns all active plant batches for a licensed facility
     *
     * @param $licenseNumber
     * @param $lastModifiedStart (optional)
     * @param $lastModifiedEnd (optional)
     *
     * Returns an array of plant batches. See getPlantBatches($id) for example of data returned for a single plant batch.
     */
    public function getActivePlantBatches($user,$lastModifiedStart=0,$lastModifiedEnd=0) {
        $result = $this->get($user,"/plantbatches/v1/active?lastModifiedStart={$lastModifiedStart}&lastModifiedEnd={$lastModifiedEnd}",[],true);
            
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }
    
    /**
     * Returns all inactive plant batches for a licensed facility
     *
     * @param $licenseNumber
     * @param $lastModifiedStart (optional)
     * @param $lastModifiedEnd (optional)
     *
     * Returns an array of plant batches. See getPlantBatches($id) for example of data returned for a single plant batch.
     */
    public function getInactivePlantBatches($user,$lastModifiedStart=0,$lastModifiedEnd=0) {
        $result = $this->get($user,"/plantbatches/v1/inactive?lastModifiedStart={$lastModifiedStart}&lastModifiedEnd={$lastModifiedEnd}");
            
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }
    
    /**
     * Return all types of plant batches
     *
     * Example response:
     * [
     *  "Seed",
     *  "Clone"
     * ]
     */
    public function getPlantBatchTypes($user) {
        $result = $this->get($user,"/plantbatches/v1/types",[],false);
            
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }
    
    /**
     * Create plantings from clone or seed
     *
     * @param $licenseNumber
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "Name": "B. Kush 5-30",
     *   "Type": "Clone",
     *   "Count": 25,
     *   "Strain": "Spring Hill Kush",
     *   "Room": null,
     *   "PatientLicenseNumber": "X00001",
     *   "ActualDate": "2015-12-15"
     *  },
     * ]
     */
/*    public function createPlantings($user,$data) {
        $result = $this->post($user,"/plantbatches/v1/createplantings",$data);
            
        // Metrc doesn't return a response, just an HTTP code
        return $result->http_code;
    }.   */
    
    /**
     * Create packages from plant batches
     *
     * @param $licenseNumber
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "Id": null,
     *   "PlantBatch": "Demo Plant Batch 1",
     *   "Count": 10,
     *   "Room": null,
     *   "Item": "Immature Plants",
     *   "Tag": "ABCDEF012345670000020201",
     *   "PatientLicenseNumber": "X00001",
     *   "ActualDate": "2015-12-15"
     *  },
     * ]
     */
 /*   public function createPackages($user,$data) {
        $result = $this->post($user,"/plantbatches/v1/createpackages",$data);
            
        if ($result->info->http_code == 200)
            return $result->http_code;
        else
            return $result->data;
        
    }.  */
    
    /**
     * Change the growht phase of plant batches
     *
     * @param $licenseNumber
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "Name": "AK-47 Clone 1/31/2017",
     *   "Count": 25,
     *   "StartingTag": "ABCDEF012345670000020401",
     *   "GrowthPhase": "Flowering",
     *   "NewRoom": "Plants Room",
     *   "GrowthDate": "2015-12-15",
     *   "PatientLicenseNumber": "X00001"
     *  },
     * ]
     */
    public function changeGrowthPhase($user,$data) {
        $result = $this->post($user,"/plantbatches/v1/changegrowthphase",$data);

        if ($result->http_code == 200) {
            return $result->info->http_code;
        }
        else {
            return $result->data;
            //we got an invalid response or an error on their side
        }
    }

    // /**
    //  * /plantbatches/v1/moveplantbatches?licenseNumber=123-ABC
    // [
    // {
    // "Name": "AK-47 Clone 1/31/2017",
    // "Room": "Plants Room",
    // "MoveDate": "2015-12-15"
    // },
    // {
    // "Name": "Metrc Bliss 5/30/2018",
    // "Room": "Plants Room",
    // "MoveDate": "2018-01-05"
    // }
    // ]
    //  */
    public function movePlantBatches($user,$data) {
        $result = $this->put($user,"/plantbatches/v1/moveplantbatches",$data);

        // Metrc doesn't return a response, just an HTTP code
        return $result->http_code;
    }

    /**
     * Destroy plant batches
     *
     * @param $licenseNumber
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "PlantBatch": "AK-47 Clone 1/31/2017",
     *   "Count": 25,
     *   "ReasonNote": "",
     *   "ActualDate": "2015-12-15"
     *  },
     * ]
     */
    public function destroyPlantBatch($user,$data) {
        $result = $this->post($user,"/plantbatches/v1/destroy",$data);
            
        // Metrc doesn't return a response, just an HTTP code
        return $result->http_code;
    }
}
