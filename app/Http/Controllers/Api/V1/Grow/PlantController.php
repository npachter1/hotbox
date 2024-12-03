<?php

namespace App\Http\Controllers\Api\V1\Grow;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Resources\Grow\PlantCollectionResource;
use App\Http\Resources\Grow\PlantResource;

use App\Services\Grow\PlantService;

use App\Models\Grow\Plant;


use Auth;
use Exception;
use Carbon\Carbon;



/**
 * SuiteController.
 */
class PlantController extends ApiController
{
    
    private $service;


    public function __construct(PlantService $service){
        
        $this->service = $service;
        
    }


    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->search($request->all())) == null) abort(412,'Whoops - Could not fetch item - please try again later');
        
        
        return new PlantCollectionResource($grid);

    }

    

    /* get Item info for editing */
    public function show(Request $request, $id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->show($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch item information - please try again later');


        return new PlantResource($item);
        
    }

    public function update(ApiQueryRequest $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->update($request->all(),$id)) == null) abort(412,'Whoops - Could not update - please try again later');
        
        
        return new PlantResource($upd);
        
    }

    public function createPlantings(Request $request)
    {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->createPlantings($request->all())) == null) abort(412,'Whoops - Could not fetch item information - please try again later');
        return new PlantResource($item);
    }

    public function destroy(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($items = $this->service->destroy($request->all(), $user)) == null) abort(412,'Whoops - Could not fetch the Plants - please try again later');
        
        
        return $items;

    }

    public function movePlants(ApiQueryRequest $request)
    {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($items = $this->service->movePlants($request->all(), $user)) == null) abort(412,'Whoops - Could not fetch the Plants - please try again later');
        
        
        return $items;
    }
    
    public function changeGrowthPhase(ApiQueryRequest $request)
    {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($items = $this->service->changeGrowthPhase($request->all(), $user)) == null) abort(412,'Whoops - Could not fetch the Plants - please try again later');
        
        return $items;
    }
    
    public function manicure(ApiQueryRequest $request)
    {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($items = $this->service->manicure($request->all(), $user)) == null) abort(412,'Whoops - Could not fetch the Plants - please try again later');
        
        
        return new PlantCollectionResource($items);
    }
    
    public function harvest(ApiQueryRequest $request)
    {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($items = $this->service->harvest($request->all(), $user)) == null) abort(412,'Whoops - Could not fetch the Plants - please try again later');
        
        
        return $items;
    }
    
    
    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/plant/');

    }
    
    public function exportView(Request $request, $id, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/plant/'.$id.'/');

    }
    
    
    /* batch grid search */
    public function batch(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->getBatch($request->all())) == null) abort(412,'Whoops - Could not fetch the Plants batch - please try again later');
        
        
        return new PlantCollectionResource($item);

    }
    
}