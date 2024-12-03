<?php

namespace App\Http\Requests\Dispensary;

use App\Http\Requests\ApiFormRequest;


class ReceivingUpdateRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'po_number'              => 'required',
            'addressbook_id'         => 'required' // need a vendor!

        ];
        
    }


    public function authorize(){

        return ($this->user()->can('Receiving Update') ? true : false);           // user permission
        return true;

    }
    
}