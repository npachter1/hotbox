<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;



class AddressbookUpdateRequest extends ApiFormRequest
{

    public function rules(){

        return [
            'name'              => 'required',
            'city'              => 'required',
            'region'            => 'required'
        ];

    }


    public function authorize(){

        //return ($this->user()->can('manage-staff') ? true : false);           // user permission
        return true;

    }

}
