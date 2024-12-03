<?php

namespace App\Http\Controllers\Api\V1\Dispensary;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Requests\Dispensary\TaxUpdateRequest;
use App\Http\Resources\Dispensary\TaxCollectionResource;
use App\Http\Resources\Dispensary\TaxResource;

use App\Services\Dispensary\TaxService;

use App\Models\Dispensary\Tax;

use Auth;
use Exception;
use Carbon\Carbon;


/**
 * SuiteController.
 */
class TaxController extends ApiController
{
    
    private $service;


    public function __construct(TaxService $service){
        
        $this->service = $service;
        
    }


    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->search($request->all())) == null) abort(412,'Whoops - Could not fetch the Taxes - please try again later');
        
        
        return new TaxCollectionResource($grid);

    }


    /* get Tax info for editing */
    public function show(Request $request, $id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->show($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch Tax information - please try again later');


        return new TaxResource($item);
        
    }

    public function rateShow(Request $request, $id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->rateShow($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch tax rate information - please try again later');


        return new TaxResource($item);
        
    }


    /* store new Tax record */
    public function store(TaxUpdateRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($add = $this->service->create($request->all(),$user)) == null) abort(412,'Whoops - Could not create Tax entry - please try again later');
        
        
        return new TaxResource($add);
        
    }
    
    /* store new Tax record */
    public function rateCreate(TaxUpdateRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($add = $this->service->rateCreate($request->all(),$user)) == null) abort(412,'Whoops - Could not create Tax entry - please try again later');
        
        
        return new TaxResource($add);
        
    }


    /* update Tax info */
    public function update(TaxUpdateRequest $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->update($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not update - please try again later');
        
        
        return new TaxResource($upd);
        
    }

    public function rateUpdate(TaxUpdateRequest $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->rateUpdate($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not update - please try again later');
        
        
        return new TaxResource($upd);
        
    }
    

    /* remove Tax entry */
    public function destroy(Request $request,$id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif($this->service->archive($request->all(),$id,$user)!=true) abort(412,'could not remove entry - aborting.');
        return $this->respondDeleted('Customer entry has been removed upon your request.');
        
    }    
    

    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/taxcategory/');

    }
    
    public function exportView(Request $request, $id, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/taxcategory/'.$id.'/');

    }
    
    
}