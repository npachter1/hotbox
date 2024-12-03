<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;


class ResetConfirmRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'current_password'          => 'required',
            'new_password'              => 'required|confirmed|different:current_password|min:4',
            'new_password_confirmation' => 'required|same:new_password'
        ];
        
    }


    public function authorize(){
        
        return true;
        
    }
    
    
}