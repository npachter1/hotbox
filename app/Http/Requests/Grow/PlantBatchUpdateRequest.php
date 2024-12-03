<?php

namespace App\Http\Requests\Grow;

use App\Http\Requests\ApiFormRequest;


class PlantBatchUpdateRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'name'              => 'required'

        ];
        
    }


    public function authorize(){

        return ($this->user()->can('Grow Plant Batches Update') ? true : false);           // user permission
        //return true;

    }
    
}