<?php

namespace App\Http\Controllers\Api\V1\Dispensary;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Requests\Dispensary\InventoryUpdateRequest;
use App\Http\Resources\Dispensary\InventoryCollectionResource;
use App\Http\Resources\Dispensary\InventoryLogCollectionResource;
use App\Http\Resources\Dispensary\InventoryStrainCollectionResource;
use App\Http\Resources\Dispensary\InventoryResource;

use App\Services\Dispensary\InventoryService;

use App\Models\Dispensary\Inventory;

use Auth;
use Exception;


/**
 * SuiteController.
 */
class InventoryController extends ApiController
{

    private $service;


    public function __construct(InventoryService $service){

        $this->service = $service;

    }


    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');

        if($request->input('is_search')==1) $grid = $this->service->search($request->all());
        elseif($request->input('is_scan')==1) $grid = $this->service->searchByBarcodeOrRfid($request->input('search'));
        else $grid = $this->service->onHand($request->all());


        return new InventoryCollectionResource($grid);

    }

    public function searchByBarcodeOrRfid($barcode){

        if (!$barcode) abort(422,'barcode parameter missing');

        $inventory = $this->service->searchByBarcodeOrRfid($barcode);
        return new InventoryResource($inventory);

    }


    /* get Inventory info for editing */
    public function show(Request $request, $id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->show($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch Inventory information - please try again later');


        return new InventoryResource($item);

    }

    /* show strains index manager */
    public function strainShow(Request $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($items = $this->service->strainShow($request->all())) == null) abort(412,'Whoops - Could not fetch trigger information - please try again later');


        return new InventoryStrainCollectionResource($items);

    }


    /*batch grid search */
    public function batch(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->getBatch($request->all())) == null) abort(412,'Whoops - Could not fetch the Product batch - please try again later');


        return new InventoryCollectionResource($item);

    }


    /* update Inventory info */
    public function update(InventoryUpdateRequest $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->update($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not process adjustment - please try again later');


        return new InventoryResource($upd);

    }


    /* update Strian index */
    public function strainUpdate(Request $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->strainUpdate($request->all(),$user)) == null) abort(412,'Whoops - Could not update - please try again later');

        $this->setStatusCode(data_get($upd,'status',200));
        return $this->respond($upd);

    }


    /* batch update Product batch edit info */
    public function updateBatch(Request $request,$type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');

        switch($type){
            case 'edit':
                $upd = $this->service->updateBatchData($request->all(),$user);
                break;
            case 'audit':
                $upd = $this->service->updateBatchAudit($request->all(),$user);
                break;
            default: abort(412, 'the Batch type of '.$type.' is not a supported function - aborting');
        }


        $this->setStatusCode(data_get($upd,'status',200));
        return $this->respond($upd);

    }


    /* remove Inventory entry */
    public function destroy(Request $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif($this->service->archive($request->all(),$id,$user)!=true) abort(412,'could not remove entry - aborting.');
        return $this->respondDeleted('Addressbook entry has been removed upon your request.');

    }


    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/inventory/');

    }

    public function exportView(Request $request, $id, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/inventory/'.$id.'/');

    }

    /* get a tag for an item */
    public function getTag(Request $request, $id){

        if(!$id) abort(404, 'Item ID not provided.');
        elseif(($user = Auth::guard()->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');


        // TODO return a download of a tag of an item








        abort(422, 'Feature coming soon..');

    }


    /* retrieve a current price for an inventory_item, passing quantity and checking its assigned priceset or fixed retail */
    public function getPricing(PricingRequest $request, $id){

        if(!$id) abort(404, 'Item ID not provided.');
        elseif(($user = Auth::guard()->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($pricing = $this->service->_getCurrentPrice($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not process pricing - please try again later');


        return $this->respond(['data'=>$pricing]);

    }


}
