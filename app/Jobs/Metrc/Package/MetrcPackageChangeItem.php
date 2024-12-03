<?php

namespace App\Jobs\Metrc\Package;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Models\METRC\MetrcPackageApi;
use App\Models\Grow\Package;
use App\Models\Grow\Item;
use Exception;

class MetrcPackageChangeItem extends MetrcBaseJob
{
    protected $productId;

    public function __construct($items, $user, $productId)
    {
        $this->productId = $productId;
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
                $package_item = Item::where('id', $this->productId)->first();
                
                $jsonItems[] = [
                    'Label' => $package->label,
                    'Item' => $package_item->name
                ];
            }

            $http_code = $api->changeItems($this->user, $jsonItems);

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
            // check that the package has the newly changed item in Metrc
            foreach($this->items as $package) {
                $metrc_item = $api->getPackagesByLabel($this->user, $package->label);
                $package_item = Item::where('id', $this->productId)->first();
                if ($metrc_item->ProductName != $package_item->name) {
                    foreach($this->items as $item) {
                        $item->metrc_status = 'error';
                        $item->save();
                    }
                    throw new Exception('Package Item Not Changed Successfully');
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
