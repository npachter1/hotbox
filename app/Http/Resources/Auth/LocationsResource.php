<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\Resource;

use App\Models\Auth\Location;
use App\Models\Auth\User;

use Auth;

class LocationsResource extends Resource
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
        
        $user = Auth::guard('api')->user();
        $data['users_to_link'] = User::with('locationsAssigned')
            ->select('id','name','location_id','email','avatar','created_at')
            ->where('status','activated')
            ->whereIn('location_id',$user->locationsAssigned()->pluck('id')->toArray())
            ->get();


        return $data;

    }
    
    
}
