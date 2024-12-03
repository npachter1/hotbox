<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class TaskAttachment extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $table = 'auth_task_attachments';
	protected $fillable = ['task_id','url', 'name', 'created_by', 'updated_by', 'deleted_by'];
	protected $dates = ['created_at','updated_at','deleted_at'];
    protected $casts = [];


    public function taskId()
    {
        return $this->belongsTo('App\Models\Auth\Task', 'task_id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\Auth\User', 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo('App\Models\Auth\User', 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo('App\Models\Auth\User', 'deleted_by');
    }
}
