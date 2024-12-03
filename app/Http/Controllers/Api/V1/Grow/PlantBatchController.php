<?php

namespace App\Http\Controllers\Api\V1\Grow;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Requests\Grow\PlantBatchUpdateRequest;
use App\Http\Resources\Grow\PlantBatchCollectionResource;
use App\Http\Resources\Grow\PlantBatchResource;

use App\Services\Grow\PlantBatchService;

use App\Models\Grow\PlantBatch;


use Auth;
use Exception;
use Carbon\Carbon;



/**
 * SuiteController.
 */
class PlantBatchController extends ApiController
{
    
    private $service;


    public function __construct(PlantBatchService $service){
        
        $this->service = $service;
        
    }


    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->search($request->all())) == null) abort(412,'Whoops - Could not fetch item - please try again later');
        
        
        return new PlantBatchCollectionResource($grid);

    }

    

    /* get Item info for editing */
    public function show(Request $request, $id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->show($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch item information - please try again later');

        return new PlantBatchResource($item);
        
    }

    public function store(PlantBatchUpdateRequest $request)
    {
        return new PlantBatchResource($this->service->store($request->input()));
    }

    public function storeMetrc(Request $request)
    {
        return new PlantBatchResource($this->service->storeMetrc($request->input()));
    }

    public function move(Request $request, $id)
    {
        return new PlantBatchResource($this->service->move($request->input(), $id));
    }

    public function changeGrowthPhase(Request $request, $id)
    {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return new PlantBatchResource($this->service->changeGrowthPhase($request->input(), $id, $user));
    }

    public function splitBatch(Request $request)
    {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return new PlantBatchResource($this->service->splitBatch($request->input()));
    }

    public function destroyImmature(Request $request, $id)
    {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return new PlantBatchResource($this->service->destroy($request->input(), $id, $user));
    }
    
    public function createPackage(Request $request)
    {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return new PlantBatchResource($this->service->createPackage($request->input()));
    }

    public function addMaterial(Request $request, $id)
    {
        return new PlantBatchMaterialRecordResource($this->service->addMaterial($request->input(), $id));
    }

    public function addActivity(Request $request, $id)
    {
        return new PlantBatchActivityRecordResource($this->service->addActivity($request->input(), $id));
    }

    public function update(PlantBatchUpdateRequest $request, $id)
    {
        return new PlantBatchResource($this->service->update($request->input(), $id));
    }

    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/plantbatch/');

    }
    
    public function exportView(Request $request, $id, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/plantbatch/'.$id.'/');

    }
    
    
    
    
}