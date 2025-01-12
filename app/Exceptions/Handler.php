<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;


//........................................
/*
   Define Custom Exceptions ( courses model index function ) , when cant find
   teacher name or category name that user send in request
   the exception throw in courses service class , listCourse function .
*/
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class CategoryIdsNotFoundException extends Exception
{
    //
}

class TeacherIdsNotFoundException extends Exception
{
    //
}
//.....................................


class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return response()->json(['message' => 'Validation Error', 'errors' => $exception->errors()], 422);
        }

        if ($exception instanceof QueryException) {
            return response()->json(['message' => 'Database Error'], 500);
        }

         if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'status' => false,
                'message' => 'Resource not found',
            ], 404);
        }


        if ($exception instanceof AuthorizationException) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized action',
            ], 403);
        }


        if ($exception instanceof ValidationException) {
            return response()->json([
                'status' => false,
                'errors' => $exception->errors(),
                'message' => 'Validation failed',
            ], 422);
        }



        //........................................................
        return response()->json(['message' => 'something went wronge : '.$exception->getMessage()], 404);
    }
}
