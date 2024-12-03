<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;


class LoginRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'email'         => 'required|string|email|min:2|max:255',
            'password'      => 'required|string|min:3'
        ];
        
    }


    public function authorize(){
        
        return true;
        
    }
    
    
}