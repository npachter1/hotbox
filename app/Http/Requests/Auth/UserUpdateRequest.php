<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;


class UserUpdateRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'id'                => 'required',
            'name'              => 'required',
            'settings.alias'    => ''
        ];
        
    }


    public function authorize(){

        return ($this->user()->can('Staff Update') ? true : false);
        
        //return true;

    }
    
    
}