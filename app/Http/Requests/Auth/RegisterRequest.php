<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;


class RegisterRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'company_name'          => 'required',
            'name'                  => 'required',
            'email'                 => 'required|email|unique:users',
            'password'              => 'required|min:4',
            'password_confirmation' => 'required|same:password'
        ];
        
    }


    public function authorize(){
        
        return true;
        
    }
    
    
}