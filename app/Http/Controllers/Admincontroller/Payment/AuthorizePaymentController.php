<?php

namespace App\Http\Controllers\Admincontroller\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class AuthorizePaymentController extends Controller
{
    public function check_payment_status(Request $request, $id)
    {
        /** Defining rules to validate */
        $rules = [
        ];
        /** Defining rules message to show validation messages */
        $messages = [
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),], 422);
        }
        try {

            $payment = Payment::where('id', $id)->where('payment_gateway', 'authorize')->first();
            if (!$payment) {
                return response()->json(['errors' => 'Oops! Payment not found.',], 404);
            }
            if (!$payment->authorizenet_transaction_id) {
                return response()->json(['errors' => 'Oops! Payment transaction id not found.',], 404);
            }
            if (!$payment->merchant_id) {
                return response()->json(['errors' => 'Oops! Payment merchant id not found.',], 404);
            }
            $payment_method = PaymentMethod::where('id', $payment->merchant_id)->where('status', 1)->first();
            if (!$payment_method) {
                return response()->json(['errors' => 'Oops! Payment merchant not found.',], 404);
            }

            if ($payment_method->mode == 0) {
                $loginId = $payment_method->live_login_id;
                $transcation_key = $payment_method->live_transaction_key;
            } else {
                $loginId = $payment_method->test_login_id;
                $transcation_key = $payment_method->test_transaction_key;
            }

            $merchant_authentication = new AnetAPI\MerchantAuthenticationType();
            $merchant_authentication->setName($loginId);
            $merchant_authentication->setTransactionKey($transcation_key);

            $get_transaction_details = new AnetAPI\GetTransactionDetailsRequest();
            $get_transaction_details->setMerchantAuthentication($merchant_authentication);
            $get_transaction_details->setTransId($payment->authorizenet_transaction_id);
            $paymentResponse = json_decode($payment->payment_response, true);
            $refId = isset($paymentResponse['refId']) ? $paymentResponse['refId'] : null;

            $get_transaction_details_controller = new AnetController\GetTransactionDetailsController($get_transaction_details);

            if ($payment_method->mode == 0) {
                $api_response = $get_transaction_details_controller->executeWithApiResponse(ANetEnvironment::PRODUCTION);
            } else {
                $api_response = $get_transaction_details_controller->executeWithApiResponse(ANetEnvironment::SANDBOX);
            }
            if ($api_response != null) {
                $api_response_messages = $api_response->getMessages();
                $api_response_res_code = $api_response_messages ? $api_response_messages->getResultCode() : null;
                $transaction = $api_response->getTransaction();
                $trans_status = $transaction ? $transaction->getTransactionStatus() : null;
                $trans_res_code = $transaction ? $transaction->getResponseCode() : null;

                $data = [
                    'api_response' => $api_response,
                    'api_response_res_code' => $api_response_res_code,
                    'api_response_messages' => $api_response_messages,
                    'transaction' => $transaction,
                    'trans_status' => $trans_status,
                    'trans_res_code' => $trans_res_code,
                ];
                $payment->settlement_response = json_encode($data);

                if ($api_response_res_code === "Ok") {

                    $eventTypeFormatted = strtolower(Str::headline($trans_status));
                    if ($trans_status === 'FDSPendingReview') {
                        $eventTypeFormatted = 'fds pending review';
                    } elseif ($trans_status === 'FDSAuthorizedPendingReview') {
                        $eventTypeFormatted = 'fds authorized pending review';
                    }
                    $payment->settlement = $eventTypeFormatted;
                    $payment->settlement_process_time = Carbon::now();
                    $payment->save();
                    if ($trans_status === 'settledSuccessfully') {
                        return response()->json([
                            'success' => 'Payment settled successfully.',
                            'id' => $payment->id,
                            'api_response' => $api_response,
                            'api_response_res_code' => $api_response_res_code,
                            'api_response_messages' => $api_response_messages,
                            'transaction' => $transaction,
                            'trans_status' => $trans_status,
                            'trans_res_code' => $trans_res_code,
                            'event' => $eventTypeFormatted,
                            'status' => 1
                        ]);
                    }

//                    if ($trans_status === 'capturedPendingSettlement') {
//                        $payment->settlement = 'unsettled';
//                        $payment->save();
//                        return response()->json([
//                            'success' => 'Payment is still in unsettle state.',
//                            'id' => $payment->id,
//                            'api_response' => $api_response,
//                            'api_response_res_code' => $api_response_res_code,
//                            'api_response_messages' => $api_response_messages,
//                            'transaction' => $transaction,
//                            'trans_status' => $trans_status,
//                            'trans_res_code' => $trans_res_code,
//                            'status' => 0
//                        ]);
//
//                    }

                    return response()->json([
                        'success' => "Payment is in {$eventTypeFormatted} state.",
                        'id' => $payment->id,
                        'api_response' => $api_response,
                        'api_response_res_code' => $api_response_res_code,
                        'api_response_messages' => $api_response_messages,
                        'transaction' => $transaction,
                        'trans_status' => $trans_status,
                        'trans_res_code' => $trans_res_code,
                        'event' => $eventTypeFormatted,
                        'status' => 0
                    ]);
                }
                return response()->json([
                    'error' => 'Transaction details retrieval failed.',
                    'id' => $payment->id,
                    'api_response_messages' => $api_response_messages,
                    'api_response_res_code' => $api_response_res_code,
                    'transaction' => $transaction,
                    'trans_res_code' => $trans_res_code,
                    'status' => 0
                ], 422);
            }
            return response()->json(['error' => 'Failed to retrieve transaction details. API response is null.', 'id' => $payment->id, 'status' => 0]);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage(),
            ], 422);
        }
    }

}
