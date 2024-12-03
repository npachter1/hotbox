<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Requests\Auth\ServicelogUpdateRequest;
use App\Http\Resources\Auth\ServicelogCollectionResource;
use App\Http\Resources\Auth\ServicelogResource;

use App\Services\Auth\ServicelogService;

use App\Models\Auth\Servicelog;

use Auth;


/**
 * SuiteController.
 */
class ServicelogController extends ApiController
{
    
    private $service;


    public function __construct(ServicelogService $service){
        
        $this->service = $service;
        
    }


    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->search($request->all())) == null) abort(412,'Whoops - Could not fetch the addresses - please try again later');
        
        
        return new ServicelogCollectionResource($grid);

    }


    /* get Servicelog info for editing */
    public function show(Request $request, $id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->show($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch address information - please try again later');


        return new ServicelogResource($item);
        
    }


    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/servicelog/');

    }
    

}