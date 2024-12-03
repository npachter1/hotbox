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
    
trait MetrcPackageApi
{
    
    /**
     * Return package by $id
     *
     * @param $id
     *
     * Example response:
     * {
     *   "Id": 2,
     *   "Label": "ABCDEF012345670000010042",
     *   "PackageType": "Product",
     *   "SourceHarvestNames": null,
     *   "RoomId": null,
     *   "RoomName": null,
     *   "Quantity": 1.0,
     *   "UnitOfMeasureName": "Ounces",
     *   "UnitOfMeasureAbbreviation": "oz",
     *   "PatientLicenseNumber": null,
     *   "ProductId": 1,
     *   "ProductName": "Buds",
     *   "ProductCategoryName": "Buds",
     *   "PackagedDate": "2014-11-29",
     *   "InitialLabTestingState": "NotSubmitted",
     *   "LabTestingState": "NotSubmitted",
     *   "LabTestingStateDate": "2014-11-29",
     *   "IsProductionBatch": false,
     *   "ProductionBatchNumber": null,
     *   "IsTestingSample": false,
     *   "IsProcessValidationTestingSample": false,
     *   "ProductRequiresRemediation": false,
     *   "ContainsRemediatedProduct": false,
     *   "RemediationDate": null,
     *   "ReceivedFromManifestNumber": null,
     *   "ReceivedFromFacilityLicenseNumber": null,
     *   "ReceivedFromFacilityName": null,
     *   "ReceivedDateTime": null,
     *   "IsOnHold": false,
     *   "ArchivedDate": null,
     *   "FinishedDate": null,
     *   "LastModified": "2018-09-03T13:09:24.3754846+00:00"
     * }
     */
    public function getPackages($user,$id) {
        
        $result = $this->get($user,"/packages/v1/{$id}");
           
        if ($result->http_code == 200) {
            return $result->data;
        }else{
            //we got an invalid response or an error on their side
        }
        
    }
    
    
    /**
     * Return package by Metrc RFID tag number
     * Label is Metrc RFID tag number
     *
     * @param $label
     *
     * Returns a package. See getPackages($id) for example of data returned for a package.
     */
    public function getPackagesByLabel($user,$label) {
        
        $result = $this->get($user,"/packages/v1/{$label}");
            
        if ($result->http_code == 200) {
            return $result->data;
        }else{
            //we got an invalid response or an error on their side
        }
        
    }
    
    
    /**
     * Return all active packages for a licensed facility
     *
     * @param $licenseNumber
     * @param $lastModifiedStart (optional)
     * @param $lastModifiedEnd (optional)
     *
     * Returns an array of packages. See getPackages($id) for example of data returned for a single package.
     */
    public function getActivePackages($user,$lastModifiedStart=null,$lastModifiedEnd=null){
        
        $result = $this->get($user, "/packages/v1/active?lastModifiedStart={$lastModifiedStart}&lastModifiedEnd={$lastModifiedEnd}",[],true);

        if ($result->http_code == 200) {
            return $result->data;
        }else{
            // TODO we got an invalid response or an error on their side
        }        
        
    }
    
    
    /**
     * Return all packages that have been put On Hold by the Marijuana Enforcement Division for a licensed facility
     *
     * @param $licenseNumber
     * @param $lastModifiedStart (optional)
     * @param $lastModifiedEnd (optional)
     *
     * Returns an array of packages. See getPackages($id) for example of data returned for a single package.
     */
    public function getOnHoldPackages($user,$lastModifiedStart=null,$lastModifiedEnd=null) {
  
        $result = $this->get($user, "/packages/v1/onhold?lastModifiedStart={$lastModifiedStart}&lastModifiedEnd={$lastModifiedEnd}",[],true);

        if ($result->http_code == 200) {
            return $result->data;
        }else{
            // TODO we got an invalid response or an error on their side
        } 

    }
    
    
    /**
     * Return all inactive packages for a licensed facility
     *
     * @param $licenseNumber
     * @param $lastModifiedStart (optional)
     * @param $lastModifiedEnd (optional)
     *
     * Returns an array of packages. See getPackages($id) for example of data returned for a single package.
     */
    public function getInactivePackages($user,$lastModifiedStart=null,$lastModifiedEnd=null){
        
        $result = $this->get($user, "/packages/v1/inactive?lastModifiedStart={$lastModifiedStart}&lastModifiedEnd={$lastModifiedEnd}",[],true);


        if ($result->http_code == 200){
            return $result->data;
        }else{
            // TODO we got an invalid response or an error on their side
        }       

    }
    
    
    /**
     * Return all package types
     *
     * Example response:
     * [
     *   "Product",
     *   "ImmaturePlant",
     *   "VegetativePlant"
     * ]
     */
    public function getPackageTypes($user) {
        
        $result = $this->get($user,"/packages/v1/types");
            
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }
    
