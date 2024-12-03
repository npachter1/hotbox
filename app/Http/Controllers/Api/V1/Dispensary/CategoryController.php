<?php

namespace App\Http\Controllers\Api\V1\Dispensary;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Requests\Dispensary\CategoryUpdateRequest;
use App\Http\Resources\Dispensary\CategoryCollectionResource;
use App\Http\Resources\Dispensary\CategoryResource;

use App\Services\Dispensary\CategoryService;

use App\Models\Dispensary\Category;


use Auth;
use Exception;
use Carbon\Carbon;



/**
 * SuiteController.
 */
class CategoryController extends ApiController
{
    
    private $service;


    public function __construct(CategoryService $service){
        
        $this->service = $service;
        
    }


    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->search($request->all())) == null) abort(412,'Whoops - Could not fetch the Categories - please try again later');
        
        
        return new CategoryCollectionResource($grid);

    }

    

    /* get Category info for editing */
    public function show(Request $request, $id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->show($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch category information - please try again later');


        return new CategoryResource($item);
        
    }


    /*batch grid search */
    public function batch(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->getBatch($request->all())) == null) abort(412,'Whoops - Could not fetch the category batch - please try again later');
        
        
        return new CategoryCollectionResource($item);

    }


    /* store new Category record */
    public function store(CategoryUpdateRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($add = $this->service->create($request->all(),$user)) == null) abort(412,'Whoops - Could not create category entry - please try again later');
        
        
        return new CategoryResource($add);
        
    }


    /* update Category info */
    public function update(CategoryUpdateRequest $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->update($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not update - please try again later');
        
        
        return new CategoryResource($upd);
        
    }


    /* batch update Category batch edit info */
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
    
    
    /* remove Category entry */
    public function destroy(Request $request,$id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif($this->service->archive($request->all(),$id,$user)!=true) abort(412,'could not remove entry - aborting.');
        return $this->respondDeleted('Category entry has been removed upon your request.');
        
    }
    
    
    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/category/');

    }
    
    public function exportView(Request $request, $id, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/category/'.$id.'/');

    }
    
    
    /* sync metrc categories api */
    public function syncMetrcCategories(Request $request){
        
        if(($user = Auth::guard('api')->user()) == null) abort(401, 'You are no longer Authenticated, Please (re)Login');
        return response()->json($this->service->syncMetrcCategories($user));
        
    }
    
    
    
    /* check if name exists */
    public function checkExist(Request $request){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');

        return response()->json([
                'exists'       => $this->service->checkNameExists($request->all(),$user)
            ],200);
        
    }
    
    
    
    
}