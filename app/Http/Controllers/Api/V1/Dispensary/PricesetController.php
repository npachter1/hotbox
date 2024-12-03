<?php

namespace App\Http\Controllers\Api\V1\Dispensary;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Requests\Dispensary\PricesetUpdateRequest;
use App\Http\Resources\Dispensary\PricesetCollectionResource;
use App\Http\Resources\Dispensary\PricesetResource;

use App\Services\Dispensary\PricesetService;

use App\Models\Dispensary\Priceset;

use Auth;
use Exception;


/**
 * SuiteController.
 */
class PricesetController extends ApiController
{
    
    private $service;


    public function __construct(PricesetService $service){
        
        $this->service = $service;
        
    }


    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->search($request->all())) == null) abort(412,'Whoops - Could not fetch the Pricesets - please try again later');
        
        
        return new PricesetCollectionResource($grid);

    }


    /* get Priceset info for editing */
    public function show(Request $request, $id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->show($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch Priceset information - please try again later');


        return new PricesetResource($item);
        
    }

    /* store new Priceset record */
    public function store(PricesetUpdateRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($add = $this->service->create($request->all())) == null) abort(412,'Whoops - Could not create Priceset entry - please try again later');
        
        
        return new PricesetResource($add);
        
    }


    /* update Priceset info */
    public function update(PricesetUpdateRequest $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->update($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not update - please try again later');
        
        
        return new PricesetResource($upd);
        
    }
    

    /* remove Priceset entry */
    public function destroy(Request $request,$id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif($this->service->archive($request->all(),$id,$user)!=true) abort(412,'could not remove entry - aborting.');
        return $this->respondDeleted('Customer entry has been removed upon your request.');
        
    }
    
    
    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/priceset/');

    }
    
    public function exportView(Request $request, $id, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/priceset/'.$id.'/');

    }
    
    
}