<?php

namespace App\Http\Resources\Grow;

use Illuminate\Http\Resources\Json\Resource;

use App\Models\Grow\Transfer;


class TransferResource extends Resource
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
        $data['schema'] = Transfer::_getSchema();


        return $data;

    }
    
}