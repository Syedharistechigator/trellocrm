<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Admincontroller\Payment\AuthorizePaymentController;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class ApiAuthorizeUnsettledPaymentController extends Controller
{
    public function check_payment_status(Request $request)
    {
        $unsettle_payment = Payment::where('payment_gateway', 'authorize')
            ->where('merchant_id', '!=', 0)
            ->whereNotIn('settlement', ['settled successfully'])
            ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)
            ->inRandomOrder()->first();

        if (!$unsettle_payment) {
            return response()->json(['success' => 'No unsettled payment remained.']);
        }

        try {
            $controller_request = new Request();
            $response = app()->make(AuthorizePaymentController::class)->check_payment_status($controller_request, $unsettle_payment->id);
            $response_data = $response->getData();
            if ($response->status() === 200 && isset($response_data->success)) {
                return response()->json(['id' => $unsettle_payment->id, 'response_message' => $response_data->success]);
            }
            $errorMessage = $response_data->error ?? ($response_data->errors ?? 'Failed to check payment status or no message found.');
            return response()->json(['error' => $errorMessage], 422);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

}
