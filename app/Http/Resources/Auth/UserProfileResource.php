<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\Resource;

use App\Models\Auth\User;


class UserProfileResource extends Resource
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
            'id'         => $this->id,
            'suite_id'   => $this->suite_id,
            'email'      => $this->email,
            'name'       => $this->name,
            'status'     => $this->status,
            'avatar'     => $this->avatar,
            'settings'   => $this->settings,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'role_ids'   => $this->roles()->pluck('id')->toArray(),
            'schema'     => User::_getSchema()
        ];
    }
    
    
}
