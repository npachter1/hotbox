<?php

namespace App\Http\Resources\Dispensary;

use Illuminate\Http\Resources\Json\Resource;

use App\Models\Dispensary\Product;
use App\Models\Dispensary\Priceset;


class ProductResource extends Resource
{
    
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     *
     * @return array
     */
    public function toArray($request){
        
        $data = parent::toArray($request);
        $data['schema'] = Product::_getSchema();
        $data['pricesetSchema'] = Priceset::_getSchema();

        return $data;

    }
    
}