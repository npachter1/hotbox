<?php

namespace App\Http\Controllers\Api\V1\Dispensary;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Requests\Dispensary\GroupUpdateRequest;
use App\Http\Resources\Dispensary\GroupCollectionResource;
use App\Http\Resources\Dispensary\CustomerCollectionResource;
use App\Http\Resources\Dispensary\GroupResource;

use App\Services\Dispensary\GroupService;

use App\Models\Dispensary\Group;

use Auth;
use Exception;


/**
 * SuiteController.
 */
class GroupController extends ApiController
{
    
    private $service;


    public function __construct(GroupService $service){
        
        $this->service = $service;
        
    }


    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->search($request->all())) == null) abort(412,'Whoops - Could not fetch the Groups - please try again later');
        
        
        return new GroupCollectionResource($grid);

    }

    /* get customers by the filter object */
    public function getFilters(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($filters = $this->service->getFilters($request->all(),$user)) == null) abort(412,'Whoops - Could not fetch the Group Filters data - please try again later');
        
        
        return response()->json($filters,200);

    }


    /* get customers by the filter object */
    public function filter(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($res = $this->service->filterCustomers($request->all(),$user)) == null) abort(412,'Whoops - Could not fetch the Group filtered customers - please try again later');
        
        
        return new CustomerCollectionResource($res);

    }


    /* get Group info for editing */
    public function show(Request $request, $id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->show($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch Group information - please try again later');


        return new GroupResource($item);
        
    }


    /* store new Group record */
    public function store(GroupUpdateRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($add = $this->service->create($request->all(),$user)) == null) abort(412,'Whoops - Could not create Group entry - please try again later');
        
        
        return new GroupResource($add);
        
    }


    /* update Group info */
    public function update(GroupUpdateRequest $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->update($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not update - please try again later');
        
        
        return new GroupResource($upd);
        
    }

    
    
    /* remove Group entry */
    public function destroy(Request $request,$id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif($this->service->archive($request->all(),$id,$user)!=true) abort(412,'could not remove entry - aborting.');
        return $this->respondDeleted('Group entry has been removed upon your request.');
        
    }
    
    
    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/group/');

    }
    
    public function exportView(Request $request, $id, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/group/'.$id.'/');

    }

    public function customergroups(){
       return Group::where('type', '!=', 'auto')->get();
    }
    
    
}