    /**
     * Return all possible reasons for adjusting a package
     *
     * Example response:
     * [
     *  {
     *   "Name": "Drying",
     *   "RequiresNote": false
     *  },
     * ]
     */
    public function getPackageAdjustReasons($user) {
        
        $result = $this->get($user,"/packages/v1/adjust/reasons");
            
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }
    
    
    /**
     * Create a new package
     *
     * @param $licenseNumber
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "Tag": "ABCDEF012345670000020201",
     *   "Room": null,
     *   "Item": "Buds",
     *   "Quantity": 16.0,
     *   "UnitOfMeasure": "Ounces",
     *   "PatientLicenseNumber": "X00001",
     *   "IsProductionBatch": false,
     *   "ProductionBatchNumber": null,
     *   "ProductRequiresRemediation": false,
     *   "ActualDate": "2015-12-15",
     *   "Ingredients": [
     *    {
     *     "Package": "ABCDEF012345670000010041",
     *     "Quantity": 8.0,
     *     "UnitOfMeasure": "Ounces"
     *    },
     *    {
     *     "Package": "ABCDEF012345670000010042",
     *     "Quantity": 8.0,
     *     "UnitOfMeasure": "Ounces"
     *    }
     *   ]
     *  },
     * ]
     */
    public function createPackages($user,$data) {
        
        $result = $this->post($user,"/packages/v1/create",$data);

        if ($result->http_code == 200)
            return $result->http_code;
        else
            return $result->data;
        
    }
    
    
    /**
     * Create packages for lab testing
     *
     * @param $licenseNumber
     * @param $data
     *
     * See createPackages($licenseNumber,$data) for example of request data
     */
    public function createTestingPackages($user,$data) {
        
        $result = $this->post($user,"/packages/v1/create/testing",$data);
            
        if ($result->http_code == 200)
            return $result->http_code;
        else
            return $result->data;
        
    }
    
    
    /**
     * Create packages from plantings (groups of clones or germinated seeds)
     *
     * @param $licenseNumber
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "PackageLabel": "ABCDEF012345670000010041",
     *   "PackageAdjustmentAmount": 2.0,
     *   "PackageAdjustmentUnitOfMeasureName": "Ounces",
     *   "PlantBatchName": "AK-47 Clone 1/31/2017",
     *   "PlantBatchType": "Clone",
     *   "PlantCount": 1,
     *   "RoomName": "Plant Batch Room",
     *   "StrainName": "AK-47",
     *   "PatientLicenseNumber": "X00001",
     *   "PlantedDate": "2017-01-31T00:00:00Z",
     *   "UnpackagedDate": "0001-01-01T00:00:00Z"
     *  },
     * ]
     */
 /*   public function createPlantings($user,$data) {
        
        $result = $this->post($user,"/packages/v1/create/plantings",$data);
            
        if ($result->http_code == 200)
            return $result->http_code;
        else
            return $result->data;
        
    }.  */
    
    
    /**
     * Edit the item type of packages
     *
     * @param $licenseNumber
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "Label": "ABCDEF012345670000010041",
     *   "Item": "Shake"
     *  },
     * ]
     */
    public function changeItems($user,$data) {
        
        $result = $this->post($user,"/packages/v1/change/item",$data);
            
        if ($result->http_code == 200)
            return $result->http_code;
        else
            return $result->data;
        
    }
    
    
    /**
     * Edit the weight or quantity of packages
     *
     * @param $licenseNumber
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "Label": "ABCDEF012345670000010041",
     *   "Quantity": -2.0,
     *   "UnitOfMeasure": "Ounces",
     *   "AdjustmentReason": "Drying",
     *   "AdjustmentDate": "2015-12-15",
     *   "ReasonNote": null
     *  },
     * ]
     */
    public function adjustItems($user,$data) {
        
        $result = $this->post($user,"/packages/v1/adjust",$data);
            
        if ($result->http_code == 200)
            return $result->http_code;
        else
            return $result->data;
        
    }
    
    
    /**
     * Finish out packages
     *
     * @param $licenseNumber
     * @data
     *
     * Example request:
     * [
     *  {
     *   "Label": "ABCDEF012345670000010041",
     *   "ActualDate": "2015-12-15"
     *  },
     * ]
     */
    public function finishItems($user,$data) {
        
        $result = $this->post($user,"/packages/v1/finish",$data);
            
        if ($result->http_code == 200)
            return $result->http_code;
        else
            return $result->data;
        
    }
    
    
    /**
     * Unfinish packages
     *
     * @param $licenseNumber
     * @data
     *
     * Example request:
     * [
     *  {
     *   "Label": "ABCDEF012345670000010041",
     *  },
     * ]
     */
    public function unfinishItems($user,$data) {
        
        $result = $this->post($user,"/packages/v1/unfinish",$data);
            
        if ($result->http_code == 200)
            return $result->http_code;
        else
            return $result->data;
        
    }
    
    /**
     * Remediate packages
     *
     * @param $licenseNumber
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "PackageLabel": "ABCDEF012345670000020201",
     *   "RemediationMethodName": "Further Drying",
     *   "RemediationDate": "2016-10-17",
     *   "RemediationSteps": "Used hair dryer"
     *  },
     * ]
     */
    public function remediateItems($user,$data){
        
        $result = $this->post($user,"/packages/v1/remediate",$data);
            
        if ($result->http_code == 200)
            return $result->http_code;
        else
            return $result->data;
        
    }
    
    
}
