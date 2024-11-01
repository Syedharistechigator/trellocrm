<?php

namespace App\Http\Controllers\Admincontroller\Payment;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\MultiPaymentResponse;
use App\Models\Team;
use Illuminate\Http\Request;

class PaymentMultipleResponseController extends Controller
{
    public function index(Request $request)
    {
        $result = $this->getData($request, new MultiPaymentResponse(),false);
        $multi_payment_responses = $result['data'];
        $fromDate = $result['fromDate'];
        $toDate = $result['toDate'];
        return view('admin.payment.payment-multiple-response.index',compact('multi_payment_responses','fromDate','toDate'));
    }
}
