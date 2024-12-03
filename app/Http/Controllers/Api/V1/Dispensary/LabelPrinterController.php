<?php

namespace App\Http\Controllers\Api\V1\Dispensary;

use App\Services\Dispensary\LabelPrinterService;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

class LabelPrinterController extends ApiController {

    private $service;

    public function __construct(LabelPrinterService $service)
    {
        $this->service = $service;
    }

    public function getDymoLabelSchema(Request $request)
    {
        return response()->json(['schema'=>$this->service->_getDymoLabelList()],200);
    }    


    public function render(Request $request) {
        $label = $this->service->generateLabel($request->all());
        return response()->json($label, 200);
    }
}
