<?php

namespace App\Http\Controllers\Api\V1\Dispensary;

use App\Services\Dispensary\InventoryService;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Requests\ApiQueryRequest;
use App\Http\Requests\Dispensary\ProductUpdateRequest;
use App\Http\Resources\Dispensary\ProductCollectionResource;
use App\Http\Resources\Dispensary\ProductResource;

use App\Services\Dispensary\ProductService;

use App\Models\Dispensary\Product;

use Auth;
use Exception;
use Carbon\Carbon;


/**
 * SuiteController.
 */
class ProductController extends ApiController
{

    private $service;


    public function __construct(ProductService $service){

        $this->service = $service;

    }


    /* index grid search */
    public function index(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($grid = $this->service->search($request->all())) == null) abort(412,'Whoops - Could not fetch the Products - please try again later');


        //Allow to include pricing of last received inventory for online
        if($request->has('withPricing')) {
            //Include default pricing
            $invService = new InventoryService();
            foreach ($grid as $g) {
                $g->current_price = $invService->_getCurrentPrice([], $g->inventory->first()->id);
            }
        }

        return new ProductCollectionResource($grid);

    }


    /* get Product info for editing */
    public function show(Request $request, $id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->show($request->all(),$id)) == null) abort(412,'Whoops - Could not fetch Product information - please try again later');


        return new ProductResource($item);

    }


    /*batch grid search */
    public function batch(ApiQueryRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($item = $this->service->getBatch($request->all())) == null) abort(412,'Whoops - Could not fetch the Product batch - please try again later');


        return new ProductCollectionResource($item);

    }


    /* store new Product record */
    public function store(ProductUpdateRequest $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($add = $this->service->create($request->all(),$user)) == null) abort(412,'Whoops - Could not create Product entry - please try again later');


        return new ProductResource($add);

    }


    /* update Product info */
    public function update(ProductUpdateRequest $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif(($upd = $this->service->update($request->all(),$id,$user)) == null) abort(412,'Whoops - Could not update - please try again later');


        return new ProductResource($upd);

    }


    /* batch update Product batch edit info */
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


    /* remove Product entry */
    public function destroy(Request $request,$id){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        elseif($this->service->archive($request->all(),$id,$user)!=true) abort(412,'could not remove entry - aborting.');
        return $this->respondDeleted('Customer entry has been removed upon your request.');

    }


    /* download export */
    public function exportGrid(ApiQueryRequest $request, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportCollection($request->all(),$type,'downloads/'.$user->suite_id.'/product/');

    }

    public function exportView(Request $request, $id, $type){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/product/'.$id.'/');

    }


    /* check if name exists */
    public function checkExist(Request $request){

        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');

        return response()->json([
                'exists'       => $this->service->checkNameExists($request->all(),$user)
            ],200);

    }


}
