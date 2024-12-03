<?php

namespace App\Http\Requests\Dispensary;

use App\Http\Requests\ApiFormRequest;


class GroupUpdateRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'name'              => 'required'

        ];
        
    }


    public function authorize(){

        //return ($this->user()->can('Reporting View') ? true : false);         // user permission
        return true;

    }
    
}