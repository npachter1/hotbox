<?php

namespace App\Http\Resources\Dispensary;

use Illuminate\Http\Resources\Json\ResourceCollection;

use App\Models\Dispensary\Customer;


class CustomerCollectionResource extends ResourceCollection
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