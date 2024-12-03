<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use Auth;


class LocationScope implements Scope
{
    
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model){
        
        $tenant_id = (Auth::guard('api')->user() ? Auth::guard('api')->user()->location_id : 0);
        $builder->where('location_id',$tenant_id);
        
    }
    
    
}