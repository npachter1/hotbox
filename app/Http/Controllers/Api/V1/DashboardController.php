<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Requests\ApiUploadAssetRequest;
use App\Http\Requests\ApiGraphRequest;

use App\Services\GraphService;
use App\Models\Auth\User;

use Auth;
use stdClass;
use Exception;
use App\Helpers\Util;


/**
 * AuthController.
 */
class DashboardController extends ApiController
{
    
    private $service;

    public function __construct(GraphService $service){
        
        $this->service = $service;

    }


    /* get a graph */
    public function getGraph(ApiGraphRequest $request,$name){
        
        if(($user = Auth::guard('api')->user()) == null) abort(401,'Unauthenticated - Please relogin');
        elseif(!method_exists($this->service,$name)) abort(422,'Cannot load graph - incorrect Graph Name');


        $pay = $this->service->{$name}($request->all(),$user);
        return response()->json((array)$pay,200);

    }

    

}
