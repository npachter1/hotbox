<?php

namespace App\Http\Controllers\Api\V1\Dispensary;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Requests\Dispensary\ReceivingUpdateRequest;
use App\Http\Requests\Dispensary\ReceivingGenerateRequest;
use App\Http\Resources\Dispensary\ReceivingCollectionResource;
use App\Http\Resources\Dispensary\ReceivingResource;

use App\Services\Dispensary\ReceivingService;

use App\Models\Dispensary\Receiving;

use Auth;
use Exception;


/**
 * SuiteController.
 */
class ReceivingController extends ApiController
{
    
    private $service;


    public function __construct(ReceivingService $service){
        
        $this->service = $service;
        
    }


    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->search($request->all())) == null) abort(412,'Whoops - Could not fetch the Receiving files - please try again later');
        
        
        return new ReceivingCollectionResource($grid);

    }


    public function transfersIndex(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->searchTransfers($request->all())) == null) abort(412,'Whoops - Could not fetch the metrc Transfer files - please try again later');
        
        
        return new ReceivingCollectionResource($grid);

    }

    public function packagesIndex(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->searchPackages($request->all())) == null) abort(412,'Whoops - Could not fetch the metrc Packages - please try again later');
        
        
        return new ReceivingCollectionResource($grid);

    }


    /* get Receiving info for editing */
    public function show(Request $request, $id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->show($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch Receiving information - please try again later');


        return new ReceivingResource($item);
        
    }


    /*batch grid search */
    public function batch(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->getBatch($request->all())) == null) abort(412,'Whoops - Could not fetch the Receiving batch - please try again later');
        
        
        return new ReceivingCollectionResource($item);

    }


    /* store new Receiving record */
    public function store(ReceivingUpdateRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($add = $this->service->create($request->all(),$user)) == null) abort(412,'Whoops - Could not create Receiving entry - please try again later');
        
        
        return new ReceivingResource($add);
        
    }
    
    
    /* 04.01.2020 autoGenerate purchaseorder(s) from list of items */
    public function autoGenerate(ReceivingGenerateRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($added = $this->service->generate($request->all(),$user)) == null) abort(412,'Whoops - Could not generate PurchaseOrder(s) due to a Misc issue - please try again later');

        $this->setStatusCode(201);
        return $this->respond([
            'message'       => count($added).' PurchaseOrder(s) have been successfully auto-generated',
            'resource_ids'  => $added,
            'schema'    => Receiving::_getSchema()                              // include newly updated/injected schema as these batch changes may update the scope of some filters.
        ]);
        
    }
    


    /* update Receiving info */
    public function update(ReceivingUpdateRequest $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->update($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not update - please try again later');
        
        
        return new ReceivingResource($upd);
        
    }
    

    /* batch update Receiving batch edit info */
    public function updateBatch(Request $request,$type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        
        switch($type){
            case 'edit':
                $upd = $this->service->updateBatchData($request->all(),$user);
                break;
            case 'archive':
                $upd = $this->service->updateBatchArchive($request->all(),$user);
                break;
            default: abort(412, 'the Batch type of '.$type.' is not a supported function - aborting');
        }
            

        $this->setStatusCode(data_get($upd,'status',200));
        return $this->respond($upd);
        
    }
    
    
    /* remove Receiving entry */
    public function destroy(Request $request,$id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif($this->service->archive($request->all(),$id,$user)!=true) abort(412,'could not remove entry - aborting.');
        return $this->respondDeleted('Receiving File has been removed upon your request.');
        
    }
    
    
    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/receiving/');

    }
    
    public function exportView(Request $request, $id, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/receiving/'.$id.'/');

    }
    
    
    /* frontend call to sync metrc transfers / packages */
    public function syncMetrcTransfers(Request $request){
        
        if(($user = Auth::guard('api')->user()) == null) abort(401, 'You are no longer Authenticated, Please (re)Login');
        return response()->json($this->service->syncMetrcTransfers($user,data_get($user->location,'settings.metrc_last_transfer_sync',(time()-(86400*14)))));
        
    }
    
    public function syncMetrcPackages(Request $request){
        
        if(($user = Auth::guard('api')->user()) == null) abort(401, 'You are no longer Authenticated, Please (re)Login');
        return response()->json($this->service->syncMetrcPackages($user,data_get($user->location,'settings.metrc_last_package_sync',(time()-(86400*14)))));
        
    }
    
    
    /* frontend call to import a metrc transfer */
    public function importTransfer(Request $request, $id){
        
        if(($user = Auth::guard('api')->user()) == null) abort(401, 'You are no longer Authenticated, Please (re)Login');
        return response()->json($this->service->importMetrcTransfer($id,data_get($user->location,'settings.metrc_last_package_sync',(time()-(86400*14))),$user));
        
    }
    
    
    
}