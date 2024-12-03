<?php

namespace App\Http\Controllers\Api\V1\Grow;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Resources\Grow\TransferCollectionResource;
use App\Http\Resources\Grow\TransferResource;

use App\Services\Grow\TransferService;

use App\Models\Grow\Transfer;


use Auth;
use Exception;
use Carbon\Carbon;



/**
 * SuiteController.
 */
class TransferController extends ApiController
{
    
    private $service;


    public function __construct(TransferService $service){
        
        $this->service = $service;
        
    }


    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->search($request->all())) == null) abort(412,'Whoops - Could not fetch transfers data - please try again later');
        
        
        return new TransferCollectionResource($grid);

    }

    

    /* get Item info for editing */
    public function show(Request $request, $id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->show($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch transfer information - please try again later');


        return new TransferResource($item);
        
    }


    /* store new Item record */
    public function store(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($add = $this->service->create($request->all())) == null) abort(412,'Whoops - Could not create item entry - please try again later');
        
        
        return new TransferResource($add);
        
    }
    
    
    // Resend an "internal" transfer import (to receiving)
    public function resendTransferImport(Request $request,$id){
        
        if(($user = Auth::guard('api')->user()) == null) abort(401, 'You are no longer Authenticated, Please (re)Login');
        
        if(($success = $this->service->_importTransfer($id))===true)            // attempt an import to dispensary location (true) or record error
                $mes .= 'We have ReSubmitted your Internal Transfer to your Dispensary for Receiving';
        else $mes .= 'We could not re-submit a PurchaseOreder to your Dispensary Location: '.$success;
        
        
        return [
            'status'    => ($success ? 200 : 422),
            'message'   => $mes,
            'schema'    => Transfer::_getSchema()                               // include schema as this may update the scope of some filters.
        ];

    }
    
    
    public function modify(Request $request,$id){
        
        if(($user = Auth::guard('api')->user()) == null) abort(401, 'You are no longer Authenticated, Please (re)Login');
        
        if(($success = $this->service->modify($request->all(),$id,$user))===true)               // attempt an do a select set of modifications
            $mes = 'Transfer data has been updated';
        else $mes = 'We could not update this transfer: '.$success;
        
        
        return [
            'status'    => ($success ? 200 : 422),
            'message'   => $mes,
            'schema'    => Transfer::_getSchema()                               // include schema as this may update the scope of some filters.
        ];

    }
    

    public function cancel(ApiQueryRequest $request,$id){
        
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($status = $this->service->cancel($request->all(),$id,$user)) === null) abort(412,'Whoops - Could not Cancel the Transfer - please try again later');


        return $this->respondDeleted('Transfer has been Cancelled, abd all packages receiving has been reversed.');
    }

    public function generateInvoicePDF(ApiQueryRequest $request, $id, $type = 'PDF'){
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/transfer/'.$id.'/');
    }
    
    
    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/transfer/');

    }
    
}