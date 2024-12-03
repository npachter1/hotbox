<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;


class SchemasUpdateRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'codename'          => 'required|alpha_dash',
            'type'              => 'required',
            'content'           => 'required|json'
        ];
        
    }


    public function authorize(){

        return ($this->user()->can('Manage Server') ? true : false);
        //return true;

    }
    
    
}