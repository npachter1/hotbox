<?php

namespace App\Http\Requests\Grow;

use App\Http\Requests\ApiFormRequest;


class ItemAddRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'name'              => 'unique:grow_items|required'

        ];
        
    }


    public function authorize(){

        return ($this->user()->can('Grow Items Update') ? true : false);           // user permission
        //return true;

    }
    
}