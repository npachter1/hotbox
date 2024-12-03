<?php

namespace App\Events;


use App\Models\Auth\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Auth;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class InventoryItemUpdated implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $data;
   
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, $upd)
    {
        $this->data = json_decode($upd,true);
        // $this->dontBroadcastToCurrentUser();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('inventoryitem');
    }

}
