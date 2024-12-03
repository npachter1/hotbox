<?php

namespace App\Jobs\Metrc\Package;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Models\METRC\MetrcPackageApi;
use App\Models\Grow\Package;
use Exception;

class MetrcPackageFinish extends MetrcBaseJob
{
    protected $finishDate;

    public function __construct($items, $user, $finishDate)
    {
        $this->finishDate = $finishDate;
        parent::__construct($items, $user);
    }
    /**
     * Execute the job.
     */
    public function handle(MetrcPackageApi $api)
    {
        if (!$this->user->location || !$this->user->location->licensenum) {
            throw new Exception('User with ID '.$this->user->id.' does not have a location licensenum.');
        }

        if (!$this->test_mode) {
            $jsonItems = [];
            foreach($this->items as $package) {
                $jsonItems[] = [
                    'Label' => $package->label,
                    'ActualDate' => $this->finishDate
                ];
            }

            $http_code = $api->finishItems($this->user, $jsonItems);

            foreach($this->items as $item) {
                $item->metrc_update_transaction_id = $api->metrcTransactionId;
            }

            if ($http_code !== 200) {
                foreach($this->items as $item) {
                    $item->metrc_status = 'error';
                    $item->save();
                }
                if (is_array($http_code))
                    throw new Exception($http_code[0]->message);
                else
                    throw new Exception($http_code->Message);
            }
            // check that the product now has a FinishedDate in Metrc
            foreach($this->items as $package) {
                $metrc_item = $api->getPackagesByLabel($this->user, $package->label);
                if (!$metrc_item->FinishedDate) {
                    foreach($this->items as $item) {
                        $item->metrc_status = 'error';
                        $item->save();
                    }
                    throw new Exception('Package Not Finished Successfully');
                }
            }
        }
        foreach($this->items as $item) {
            $item->metrc_status = 'synced';
            $item->save();
        }

        return $this->items;
    }
}
