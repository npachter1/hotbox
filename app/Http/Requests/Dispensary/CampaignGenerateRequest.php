<?php

namespace App\Http\Requests\Dispensary;

use App\Http\Requests\ApiFormRequest;


class CampaignGenerateRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'items'              => 'required',
        ];
        
    }


    public function authorize(){

        //return ($this->user()->can('Loyalty Update') ? true : false);          // user permission
        return true;

    }
    
}