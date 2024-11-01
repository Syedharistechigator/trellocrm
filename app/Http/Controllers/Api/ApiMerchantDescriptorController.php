<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodExpigate;
use Illuminate\Http\Request;
use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class ApiMerchantDescriptorController extends Controller
{
    public function merchant_descriptor($merchant=null, $id=0)
    {
        try {
            if (!$merchant || !($merchant === "authorize" || $merchant === "expigate")) {
                return response()->json(['errors' => 'Oops! Payment merchant not found.',], 404);
            }
            if (!$id || $id < 1) {
                return response()->json(['errors' => 'Merchant id is required.',], 404);
            }
            $model = 'PaymentMethod';
            if ($merchant === "expigate") {
                return response()->json(['errors' => 'Temporary Unavailable.',], 404);
//                $model = 'PaymentMethodExpigate';
            }
            $payment_method = resolve("App\\Models\\$model")::where('id', $id)->first();
            if (!$payment_method) {
                return response()->json(['errors' => 'Oops! Payment merchant not found.',], 404);
            }

//            if ($payment_method->mode == 0) {
            $loginId = $payment_method->live_login_id;
            $transcation_key = $payment_method->live_transaction_key;
//            } else {
//                $loginId = $payment_method->test_login_id;
//                $transcation_key = $payment_method->test_transaction_key;
//            }
            if (!$loginId || !$transcation_key) {
                return response()->json(['errors' => 'Oops! Payment merchant credentials not found.',], 404);
            }
            $merchant_authentication = new AnetAPI\MerchantAuthenticationType();
            $merchant_authentication->setName($loginId);
            $merchant_authentication->setTransactionKey($transcation_key);

            $merchant_details_request = new AnetAPI\GetMerchantDetailsRequest();
            $merchant_details_request->setMerchantAuthentication($merchant_authentication);
            $get_merchant_details_controller = new AnetController\GetMerchantDetailsController($merchant_details_request);

//            if ($payment_method->mode == 0) {
            $api_response = $get_merchant_details_controller->executeWithApiResponse(ANetEnvironment::PRODUCTION);
//            } else {
//                $api_response = $get_merchant_details_controller->executeWithApiResponse(ANetEnvironment::SANDBOX);
//            }
            if ($api_response != null) {
                if ($api_response != null && $api_response->getMessages()->getResultCode() == "Ok") {
                    return response()->json(['success' => true, 'merchant_name' => $api_response->getMerchantName(), 'id' => $id]);
                }

                $errorMessages = $api_response->getMessages()->getMessage();
                $errorCode = $errorMessages[0]->getCode();
                $errorMessage = $errorMessages[0]->getText();

                return response()->json(['success' => false, 'error_code' => $errorCode, 'error_message' => $errorMessage]);
            }

            return response()->json(['error' => 'Failed to retrieve merchant details. API response is null.', 'id' => $payment_method->id, 'status' => 0, 'api_response' => $api_response]);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage(),
            ], 422);
        }
    }
}
