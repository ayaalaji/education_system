<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    /**
     * Return a success JSON Response
     * @param mixed $data
     * @param string $message
     * @param int $status
     * @return /Illuminate\Http\JsonResponse
     */
    protected function success($data = null,$message = 'Operation Successfull' , $satus = 200)
    {
        return response()->json([
            'status' =>'success',
            'message' => $message,
            'data' =>$data
        ] , $satus);
    }
    /**
     * @param string $message
     * @param int $status
     * @param mixed $data
     * @return /Illuminate\Http\JsonResponse
     */
    protected function error($message = 'Operation Failed' , $status =400 , $data =  null) 
    {
        return response()->json([
            'status' =>'error' ,
            'message' =>$message,
            'data' =>$data
        ], $status);
    }
}
