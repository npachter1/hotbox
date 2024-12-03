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

trait MetrcEmployeeApi
{
    
    /**
     * Return all employees for a licensed facility
     *
     * @param $user
     *
     * Example response:
     * [
     *  {
     *   "FullName": " ",
     *   "License": null
     *  },
     *  {
     *   "FullName": " ",
     *   "License": null
     *  }
     * ]
     */
    public function getEmployees($user) {
        $result = $this->get($user, "/employees/v1");
           
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
               //we got an invalid response or an error on their side
        }
    }
}
