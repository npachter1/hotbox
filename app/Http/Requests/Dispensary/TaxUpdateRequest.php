<?php

namespace App\Http\Requests\Dispensary;

use App\Http\Requests\ApiFormRequest;


class TaxUpdateRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'name'              => 'required'

        ];
        
    }


    public function authorize(){

        return ($this->user()->can('Store Admin Update') ? true : false);           // user permission
        return true;

    }
    
}