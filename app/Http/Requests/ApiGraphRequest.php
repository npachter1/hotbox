<?php

namespace App\Http\Requests;

use App\Http\Requests\ApiFormRequest;


class ApiGraphRequest extends ApiFormRequest
{
    
    public function rules(){

        return [

        ];
        
    }


    public function authorize(){

        return ($this->user()->can('Reporting View') ? true : false);           // user permission
        //return true;

    }
    
}