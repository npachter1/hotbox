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

trait MetrcStrainApi
{
    /**
     * Return a strain by $id
     *
     * @param $user
     * @param $id
     * @return mixed
     * @throws \RestClientException
     *
     * Example response:
     * {
     *   "Id": 201,
     *   "Name": "Spring Hill Kush",
     *   "TestingStatus": "ThirdParty",
     *   "ThcLevel": null,
     *   "CbdLevel": null,
     *   "IndicaPercentage": 60.0,
     *   "SativaPercentage": 40.0,
     *   "Genetics": "60% Indica / 40% Sativa"
     * }
     */
    public function getStrains($user, $id) {
        $result = $this->get($user, "/strains/v1/{$id}");
           
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }
    
    /**
     * Return all active strains for a licensed facility
     *
     * @param $user
     * @return mixed
     * @throws \RestClientException
     *
     * Returns an array of strains. See getStrains($id) for example of data returned for a single strain.
     */
    public function getActiveStrains($user) {
        $result = $this->get($user, "/strains/v1/active");
            
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }
    
    /**
     * Create new strains
     *
     * @param $user
     * @param $data
     * @return bool|mixed
     * @throws \RestClientException
     *
     * Example request:
     * [
     *  {
     *   "Name": "Spring Hill Kush",
     *   "TestingStatus": "None",
     *   "ThcLevel": 0.1865,
     *   "CbdLevel": 0.1075,
     *   "IndicaPercentage": 25.0,
     *   "SativaPercentage": 75.0
     *  },
     * ]
     */
    public function createStrains($user, $data) {
        $result = $this->post($user, "/strains/v1/create", $data);
            
        // Metrc doesn't return a response, just an HTTP code
        return $result->http_code;
    }
    
    /**
     * Edit strains
     *
     * @param $user
     * @param $data
     * @return mixed
     * @throws \RestClientException
     *
     * Example request:
     * [
     *  {
     *   "Id": 1,
     *   "Name": "Spring Hill Kush",
     *   "TestingStatus": "InHouse",
     *   "ThcLevel": 0.1865,
     *   "CbdLevel": 0.1075,
     *   "IndicaPercentage": 25.0,
     *   "SativaPercentage": 75.0
     *  },
     * ]
     */
    public function updateStrains($user, $data) {
        $result = $this->post($user, "/strains/v1/update", $data);
            
        // Metrc doesn't return a response, just an HTTP code
        return $result->http_code;

    }
    
    /**
     * Delete strains from a licensed facility
     *
     * @param $user
     * @param $id
     * @return mixed
     * @throws \RestClientException
     */
    public function deleteStrains($user, $id) {
        $result = $this->delete($user, "/strains/v1/{$id}");
            
        // Metrc doesn't return a response, just an HTTP code
        return $result->http_code;
    }
}
