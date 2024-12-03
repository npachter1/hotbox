<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\ResourceCollection;


class SchemasCollectionResource extends ResourceCollection
{
    
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     *
     * @return array
     */
    public function toArray($request){

        $data = [];
        foreach(parent::toArray($request) as $row)
            $data[$row['type']][] = $row;


        return $data;
    
    }
    
    
}
