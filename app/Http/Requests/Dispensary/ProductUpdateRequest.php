<?php

namespace App\Http\Requests\Dispensary;

use App\Http\Requests\ApiFormRequest;


class ProductUpdateRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'name'                 => 'required',
            //'sku'                   => 'alpha_dash',
            'category_id'           => 'required'

        ];
        
    }


    public function authorize(){

        return ($this->user()->can('Product Update') ? true : false);           // user permission
        //return true;

    }
    
}