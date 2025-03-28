<?php

namespace App\Http\Requests\Dispensary;

use App\Http\Requests\ApiFormRequest;


class PricesetUpdateRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'name_grade'              => 'required'
            

        ];
        
    }


    public function authorize(){

        return ($this->user()->can('Product Update') ? true : false);           // user permission
        //return true;

    }
    
}