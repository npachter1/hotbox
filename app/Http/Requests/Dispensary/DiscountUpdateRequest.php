<?php

namespace App\Http\Requests\Dispensary;

use App\Http\Requests\ApiFormRequest;


class DiscountUpdateRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'descriptor'                => 'required',
            'discount_amount'           => 'required',
            //'discount_code'             => 'alpha_num'
        ];
        
    }


    public function authorize(){

        return ($this->user()->can('Loyalty Update') ? true : false);           // user permission
        //return true;

    }
    
}