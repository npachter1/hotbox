<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Illuminate\Http\Request;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Requests\Auth\LocationpayChargeRequest;
use App\Http\Requests\Auth\LocationUpdateRequest;
use App\Http\Resources\Auth\LocationResource;
use App\Http\Resources\Auth\TransactionCollectionResource;

use App\Services\Auth\AuthService;
use App\Services\Auth\LocationsService;

use App\Models\Auth\Location;

use Auth;
use Exception;


/**
 * LocationController.
 */
class LocationController extends ApiController
{

    private $service;


    public function __construct(AuthService $service){

        $this->service = $service;

    }


    /* get location data*/
    public function show(Request $request, $id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->showLocation($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch location data at the moment - please try again later');


        return new LocationResource($item);

    }


    /* update Location & profile */
    public function update(LocationUpdateRequest $request,$id){
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($location = $this->service->updateLocationProfile($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not process resource - please try again later');


        return new LocationResource($location);

    }

    /* update label printer info */
    public function updateLabelPrinter(Request $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->updateLabelPrinter($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not update - please try again later');


        return new LocationResource($upd);

    }

    public function isMedical($id) {
        $service = new LocationsService;
        return $service->isMedical($id);
    }


}
