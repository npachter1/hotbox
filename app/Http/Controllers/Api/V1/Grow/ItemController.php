<?php

namespace App\Http\Controllers\Api\V1\Grow;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Requests\Grow\ItemAddRequest;
use App\Http\Requests\Grow\ItemUpdateRequest;
use App\Http\Resources\Grow\ItemCollectionResource;
use App\Http\Resources\Grow\ItemResource;

use App\Services\Grow\ItemService;

use App\Models\Grow\Item;


use Auth;
use Exception;
use Carbon\Carbon;



/**
 * SuiteController.
 */
class ItemController extends ApiController
{
    
    private $service;


    public function __construct(ItemService $service){
        
        $this->service = $service;
        
    }


    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->search($request->all())) == null) abort(412,'Whoops - Could not fetch item - please try again later');
        
        
        return new ItemCollectionResource($grid);

    }

    

    /* get Item info for editing */
    public function show(Request $request, $id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->show($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch item information - please try again later');


        return new ItemResource($item);
        
    }


    /* store new Item record */
    public function store(ItemAddRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($add = $this->service->create($request->all(),$user)) == null) abort(412,'Whoops - Could not create item entry - please try again later');
        
        
        return new ItemResource($add);
        
    }


    /* update Item info */
    public function update(ItemUpdateRequest $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->update($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not update - please try again later');
        
        
        return new ItemResource($upd);
        
    }
    
    
    /* remove Item entry */
    public function destroy(Request $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(!$id) abort(404, 'Item ID not provided.');
        elseif(($del = $this->service->destroy($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not remove - please try again later');


        return $this->respondDeleted($del.' has been Deleted upon your request.');
        
    }
    
    
    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/item/');

    }
    
    public function exportView(Request $request, $id, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/item/'.$id.'/');

    }
    
    
    
    
}