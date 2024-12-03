<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;


class RegisterLocationRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'name'                  => 'required',
            'email'                 => 'required|email',
            'type'                  => 'required',
            'licensenum'            => 'required', // todo could sue some more validation id guess
        ];
        
    }


    public function authorize(){
        
        return ($this->user()->can('Staff Update') ? true : false);
        //return true; // TODO who can actually register a location
        
    }
    
    
}