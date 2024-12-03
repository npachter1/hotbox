<?php

namespace App\Http\Resources\Grow;

use Illuminate\Http\Resources\Json\Resource;

use App\Models\Grow\Strain;


class StrainResource extends Resource
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
        $data['schema'] = Strain::_getSchema();


        return $data;

    }
    
}