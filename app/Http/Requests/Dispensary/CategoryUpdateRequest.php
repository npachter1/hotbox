<?php

namespace App\Http\Requests\Dispensary;

use App\Http\Requests\ApiFormRequest;


class CategoryUpdateRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'name'              => 'required',
            'equivalency_type'  => 'required'
            
        ];
        
    }


    public function authorize(){

        return ($this->user()->can('Product Update') ? true : false);           // user permission
        //return true;

    }
    
}