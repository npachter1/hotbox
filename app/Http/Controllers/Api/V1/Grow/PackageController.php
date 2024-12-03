<?php

namespace App\Http\Controllers\Api\V1\Grow;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Resources\Grow\PackageCollectionResource;
use App\Http\Resources\Grow\PackageResource;

use App\Services\Grow\PackageService;

use App\Models\Grow\Package;


use Auth;
use Exception;
use Carbon\Carbon;



/**
 * SuiteController.
 */
class PackageController extends ApiController
{
    
    private $service;


    public function __construct(PackageService $service){
        
        $this->service = $service;
        
    }


    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->search($request->all())) == null) abort(412,'Whoops - Could not fetch item - please try again later');
        
        
        return new PackageCollectionResource($grid);

    }

    

    /* get Item info for editing */
    public function show(Request $request, $id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->show($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch item information - please try again later');


        return new PackageResource($item);
        
    }

    public function update(ApiQueryRequest $request, $id)
    {
        return new PackageResource($this->service->update($request->input(), $id));
    }

    public function changeItem(ApiQueryRequest $request)
    {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->changeItem($request->all(),$user)) == null) abort(412,'Whoops - Could not fetch item information - please try again later');


        return new PackageResource($item);
    }

    public function adjust(ApiQueryRequest $request)
    {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->adjust($request->all(),$user)) == null) abort(412,'Whoops - Could not fetch item information - please try again later');


        return new PackageResource($item);
    }

    public function createPackage(ApiQueryRequest $request)
    {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->createPackage($request->all(),$user)) == null) abort(412,'Whoops - Could not fetch item information - please try again later');


        return new PackageResource($item);
    }

    public function createPackages(ApiQueryRequest $request)
    {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->createPackages($request->all(),$user)) == null) abort(412,'Whoops - Could not fetch item information - please try again later');


        return new PackageCollectionResource($item);
    }

    public function remediate(ApiQueryRequest $request)
    {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->remediate($request->all(),$user)) == null) abort(412,'Whoops - Could not fetch item information - please try again later');


        return new PackageResource($item);
    }
    
    
    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/package/');

    }
    
    public function exportView(Request $request, $id, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/package/'.$id.'/');

    }
    
    
    /* batch grid search */
    public function batch(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->getBatch($request->all())) == null) abort(412,'Whoops - Could not fetch the Packages batch - please try again later');
        
        
        return new PackageCollectionResource($item);

    }
    
}