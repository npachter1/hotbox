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


trait MetrcPlantApi
{

    /**
     * Return a plant by $id
     *
     * @param $id
     *
     * Example response:
     * {
     *   "Id": 24,
     *   "Label": "ABCDEF012345670000000024",
     *   "State": "Tracked",
     *   "GrowthPhase": "Vegetative",
     *   "PlantBatchId": 1,
     *   "PlantBatchName": "Demo Plant Batch",
     *   "PlantBatchTypeName": "Seed",
     *   "StrainId": 1,
     *   "StrainName": "Spring Hill Kush",
     *   "RoomId": 2,
     *   "RoomName": "Plants Room",
     *   "PatientLicenseNumber": null,
     *   "HarvestId": null,
     *   "HarvestedUnitOfWeightName": null,
     *   "HarvestedUnitOfWeightAbbreviation": null,
     *   "HarvestedWetWeight": null,
     *   "HarvestCount": 0,
     *   "IsOnHold": false,
     *   "PlantedDate": "2014-10-10",
     *   "VegetativeDate": "2014-10-20",
     *   "FloweringDate": null,
     *   "HarvestedDate": null,
     *   "DestroyedDate": null,
     *   "DestroyedNote": null,
     *   "DestroyedByUserName": null,
     *   "LastModified": "0001-01-01T00:00:00+00:00"
     * }
     */
    public function getPlants($user,$id) {
        print_r($id);
        $result = $this->get($user,"/plants/v1/{$id}",[],false);

        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }

    /**
     * Return a plant by Metrc RFID tag number
     * Label is Metrc RFID tag number
     *
     * @param $label
     *
     * Returns a plant. See getPlants($id) for example of data returned for a plant.
     */
    public function getPlantsByLabel($user,$label) {
        $result = $this->get($user,"/plants/v1/{$label}",[],false);

        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }

    /**
     * Return all vegetative plants at a licensed facility
     *
     * @param $licenseNumber
     * @param $lastModifiedStart (optional)
     * @param $lastModifiedEnd (optional)
     *
     * Returns an array of plants. See getPlants($id) for example of data returned for a single plant.
     */
    public function getVegetativePlants($user,$lastModifiedStart=0,$lastModifiedEnd=0) {


        $result = $this->get($user,"/plants/v1/vegetative?lastModifiedStart={$lastModifiedStart}&lastModifiedEnd={$lastModifiedEnd}",[],true);

        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }

