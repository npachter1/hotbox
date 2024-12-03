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

trait MetrcPatientApi
{
    
    /**
     * Return patient information by $id
     *
     * @param $id
     *
     * Example response:
     * {
     *   "PatientId": 1,
     *   "LicenseNumber": "000001",
     *   "RegistrationDate": "2015-01-08",
     *   "LicenseEffectiveStartDate": "2014-07-12",
     *   "LicenseEffectiveEndDate": "2015-07-07",
     *   "RecommendedPlants": 6,
     *   "RecommendedSmokableQuantity": 2.0,
     *   "OtherFacilitiesCount": 1
     * }
     */
    public function getPatients($user,$id) {
        $result = $this->get($user,"/patients/v1/{$id}");
           
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }
    
    /**
     * Return all active patients for a licensed facility
     *
     * @param $licenseNumber
     *
     * Returns an array of patients. See getPatients($id) for example of data returned for a single patient
     */
    public function getActivePatients($user) {
        $result = $this->get($user,"/patients/v1/active",[],true);
            
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }
    
    /**
     * Return FlowerOuncesAvailable and ThcOuncesAvailable for a patient by medical card number
     *
     * @param $patientLicenseNumber
     * @param $licenseNumber
     *
     * Example response:
     * {
     *  "PatientLicenseNumber": "PTN-123-456",
     *  "FlowerOuncesAvailable": 120.0,
     *  "ThcOuncesAvailable": 10.0
     * }
     */
    public function getPatientStatus($user,$patientLicenseNumber) {
        $result = $this->get($user,"/patients/v1/status/{$patientLicenseNumber}");
            
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }
    
    /**
     * Add new patients to a licensed facility
     *
     * @param $licenseNumber
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "LicenseNumber": "000001",
     *   "LicenseEffectiveStartDate": "2015-06-21",
     *   "LicenseEffectiveEndDate": "2016-06-15",
     *   "RecommendedPlants": 6,
     *   "RecommendedSmokableQuantity": 2.0,
     *   "ActualDate": "2015-12-15"
     *  },
     * ]
     */
    public function addPatients($user,$data) {
        $result = $this->post($user, "/patients/v1/add",$data);
            
        // Metrc doesn't return a response, just an HTTP code
        return $result->http_code;
    }
    
    /**
     * Update patient information
     *
     * @param $licenseNumber
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "LicenseNumber": "000001",
     *   "NewLicenseNumber": null,
     *   "LicenseEffectiveStartDate": "2015-06-21",
     *   "LicenseEffectiveEndDate": "2016-06-15",
     *   "RecommendedPlants": 7,
     *   "RecommendedSmokableQuantity": 2.0,
     *   "ActualDate": "2015-12-15"
     *  },
     * ]
     */
    public function updatePatients($user,$data) {
        $result = $this->post($user,"/patients/v1/update",$data);
            
        // Metrc doesn't return a response, just an HTTP code
        return $result->http_code;
    }
    
    /**
     * Delete a patient from a licensed facility
     *
     * @param $id
     * @param $licenseNumber
     */
    public function deletePatients($user,$id) {
        $result = $this->delete($user,"/patients/v1/{$id}");
        
        // Metrc doesn't return a response, just an HTTP code
        return $result->http_code;
    }
}
