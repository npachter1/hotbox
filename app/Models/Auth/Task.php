<?php

namespace App\Models\Auth;

use App\Models\Auth\User;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\FilterScopes;
use Illuminate\Support\Facades\DB;
use App\Models\AppSchema;
use Illuminate\Support\Collection;
use App\Helpers\Util;
use Auth;

class Task extends Eloquent
{
    
    use SoftDeletes, FilterScopes;
    public $timestamps = true;

            
    /* set up conig */
    protected $table = 'auth_tasks';
	protected $fillable = ['location_id','name','description','status','priority'];
	protected $dates = ['created_at','due_date','updated_at','deleted_at'];
    protected $casts = [];

    protected $appends = ['priority_name'];

    public function location(){
        return $this->belongsTo('App\Models\Auth\Location','location_id');
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

    public function assignees(){
        return $this->hasMany(TaskAssignee::class,'task_id','id');
    }

    public function attachments(){
        return $this->hasMany(TaskAttachment::class,'task_id','id');
    }

    public function getPriorityNameAttribute()
    {
        $priorityNames = array();
        $priorityNames[] = new PriorityClass(1, "Low");
        $priorityNames[] = new PriorityClass(2, "Medium");
        $priorityNames[] = new PriorityClass(3, "High");
        $priorityNames[] = new PriorityClass(4, "Urgent");
        
        $searchedValue = $this->priority;

        $priorityObj = array_filter(
            $priorityNames,
            function ($e) use (&$searchedValue) {
                return $e->priority === $searchedValue;
            }
        );
        
        $priorityObj = reset($priorityObj);

        return $priorityObj->priorityName;

    }

    public function scopeOfFieldMatch($query,$field,$val){
        
        if(!$val || strtolower($val)=='all') return $query;
        else return $query->where($field,$val);
        
    }

    /* filter by frontend query orderBy filter */
    public function scopeOfActive($query){
        
        return $query;
        //return $query->whereNull('archived_at');

    }

    /* config settings and form/data defaults schema */
    public static function _getSchema($locationId = null){

        $user = Auth::guard()->user();
        if($locationId === null){
            $locationId = $user->location->id;
        }

        $schema = AppSchema::getSchema('task_schema');
        data_set($schema,'filters.status.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'status'),true);
        data_set($schema,'filters.priority.values',Util::getActiveFilterList(self::query()->ofActive(),$schema,'priority'),true);
        $schema->form->users->values = self::_getUserList($locationId);    // inject users in current location or task location for form multiselection
        $schema->form->location_users->values = self::_getUserList($user->location->id);    // inject users in current location for form multiselection
        return $schema;
        
    }

    /* get a scoped list of users based on location */
    public static function _getUserList($locationId){

        $data = [];
        foreach(User::select(DB::raw('id as user_id,name'))->where('location_id','=',$locationId)
            ->get() as $assignee){
                $data[$assignee->user_id] = $assignee;
            }

        return $data;
    }

}

class PriorityClass {
    public $priority;
    public $priorityName;
    
    function __construct($priority, $priorityName)
    {
        $this->priority = $priority;
        $this->priorityName = $priorityName;
    }
    
}
