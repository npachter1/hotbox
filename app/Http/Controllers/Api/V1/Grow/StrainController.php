<?php

namespace App\Http\Controllers\Api\V1\Grow;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Requests\Grow\StrainUpdateRequest;
use App\Http\Resources\Grow\StrainCollectionResource;
use App\Http\Resources\Grow\StrainResource;

use App\Services\Grow\StrainService;

use App\Models\Grow\Strain;


use Auth;
use Exception;
use Carbon\Carbon;



/**
 * SuiteController.
 */
class StrainController extends ApiController
{
    
    private $service;


    public function __construct(StrainService $service){
        
        $this->service = $service;
        
    }


    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->search($request->all())) == null) abort(412,'Whoops - Could not fetch strain - please try again later');
        
        
        return new StrainCollectionResource($grid);

    }

    

    /* get Strain info for editing */
    public function show(Request $request, $id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->show($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch strain information - please try again later');


        return new StrainResource($item);
        
    }


    /* store new Strain record */
    public function store(StrainUpdateRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($add = $this->service->create($request->all())) == null) abort(412,'Whoops - Could not create strain entry - please try again later');
        
        
        return new StrainResource($add);
        
    }


    /* update Strain info */
    public function update(StrainUpdateRequest $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->update($request->all(),$id)) == null) abort(412,'Whoops - Could not update - please try again later');
        
        
        return new StrainResource($upd);
        
    }
    
    
    /* remove Strain entry */
    public function destroy(Request $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(!$id) abort(404, 'Item ID not provided.');
        elseif(($del = $this->service->destroy($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not remove - please try again later');


        return $this->respondDeleted($del.' has been Deleted upon your request.');
        
    }
    
    
    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/strain/');

    }
    
    public function exportView(Request $request, $id, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/strain/'.$id.'/');

    }
    
    
    
    
}