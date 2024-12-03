<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Illuminate\Http\Request;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Requests\Auth\LocationsUpdateRequest;
use App\Http\Resources\Auth\LocationsCollectionResource;
use App\Http\Resources\Auth\LocationsResource;

use App\Services\Auth\LocationsService;

use App\Models\Auth\Location;

use App\Helpers\LocationScope;

use Auth;
use Exception;


/**
 * LocationController.
 */
class LocationsController extends ApiController
{
    
    private $service;


    public function __construct(LocationsService $service){
        
        $this->service = $service;
        
    }

    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($locations = $this->service->search($request->all(),$user)) == null) abort(412,'Whoops - Could not fetch the addresses - please try again later');
        
        
        return new LocationsCollectionResource($locations);

    }


    /* get address info for editing */
    public function show(Request $request, $id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($location = $this->service->show($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch address information - please try again later');


        return new LocationsResource($location);
        
    }


    /* update address info */
    public function update(LocationsUpdateRequest $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->update($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not update - please try again later');
        
        
        return new LocationsResource($upd);
        
    }

    
    /* modify a location account */
    public function modify(Request $request,$id,$type){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(!$user->can('Staff Update')) abort(403,'Caller User does not have permission to modify location data'); 
        
        switch($type){
            case 'status':
                if(($mes = $this->service->modifyAccountStatus($request->all(),$id,$user)) == null) abort(400,'Whoops - Could not update - please try again later');
                break;
            case 'links':
                if(($mes = $this->service->modifyAccountLinks($request->all(),$id,$user)) == null) abort(400,'Whoops - Could not update - please try again later');
                break;
            default: abort(422,'Sorrty, Location Modification type not registered - aborting.');
        }


        return response()->json(['message'=>$mes],200);
        
    }


    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->location_id.'/Locations/');

    }
    
    public function exportView(Request $request, $id, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/location/'.$id.'/');

    }

    
}
