<?php

namespace App\Http\Resources\Dispensary;

use Illuminate\Http\Resources\Json\Resource;

use App\Models\Dispensary\Group;


class GroupResource extends Resource
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
        $data['schema'] = Group::_getSchema();


        return $data;

    }
    
}