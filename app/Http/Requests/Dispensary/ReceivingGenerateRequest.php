<?php

namespace App\Http\Requests\Dispensary;

use App\Http\Requests\ApiFormRequest;


class ReceivingGenerateRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'vendorlist'                   => 'required',                       // require a vendorlist array of items
        ];
        
    }


    public function authorize(){

        return ($this->user()->can('Receiving Update') ? true : false);         // user permission
        //return true;

    }
    
}