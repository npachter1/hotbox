<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;


class ServicelogUpdateRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'name'              => 'required'

        ];
        
    }


    public function authorize(){

        return ($this->user()->can('Manage Server') ? true : false);           // user permission
        //return true;

    }
    
}