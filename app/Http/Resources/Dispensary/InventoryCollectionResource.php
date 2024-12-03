<?php

namespace App\Http\Resources\Dispensary;

use Illuminate\Http\Resources\Json\ResourceCollection;

use App\Models\Dispensary\Inventory;


class InventoryCollectionResource extends ResourceCollection
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