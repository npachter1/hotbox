<?php

namespace App\Http\Requests\Dispensary;

use App\Http\Requests\ApiFormRequest;


class RewardUpdateRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'descriptor'                => 'required',
            'customer_id'               => 'required'

        ];
        
    }


    public function authorize(){

        return ($this->user()->can('Loyalty Update') ? true : false);           // user permission
        //return true;

    }
    
}