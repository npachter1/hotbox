<?php

namespace App\Http\Requests;


class ApiUploadAssetRequest extends ApiFormRequest
{
    
    public function rules(){

        return [
            'image'     => 'required_without:file|image|max:5000',
            'file'      => 'required_without:image|mimes:doc,docx,pdf,png,jpeg,bmp,gif,tiff,txt|max:5000'
        ];
        
    }


    public function authorize(){

        return true;

    }
    
    
}