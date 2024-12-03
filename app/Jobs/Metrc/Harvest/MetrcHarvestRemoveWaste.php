<?php

namespace App\Jobs\Metrc\Harvest;

use App\Jobs\Metrc\MetrcBaseJob;
use App\Models\METRC\MetrcHarvestApi;
use App\Models\Grow\Harvest;
use Exception;

class MetrcHarvestRemoveWaste extends MetrcBaseJob
{
    protected $removeWasteDate;
    protected $removeWasteUom;
    protected $wasteWeights;
    protected $wasteTypes;

    public function __construct($items, $user, $removeWasteDate, $removeWasteUom, $wasteWeights, $wasteTypes)
    {
        $this->removeWasteDate = $removeWasteDate;
        $this->removeWasteUom = $removeWasteUom;
        $this->wasteWeights = $wasteWeights;
        $this->wasteTypes = $wasteTypes;
        parent::__construct($items, $user);
    }
    /**
     * Execute the job.
     */
    public function handle(MetrcHarvestApi $api)
    {
        if (!$this->user->location || !$this->user->location->licensenum) {
            throw new Exception('User with ID '.$this->user->id.' does not have a location licensenum.');
        }

        if (!$this->test_mode) {
            $jsonItems = [];
            foreach($this->items as $harvest) {
                $jsonItems[] = [
                    'Id' => $harvest->metrc_id,
                    'WasteType' => $this->wasteTypes[$harvest->id],
                    'UnitOfWeight' => $this->removeWasteUom,
                    'WasteWeight' => $this->wasteWeights[$harvest->id],
                    'ActualDate' => $this->removeWasteDate
                ];
            }

            $http_code = $api->removeWaste($this->user, $jsonItems);

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
            // check that the harvest's current weight and waste weight are correct in Metrc
            foreach($this->items as $harvest) {
                $metrc_item = $api->getHarvests($this->user, $harvest->metrc_id);
                if ($metrc_item->CurrentWeight != $harvest->current_weight || $metrc_item->TotalWasteWeight != $harvest->total_waste_weight) {
                    foreach($this->items as $item) {
                        $item->metrc_status = 'error';
                        $item->save();
                    }
                    throw new Exception('Remove Waste Not Successful');
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
