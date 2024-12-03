<?php

namespace App\Http\Controllers\Api\V1\Dispensary;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Requests\Dispensary\CampaignUpdateRequest;
use App\Http\Requests\Dispensary\CampaignGenerateRequest;
use App\Http\Resources\Dispensary\CampaignCollectionResource;
use App\Http\Resources\Dispensary\CampaignResource;
use App\Http\Resources\Dispensary\CampaignLogResource;

use App\Services\Dispensary\CampaignService;

use App\Models\Dispensary\Campaign;

use Auth;
use Exception;


/**
 * SuiteController.
 */
class CampaignController extends ApiController
{
    
    private $service;


    public function __construct(CampaignService $service){
        
        $this->service = $service;
        
    }


    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->search($request->all())) == null) abort(412,'Whoops - Could not fetch the Campaigns - please try again later');
        
        
        return new CampaignCollectionResource($grid);

    }


    /* get Campaign info for editing */
    public function show(Request $request, $id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->show($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch Campaign information - please try again later');


        return new CampaignResource($item);
        
    }


    /* store new Campaign record */
    public function store(CampaignUpdateRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($add = $this->service->create($request->all(),$user)) == null) abort(412,'Whoops - Could not create Campaign entry - please try again later');
        
        
        return new CampaignResource($add);
        
    }


    /* 04.01.2020 autoGenerate purchaseorder(s) from list of items */
    public function autoGenerate(CampaignGenerateRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($add = $this->service->generate($request->all(),$user)) == null) abort(412,'Whoops - Could not generate PurchaseOrder(s) due to a Misc issue - please try again later');

        return new CampaignResource($add);
        
    }


    /* update Campaign info */
    public function update(CampaignUpdateRequest $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->update($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not update - please try again later');
        
        
        return new CampaignResource($upd);
        
    }

    /* requeue customer log item toggle */
    public function requeue(Request $request,$id){
        
        if(($res = $this->service->requeue($request->all(),$id)) != null) return new CampaignLogResource($res);
        else abort(404,'Whoops something went wrong');
        
    }

    public function testSMS(Request $request) {
       
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($res = $this->service->testSMS($request->all(),$user)) != null) return response()->json(['message'=>'Test Message'.' is now Sent'],200);
        else abort(412,'Whoops - Could not send - please try again later');
    }

    /* remove Campaign entry */
    public function destroy(Request $request,$id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif($this->service->archive($request->all(),$id,$user)!=true) abort(412,'could not remove entry - aborting.');
        return $this->respondDeleted('Campaign entry has been removed upon your request.');
        
    }
    
    
    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/campaign/');

    }
    
    public function exportView(Request $request, $id, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/campaign/'.$id.'/');

    }
    
    
}