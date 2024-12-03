<?php

namespace App\Http\Requests;


class ApiQueryRequest extends ApiFormRequest
{
    
    public function rules(){

        return [

        ];
        
    }


    public function authorize(){

        return true;

    }
    
    
}