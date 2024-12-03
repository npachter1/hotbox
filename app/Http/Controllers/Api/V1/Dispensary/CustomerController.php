<?php

namespace App\Http\Controllers\Api\V1\Dispensary;

use App\Mail\CustomerRegistered;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Requests\Dispensary\CustomerUpdateRequest;
use App\Http\Resources\Dispensary\CustomerCollectionResource;
use App\Http\Resources\Dispensary\CustomerResource;

use App\Services\Dispensary\CustomerService;
use App\Models\Dispensary\Customer;

use App\Helpers\Util;
use App\Events\CustomerQueueUpdated;

use Auth;
use Exception;


/**
 * SuiteController.
 */
class CustomerController extends ApiController
{

    private $service;


    public function __construct(CustomerService $service){

        $this->service = $service;

    }


    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->search($request->all())) == null) abort(412,'Whoops - Could not fetch the Customers - please try again later');


        return new CustomerCollectionResource($grid);

    }

    public function queueIndex(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->searchQueue($request->all(),$user)) == null) abort(412,'Whoops - Could not fetch the Customer Queue - please try again later');


        return new CustomerCollectionResource($grid);

    }


    /* get Customer info for editing */
    public function show(Request $request, $id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->show($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch Customer information - please try again later');


        return new CustomerResource($item);

    }

    /* customer login */
    public function login(Request $request){


        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->login($request->all())) == null) abort(412,'Whoops - Could not fetch Customer information - please try again later');



        return new CustomerResource($item);

    }

    /*batch grid search */
    public function batch(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->getBatch($request->all())) == null) abort(412,'Whoops - Could not fetch the Customer batch - please try again later');


        return new CustomerCollectionResource($item);

    }


    /* store new Customer record */
    public function store(CustomerUpdateRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($add = $this->service->create($request->all(),$user)) == null) abort(412,'Whoops - Could not create Customer entry - please try again later');

        //send email to customer
        if($add->address->email){
            \Mail::to($add->address->email)
                ->queue(new CustomerRegistered($add));
        }

        return new CustomerResource($add);

    }


    /* update Customer info */
    public function update(CustomerUpdateRequest $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->update($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not update - please try again later');


        return new CustomerResource($upd);

    }

    /* update Customer info */
    public function queueUpdate(Request $request,$type,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');

        switch($type){
            case 'add':
                $upd = $this->service->addToQueue($request->all(),$id,$user);
                break;
            case 'service':
                $upd = $this->service->setServiceQueue($request->all(),$id,$user);
                break;
            case 'remove':
                $upd = $this->service->removeFromQueue($request->all(),$id,$user);
                break;
            default: abort(412, 'The Customer Queue action of '.$type.' is not a supported function - aborting');
        }

        try {
            $grid = $this->service->searchQueue($request->all(),$user);
            event(new CustomerQueueUpdated($user, $grid->toJson()));
        }
        catch(\Exception $e){
            //TODO: handle error because the socket service is temporarily down
        }

        return new CustomerResource($upd);

    }


    /* batch update Customer batch edit info */
    public function updateBatch(Request $request,$type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');

        switch($type){
            case 'edit':
                $upd = $this->service->updateBatch($request->all(),$user);
                break;
            case 'addtogroup':
                $upd = $this->service->addToGroup($request->all(),$user);
                break;
            case 'archive':
                $upd = $this->service->updateBatchArchive($request->all(),$user);
                break;
            default: abort(412, 'the Batch type of '.$type.' is not a supported function - aborting');
        }


        $this->setStatusCode(data_get($upd,'status',200));
        return $this->respond($upd);

    }


    /* remove Customer entry */
    public function destroy(Request $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif($this->service->archive($request->all(),$id,$user)!=true) abort(412,'could not remove entry - aborting.');
        return $this->respondDeleted('Customer entry has been removed upon your request.');

    }

    /* remove Customer queue entry */
    public function queueClear(Request $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif($this->service->clearQueue($request->all(),$user)!=true) abort(412,'could not clear the queue - aborting.');
        try {
            $grid = $this->service->searchQueue($request->all(),$user);
            event(new CustomerQueueUpdated($user, $grid->toJson()));
        }
        catch(\Exception $e){
            //TODO: handle error because the socket service is temporarily down
        }
        return $this->respondDeleted('Customer Queue has been cleared for this Location.');

    }


    /* get a smurf name */
    public function getSmurfName(Request $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($smurfUp = Util::getSmurfName($user))===false) abort(422, 'No More Smurf names to assign at the moment.');

        $this->setStatusCode(200);
        return $this->respond((object)['smurf'=>$smurfUp]);

    }

    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/customer/');

    }

    public function exportView(Request $request, $id, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/customer/'.$id.'/');

    }


}
