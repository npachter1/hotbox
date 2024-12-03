<?php

namespace App\Jobs\Metrc;

use App\Models\Auth\User;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class MetrcBaseJob implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $items;   // The items that we will be deleting/storing/updating
    protected $user;    // The system user who requested the changes
    protected $test_mode;   // No interaction with Metrc during test mode
    protected $regulatory_agent;

    public function __construct($items, User $user)
    {
        // If items isn't already an Eloquent Collection, it will be after this:
        if (is_array($items)) {
            $this->items = Collection::make($items);
        } else if (!($items instanceof Collection)) {
            $this->items = Collection::make([$items]);
        } else {
            $this->items = $items;
        }

        $this->user = $user;
        // Metrc jobs should always go on a metrc queue
        $this->onQueue('metrc');
        
        // Check if it test_mode - no interaction with Metrc
        $this->test_mode = $user->location->is_demo;

        //Check if the location should use metrc
        $location = \App\Models\Auth\Location::_getSchema();
        $this->regulatory_agent = $location->model->settings->regulatory_agent;
    }
}
