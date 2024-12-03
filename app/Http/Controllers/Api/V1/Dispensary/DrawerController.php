<?php

namespace App\Http\Controllers\Api\V1\Dispensary;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Requests\Dispensary\DrawerUpdateRequest;
use App\Http\Resources\Dispensary\DrawerCollectionResource;
use App\Http\Resources\Dispensary\DrawerResource;

use App\Services\Dispensary\DrawerService;

use App\Models\Dispensary\Drawer;

use Auth;
use Exception;


/**
 * SuiteController.
 */
class DrawerController extends ApiController
{
    
    private $service;


    public function __construct(DrawerService $service){
        
        $this->service = $service;
        
    }


    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->search($request->all())) == null) abort(412,'Whoops - Could not fetch the Drawers - please try again later');
        
        
        return new DrawerCollectionResource($grid);

    }


    /* get Open Drwaer */
    public function getOpenDrawer(Request $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($drawer = $this->service->_currentDrawer($request->all(),$user)) == null) abort(412,'Whoops - Could not fetch the current drawer - please try again later');

        return new DrawerResource($drawer);

    }


    /* get Drawer info for editing */
    public function show(Request $request, $id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->show($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch Drawer information - please try again later');


        return new DrawerResource($item);
        
    }


    /*batch grid search */
    public function batch(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->getBatch($request->all())) == null) abort(412,'Whoops - Could not fetch the Drawer batch - please try again later');
        
        
        return new DrawerCollectionResource($item);

    }


    /* store new Drawer record */
    public function store(Request $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($add = $this->service->create($request->all(),$user)) == null) abort(412,'Whoops - Could not create Drawer entry - please try again later');
        
        
        return new DrawerResource($add);
        
    }


    /* update Drawer info */
    public function update(DrawerUpdateRequest $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->update($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not update - please try again later');
        
        
        return new DrawerResource($upd);
        
    }
    
    
    /* update a drawer */
    public function action(DrawerUpdateRequest $request, $id, $type){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');

        switch($type){
            case 'payout':
                $upd = $this->service->payOut($request->all(),$id,$user);
                break;
            case 'payin':
                $upd = $this->service->payIn($request->all(),$id,$user);
                break;
            case 'close':
                $upd = $this->service->close($request->all(),$id,$user);
                break;
            case 'reassign':
                $upd = $this->service->reassign($request->all(),$id,$user);
                break;
            default: abort(412, 'the type of '.$type.' is not a supported function - aborting');
        } 
        

        return new DrawerResource($upd);
        
    }
    

    /* batch update Drawer batch edit info */
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

    
    /* remove Drawer entry */
    public function destroy(Request $request,$id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif($this->service->archive($request->all(),$id,$user)!=true) abort(412,'could not remove entry - aborting.');
        return $this->respondDeleted('Drawer entry has been removed upon your request.');
        
    }
    
    
    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/drawer/');

    }
    
    public function exportView(Request $request, $id, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/drawer/'.$id.'/');

    }
    
    
}