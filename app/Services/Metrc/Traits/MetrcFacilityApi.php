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

trait MetrcFacilityApi
{
    
    /**
     * Return all licensed facilities
     *
     * Example response:
     * [
     *  {
     *   "HireDate": "0001-01-01",
     *   "HomePage": "Plants",
     *   "IsOwner": false,
     *   "IsManager": true,
     *   "Occupations": [],
     *   "Name": "Cultivation LLC",
     *   "Alias": "Cultivation on Road St",
     *   "DisplayName": "Cultivation on Road St",
     *   "CredentialedDate": "1969-08-15",
     *   "SupportActivationDate": null,
     *   "SupportExpirationDate": null,
     *   "SupportLastPaidDate": null,
     *   "FacilityType": null,
     *   "License": {
     *     "Number": "403-X0001",
     *     "StartDate": "2013-06-28",
     *     "EndDate": "2015-12-28",
     *     "LicenseType": "Medical Cultivation"
     *    }
     *   },
     * ]
     */
    public function getFacilities($user) {
        $result = $this->get($user,"/facilities/v1");
           
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }
}
