<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\ResourceCollection;


class AddressbookCollectionResource extends ResourceCollection
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
