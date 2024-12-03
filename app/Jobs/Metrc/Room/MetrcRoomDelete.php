<?php

namespace App\Jobs\Metrc\Room;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Services\Metrc\MetrcRequestService;
use Exception;

class MetrcRoomDelete extends MetrcBaseJob
{
    /**
     * Execute the job.
     */
    public function handle(MetrcRequestService $api)
    {
        if (!$this->user->location || !$this->user->location->licensenum) {
            throw new Exception('User with ID '.$this->user->id.' does not have a location licensenum.');
        }

        if (!$this->test_mode) {
            foreach($this->items as $item) {
                $http_code = $api->deleteRooms($this->user, $item->metrc_id);
                $item->metrc_update_transaction_id = $api->metrcTransactionId;

                if ($http_code !== 200) {
                    $item->deleted_by = $this->user->id;
                    $item->metrc_status = 'error';
                    $item->save();
                    throw new Exception('Delete Rooms Not Successful, returned HTTP code ' . $http_code);
                }

                // Metrc item was deleted successfully, now delete ours:
                $item->deleted_by = $this->user->id;
                $item->metrc_status = 'synced';
                $item->save(); // TODO: Refactor
                $item->delete();
            }
        }
        // test mode - no interaction with Metrc
        else {
            foreach($this->items as $item) {
                $item->deleted_by = $this->user->id;
                $item->metrc_status = 'synced';
                $item->save();
                $item->delete();
            }
        }
    }
}
