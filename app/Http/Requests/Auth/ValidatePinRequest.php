<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;


class ValidatePinRequest extends ApiFormRequest
{   
    
    public function rules(){
        
        return [
            'pin'                   => 'required|max:4',
            'location_id'           => 'required|min:5',
        ];
        
    }


    public function authorize(){
        
        //return ($this->user()->can('Staff Update') ? true : false);
        return true;                                                            // any user can give an admin pin - which is validated by this EP
    }
    
    
}