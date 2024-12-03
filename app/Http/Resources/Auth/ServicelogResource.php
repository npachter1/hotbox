<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\Resource;

use App\Models\Auth\Servicelog;


class ServicelogResource extends Resource
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
        $data['schema'] = Servicelog::_getSchema();


        return $data;

    }
    
}