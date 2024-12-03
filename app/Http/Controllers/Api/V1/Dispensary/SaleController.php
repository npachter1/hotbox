<?php

namespace App\Http\Controllers\Api\V1\Dispensary;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Requests\Dispensary\SaleUpdateRequest;
use App\Http\Resources\Dispensary\SaleCollectionResource;
use App\Http\Resources\Dispensary\SaleResource;

use App\Services\Dispensary\SaleService;

use App\Models\Dispensary\Sale;

use Auth;
use Exception;


/**
 * SuiteController.
 */
class SaleController extends ApiController
{

    private $service;


    public function __construct(SaleService $service){

        $this->service = $service;

    }


    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->search($request->all())) == null) abort(412,'Whoops - Could not fetch the Sales - please try again later');


        return new SaleCollectionResource($grid);

    }


    /* get Sale info for editing */
    public function show(Request $request, $id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->show($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch Sale information - please try again later');


        return new SaleResource($item);

    }


    /*batch grid search */
    public function batch(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->getBatch($request->all())) == null) abort(412,'Whoops - Could not fetch the Sale batch - please try again later');


        return new SaleCollectionResource($item);

    }


    /* store new Sale record */
    public function store(Request $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($add = $this->service->create($request->all(),$user)) == null) abort(412,'Whoops - Could not create Sale entry - please try again later');

        $this->service->customerUpdated($user, $add->customer_id);

        return new SaleResource($add);

    }


    /* update Sale info */
    public function update(SaleUpdateRequest $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->update($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not update - please try again later');


        return new SaleResource($upd);

    }

    public function voidOrder(Request $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->voidOrder($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not void Sale Order - please try again later');

        $this->service->customerUpdated($user, $upd->customer_id);

        return new SaleResource($upd);

    }


    /* for processing a return (negative ticket) */
    public function returnOrder(Request $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->returnOrder($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not Return/Modify Sale Order - please try again later');

        return new SaleResource($upd);

    }



    public function payOrder(Request $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->storeOrderPayments($request->all(),$id,$user)) == null) abort(422,'No Payments were made for this order..');

        $this->service->customerUpdated($user, $upd->customer_id);

        return new SaleResource($upd);

    }

    public function payAccount(Request $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->payAccount($request->all(),$id,$user)) == null) abort(422,'No Payments could be made against this account - try later?..');

        $this->setStatusCode(data_get($upd,'status',200));
        return $this->respond($upd);

    }


    /* batch update Sale batch edit info */
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


    /* remove Sale entry */
    public function destroy(Request $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif($this->service->archive($request->all(),$id,$user)!=true) abort(412,'could not remove entry - aborting.');
        return $this->respondDeleted('Sale entry has been removed upon your request.');

    }


    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/sale/');

    }

    public function exportView(Request $request, $id, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/sale/'.$id.'/');

    }


}
