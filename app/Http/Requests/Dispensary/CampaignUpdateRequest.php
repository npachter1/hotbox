<?php

namespace App\Http\Requests\Dispensary;

use App\Http\Requests\ApiFormRequest;


class CampaignUpdateRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'name'              => 'required',
            'group_id'          => 'required',
            'subject'           => 'required'            

        ];
        
    }


    public function authorize(){

        //return ($this->user()->can('Loyalty Update') ? true : false);           // user permission
        return true;

    }
    
}