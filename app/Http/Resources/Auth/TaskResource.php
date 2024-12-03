<?php

namespace App\Http\Resources\Auth;

use App\Models\Auth\Task;
use Illuminate\Http\Resources\Json\Resource;
use App\Models\Auth\User;
use Auth;

class TaskResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $data['schema'] = Task::_getSchema($data['location_id']);
        return $data;

    }
}