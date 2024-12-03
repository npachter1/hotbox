<?php

namespace App\Http\Controllers\Api\V1\Dispensary;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Requests\Dispensary\RewardUpdateRequest;
use App\Http\Resources\Dispensary\RewardCollectionResource;
use App\Http\Resources\Dispensary\RewardTriggerCollectionResource;
use App\Http\Resources\Dispensary\RewardResource;

use App\Services\Dispensary\RewardService;

use App\Models\Dispensary\Reward;

use Auth;
use Exception;


/**
 * SuiteController.
 */
class RewardController extends ApiController
{
    
    private $service;


    public function __construct(RewardService $service){
        
        $this->service = $service;
        
    }


    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->search($request->all())) == null) abort(412,'Whoops - Could not fetch the Rewards - please try again later');
        
        
        return new RewardCollectionResource($grid);

    }


    /* get Reward info for editing */
    public function show(Request $request, $id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->show($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch Reward information - please try again later');


        return new RewardResource($item);
        
    }
    
    public function triggerShow(Request $request){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($items = $this->service->triggerShow($request->all())) == null) abort(412,'Whoops - Could not fetch trigger information - please try again later');


        return new RewardTriggerCollectionResource($items);
        
    }

    /* store new Reward record */
    public function store(RewardUpdateRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($add = $this->service->create($request->all(),$user)) == null) abort(412,'Whoops - Could not create Reward entry - please try again later');
        
        
        return new RewardResource($add);
        
    }


    /* update Reward info */
    public function update(RewardUpdateRequest $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->update($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not update - please try again later');
        
        
        return new RewardResource($upd);
        
    }
    
    /* update Category info */
    public function triggerUpdate(Request $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->triggerUpdate($request->all(),$user)) == null) abort(412,'Whoops - Could not update - please try again later');
        
        $this->setStatusCode(data_get($upd,'status',200));
        return $this->respond($upd);
        
    }
    

    /* remove Reward entry */
    public function destroy(Request $request,$id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif($this->service->archive($request->all(),$id,$user)!=true) abort(412,'could not remove entry - aborting.');
        return $this->respondDeleted('Reward entry has been removed upon your request.');
        
    }
    
    
    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/reward/');

    }
    
    public function exportView(Request $request, $id, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/reward/'.$id.'/');

    }
    
    
}