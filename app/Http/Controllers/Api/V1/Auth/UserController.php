<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Illuminate\Http\Request;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Resources\Auth\UserResource;
use App\Http\Resources\Auth\UserProfileResource;
use App\Http\Resources\Auth\UserCollectionResource;
use App\Http\Requests\Auth\UserUpdateRequest;
use App\Http\Requests\Auth\UserCreateRequest;

use App\Services\Auth\AuthService;

use App\Models\Auth\User;

use Auth;
use DB;
use stdClass;
use Exception;
use Log;


/**
 * User Controller.
 */
class UserController extends APIController
{
    
    private $service;

    public function __construct(AuthService $service){
        
        $this->service = $service;
        
    }
    

    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($users = $this->service->searchUsers($request->all())) == null) abort(412,'Whoops - Could not fetch this grid resource - please try again later');
        
        
        return new UserCollectionResource($users);

    }


    /* get user info for editing */
    public function show(Request $request, $id){

        if(($user = $this->service->showUser($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch this users information - please try again later');


        return new UserProfileResource($user);
        
    }
    
    
    /* store (internal create) new user */
    public function store(UserCreateRequest $request){
        
        if(($user = $this->service->storeUser($request->all())) == null) abort(412,'Whoops - Could not create the new user - please try again later');


        return new UserProfileResource($user);
        
    }
    

    /* update appSuite & profile */
    public function update(UserUpdateRequest $request,$id){

        if(($user = $this->service->updateUserProfile($request->all(),$id)) == null) abort(412,'Whoops - Could not update - please try again later');
        
        
        return new UserProfileResource($user);
        
    }
    


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request,$id){
        
        if(($user = User::find($id)) == null) abort(422,'Whoops - We temporarially Could not find this users record - please try again later');
        
        
        return response()->json(['message' => 'Sorry, this function has been depricated.'], 403);
        
    }


    /**
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif($this->service->archiveUser($request->all(),$id,$user)!=true) abort(412,'could not remove entry - aborting.');
        return $this->respondDeleted('User has been archived upon your request.');

    }
    
    
    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/user/');

    }

    /* export view pdf */
    public function exportView(Request $request, $id,$type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');

        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/user/'.$id.'/');

    }

    public function hasPermissions(Request $request){
        try{
            $user = User::where('id', $request->input('id'))->firstOrFail();

            if($user->can($request->input('permission'))){
                return response()->json(['message' => 'This user has the correct permissions'], 200);
            }else{
                return response()->json(['message' => 'Sorry, this user is not authorized to take this action.'], 403);
            }
        }catch(Exception $error){
            return response()->json(['message' => 'Sorry, this user was not found.'], 404);
        }
            


    }
    
    
    /* unarchive */
    public function unarchive(Request $request,$id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->unarchiveUser($request->all(),$id,$user))==null) abort(412,'could not unarchive user - aborting.');

        return $this->respond($upd);
        
    }
    


}
