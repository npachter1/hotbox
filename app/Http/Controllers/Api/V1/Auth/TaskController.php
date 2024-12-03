<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\Auth\Task;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Auth\TaskUpdateRequest;
use App\Http\Resources\Auth\TaskResource;
use App\Http\Resources\Auth\TaskCollectionResource;

use App\Notifications\TaskNotification;

use App\Http\Requests\ApiQueryRequest;
use App\Services\Auth\TaskService;
use Illuminate\Http\Request;
use App\Models\Auth\User;
use Auth;
use Illuminate\Support\Facades\Notification;

class TaskController extends ApiController
{

    private $service;

    public function __construct(TaskService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ApiQueryRequest $request)
    {
        $tasks = $this->service->search($request->all());

        return new TaskCollectionResource($tasks);
    }

    /* search for Tasks for group base don posted filters */
    public function searchTask(Request $request)
    {

        $tasks = $this->service->search($request->all());
        return new TaskCollectionResource($tasks);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskUpdateRequest $request)
    {
        $task = $this->service->store($request->all());

        $array = $task->assignees->pluck('user_id');

        $users = User::whereIn('id',$array)->get();

        $this->sendNotification($users, $task);

        return new TaskResource($task);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = $this->service->show($id);
        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $task_original = $this->service->show($id);
        $task = $this->service->update($request->all(), $id);

        //Notify owner of task if the task status changed to completed by an assignee
        if($task->created_by !== $task->updated_by && $task_original->status === 'started' && $task->status === 'completed')
        {
            $array = [$task->created_by];

            $users = User::whereIn('id',$array)->get();
    
            $this->sendNotification($users, $task);

        }

        $added_assignees = array_column(array_filter($request->all()['assignees'], function($item){ 
            return isset($item['id']) ? false : $item['user_id'];}
        ), 'user_id');

        $users = User::whereIn('id',$added_assignees)->get();
        if(!$users->isEmpty())
        {
            $this->sendNotification($users, $task);
        }

        return new TaskResource($task);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        $task = Task::find($id);

        if ($user->id !== $task->created_by) {
            $this->setStatusCode(422);
            return $this->respondWithError('You do not have permission to delete this Task');
        }

        $task->deleted_by = $user->id;
        $task->save();
        $task->delete();

        return $this->respondDeleted('success');
    }

     /* download export */
     public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/task/');

    }

    /* get the user schema */
    public function _getSchema(Request $request){
        $user = Auth::guard('api')->user();
        return response()->json(['schema'=>Task::_getSchema($user->locattion_id)],200);
        
    }

    public function sendNotification($users,$task)
    {
        Notification::send($users, new TaskNotification($task));
    }

}
