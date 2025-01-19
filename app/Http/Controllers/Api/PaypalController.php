<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Services\CourseService;
use App\Http\Controllers\Controller;
use App\Services\PaypalService;


class PayPalController extends Controller
{


    
    protected $paypalService;

    public function __construct(PaypalService $paypalService)
    {

        $this->paypalService = $paypalService;
    }

    /**
     * 1. الحصول على Access Token من PayPal
     */
    public function authenticate()
    {
       $data = $this->paypalService->authWithSandbox();
        return $this->success($data,'authenticate success');
    }

    /**
     * 2. إنشاء طلب دفع (Order)
     */
    public function createOrder(Request $request)
    {
      $this->paypalService->createOrder($request);
      return $this->success(null,'created success');
    }

    /**
     * 3. التقاط الدفع (Capture Payment)
     */
    public function capturePayment(Request $request, $orderId)
{
   $payment = $this->paypalService->Capture($request,$orderId);
    return $this->success($payment,"payment Captured success");
}


    /**
     * 4. عرض تفاصيل الطلب
     */
    public function showOrder(Request $request, $orderId)
    {
     $order =  $this->paypalService->displayOrder($request,$orderId);
      return $this->success($order,'showed success');
    }

    public function successTransaction()
{
   return $this->success(null,'Transaction completed successfully!',200);
}

public function cancelTransaction()
{
    return $this->success(null,'Transaction canceled successfully!',200);
}
}
