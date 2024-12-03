<?php

namespace App\Http\Controllers\Api\V1\Grow;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Resources\Grow\HarvestCollectionResource;
use App\Http\Resources\Grow\HarvestResource;

use App\Services\Grow\HarvestService;

use App\Models\Grow\Harvest;


use Auth;
use Exception;
use Carbon\Carbon;



/**
 * SuiteController.
 */
class HarvestController extends ApiController
{
    
    private $service;


    public function __construct(HarvestService $service){
        
        $this->service = $service;
        
    }


    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->search($request->all())) == null) abort(412,'Whoops - Could not fetch item - please try again later');
        
        
        return new HarvestCollectionResource($grid);

    }

    

    /* get Item info for editing */
    public function show(Request $request, $id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->show($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch item information - please try again later');


        return new HarvestResource($item);
        
    }

    public function removeWaste(ApiQueryRequest $request)
    {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($items = $this->service->removeWaste($request->all())) == null) abort(412,'Whoops - Could not fetch the Harvests - please try again later');
        
        return $items;
    }
    
    public function finish(ApiQueryRequest $request)
    {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($items = $this->service->finish($request->all(), $user)) == null) abort(412,'Whoops - Could not fetch the Harvests - please try again later');
        
        return $items;
    }
    
    public function unfinish(ApiQueryRequest $request)
    {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($items = $this->service->unfinish($request->all(), $user)) == null) abort(412,'Whoops - Could not fetch the Harvests - please try again later');
        
        return $items;
    }
    
    public function createPackage(ApiQueryRequest $request)
    {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($items = $this->service->createPackage($request->all(), $user)) == null) abort(412,'Whoops - Could not fetch the Harvests - please try again later');
        
        
        return $items;
    }
    
    public function createPackages(ApiQueryRequest $request)
    {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($items = $this->service->createPackages($request->all(), $user)) == null) abort(412,'Whoops - Could not fetch the Harvests - please try again later');
        
        
        return $items;
    }

    public function addMaterial(ApiQueryRequest $request, $id)
    {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return new HarvestMaterialRecordResource($this->service->addMaterial($request->input(), $id));
    }

    public function addActivity(ApiQueryRequest $request, $id)
    {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return new HarvestActivityRecordResource($this->service->addActivity($request->input(), $id));
    }
    
    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/harvest/');

    }
    
    public function exportView(Request $request, $id, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/harvest/'.$id.'/');

    }
    
    
    /* batch grid search */
    public function batch(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->getBatch($request->all())) == null) abort(412,'Whoops - Could not fetch the Harvests batch - please try again later');
        
        
        return new HarvestCollectionResource($item);

    }
    
}