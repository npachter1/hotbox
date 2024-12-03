<?php

namespace App\Models\Auth;

use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Model;

class TaskAssignee extends Model
{   
    public $timestamps = true;
    protected $table = 'auth_task_assignees';
	protected $fillable = ['task_id','user_id'];
	protected $dates = ['created_at','updated_at'];
    protected $casts = [];

    protected $appends = ['name'];


    public function taskId()
    {
        return $this->belongsTo('App\Models\Auth\Task', 'task_id');
    }

    public function user() {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function getNameAttribute()
    {
        return $this->user->name;
    }
    

}
