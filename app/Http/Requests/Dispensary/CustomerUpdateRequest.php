<?php

namespace App\Http\Requests\Dispensary;

use App\Http\Requests\ApiFormRequest;


class CustomerUpdateRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'birthdate'              => 'required'

        ];
        
    }


    public function authorize(){

        //return ($this->user()->can('Manage Store Admin') ? true : false);     // this may be used by budtender - customer intake
        return true;

    }
    
}