    /**
     * Return all flowering plants at a licensed facility
     *
     * @param $licenseNumber
     * @param $lastModifiedStart (optional)
     * @param $lastModifiedEnd (optional)
     *
     * Returns an array of plants. See getPlants($id) for example of data returned for a single plant.
     */
    public function getFloweringPlants($user,$lastModifiedStart=0,$lastModifiedEnd=0) {
        $result = $this->get($user,"/plants/v1/flowering?lastModifiedStart={$lastModifiedStart}&lastModifiedEnd={$lastModifiedEnd}",[],true);

        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            throw new \Exception(print_r($result,true));
            //we got an invalid response or an error on their side
        }
    }

    /**
     * Return all plants that have been put On Hold by the Marijuana Enforcement Division at a licensed facility
     *
     * @param $licenseNumber
     * @param $lastModifiedStart (optional)
     * @param $lastModifiedEnd (optional)
     *
     * Returns an array of plants. See getPlants($id) for example of data returned for a single plant.
     */
    public function getOnHoldPlants($user,$lastModifiedStart=0,$lastModifiedEnd=0) {
        $result = $this->get($user,"/plants/v1/onhold?lastModifiedStart={$lastModifiedStart}&lastModifiedEnd={$lastModifiedEnd}",[],true);

        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }

    /**
     * Return all inactive plants at a licensed facility
     *
     * @param $licenseNumber
     * @param $lastModifiedStart (optional)
     * @param $lastModifiedEnd (optional)
     *
     * Returns an array of plants. See getPlants($id) for example of data returned for a single plant.
     */
    public function getInactivePlants($user,$lastModifiedStart=0,$lastModifiedEnd=0) {
        $result = $this->get($user,"/plants/v1/inactive?lastModifiedStart={$lastModifiedStart}&lastModifiedEnd={$lastModifiedEnd}",[],true);

        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }

    /**
     * Returns all growht phases
     *
     * @param $licenseNumber
     *
     * Example response:
     * [
     * "Vegetative",
     * "Flowering"
     * ]
     */
    public function getGrowthPhases($user) {
        $result = $this->get($user,"/plants/v1/growthphases");

        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }

    /**
     * Return all methods of wasting a plant
     *
     * Example response:
     * [
     *  {
     *   "Name": "Grinder"
     *  },
     *  {
     *   "Name": "Compost"
     *  }
     * ]
     */
    public function getWasteMethods($user) {
        $result = $this->get($user,"/plants/v1/waste/methods",[],false);

        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }

    /**
     * Return all reasons for wasting a plant
     *
     * Example response:
     * [
     *  {
     *   "Name": "Contamination",
     *   "RequiresNote": false
     *  },
     *  {
     *   "Name": "Male Plants",
     *   "RequiresNote": false
     *  }
     * ]
     */
    public function getWasteReasons($user) {
        $result = $this->get($user,"/plants/v1/waste/reasons",[],false);

        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }

    /**
     * Move plants from one room to another room
     *
     * @param $licenseNumber
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "Id": null,
     *   "Label": "ABCDEF012345670000000001",
     *   "Room": "Plants Room",
     *   "ActualDate": "2015-12-15"
     *  },
     * ]
     */
    public function movePlants($user,$data) {
        $result = $this->post($user,"/plants/v1/moveplants",$data);

        if ($result->http_code == 200)
            return $result->http_code;
        else
            return $result->data;

    }

    /**
     * Change the growth phase of plants
     *
     * @param $licenseNumber
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "Id": null,
     *   "Label": "ABCDEF012345670000000001",
     *   "NewTag": "ABCDEF012345670000020001",
     *   "GrowthPhase": "Flowering",
     *   "NewRoom": "Plants Room",
     *   "GrowthDate": "2015-12-15"
     *  },
     * ]
     */
    public function changeGrowthPhases($user,$data) {
        $result = $this->post($user,"/plants/v1/changegrowthphases",$data);

        if ($result->http_code == 200)
            return $result->http_code;
        else
            return $result->data;

    }

    /**
     * Destroy plants
     *
     * @param $licenseNumber
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "Id": null,
     *   "Label": "ABCDEF012345670000000001",
     *   "ReasonNote": "",
     *   "ActualDate": "2015-12-15"
     *  },
     * ]
     */
    public function destroyPlants($user,$data) {
        $result = $this->post($user,"/plants/v1/destroyplants",$data);

        if ($result->http_code == 200)
            return $result->http_code;
        else
            return $result->data;

    }

    /**
     * Create plantings (a group of baby plants)
     *
     * @param $licenseNumber
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "PlantLabel": "ABCDEF012345670000010011",
     *   "PlantBatchName": "Demo Plant Batch 1",
     *   "PlantBatchType": "Clone",
     *   "PlantCount": 3,
     *   "RoomName": null,
     *   "StrainName": "Spring Hill Kush",
     *   "PatientLicenseNumber": "X00001",
     *   "ActualDate": "2016-10-18T13:11:03Z"
     *  },
     * ]
     */
    public function createPlantings($user,$data) {
        $result = $this->post($user,"/plants/v1/create/plantings",$data);

        if ($result->http_code == 200) {
            return $result->http_code;
        }
        else {
            return $result->data;
        }

    }

    /**
     * Manicure some weight off of a plant, used when plant has not been completely harvested
     *
     * @param $licenseNumber
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "Plant": "ABCDEF012345670000000001",
     *   "Weight": 100.23,
     *   "UnitOfWeight": "Grams",
     *   "DryingRoom": "Plants Room",
     *   "HarvestName": null,
     *   "PatientLicenseNumber": "X00001",
     *   "ActualDate": "2015-12-15"
     *  },
     * ]
     */
    public function manicurePlants($user,$data) {
        $result = $this->post($user,"/plants/v1/manicureplants",$data);

        if ($result->http_code == 200) {
            return $result->http_code;
        }
        else {
            return $result->data;
        }
    }

    /**
     * Harvest entire plants
     *
     * @param $licenseNumber
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "Plant": "ABCDEF012345670000000001",
     *   "Weight": 100.23,
     *   "UnitOfWeight": "Grams",
     *   "DryingRoom": "Plants Room",
     *   "HarvestName": null,
     *   "PatientLicenseNumber": "X00001",
     *   "ActualDate": "2015-12-15"
     *  },
     * ]
     */
    public function harvestPlants($user,$data) {
        $result = $this->post($user,"/plants/v1/harvestplants",$data);

        if ($result->http_code == 200) {
            return $result->http_code;
        }
        else {
            return $result->data;
        }
    }
}
