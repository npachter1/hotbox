<?php

namespace App\Http\Requests\Dispensary;

use App\Http\Requests\ApiFormRequest;


class InventoryUpdateRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'id'                => 'required',
            'amount_unit'       => 'required',
            'product_id'        => 'required',
            'priceset_id'       => 'not_regex:/new/i|nullable'
        ];
        
    }


    public function authorize(){

        return ($this->user()->can('Product Update') ? true : false);           // user permission
        //return true;

    }
    
}