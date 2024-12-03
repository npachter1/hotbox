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

trait MetrcRoomApi
{

    /**
     * Return a room name by $id
     *
     * @param $user
     * @param $id
     * @return mixed
     * @throws \RestClientException
     *
     * Example response:
     * {
     *   "Id": 1,
     *   "Name": "Harvest Room"
     * }
     */
    public function getRooms($user, $id) {
        $result = $this->get($user, "/rooms/v1/{$id}");

        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }

    /**
     * Return all active rooms for a licensed facility
     *
     * @param $user
     * @return mixed
     * @throws \RestClientException
     *
     * Example response:
     * [
     *  {
     *   "Id": 1,
     *   "Name": "Harvest Room"
     *  },
     * ]
     */
    public function getActiveRooms($user) {
        $result = $this->get($user, "/rooms/v1/active");

        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }

    /**
     * Create new rooms
     *
     * @param $user
     * @param $data
     * @return bool|mixed
     * @throws \RestClientException
     *
     * Example request:
     * [
     *  {
     *   "Name": "Harvest Room"
     *  },
     *  {
     *   "Name": "Plants Room"
     *  }
     * ]
     */
    public function createRooms($user, $data) {
        $result = $this->post($user, "/rooms/v1/create", $data);
        
        return $result->http_code;
    }

    /**
     * Update room names
     *
     * @param $user
     * @param $data
     * @return mixed
     * @throws \RestClientException
     *
     * Example request:
     * [
     *  {
     *   "Id": 5,
     *   "Name": "Harvesting Room"
     *  },
     * ]
     */
    public function updateRooms($user, $data) {
        $result = $this->post($user, "/rooms/v1/update", $data);
        
        // Metrc doesn't return a response, just an HTTP code
        return $result->http_code;
    }

    /**
     * Delete rooms by $id
     *
     * @param $user
     * @param $id
     * @return mixed
     * @throws \RestClientException
     */
    public function deleteRooms($user, $id) {
        $result = $this->delete($user, "/rooms/v1/{$id}");

        // Metrc doesn't return a response, just an HTTP code
        return $result->http_code;
    }
}
