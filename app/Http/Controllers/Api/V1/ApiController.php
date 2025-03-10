<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use Illuminate\Http\Response as IlluminateResponse;

use Response;
use Validator;


/**
 * Base API Controller.
 */
class ApiController extends Controller
{

    protected $statusCode = 200;
    protected $invalidMessage = null;
    
    

    /**
     * get the status code.
     *
     * @return statuscode
     */
    public function getStatusCode(){
        
        return $this->statusCode;
        
    }



    /**
     * set the status code.
     *
     * @param [type] $statusCode [description]
     *
     * @return mix
     */
    public function setStatusCode($statusCode){
        
        $this->statusCode = $statusCode;

        return $this;
        
    }


    /**
     * responsd not found.
     *
     * @param string $message
     *
     * @return mix
     */
    public function respondNotFound($message = 'Not Found'){
        
        return $this->setStatusCode(IlluminateResponse::HTTP_NOT_FOUND)->respondWithError($message);
        
    }


    /**
     * Respond with error.
     *
     * @param string $message
     *
     * @return mix
     */
    public function respondInternalError($message = 'Internal Error'){
        
        return $this->setStatusCode('500')->respondWithError($message);
    }


    /**
     * Respond.
     *
     * @param array $data
     * @param array $headers
     *
     * @return mix
     */
    public function respond($data, $headers = []){
        
        return Response::json($data, $this->getStatusCode(), $headers);
    }


    /**
     * respond with pagincation.
     *
     * @param Paginator $items
     * @param array     $data
     *
     * @return mix
     */
    public function respondWithPagination($items, $data){
        
        $data = array_merge($data, [
            'paginator' => [
                'total_count'  => $items->total(),
                'total_pages'  => ceil($items->total() / $items->perPage()),
                'current_page' => $items->currentPage(),
                'limit'        => $items->perPage(),
             ],
        ]);

        return $this->respond($data);
        
    }


    /**
     * respond with error.
     *
     * @param $message
     *
     * @return mix
     */
    public function respondWithError($message){
        
        return $this->respond([
                'error' => [
                    'message'     => $message,
                    'status_code' => $this->getStatusCode(),
                ],
            ]);
            
    }


    /**
     * Respond Created.
     *
     * @param string $message
     *
     * @return mix
     */
    public function respondCreated($message){

        return $this->setStatusCode(201)->respond([
            'message' => $message,
        ]);
        
    }


    /* succesfull deletion */
    public function respondDeleted($message){
        
        $message = $message ?: "Item deleted successfully";

        return $this->setStatusCode(202)->respond(['message'=>$message]);
    }


    /**
     * Throw Validation.
     *
     * @param string $message
     *
     * @return mix
     */
    public function throwValidation($message){
        
        return $this->setStatusCode(422)
                    ->respondWithError($messaege);
                    
    }
    

}
