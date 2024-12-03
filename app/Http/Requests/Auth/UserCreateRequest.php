<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;


class UserCreateRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'email'                 => 'required|unique:users',
            'name'                  => 'required',
            'password'              => 'required|min:4',
            'password_confirmation' => 'required|same:password'
        ];
        
    }


    public function authorize(){

        return ($this->user()->can('Staff Update') ? true : false);
        //return true;

    }
    
    
}