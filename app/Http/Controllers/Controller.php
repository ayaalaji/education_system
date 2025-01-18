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
    protected function success($data = null, $message = 'Operation Successfull', $status = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $status);
    }
    /**
     * @param string $message
     * @param int $status
     * @param mixed $data
     * @return /Illuminate\Http\JsonResponse
     */
    protected function error($message = 'Operation Failed', $status = 400, $data =  null)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data
        ], $status);
    }

     /**
     * Return a paginated JSON response.
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $paginator The paginator instance.
     * @param string $message The success message.
     * @param int $status The HTTP status code.
     * @return \Illuminate\Http\JsonResponse The JSON response.
     */

     public static function paginated( $paginator, $message = 'Operation successful', $status = 200)
     {
         return response()->json([
             'status' => 'success',
             'message' => trans($message),
             'data' => $paginator->items(),
             'pagination' => [
                 'total' => $paginator->total(),
                 'count' => $paginator->count(),
                 'per_page' => $paginator->perPage(),
                 'current_page' => $paginator->currentPage(),
                 'total_pages' => $paginator->lastPage(),
             ],
         ], $status);
     }
}
