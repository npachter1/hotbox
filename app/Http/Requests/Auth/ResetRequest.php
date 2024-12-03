<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;


class ResetRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'email'                 => 'required|email',
            'password'              => 'required|min:4',
            'password_confirmation' => 'required|same:password'
        ];
        
    }

    public function messages(){
        return [
            'password_confirmation.same' => ' - The confirmation input does not match',
            'password_confirmation.required' => ' - Please re-enter your password to confirm above'
        ];
    }

    public function authorize(){
        
        return true;
        
    }
    
    
}