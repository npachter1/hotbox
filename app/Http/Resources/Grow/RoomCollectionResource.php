<?php

namespace App\Http\Resources\Grow;

use Illuminate\Http\Resources\Json\ResourceCollection;

use App\Models\Grow\Room;


class RoomCollectionResource extends ResourceCollection
{
    
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     *
     * @return array
     */
    public function toArray($request){
        
        return parent::toArray($request);
    
    }
    
    
}