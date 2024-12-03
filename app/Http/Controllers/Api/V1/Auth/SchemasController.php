<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Illuminate\Http\Request;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\Auth\SchemasUpdateRequest;
use App\Http\Resources\Auth\SchemasCollectionResource;
use App\Http\Resources\Auth\SchemasResource;

use App\Services\Auth\SchemasService;

use App\Models\AppSchema;

use Auth;
use Exception;


/**
 * Schemas Controller.
 */
class SchemasController extends ApiController
{
    
    private $service;


    public function __construct(SchemasService $service){
        
        $this->service = $service;
        
    }

    /* list of schemas */
    public function list(Request $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(!$user->can('manage-server')) abort(403, 'You do not have permission to use this section.');
        elseif(($schemas = $this->service->list($request->all())) == null) abort(412,'Whoops - Could not fetch the schemas list - please try again later');
        
        return $schemas;
        //return new SchemasCollectionResource($schemas);

    }


    /* update schema content */
    public function update(SchemasUpdateRequest $request,$code){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->update($request->all(),$code)) == null) abort(412,'Whoops - Could not update - please try again later');
        
        
        return new SchemasResource($upd);
        
    }
    
    
    /* remove schema record */
    public function destroy(Request $request,$code){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(!$user->can('manage-server')) abort(403, 'You do not have permission to use this section.');
        elseif(!$code) abort(404, 'Item ID not provided.');
        elseif(($sch = AppSchema::find($code)) == null) abort(412,'Whoops - could not locate entry - aborting.');

        // TODO deep logic check if schema can be deleted!
        //abort(412,'Whoops - this schema cannot be deleted due to an administrative setting - sorry.');
        
        $sch->delete();                                                         // this WILL delete schema row - tread with caution!!


        return $this->respondDeleted('Schema for '.$sch->code.' has been PERMANATELY Deleted upon your request.  Goodbye.');
        
    }
    
}
