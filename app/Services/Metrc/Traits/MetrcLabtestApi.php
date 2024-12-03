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

trait MetrcLabTestApi
{
    
    /**
     * Return all possible states of a lab test
     *
     * Example response:
     * [
     *   "NotSubmitted",
     *   "SubmittedForTesting",
     *   "TestFailed",
     *   "TestPassed",
     *   "TestingInProgress",
     *   "AwaitingConfirmation",
     *   "RetestFailed",
     *   "RetestPassed",
     *   "Remediated",
     *   "SelectedForRandomTesting",
     *   "NotRequired"
     * ]
     */
    public function getLabTestStates($user) {
        $result = $this->get($user, "/labtests/v1/states");
           
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }
    
    /**
     * Return all testing types
     *
     * Example response:
     * [
     *  {
     *   "Id": 1,
     *   "Name": "THC",
     *   "RequiresTestResult": false,
     *   "AlwaysPasses": false,
     *   "DependencyMode": 0
     *  },
     * ]
     */
    public function getLabTestTypes($user) {
        $result = $this->get($usre, "/labtests/v1/types");
            
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }
    
    /**
     * Record results of a lab test
     *
     * @param $licenseNumber
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "Label": "ABCDEF012345670000000001",
     *   "ResultDate": "2015-12-15T00:00:00Z",
     *   "Results": [
     *    {
     *     "LabTestTypeName": "THC",
     *     "Quantity": 100.2345,
     *     "Passed": true,
     *     "Notes": ""
     *    }
     *   ]
     *  },
     * ]
     */
    public function recordLabTests($user,$data) {
        $result = $this->post($user, "/labtests/v1/record",$data);
            
        // Metrc doesn't return a response, just an HTTP code
        return $result->http_code;
    }
}
