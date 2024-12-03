<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;


class LocationUpdateRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'licensenum'                    => 'required',
            'settings.rec_age_limit'        => ''
        ];
        
    }


    public function authorize(){

        return ($this->user()->can('Manage Location') ? true : false);
        
        //return true;

    }
    
    
}