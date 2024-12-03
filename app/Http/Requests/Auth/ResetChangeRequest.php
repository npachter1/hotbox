<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;


class ResetChangeRequest extends ApiFormRequest
{   
    
    public function rules(){
        
        return [
            'id'                    => 'required',                              // coming from authenticated admin console
            'password'              => 'required|min:5',
            'password_confirmation' => 'required|same:password'
        ];
        
    }


    public function authorize(){
        
        //return ($this->user()->can('Staff Update') ? true : false);
        return true;
    }
    
    
}