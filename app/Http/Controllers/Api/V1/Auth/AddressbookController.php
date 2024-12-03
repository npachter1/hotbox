<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Requests\Auth\AddressbookUpdateRequest;
use App\Http\Resources\Auth\AddressbookCollectionResource;
use App\Http\Resources\Auth\AddressbookResource;

use App\Services\Auth\AddressbookService;

use App\Models\Auth\Addressbook;

use Auth;



/**
 * SuiteController.
 */
class AddressbookController extends ApiController
{
    
    private $service;


    public function __construct(AddressbookService $service){
        
        $this->service = $service;
        
    }


    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->search($request->all())) == null) abort(412,'Whoops - Could not fetch the addresses - please try again later');
        
        
        return new AddressbookCollectionResource($grid);

    }


    /* get address info for editing */
    public function show(Request $request, $id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->show($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch address information - please try again later');


        return new AddressbookResource($item);
        
    }


    /*batch grid search */
    public function batch(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->getBatch($request->all())) == null) abort(412,'Whoops - Could not fetch the address batch - please try again later');
        
        
        return new AddressbookCollectionResource($upd);

    }


    /* store new address info */
    public function store(AddressbookUpdateRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($add = $this->service->create($request->all(),$user)) == null) abort(412,'Whoops - Could not create address entry - please try again later');
        
        
        return new AddressbookResource($add);
        
    }


    /* update address info */
    public function update(AddressbookUpdateRequest $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->update($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not update - please try again later');
        
        
        return new AddressbookResource($upd);
        
    }
    

    /* update address batch edit info */
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
    
    
    /* remove addressbook entry */
    public function destroy(Request $request,$id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif($this->service->archive($request->all(),$id,$user)!=true) abort(412,'could not remove entry - aborting.');
        return $this->respondDeleted('Addressbook entry has been removed upon your request.');
        
    }
    
    
    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/addressbook/');

    }
    
    public function exportView(Request $request, $id, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/addressbook/'.$id.'/');

    }
    
    
}
