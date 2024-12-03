<?php

namespace App\Http\Controllers\Api\V1\Grow;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Requests\Grow\RoomUpdateRequest;
use App\Http\Resources\Grow\RoomCollectionResource;
use App\Http\Resources\Grow\RoomResource;

use App\Services\Grow\RoomService;

use App\Models\Grow\Room;


use Auth;
use Exception;
use Carbon\Carbon;



/**
 * SuiteController.
 */
class RoomController extends ApiController
{
    
    private $service;


    public function __construct(RoomService $service){
        
        $this->service = $service;
        
    }


    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->search($request->all())) == null) abort(412,'Whoops - Could not fetch room - please try again later');
        
        
        return new RoomCollectionResource($grid);

    }

    

    /* get Room info for editing */
    public function show(Request $request, $id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->show($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch room information - please try again later');


        return new RoomResource($item);
        
    }


    /* store new Room record */
    public function store(RoomUpdateRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($add = $this->service->create($request->all())) == null) abort(412,'Whoops - Could not create room entry - please try again later');
        
        
        return new RoomResource($add);
        
    }


    /* update Room info */
    public function update(RoomUpdateRequest $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->update($request->all(),$id)) == null) abort(412,'Whoops - Could not update - please try again later');
        
        
        return new RoomResource($upd);
        
    }
    
    
    /* remove Room entry */
    public function destroy(Request $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(!$id) abort(404, 'Item ID not provided.');
        elseif(($del = $this->service->destroy($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not remove - please try again later');


        return $this->respondDeleted($del.' has been Deleted upon your request.');
        
    }
    
    
    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/room/');

    }
    
    public function exportView(Request $request, $id, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/room/'.$id.'/');

    }
    
    
    
    
}