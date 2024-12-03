<?php

namespace App\Http\Resources\Dispensary;

use Illuminate\Http\Resources\Json\Resource;

use App\Models\Dispensary\Inventory;


class InventoryResource extends Resource
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
        $data['schema'] = Inventory::_getSchema();


        return $data;

    }
    
}