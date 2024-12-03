<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;


class LocationsUpdateRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'name'              => 'required'
        ];
        
    }


    public function authorize(){

        return ($this->user()->can('Store Admin Update') ? true : false);
        //return true;

    }
    
    
}