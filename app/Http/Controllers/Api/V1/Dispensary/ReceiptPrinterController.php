<?php

namespace App\Http\Controllers\Api\V1\Dispensary;

use App\Services\Dispensary\ReceiptPrinterService;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;
use Auth;
use Exception;

class ReceiptPrinterController extends ApiController {

    private $service;

    public function __construct(ReceiptPrinterService $service)
    {
        $this->service = $service;
    }

    public function generateReceipt(Request $request, $id) {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        $receiptData = $this->service->generateReceipt($request->all(), $id);
        $receiptFile = base64_encode($receiptData->file());
        return response()->json(['receipt_file'=>$receiptFile, 'changedue'=>$receiptData->changedue()],200);
    }

    public function generateReceiptHTML(Request $request, $id) {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        $receiptHTML = $this->service->generateReceiptHTML($request->all(), $id);
        return response($receiptHTML);
    }

    public function generateReceiptPDF(Request $request, $id, $type){
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        return $this->service->exportPdf($request->all(),$id,'downloads/'.$user->suite_id.'/sale/'.$id.'/');
    }

    public function generateReceiptEmail(Request $request, $id) {
        if(($user = Auth::guard('api')->user()) == null)  abort(401, 'You are no longer Authenticated, Please (re)Login');
        $receiptCustomer = $this->service->generateReceiptEmail($request->all(), $id);
        return response()->json(['order'=>$receiptCustomer],200);
    }
    
}
