<?php

namespace App\Http\Resources\Grow;

use Illuminate\Http\Resources\Json\Resource;

use App\Models\Grow\Room;


class RoomResource extends Resource
{
    
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     *
     * @return array
     */
    public function toArray($request){
        
        $data = parent::toArray($request);
        $data['schema'] = Room::_getSchema();


        return $data;

    }
    
}