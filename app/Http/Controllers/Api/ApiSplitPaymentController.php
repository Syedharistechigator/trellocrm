<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SplitPayment;
use Illuminate\Http\Request;

class ApiSplitPaymentController extends Controller
{
    public function pay_now_split_payments($id = null)
    {
        if ($id == null || $id < 1) {
            $splitPayment = SplitPayment::whereHas('getInvoice', function ($query) {
                $query->where('status', 'paid');
            })->where('status', 0)->inRandomOrder()->first();
            if (!$splitPayment) {
                return response()->json(['success' => 'Success! All split payments already paid.']);
            }
            $id = $splitPayment->id;
        }
        return $this->pay_now_split_payments_global($id);
    }
}
