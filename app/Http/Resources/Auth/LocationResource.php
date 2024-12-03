<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\Resource;


class LocationResource extends Resource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     *
     * @return array
     */
    public function toArray($request){

        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'thumb'             => $this->thumb,
            'is_demo'           => $this->is_demo,
            'settings'          => $this->settings,
            'migration_settings'=> $this->migration_settings,
            'financial_settings'=> $this->financial_settings,
            'licensenum'        => $this->licensenum,
            'status'            => $this->status,
            'addressbook_id'    => $this->addressbook_id,
            'type'              => $this->type,
            'activated_at'      => $this->activated_at,
            'archived_at'       => $this->archived_at,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            'address'          => $this->address
        ];


        //return parent::toArray($request);

    }


}
