<?php

namespace App\Services\Auth;

use App\Services\BaseService;
use App\Models\Auth\Task;
use App\Http\Resources\Auth\TaskCollectionExport;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Relations\Relation;
use DB;
use Carbon\Carbon;
use Auth;

class TaskService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;

    public function search(array $data) {
        $user = Auth::guard()->user();

        $filters = data_get($data,'filter',[]);
        $textSearch = array_get($data, 'search', null);
        $pagination_size = array_get($data, 'limit', self::$PAGINATION_SIZE);

        $query = Task::query()->with('assignees','location','attachments','createdBy')->ofListFilters($filters);
        $query->where(function($query) use ($user) {
            $query->where('created_by','=',$user->id);
            $query->orWhereHas('assignees', function($query) use ($user) {
                $query->where('user_id','=', $user->id);
            });
        });
        if ($textSearch) {
            $query->where('name', 'like', '%'.$textSearch.'%');
        }

        $sort = array_get($data, 'sort', null);
        $order = array_get($data, 'order', 'ASC');
        
        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('due_date', 'asc');

        return $query->paginate($pagination_size);
    }


    public function show($id)
    {
        $user = Auth::guard()->user();

        $task = Task::with('assignees','location','attachments','createdBy')->find($id); 

        if($task->created_by !== $user->id && $task->status === 'new')
        {
            $task->status = 'started';
            $task->save();
        }
        $task->assignees = $task->assignees->pluck('id','task_id','user_id','name');

        /*
        TODO
        if (!Auth::guard()->user()->can('view',    $task)) {
            throw new AuthorizationException('This item is not viewable to you.');
        }
        */
        return $task;
    }

    public function store(array $data)
    {
        $user = Auth::guard()->user();

        $task = new Task();

        try {           
            DB::beginTransaction();
            
            $task->fill($data);
            $task->location_id = $user->location_id;
            $task->due_date = Carbon::parse(data_get($data,'due_date'))->toDateTimeString();    
            $task->created_by = $user->id;
            $task->updated_by = $user->id;
            $task->save();

            $this->processRelationWithRequest(
                $task->assignees(),
                $data['assignees']
            );

            $this->processRelationWithRequest(
                $task->attachments(),
                $data['attachments']
            );

            DB::commit();   
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }


        return Task::with('assignees','location','attachments','createdBy')->find($task->id);        

    }

    public function update(array $data, $id)
    {
        $user = Auth::guard()->user();

        if (!$id) { throw new Exception('Item ID not provided.'); }

        $task = Task::find($id);
        if (!$task) { throw new Exception('Item not found.'); }
        
        /* TODO
        if (!Auth::guard()->user()->can('update', $task)) {
            throw new AuthorizationException('This item cannot be edited by you.');
        }
        */
        try {           
            DB::beginTransaction();
            $task->fill($data);
            $task->due_date = Carbon::parse(data_get($data,'due_date'))->toDateTimeString(); 
            $task->updated_by = $user->id;
            $task->save();

            $this->processRelationWithRequest(
                $task->assignees(),
                $data['assignees']
            );

            $this->processRelationWithRequest(
                $task->attachments(),
                $data['attachments']
            );

            DB::commit();   
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

        return Task::with('assignees','location','attachments','createdBy')->find($task->id);
        
    }

    public function destroy($id)
    {
        $user = Auth::guard()->user();

        $task = Task::find($id);
        /*
         * TODO
        if (!Auth::guard()->user()->can('delete',$task)) {
            throw new AuthorizationException('This item does not belong to you.');
        }
        */
        $task->deleted_by = $user->id;
        $task->save();
        $task->delete();

        return true;
    }

    /* process an export */
    public function exportCollection($data,$typ,$file){
        
        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Task_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all') 
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;
        
        
        return $this->export(new TaskCollectionExport($data),$file,$name,$typ,[]);

    }

    protected function processRelationWithRequest(Relation $relation, array $items)
    {
        $user = Auth::guard()->user();

        $relation->getResults()->each(function($model) use ($items, $user) {
            foreach ($items as $item) {
                if ($model->id === ($item['id'] ?? null)) {
                    if(isset($item['updated_by']))
                    {
                        $item['updated_by'] = $user->id;
                    }
                    $model->fill($item)->save();
                    return;
                }
            }

            return $model->delete();
        });

        foreach ($items as $item) {
            if (!isset($item['id']))
            {   
                if(isset($item['created_by']))
                {
                    $item['created_by'] = $user->id;
                }
                $relation->create($item);
            }
        }
    }

}
