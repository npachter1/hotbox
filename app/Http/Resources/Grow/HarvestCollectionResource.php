<?php

namespace App\Http\Resources\Grow;

use Illuminate\Http\Resources\Json\ResourceCollection;

use App\Models\Grow\Harvest;


class HarvestCollectionResource extends ResourceCollection
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