<?php

namespace App\Services;

use App\Models\User;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
class PaypalService


{

    private $clientId;
    private $clientSecret;
    private $baseUrl;

    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->clientId = env('PAYPAL_SANDBOX_CLIENT_ID');
        $this->clientSecret = env('PAYPAL_SANDBOX_CLIENT_SECRET');
        $this->baseUrl = 'https://api.sandbox.paypal.com';
        $this->courseService = $courseService;
    }
    public function authWithSandbox()
    {
        try{
        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
        ->asForm()
        ->post("$this->baseUrl/v1/oauth2/token", [
            'grant_type' => 'client_credentials',
        ]);
        return response()->json($response->json(), 200);
    }catch(\Throwable $e)
    {
        Log::error($e);
        return response()->json([
            'status' => 'error',
            'message' => 'Authentication failed',
            'details' => $e->getMessage(),
        ], 500);
    }


    }

    public function createOrder(Request $request)
    {
        try{
        $accessToken = $request->bearerToken();
        $response = Http::withToken($accessToken)
            ->timeout(30)
            ->post("$this->baseUrl/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => $request->amount,
                        ],
                        'description' => $request->description ?? 'Course Payment',
                    ],
                ],
                'application_context' => [
                    'return_url' => ('api/paypal/success'),
                    'cancel_url' => ('api/paypal/cancel'),
                ],
            ]);
        }catch(\Throwable $e)
        {
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'message' => 'Order creation failed',
                'details' => $e->getMessage(),
            ], 500);
        }

    }

    public function Capture(Request $request,$orderId)
    {
        try{
            $accessToken = $request->bearerToken();
            $response = Http::withToken($accessToken)
                ->post("$this->baseUrl/v2/checkout/orders/$orderId/capture");

            if ($response->successful()) {

                $courseId = $request->course_id;
                $userId = $request->user_id;
                $course = Course::find($courseId);
                 $this->courseService->addUserToCourse(['user' => $userId], $course);
        }}
        catch(\Throwable $e)
        {
             Log::error($e);
             return response()->json([
                'status' => 'error',
                'message' => 'Payment capture failed',
                'details' => $e->getMessage(),
            ], 500);
        }

        }

        public function displayOrder(Request $request, $orderId)
        {
            try{
                $accessToken = $request->bearerToken(); // يجب إرسال الـ Access Token في هيدر Authorization
                $response = Http::withToken($accessToken)
                    ->get("$this->baseUrl/v2/checkout/orders/$orderId");
            }

                catch(\Throwable $e)
                {
                    Log::error($e);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to fetch order details',
                        'details' => $e->getMessage(),
                    ], 500);

                }
     }
}
