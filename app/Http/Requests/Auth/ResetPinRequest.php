<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;


class ResetPinRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'pincode'                   => 'required|digits_between:4,4|unique:users',
            'pincode_confirmation'      => 'required|digits_between:4,4|same:pincode'
        ];
        
    }
    
    public function messages(){
        return [
            'pincode_confirmation.same' => ' - The confirmation input does not match',
            'pincode_confirmation.required' => ' - Please re-enter your pin to confirm above',
            'pincode.unique' => ' - This pin is already in use'
        ];
    }

    public function authorize(){
        
        // TODO, what permisisons allow an admin pin change
        return true;
        
    }
    
    
}