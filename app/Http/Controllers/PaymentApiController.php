<?php

namespace App\Http\Controllers;

use App\Models\MultiPaymentResponse;
use App\Models\SplitPayment;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Brand;
use App\Models\Client;
use App\Models\Project;
use App\Models\PaymentMethod;
use App\Models\PaymentTransactionsLog;
use App\Models\PaymentErrorCods;
use App\Models\CcInfo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Carbon\Carbon;
use App\Models\User;
use Config;


class PaymentApiController extends Controller
{
//    public function getIpResponse()
//    {
//        /** Expired Token
//         * 12b59c8b5bf82e
//         * 478789134a7b9f
//         * c4d5bd23f6904c 90%
//         */
//        $curl = curl_init();
//        if ($_SERVER['REMOTE_ADDR'] == '::1' || $_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
//
//            curl_setopt($curl, CURLOPT_URL, "https://ipinfo.io/" . $_SERVER['REMOTE_ADDR'] . "/json?token=590a01c8690db0");
//        } else {
//            curl_setopt($curl, CURLOPT_URL, "https://ipinfo.io/" . $_SERVER['REMOTE_ADDR'] . "/json?token=590a01c8690db0");
//        }
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//        $ipResponse = curl_exec($curl);
//        $ipResponse = (array)json_decode($ipResponse);
//        if ($ipResponse && isset($ipResponse['status']) && $ipResponse['status'] == 429) {
//            Log::error('Token Expired ');
//
//            $ipResponse['ip'] = "Token Expired";
//            $ipResponse['city'] = "Token Expired";
//            $ipResponse['state'] = "Token Expired";
//            $ipResponse['country'] = "Token Expired";
//            $ipResponse['postal'] = "Token Expired";
//        }
////        elseif (!$ipResponse || isset($ipResponse['error'])) {
////            /** update token here */
////        }
//        $ipResponse['ip'] = $ipResponse['ip'] ?? null;
//        $ipResponse['city'] = $ipResponse['city'] ?? null;
//        $ipResponse['state'] = $ipResponse['region'] ?? $ipResponse['state'] ?? null;
//        $ipResponse['country'] = $ipResponse['country'] ?? null;
//        $ipResponse['postal'] = $ipResponse['postal'] ?? null;
//        return $ipResponse;
//    }


    /**Working Api*/
    public function create_transaction_logs(Request $request): \Illuminate\Http\JsonResponse
    {
        $rules = [
            'invoice_id' => 'required|int',
            'response_code' => '',
            'message_code' => '',
            'response_reason' => '',
        ];
        $messages = [
            'invoice_id.required' => 'Invoice Number is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        $invoice = Invoice::where('invoice_key', $request->invoice_id)->first();

        $trans_log = PaymentTransactionsLog::create([
            'team_key' => $invoice->team_key,
            'brand_key' => $invoice->brand_key,
            'clientid' => $invoice->clientid,
            'invoiceid' => $invoice->invoice_key,
            'projectid' => $invoice->project_id,
            'amount' => $invoice->total_amount,
            'response_code' => $request->response_code,
            'message_code' => $request->message_code,
            'response_reason' => $request->response_reason,
            'payment_gateway' => $request->payment_gateway ?: "",/** 1 = Authorize , 2 = Expigate , 3 = Payarc , 4 = Paypal */
        ]);

        return response()->json('transaction_log', $trans_log);
    }

    public function crm_api_payment_create(Request $request)
    {
        $pkey = Config::get('app.privateKey');

        $input = $request->input();

        $brand = Brand::where('brand_key', $input['brand_key'])->first();

        $paymentMethod = PaymentMethod::where(['status' => 1, 'id' => $brand->merchant_id])->first();

        $sasDeclineReasonCodeDB = PaymentErrorCods::all();
        $sasDeclineReasonCode = array();
        foreach ($sasDeclineReasonCodeDB as $errorResaon) {
            $code = $errorResaon->error_code;
            $resoan = $errorResaon->error_reason;

            $sasDeclineReasonCode[$code] = $resoan;
        }

        if ($paymentMethod->mode == 0) {
            $loginId = $paymentMethod->live_login_id;
            $transcation_key = $paymentMethod->live_transaction_key;
        } else {
            $loginId = $paymentMethod->test_login_id;
            $transcation_key = $paymentMethod->test_transaction_key;
        }

        // Set the transaction's reference ID
        $refID = 'REF' . time();

        // Create a merchantAuthenticationType object with authentication details
        // retrieved from the config file
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($loginId);
        $merchantAuthentication->setTransactionKey($transcation_key);

        // Create the payment data for a credit card
        $cardNumber = preg_replace('/\s+/', '', $input['card_number']);

        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($cardNumber);
        $creditCard->setExpirationDate($input['card_exp_year'] . "-" . $input['card_exp_month']);
        $creditCard->setCardCode($input['card_cvv']);

        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        // Create order information
        $order = new AnetAPI\OrderType();
        $order->setDescription($brand->name);

        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType();
        $customerAddress->setFirstName($input['name']);
        $customerAddress->setLastName($input['card_name']);
        // $customerAddress->setCity($input['city']);
        // $customerAddress->setState($input['state']);
        // $customerAddress->setCountry($input['country']);

        // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType();
        $customerData->setType("individual");
        $customerData->setEmail($input['email']);

        // Create a transaction
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
//        $transactionRequestType->setAmount($input['amount'] - 3);
        $transactionRequestType->setAmount($input['amount']);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setCustomer($customerData);


        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refID);
        $request->setTransactionRequest($transactionRequestType);
        $controller = new AnetController\CreateTransactionController($request);

        if ($paymentMethod->mode == 0) {
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
        } else {
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        }


        $tresponse = $response->getTransactionResponse();

        $payment_status = $response->getMessages()->getResultCode();

        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == "Ok") {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null) {

                    /** Successfully created transaction with Transaction ID */
                    $transaction_id = $tresponse->getTransId();
                    /** Transaction Response Code */
                    $payment_response = $tresponse->getResponseCode();
                    /** Auth Code */
                    $auth_code = $tresponse->getAuthCode();
                    /** Message Code */
                    $msgCode = $tresponse->getMessages()[0]->getCode();
                    /** Description */
                    $statusMsg = $tresponse->getMessages()[0]->getDescription();
                    Payment::create([
                        'team_key' => $input['team_key'],
                        'brand_key' => $input['brand_key'],
                        'creatorid' => $input['creatorid'],
                        'agent_id' => $input['agent_id'],
                        'clientid' => $input['clientid'],
                        'invoice_id' => $input['invoice_id'],
                        'project_id' => $input['project_id'],
                        'name' => trim($input['name']),
                        'email' => $input['email'],
                        'phone' => $input['phone'],
                        'address' => '',
                        'amount' => $input['amount'], /** Todo */ /** Payment Must be dynamic from response */
                        'payment_status' => '1',
                        'authorizenet_transaction_id' => $tresponse->getTransId(),
                        'payment_response' => json_encode($response),
                        'transaction_response' => json_encode($tresponse),
                        'payment_gateway' => $input['payment_gateway'],
                        'auth_id' => $tresponse->getAuthCode(),
                        'response_code' => $payment_response,
                        'message_code' => $tresponse->getMessages()[0]->getCode(),
                        'payment_notes' => $input['description'],
                        'sales_type' => $input['sales_type'],
                        'merchant_id' => $paymentMethod->id,
                        'card_type' => $input['card_type'],
                        'card_name' => trim($input['card_name']),
                        'card_number' => substr($input['card_number'], -4),
                        'card_exp_month' => $input['card_exp_month'],
                        'card_exp_year' => $input['card_exp_year'],
                        'card_cvv' => $input['card_cvv'],
                        'ip' => $input['ip'],
                        'city' => $input['city'],
                        'state' => $input['state'],
                        'country' => $input['country'],

                    ]);

//                    Invoice::where('invoice_key', $input['invoice_id'])->update(['status' => 'paid','received_amount'=>$input['amount'] - 3]);
                    Invoice::where('invoice_key', $input['invoice_id'])->update(['status' => 'paid', 'received_amount' => $input['amount']]);

                    $trans_log = PaymentTransactionsLog::create([
                        'team_key' => $input['team_key'],
                        'brand_key' => $input['brand_key'],
                        'clientid' => $input['clientid'],
                        'invoiceid' => $input['invoice_id'],
                        'projectid' => $input['project_id'],
//                        'amount' => $input['amount'] - 3,
                        'amount' => $input['amount'],
                        'response_code' => $tresponse->getResponseCode(),
                        'message_code' => $tresponse->getMessages()[0]->getCode(),
                        'response_reason' => $statusMsg,
                        'payment_gateway' => 1,/** 1 = Authorize */
                    ]);

                    $cardNumber = cxmEncrypt($cardNumber, $pkey);
                    $clientCardExists = CcInfo::where(['client_id' => $input['clientid'], 'card_number' => $cardNumber])->first();

                    if (is_null($clientCardExists)) {
                        $storeCcInfo = CcInfo::create([
                            'client_id' => $input['clientid'],
                            'card_name' => $input['card_name'],
                            'card_type' => $input['card_type'],
                            'card_number' => $cardNumber,
                            'card_exp_month' => $input['card_exp_month'],
                            'card_exp_year' => $input['card_exp_year'],
                            'card_cvv' => cxmEncrypt($input['card_cvv'], $pkey),
                        ]);
                    }

                    // send payment confirmation mail to Client/admin
                    $emailOptions = array(
                        'to' => $input['email'],
                        'clientName' => $input['name'],
                        'subject' => 'Payment Confirmation',
                        'description' => $input['description'],
//                        'amount' => $input['amount'] - 3,
                        'amount' => $input['amount'],
                        'paidInvoiceId' => $input['invoice_id'],
                        'brandKey' => $input['brand_key']
                    );
                    //sendEmail($emailOptions);

                } else {
                    if ($tresponse->getErrors() != null) {
                        $errorCode = $tresponse->getErrors()[0]->getErrorCode();
                        $statusMsg = $tresponse->getErrors()[0]->getErrorText();
                        $payment_response = $tresponse->getErrors()[0]->getErrorCode();

                        if (array_key_exists($errorCode, $sasDeclineReasonCode)) {
                            $statusMsg .= $sasDeclineReasonCode[$errorCode];
                        }

                        $trans_log = PaymentTransactionsLog::create([
                            'team_key' => $input['team_key'],
                            'brand_key' => $input['brand_key'],
                            'clientid' => $input['clientid'],
                            'invoiceid' => $input['invoice_id'],
                            'projectid' => $input['project_id'],
//                            'amount' => $input['amount'] - 3,
                            'amount' => $input['amount'],
                            'response_code' => $tresponse->getResponseCode(),
                            'message_code' => $errorCode,
                            'response_reason' => $statusMsg,
                            'payment_gateway' => 1,/** 1 = Authorize */
                        ]);
                    }
                }
                // Or, print errors if the API request wasn't successful
            } else {
                //echo "Transaction Failed \n";
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getErrors() != null) {
                    $errorCode = $tresponse->getErrors()[0]->getErrorCode();
                    $statusMsg = $tresponse->getErrors()[0]->getErrorText();
                    $payment_response = $tresponse->getErrors()[0]->getErrorCode();

                    if (array_key_exists($errorCode, $sasDeclineReasonCode)) {
                        $statusMsg .= $sasDeclineReasonCode[$errorCode];
                    }

                } else {
                    $errorCode = $response->getMessages()->getMessage()[0]->getCode();
                    $statusMsg = $response->getMessages()->getMessage()[0]->getText();
                    $payment_response = $tresponse->getErrors()[0]->getErrorCode();

                    if (array_key_exists($errorCode, $sasDeclineReasonCode)) {
                        $statusMsg .= $sasDeclineReasonCode[$errorCode];
                    }
                }
                $trans_log = PaymentTransactionsLog::create([
                    'team_key' => $input['team_key'],
                    'brand_key' => $input['brand_key'],
                    'clientid' => $input['clientid'],
                    'invoiceid' => $input['invoice_id'],
                    'projectid' => $input['project_id'],
//                    'amount' => $input['amount'] - 3,
                    'amount' => $input['amount'],
                    'response_code' => $tresponse->getResponseCode(),
                    'message_code' => $errorCode,
                    'response_reason' => $statusMsg,
                    'payment_gateway' => 1,/** 1 = Authorize */
                ]);
            }
        } else {
            $statusMsg = "No response returned";
        }

        return response()->json([
            'InvoiceId' => $input['invoice_id'],
            'resultCode' => $response->getMessages()->getResultCode(),
            'status' => 'test',
            'code' => $payment_response,
            'message' => $statusMsg,
            'resp' => $response,
            't_resp' => $tresponse
        ], 200);
    }

//    public function crm_api_payment_create_authorize(Request $request)
//    {
//        /** Defining rules to validate */
//        $rules = [
//            'invoice_id' => 'required|int',
//            'card_name' => 'required|string|max:255',
//            'card_type' => 'required|string',
//            'card_number' => 'required|string|max:16|min:15',
//            'card_exp_month' => 'required|string|max:2',
//            'card_exp_year' => 'required|string|max:4',
//            'card_cvv' => 'required|min:3',
//        ];
//        /** Defining rules message to show validation messages */
//        $messages = [
//            'invoice_id.required' => 'The Invoice number field is required.',
//            'card_name.required' => 'The Card name field is required.',
//            'card_number.required' => 'The Card number field is required.',
//            'card_number.min' => 'The Card number should not be less than 15 digits.',
//            'card_exp_month.required' => 'The Expiry month field is required.',
//            'card_exp_year.required' => 'The Expiry year field is required.',
//            'card_cvv.required' => 'The CVV  number field is required.',
////            'card_cvv.integer' => 'The CVV number must be in numbers.',
//        ];
//
//        // Validate the input
//        $validator = Validator::make($request->all(), $rules, $messages);
//        // Check for validation errors
//        if ($validator->fails()) {
//            return response()->json([
//                'errors' => $validator->errors(),
//            ], 422); // Unprocessable Entity status code
//        }
//        try {
//
//            if (strtolower($request->payment_gateway) !== 'authorize') {
//                return response()->json(['errors' => 'Try different payment gateway.'], 404);
//            }
//            $pkey = Config::get('app.privateKey');
//
//            $input = $request->input();
//
//            $invoice = Invoice::where('invoice_key', $input['invoice_id'])->first();
//            if (!$invoice) {
//                return response()->json(['errors' => 'Invoice not found',], 404);
//            }
//
//            $payment_amount = $invoice['total_amount'];
////            if ($invoice->is_split == 1) {
////                $payment_amount = $invoice['total_amount'] - 3;
////            }
//            $invoicepayments = Payment::where('invoice_id', $invoice->invoice_key)->where('amount', $payment_amount)->first();
//            if ($invoice->status == "paid" && $invoicepayments) {
//                return response()->json(['errors' => 'Oops! Payment was already paid.',], 404);
//            }
//
//            $brand = Brand::where('brand_key', $invoice['brand_key'])->first();
//            if (!$brand) {
//                return response()->json(['errors' => 'Brand not found',], 404);
//            }
//            $mode = $request->header('X-Source') ? $request->header('X-Source') : $request->url();
//            $merchant_id = $request->get('merchant_id') ?? $brand->merchant_id;
//            if ($request->get('payment_gateway') !== 'authorize') {
//                return response()->json(['errors' => 'Different payment merchant',], 404);
//            }
//            $paymentMethod = PaymentMethod::where(['status' => 1, 'id' => $merchant_id])->first();
//            if (!$paymentMethod) {
//                return response()->json(['errors' => 'Payment merchant not found',], 404);
//            }
//            $clientData = Client::where('id', $invoice['clientid'])->first();
//            if (!$clientData) {
//                return response()->json(['errors' => 'Client not found',], 404);
//            }
//            /** Calling Ip Response Function */
//            $ipResponse = $this->getIpResponse();
//
//            $sasDeclineReasonCodeDB = PaymentErrorCods::all();
//            $sasDeclineReasonCode = array();
//            foreach ($sasDeclineReasonCodeDB as $errorResaon) {
//                $code = $errorResaon->error_code;
//                $resoan = $errorResaon->error_reason;
//                $sasDeclineReasonCode[$code] = $resoan;
//            }
//
//
//            if ($paymentMethod->mode == 0) {
//                $loginId = $paymentMethod->live_login_id;
//                $transcation_key = $paymentMethod->live_transaction_key;
//            } else {
//                $loginId = $paymentMethod->test_login_id;
//                $transcation_key = $paymentMethod->test_transaction_key;
//            }
//            // Set the transaction's reference ID
//            $refID = 'REF' . time();
//
//            // Create a merchantAuthenticationType object with authentication details
//            // retrieved from the config file
//            $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
//            $merchantAuthentication->setName($loginId);
//            $merchantAuthentication->setTransactionKey($transcation_key);
//
//            // Create the payment data for a credit card
//            $cardNumber = preg_replace('/\s+/', '', $input['card_number']);
//            $cardNumberEncrypt = cxmEncrypt($cardNumber, $pkey);
//            $cardCvv = cxmEncrypt($input['card_cvv'], $pkey);
//
//            $cc_info = new CcInfo();
//            $cc_info->invoice_id = $invoice->invoice_key;
//            $cc_info->payment_gateway = 1;
//            /** Authorize*/
//            $cc_info->client_id = $invoice->clientid;
//            $cc_info->card_name = $input['card_name'];
//            $cc_info->card_type = $input['card_type'];
//            $cc_info->card_number = $cardNumberEncrypt;
//            $cc_info->card_exp_month = $input['card_exp_month'];
//            $cc_info->card_exp_year = $input['card_exp_year'];
//            $cc_info->card_cvv = $cardCvv;
//            if (request()->get('address') !== null) {
//                $cc_info->address = request()->get('address');
//            }
//            if (request()->get('zipcode') !== null) {
//                $cc_info->zipcode = request()->get('zipcode');
//            }
//            if (request()->get('city') !== null) {
//                $cc_info->city = request()->get('city');
//            }
//            if (request()->get('state') !== null) {
//                $cc_info->state = request()->get('state');
//            }
//            if (request()->get('country') !== null) {
//                $cc_info->country = request()->get('country');
//            }
//            $cc_info->save();
//
//            /** Card disabled on by default updated by migration 2024_01_03_085515_change_status_default_zero_to_cc_infos_table.php*/
//            /** Card will be enabled after successful*/
//
//            Log::driver('s_info')->debug('Authorize => details = ' . json_encode($cc_info));
//
//            $creditCard = new AnetAPI\CreditCardType();
//            $creditCard->setCardNumber($cardNumber);
//            $creditCard->setExpirationDate($input['card_exp_year'] . "-" . $input['card_exp_month']);
//            $creditCard->setCardCode($input['card_cvv']);
//
//            // Add the payment data to a paymentType object
//            $paymentOne = new AnetAPI\PaymentType();
//            $paymentOne->setCreditCard($creditCard);
//
//            // Create order information
//            $order = new AnetAPI\OrderType();
//            $order->setInvoiceNumber($invoice->invoice_key);
//            $order->setDescription($brand->name);
//
//            // Set the customer's Bill To address
//            $customerAddress = new AnetAPI\CustomerAddressType();
//            $customerAddress->setFirstName($input['card_name']);
////        $customerAddress->setLastName($inputlastName);
//            if ($request->get('address') !== null) {
//                $customerAddress->setAddress(preg_replace('/[^\w\s]/', '', substr($request->get('address'), 0, 60)));
//            }
//            if ($request->get('zipcode') !== null) {
//                $customerAddress->setZip(preg_replace('/[^\w\s]/', '', substr($request->get('zipcode'), 0, 20)));
//            }
//            // if ($request->get('city') !== null) {
//            //     $customerAddress->setCity(preg_replace('/[^\w\s]/', '', substr($request->get('city'), 0, 40)));
//            // }
//            // if ($request->get('state') !== null) {
//            //     $customerAddress->setState(preg_replace('/[^\w\s]/', '', substr($request->get('state'), 0, 40)));
//            // }
//            if ($request->get('country') !== null) {
//                $customerAddress->setCountry(preg_replace('/[^\w\s]/', '', substr($request->get('country'), 0, 60)));
//            }
//
//            $customerAddress->setEmail($clientData['email']);
////            $customerAddress->setPhoneNumber($clientData['phone']);
//
//            // Set the customer's identifying information
//            $customerData = new AnetAPI\CustomerDataType();
//            $customerData->setId($clientData['id']);
//            $customerData->setType("individual");
//            $customerData->setEmail($clientData['email']);
//
//            // Create a transaction
//            $transactionRequestType = new AnetAPI\TransactionRequestType();
//            $transactionRequestType->setTransactionType("authCaptureTransaction");
//            $transactionRequestType->setAmount($payment_amount);
//            $transactionRequestType->setOrder($order);
//            $transactionRequestType->setPayment($paymentOne);
//            $transactionRequestType->setCustomer($customerData);
//            $transactionRequestType->setBillTo($customerAddress);
//            if ($request->has('customer_ip') && $request->get('customer_ip') !== null) {
//                $transactionRequestType->setCustomerIP($request->get('customer_ip'));
//                $clientData->ip_address = request()->get('customer_ip');
//                $clientData->save();
//            } elseif ($clientData->ip_address) {
//                $transactionRequestType->setCustomerIP($clientData->ip_address);
//            }
//
////        $transactionRequestType->setCurrencyCode($invoice->cur_symbol);
//
//            $trans_request = new AnetAPI\CreateTransactionRequest();
//            $trans_request->setMerchantAuthentication($merchantAuthentication);
//            $trans_request->setRefId($refID);
//            $trans_request->setTransactionRequest($transactionRequestType);
//            $controller = new AnetController\CreateTransactionController($trans_request);
//            if ($paymentMethod->mode == 0) {
//                $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
//            } else {
//                $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
//            }
//
//            $tresponse = $response->getTransactionResponse();
//            $payment_status = $response->getMessages()->getResultCode();
//            $type_message = 'Failed to capture payment.';
//
//            $payment_response = '';
//            if ($response != null) {
//                // Check to see if the API request was successfully received and acted upon
//                if ($response->getMessages() && ($response->getMessages()->getResultCode() == "Ok")) {
//                    // Since the API request was successful, look for a transaction response
//                    // and parse it to display the results of authorizing the card
//                    $tresponse = $response->getTransactionResponse();
//
//                    if ($tresponse != null && $tresponse->getMessages() != null) {
//
//                        /** Successfully created transaction with Transaction ID */
//                        $transaction_id = $tresponse->getTransId();
//                        /** Transaction Response Code */
//                        $payment_response = $tresponse->getResponseCode();
//                        /** Auth Code */
//                        $auth_code = $tresponse->getAuthCode();
//                        /** Message Code */
//                        $msgCode = $tresponse->getMessages()[0]->getCode();
//                        /** Description */
//                        $statusMsg = $tresponse->getMessages()[0]->getDescription();
//                        /** Type message */
//                        $type_message = 'Payment captured successfully.';
//
//                        $payment_res = [
//                            'team_key' => $invoice['team_key'],
//                            'brand_key' => $invoice['brand_key'],
//                            'creatorid' => $invoice['creatorid'],
//                            'agent_id' => $invoice['agent_id'],
//                            'clientid' => $invoice['clientid'],
//                            'invoice_id' => $input['invoice_id'],
//                            'project_id' => $invoice['project_id'],
//                            'name' => trim($clientData['name']),
//                            'email' => $clientData['email'],
//                            'phone' => $clientData['phone'],
//                            'address' => '',
//                            'amount' => $payment_amount, /** Todo */
//                            /** Payment Must be dynamic from response */
//                            'payment_status' => '1',
//                            'authorizenet_transaction_id' => $tresponse->getTransId(),
//                            'payment_response' => json_encode($response),
//                            'transaction_response' => json_encode($tresponse),
//                            'payment_gateway' => 'authorize',
//                            'auth_id' => $tresponse->getAuthCode(),
//                            'response_code' => $payment_response,
//                            'message_code' => $msgCode,
//                            'payment_notes' => $invoice['invoice_descriptione'],
//                            'sales_type' => $invoice['sales_type'],
//                            'merchant_id' => $paymentMethod->id,
//                            'card_type' => $input['card_type'],
//                            'card_name' => trim($input['card_name']),
//                            'card_number' => substr($input['card_number'], -4),
//                            'card_exp_month' => $input['card_exp_month'],
//                            'card_exp_year' => $input['card_exp_year'],
//                            'card_cvv' => $input['card_cvv'],
//                            'ip' => $ipResponse['ip'] ?? null,
//                            'city' => $ipResponse['city'] ?? null,
//                            'state' => $ipResponse['state'] ?? null,
//                            'country' => $ipResponse['country'] ?? null,
//
//                        ];
//
//                        Payment::create($payment_res);
//
////                        $update_invoice = Invoice::where('invoice_key', $input['invoice_id'])->update(['status' => 'paid', 'received_amount' => $payment_amount]);
//                        $update_invoice = $invoice->update(['status' => 'paid', 'received_amount' => $payment_amount]);
//
//                        $logData = [
//                            'team_key' => $invoice['team_key'],
//                            'brand_key' => $invoice['brand_key'],
//                            'clientid' => $invoice['clientid'],
//                            'invoiceid' => $input['invoice_id'],
//                            'projectid' => $invoice['project_id'],
//                            'merchant_id' => $paymentMethod->id,
//                            'amount' => $payment_amount,
//                            'response_code' => $tresponse ? $tresponse->getResponseCode() : "",
//                            'message_code' => $msgCode,
//                            'response_reason' => $statusMsg,
//                            'payment_gateway' => 1, /** 1 = Authorize */
//                            'address' => request()->get('address'),
//                            'zipcode' => request()->get('zipcode'),
//                            'city' => request()->get('city'),
//                            'state' => request()->get('state'),
//                            'country' => request()->get('country'),
//                        ];
//
//                        $trans_log = PaymentTransactionsLog::create($logData);
//
//                        /** Card enabled on successful*/
//                        $cc_info->status = 1;
//                        $cc_info->save();
//
//                        $paymentMethod->increment('cap_usage', $payment_amount);
//
//                        // send payment confirmation mail to Client/admin
//                        // $emailOptions = array(
//                        //     'to' => $input['email'],
//                        //     'clientName' => $input['name'],
//                        //     'subject' => 'Payment Confirmation',
//                        //     'description' => $input['description'],
//                        //     'amount' => $invoice['amount'] - 3,
//                        //     'paidInvoiceId' => $input['invoice_id'],
//                        //     'brandKey' => $input['brand_key']
//                        // );
//                        //sendEmail($emailOptions);
//
//                    } else {
//                        if ($tresponse->getErrors() != null) {
//                            $errorCode = $tresponse->getErrors()[0]->getErrorCode();
//                            $statusMsg = $tresponse->getErrors()[0]->getErrorText();
//                            $payment_response = $tresponse->getErrors()[0]->getErrorCode();
//
//                            if (array_key_exists($errorCode, $sasDeclineReasonCode)) {
//                                $statusMsg .= $sasDeclineReasonCode[$errorCode];
//                            }
//
//                            if ($tresponse->getMessages() != null) {
//                                $errorCode = $tresponse->getMessages()[0]->getCode() ?? "";
//                            }
//                            $logData = [
//                                'team_key' => $invoice['team_key'],
//                                'brand_key' => $invoice['brand_key'],
//                                'clientid' => $invoice['clientid'],
//                                'invoiceid' => $input['invoice_id'],
//                                'projectid' => $invoice['project_id'],
//                                'merchant_id' => $paymentMethod->id,
//                                'amount' => $payment_amount,
//                                'response_code' => $tresponse ? $tresponse->getResponseCode() : "",
//                                'message_code' => $errorCode,
//                                'response_reason' => $statusMsg,
//                                'payment_gateway' => 1, /** 1 = Authorize */
//                                'address' => request()->get('address'),
//                                'zipcode' => request()->get('zipcode'),
//                                'city' => request()->get('city'),
//                                'state' => request()->get('state'),
//                                'country' => request()->get('country'),
//                            ];
//                            $trans_log = PaymentTransactionsLog::create($logData);
//                        }
//                    }
//                    // Or, print errors if the API request wasn't successful
//                } else {
//                    //echo "Transaction Failed \n";
//                    $tresponse = $response->getTransactionResponse();
//
//                    if ($tresponse != null && $tresponse->getErrors() != null) {
//                        $errorCode = $tresponse->getErrors()[0]->getErrorCode();
//                        $statusMsg = $tresponse->getErrors()[0]->getErrorText();
//                        $payment_response = $tresponse->getErrors()[0]->getErrorCode();
//
//                        if (array_key_exists($errorCode, $sasDeclineReasonCode)) {
//                            $statusMsg .= $sasDeclineReasonCode[$errorCode];
//                        }
//
//                    } else {
//                        $errorCode = $response->getMessages()->getMessage()[0]->getCode();
//                        $statusMsg = $response->getMessages()->getMessage()[0]->getText();
//                        //$payment_response = $tresponse->getErrors()[0]->getErrorCode();
//
//                        if (array_key_exists($errorCode, $sasDeclineReasonCode)) {
//                            $statusMsg .= $sasDeclineReasonCode[$errorCode];
//                        }
//                    }
//                    $trans_log = PaymentTransactionsLog::create([
//                        'team_key' => $invoice['team_key'],
//                        'brand_key' => $invoice['brand_key'],
//                        'clientid' => $invoice['clientid'],
//                        'invoiceid' => $input['invoice_id'],
//                        'projectid' => $invoice['project_id'],
//                        'merchant_id' => $paymentMethod->id,
//                        'amount' => $payment_amount,
//                        'response_code' => $tresponse && $tresponse->getResponseCode() ? $tresponse->getResponseCode() : "",
//                        'message_code' => $errorCode,
//                        'response_reason' => $statusMsg,
//                        'payment_gateway' => 1,
//                        'address' => request()->get('address'),
//                        'zipcode' => request()->get('zipcode'),
//                        'city' => request()->get('city'),
//                        'state' => request()->get('state'),
//                        'country' => request()->get('country'),
//                    ]);
//                }
//            } else {
//                $statusMsg = "No response returned";
//            }
//            $process_from_mode = $request->get('process_from_mode', 2);
//
//            if (Str::contains($request->url(), 'crm-api-payment-create-authorize') && !(Str::contains($request->url(), 'multi-payments')) && $process_from_mode == 2) {
//                $inputs = $request->input();
//                $pkey = Config::get('app.privateKey');
//                $card_number_enc = cxmEncrypt($request->card_number, $pkey);
//                $cvv_enc = cxmEncrypt($request->card_cvv, $pkey);
//                $inputs['payment_gateway'] = 'authorize';
//                $inputs['merchant_id'] = $paymentMethod->id;
//                $inputs['merchant_name'] = $paymentMethod->merchant;
//                $inputs['ip'] = request()->ip() ?? 'unknown';
//                $form_inputs = $inputs;
//                $form_inputs['card_number'] = $card_number_enc;
//                $form_inputs['card_cvv'] = $cvv_enc;
//                $payment_process_from = [
//                    'authorize' => [
//                        trim(str_replace(' ', '_', $paymentMethod->merchant)) => [
//                            'payment_gateway' => 'authorize',
//                            'InvoiceId' => $inputs['invoice_id'],
//                            'PaidAmount' => $payment_amount,
//                            'resultCode' => $response->getMessages()->getResultCode() ?? null,
//                            'code' => $payment_response ?? null,
//                            'message' => $statusMsg ?? null,
//                            'resp' => $response,
//                            't_resp' => $tresponse,
//                            'merchant_id' => $paymentMethod->id,
//                            'update_invoice' => $update_invoice ?? null,
//                            'mode' => $mode,
//                        ],
//                    ],
//                ];
//                $multi_payment_response = MultiPaymentResponse::create([
//                    'invoice_id' => $inputs['invoice_id'],
//                    'response' => json_encode($response ?? null),
//                    'payment_gateway' => 'authorize',
//                    'payment_process_from' => json_encode($payment_process_from),
//                    'response_status' => 200,
//                    'form_inputs' => json_encode($form_inputs),
//                    'controlling_code' => 'single',
//                ]);
//            }
//
//            return response()->json([
//                'payment_gateway' => 'authorize',
//                'InvoiceId' => $input['invoice_id'],
//                'PaidAmount' => $payment_amount,
//                'resultCode' => $response->getMessages()->getResultCode(),
//                'code' => $payment_response,
//                'message' => $statusMsg,
//                'type_message' => $type_message,
//                'resp' => $response,
//                't_resp' => $tresponse,
//                'merchant_id' => $paymentMethod->id,
//                'update_invoice' => $update_invoice ?? null,
//                'mode' => $mode,
//            ], 200);
//        } catch (\Exception $e) {
//            return response()->json([
//                'errors' => $e->getMessage(),
//                'line' => $e->getLine(),
//            ], 422);
//        }
//    }

    public function crm_api_payment_create_authorize(Request $request)
    {
        /** Defining rules to validate */
        $rules = [
            'invoice_id' => 'required|int',
            'card_name' => 'required|string|max:255',
            'card_type' => 'required|string',
            'card_number' => 'required|string|max:16|min:15',
            'card_exp_month' => 'required|string|max:2',
            'card_exp_year' => 'required|string|max:4',
            'card_cvv' => 'required|min:3',
        ];
        /** Defining rules message to show validation messages */
        $messages = [
            'invoice_id.required' => 'The Invoice number field is required.',
            'card_name.required' => 'The Card name field is required.',
            'card_number.required' => 'The Card number field is required.',
            'card_number.min' => 'The Card number should not be less than 15 digits.',
            'card_exp_month.required' => 'The Expiry month field is required.',
            'card_exp_year.required' => 'The Expiry year field is required.',
            'card_cvv.required' => 'The CVV  number field is required.',
//            'card_cvv.integer' => 'The CVV number must be in numbers.',
        ];

        // Validate the input
        $validator = Validator::make($request->all(), $rules, $messages);
        // Check for validation errors
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422); // Unprocessable Entity status code
        }
        try {

            if (strtolower($request->payment_gateway) !== 'authorize') {
                return response()->json(['errors' => 'Try different payment gateway.'], 404);
            }
            $pkey = Config::get('app.privateKey');

            $input = $request->input();

            $invoice = Invoice::where('invoice_key', $input['invoice_id'])->first();
            if (!$invoice) {
                return response()->json(['errors' => 'Invoice not found',], 404);
            }
            $payment_amount = $total_amount = $invoice->total_amount;
            $merchant_handling_fee = $invoice->merchant_handling_fee;
            $tax_paid = $merchant_handling_fee_paid = $tax_curl = $merchant_handling_fee_curl = 0;
            $tax_amount = $invoice->tax_amount;

            if ($invoice->is_merchant_handling_fee == 1) {
                $total_amount -= $merchant_handling_fee;
                if ($invoice->is_tax == 1) {
                    $total_amount -= $tax_amount;
                }
            }
            if ($payment_amount != $total_amount) {
                $payment_amount = $total_amount;
            }
//            if ($invoice->is_split == 1) {
//                $payment_amount = $invoice['total_amount'] - 3;
//            }
            $invoice_payment = Payment::where('invoice_id', $invoice->invoice_key)->where('amount', $payment_amount)->first();
            if ($invoice->status == "paid" && $invoice_payment && $invoice->is_merchant_handling_fee == 0) {
                return response()->json(['errors' => 'Oops! Payment was already paid.',], 404);
            }

            if ($invoice->status == "paid" && $invoice_payment && $invoice->is_merchant_handling_fee == 1 && $invoice->is_tax == 1 && $tax_amount > 0 && $invoice->tax_paid == 0) {
                if ($request->has('is_curl') && $request->get('is_curl') == 1) {
                    $invoice->update(['is_tax_curl' => 1]);
                }
                $tax_paid = 1;
                $payment_amount = $tax_amount;
                $invoice_payment = Payment::where('invoice_id', $invoice->invoice_key)->where('amount', $payment_amount)->first();
            } else if ($invoice->status == "paid" && $invoice_payment && $invoice->is_merchant_handling_fee == 1 && $invoice->merchant_handling_fee_paid == 0) {
                if ($request->has('is_curl') && $request->get('is_curl') == 1) {
                    $invoice->update(['is_merchant_handling_fee_curl' => 1]);
                }
                $merchant_handling_fee_paid = 1;
                $payment_amount = $merchant_handling_fee;
                $invoice_payment = Payment::where('invoice_id', $invoice->invoice_key)->where('amount', $payment_amount)->first();
            }

            if ($invoice->status == "paid" && $invoice_payment
                && ($invoice->is_merchant_handling_fee == 0 || ($invoice->is_merchant_handling_fee == 1 && ($merchant_handling_fee < 1 || $invoice->merchant_handling_fee_paid == 1)))
                && ($invoice->is_tax == 0 || ($invoice->is_merchant_handling_fee == 1 && $invoice->is_tax == 1 && ($tax_amount < 1 || $invoice->tax_paid == 1)))) {
                return response()->json(['errors' => 'Oops! Payment was already paid..',], 404);
            }
            if ($payment_amount < 1) {
                return response()->json(['errors' => 'The payment amount must be greater than zero.',], 404);
            }
            $brand = Brand::where('brand_key', $invoice['brand_key'])->first();
            if (!$brand) {
                return response()->json(['errors' => 'Brand not found',], 404);
            }
            $mode = $request->header('X-Source') ? $request->header('X-Source') : $request->url();
            $merchant_id = $request->get('merchant_id') ?? $brand->merchant_id;
            if ($request->get('payment_gateway') !== 'authorize') {
                return response()->json(['errors' => 'Different payment merchant',], 404);
            }
            $paymentMethod = PaymentMethod::where(['status' => 1, 'id' => $merchant_id])->first();
            if (!$paymentMethod) {
                return response()->json(['errors' => 'Payment merchant not found',], 404);
            }
            $clientData = Client::where('id', $invoice['clientid'])->first();
            if (!$clientData) {
                return response()->json(['errors' => 'Client not found',], 404);
            }
            /** Calling Ip Response Function */
            $ipResponse = $this->getIpResponse();

            $sasDeclineReasonCodeDB = PaymentErrorCods::all();
            $sasDeclineReasonCode = array();
            foreach ($sasDeclineReasonCodeDB as $errorResaon) {
                $code = $errorResaon->error_code;
                $resoan = $errorResaon->error_reason;
                $sasDeclineReasonCode[$code] = $resoan;
            }


            if ($paymentMethod->mode == 0) {
                $loginId = $paymentMethod->live_login_id;
                $transcation_key = $paymentMethod->live_transaction_key;
            } else {
                $loginId = $paymentMethod->test_login_id;
                $transcation_key = $paymentMethod->test_transaction_key;
            }
            // Set the transaction's reference ID
            $refID = 'REF' . time();

            // Create a merchantAuthenticationType object with authentication details
            // retrieved from the config file
            $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
            $merchantAuthentication->setName($loginId);
            $merchantAuthentication->setTransactionKey($transcation_key);

            // Create the payment data for a credit card
            $cardNumber = preg_replace('/\s+/', '', $input['card_number']);
            $cardNumberEncrypt = cxmEncrypt($cardNumber, $pkey);
            $cardCvv = cxmEncrypt($input['card_cvv'], $pkey);

            $cc_info = new CcInfo();
            $cc_info->invoice_id = $invoice->invoice_key;
            $cc_info->payment_gateway = 1;
            /** Authorize*/
            $cc_info->client_id = $invoice->clientid;
            $cc_info->card_name = $input['card_name'];
            $cc_info->card_type = $input['card_type'];
            $cc_info->card_number = $cardNumberEncrypt;
            $cc_info->card_exp_month = $input['card_exp_month'];
            $cc_info->card_exp_year = $input['card_exp_year'];
            $cc_info->card_cvv = $cardCvv;
            if (request()->get('address') !== null) {
                $cc_info->address = request()->get('address');
            }
            if (request()->get('zipcode') !== null) {
                $cc_info->zipcode = request()->get('zipcode');
            }
            if (request()->get('city') !== null) {
                $cc_info->city = request()->get('city');
            }
            if (request()->get('state') !== null) {
                $cc_info->state = request()->get('state');
            }
            if (request()->get('country') !== null) {
                $cc_info->country = request()->get('country');
            }
            $cc_info->save();

            /** Card disabled on by default updated by migration 2024_01_03_085515_change_status_default_zero_to_cc_infos_table.php*/
            /** Card will be enabled after successful*/

            Log::driver('s_info')->debug('Authorize => details = ' . json_encode($cc_info));

            $creditCard = new AnetAPI\CreditCardType();
            $creditCard->setCardNumber($cardNumber);
            $creditCard->setExpirationDate($input['card_exp_year'] . "-" . $input['card_exp_month']);
            $creditCard->setCardCode($input['card_cvv']);

            // Add the payment data to a paymentType object
            $paymentOne = new AnetAPI\PaymentType();
            $paymentOne->setCreditCard($creditCard);

            // Create order information
            $order = new AnetAPI\OrderType();
            $order->setInvoiceNumber($invoice->invoice_key);
            $order->setDescription($brand->name);

            // Set the customer's Bill To address
            $customerAddress = new AnetAPI\CustomerAddressType();
            $customerAddress->setFirstName($input['card_name']);
//        $customerAddress->setLastName($inputlastName);
            if ($request->get('address') !== null) {
                $customerAddress->setAddress(preg_replace('/[^\w\s]/', '', substr($request->get('address'), 0, 60)));
            }
            if ($request->get('zipcode') !== null) {
                $customerAddress->setZip(preg_replace('/[^\w\s]/', '', substr($request->get('zipcode'), 0, 20)));
            }
            // if ($request->get('city') !== null) {
            //     $customerAddress->setCity(preg_replace('/[^\w\s]/', '', substr($request->get('city'), 0, 40)));
            // }
            // if ($request->get('state') !== null) {
            //     $customerAddress->setState(preg_replace('/[^\w\s]/', '', substr($request->get('state'), 0, 40)));
            // }
            if ($request->get('country') !== null) {
                $customerAddress->setCountry(preg_replace('/[^\w\s]/', '', substr($request->get('country'), 0, 60)));
            }

            $customerAddress->setEmail($clientData['email']);
//            $customerAddress->setPhoneNumber($clientData['phone']);

            // Set the customer's identifying information
            $customerData = new AnetAPI\CustomerDataType();
            $customerData->setId($clientData['id']);
            $customerData->setType("individual");
            $customerData->setEmail($clientData['email']);

            // Create a transaction
            $transactionRequestType = new AnetAPI\TransactionRequestType();
            $transactionRequestType->setTransactionType("authCaptureTransaction");
            $transactionRequestType->setAmount($payment_amount);
            $transactionRequestType->setOrder($order);
            $transactionRequestType->setPayment($paymentOne);
            $transactionRequestType->setCustomer($customerData);
            $transactionRequestType->setBillTo($customerAddress);
            if ($request->has('customer_ip') && $request->get('customer_ip') !== null) {
                $transactionRequestType->setCustomerIP($request->get('customer_ip'));
                $clientData->ip_address = request()->get('customer_ip');
                $clientData->save();
            } elseif ($clientData->ip_address) {
                $transactionRequestType->setCustomerIP($clientData->ip_address);
            }

//        $transactionRequestType->setCurrencyCode($invoice->cur_symbol);

            $trans_request = new AnetAPI\CreateTransactionRequest();
            $trans_request->setMerchantAuthentication($merchantAuthentication);
            $trans_request->setRefId($refID);
            $trans_request->setTransactionRequest($transactionRequestType);
            $controller = new AnetController\CreateTransactionController($trans_request);
            if ($paymentMethod->mode == 0) {
                $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
            } else {
                $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
            }

            $tresponse = $response->getTransactionResponse();
            $payment_status = $response->getMessages()->getResultCode();
            $type_message = 'Failed to capture payment.';

            $payment_response = '';
            if ($response != null) {
                // Check to see if the API request was successfully received and acted upon
                if ($response->getMessages() && ($response->getMessages()->getResultCode() == "Ok")) {
                    // Since the API request was successful, look for a transaction response
                    // and parse it to display the results of authorizing the card
                    $tresponse = $response->getTransactionResponse();

                    if ($tresponse != null && $tresponse->getMessages() != null) {

                        /** Successfully created transaction with Transaction ID */
                        $transaction_id = $tresponse->getTransId();
                        /** Transaction Response Code */
                        $payment_response = $tresponse->getResponseCode();
                        /** Auth Code */
                        $auth_code = $tresponse->getAuthCode();
                        /** Message Code */
                        $msgCode = $tresponse->getMessages()[0]->getCode();
                        /** Description */
                        $statusMsg = $tresponse->getMessages()[0]->getDescription();
                        /** Type message */
                        $type_message = 'Payment captured successfully.';

                        $payment_res = [
                            'team_key' => $invoice['team_key'],
                            'brand_key' => $invoice['brand_key'],
                            'creatorid' => $invoice['creatorid'],
                            'agent_id' => $invoice['agent_id'],
                            'clientid' => $invoice['clientid'],
                            'invoice_id' => $input['invoice_id'],
                            'project_id' => $invoice['project_id'],
                            'name' => trim($clientData['name']),
                            'email' => $clientData['email'],
                            'phone' => $clientData['phone'],
                            'address' => '',
                            'amount' => $payment_amount, /** Todo */
                            /** Payment Must be dynamic from response */
                            'payment_status' => '1',
                            'authorizenet_transaction_id' => $tresponse->getTransId(),
                            'payment_response' => json_encode($response),
                            'transaction_response' => json_encode($tresponse),
                            'payment_gateway' => 'authorize',
                            'auth_id' => $tresponse->getAuthCode(),
                            'response_code' => $payment_response,
                            'message_code' => $msgCode,
                            'payment_notes' => $invoice['invoice_descriptione'],
                            'sales_type' => $invoice['sales_type'],
                            'merchant_id' => $paymentMethod->id,
                            'card_type' => $input['card_type'],
                            'card_name' => trim($input['card_name']),
                            'card_number' => substr($input['card_number'], -4),
                            'card_exp_month' => $input['card_exp_month'],
                            'card_exp_year' => $input['card_exp_year'],
                            'card_cvv' => $input['card_cvv'],
                            'ip' => $ipResponse['ip'] ?? null,
                            'city' => $ipResponse['city'] ?? null,
                            'state' => $ipResponse['state'] ?? null,
                            'country' => $ipResponse['country'] ?? null,

                        ];

                        Payment::create($payment_res);

                        $invoice_data = ['status' => 'paid', 'received_amount' => $invoice->received_amount + $payment_amount];
                        if ($tax_paid == 1) {
                            $invoice_data['tax_paid'] = 1;
                        }
                        if ($merchant_handling_fee_paid == 1) {
                            $invoice_data['merchant_handling_fee_paid'] = 1;
                        }
//                        $update_invoice = Invoice::where('invoice_key', $input['invoice_id'])->update(['status' => 'paid', 'received_amount' => $payment_amount]);
                        $update_invoice = $invoice->update($invoice_data);

                        $logData = [
                            'team_key' => $invoice['team_key'],
                            'brand_key' => $invoice['brand_key'],
                            'clientid' => $invoice['clientid'],
                            'invoiceid' => $input['invoice_id'],
                            'projectid' => $invoice['project_id'],
                            'merchant_id' => $paymentMethod->id,
                            'amount' => $payment_amount,
                            'response_code' => $tresponse ? $tresponse->getResponseCode() : "",
                            'message_code' => $msgCode,
                            'response_reason' => $statusMsg,
                            'payment_gateway' => 1, /** 1 = Authorize */
                            'address' => request()->get('address'),
                            'zipcode' => request()->get('zipcode'),
                            'city' => request()->get('city'),
                            'state' => request()->get('state'),
                            'country' => request()->get('country'),
                        ];

                        $trans_log = PaymentTransactionsLog::create($logData);

                        /** Card enabled on successful*/
                        $cc_info->status = 1;
                        $cc_info->save();

                        $paymentMethod->increment('cap_usage', $payment_amount);

                        // send payment confirmation mail to Client/admin
                        // $emailOptions = array(
                        //     'to' => $input['email'],
                        //     'clientName' => $input['name'],
                        //     'subject' => 'Payment Confirmation',
                        //     'description' => $input['description'],
                        //     'amount' => $invoice['amount'] - 3,
                        //     'paidInvoiceId' => $input['invoice_id'],
                        //     'brandKey' => $input['brand_key']
                        // );
                        //sendEmail($emailOptions);

                    } else {
                        if ($tresponse->getErrors() != null) {
                            $errorCode = $tresponse->getErrors()[0]->getErrorCode();
                            $statusMsg = $tresponse->getErrors()[0]->getErrorText();
                            $payment_response = $tresponse->getErrors()[0]->getErrorCode();

                            if (array_key_exists($errorCode, $sasDeclineReasonCode)) {
                                $statusMsg .= $sasDeclineReasonCode[$errorCode];
                            }

                            if ($tresponse->getMessages() != null) {
                                $errorCode = $tresponse->getMessages()[0]->getCode() ?? "";
                            }
                            $logData = [
                                'team_key' => $invoice['team_key'],
                                'brand_key' => $invoice['brand_key'],
                                'clientid' => $invoice['clientid'],
                                'invoiceid' => $input['invoice_id'],
                                'projectid' => $invoice['project_id'],
                                'merchant_id' => $paymentMethod->id,
                                'amount' => $payment_amount,
                                'response_code' => $tresponse ? $tresponse->getResponseCode() : "",
                                'message_code' => $errorCode,
                                'response_reason' => $statusMsg,
                                'payment_gateway' => 1, /** 1 = Authorize */
                                'address' => request()->get('address'),
                                'zipcode' => request()->get('zipcode'),
                                'city' => request()->get('city'),
                                'state' => request()->get('state'),
                                'country' => request()->get('country'),
                            ];
                            $trans_log = PaymentTransactionsLog::create($logData);
                        }
                    }
                    // Or, print errors if the API request wasn't successful
                } else {
                    //echo "Transaction Failed \n";
                    $tresponse = $response->getTransactionResponse();

                    if ($tresponse != null && $tresponse->getErrors() != null) {
                        $errorCode = $tresponse->getErrors()[0]->getErrorCode();
                        $statusMsg = $tresponse->getErrors()[0]->getErrorText();
                        $payment_response = $tresponse->getErrors()[0]->getErrorCode();

                        if (array_key_exists($errorCode, $sasDeclineReasonCode)) {
                            $statusMsg .= $sasDeclineReasonCode[$errorCode];
                        }

                    } else {
                        $errorCode = $response->getMessages()->getMessage()[0]->getCode();
                        $statusMsg = $response->getMessages()->getMessage()[0]->getText();
                        //$payment_response = $tresponse->getErrors()[0]->getErrorCode();

                        if (array_key_exists($errorCode, $sasDeclineReasonCode)) {
                            $statusMsg .= $sasDeclineReasonCode[$errorCode];
                        }
                    }
                    $trans_log = PaymentTransactionsLog::create([
                        'team_key' => $invoice['team_key'],
                        'brand_key' => $invoice['brand_key'],
                        'clientid' => $invoice['clientid'],
                        'invoiceid' => $input['invoice_id'],
                        'projectid' => $invoice['project_id'],
                        'merchant_id' => $paymentMethod->id,
                        'amount' => $payment_amount,
                        'response_code' => $tresponse && $tresponse->getResponseCode() ? $tresponse->getResponseCode() : "",
                        'message_code' => $errorCode,
                        'response_reason' => $statusMsg,
                        'payment_gateway' => 1,
                        'address' => request()->get('address'),
                        'zipcode' => request()->get('zipcode'),
                        'city' => request()->get('city'),
                        'state' => request()->get('state'),
                        'country' => request()->get('country'),
                    ]);
                }
            } else {
                $statusMsg = "No response returned";
            }
            $process_from_mode = $request->get('process_from_mode', 2);

            if (Str::contains($request->url(), 'crm-api-payment-create-authorize') && !(Str::contains($request->url(), 'multi-payments')) && $process_from_mode == 2) {
                $inputs = $request->input();
                $pkey = Config::get('app.privateKey');
                $card_number_enc = cxmEncrypt($request->card_number, $pkey);
                $cvv_enc = cxmEncrypt($request->card_cvv, $pkey);
                $inputs['payment_gateway'] = 'authorize';
                $inputs['merchant_id'] = $paymentMethod->id;
                $inputs['merchant_name'] = $paymentMethod->merchant;
                $inputs['ip'] = request()->ip() ?? 'unknown';
                $form_inputs = $inputs;
                $form_inputs['card_number'] = $card_number_enc;
                $form_inputs['card_cvv'] = $cvv_enc;
                $payment_process_from = [
                    'authorize' => [
                        trim(str_replace(' ', '_', $paymentMethod->merchant)) => [
                            'payment_gateway' => 'authorize',
                            'InvoiceId' => $inputs['invoice_id'],
                            'PaidAmount' => $payment_amount,
                            'resultCode' => $response->getMessages()->getResultCode() ?? null,
                            'code' => $payment_response ?? null,
                            'message' => $statusMsg ?? null,
                            'resp' => $response,
                            't_resp' => $tresponse,
                            'merchant_id' => $paymentMethod->id,
                            'update_invoice' => $update_invoice ?? null,
                            'mode' => $mode,
                        ],
                    ],
                ];
                $multi_payment_response = MultiPaymentResponse::create([
                    'invoice_id' => $inputs['invoice_id'],
                    'response' => json_encode($response ?? null),
                    'payment_gateway' => 'authorize',
                    'payment_process_from' => json_encode($payment_process_from),
                    'response_status' => 200,
                    'form_inputs' => json_encode($form_inputs),
                    'controlling_code' => 'single',
                ]);
            }

            return response()->json([
                'payment_gateway' => 'authorize',
                'InvoiceId' => $input['invoice_id'],
                'PaidAmount' => $payment_amount,
                'resultCode' => $response->getMessages()->getResultCode(),
                'code' => $payment_response,
                'message' => $statusMsg,
                'type_message' => $type_message,
                'resp' => $response,
                't_resp' => $tresponse,
                'merchant_id' => $paymentMethod->id,
                'update_invoice' => $update_invoice ?? null,
                'mode' => $mode,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 422);
        }
    }


    public function crm_api_paypal_create(Request $request)
    {
        /** Defining rules to validate */
        $rules = [
            'invoice_id' => 'required|int',
            'payment_id' => 'required',
            'payerID' => 'required',
        ];
        /** Defining rules message to show validation messages */
        $messages = [
            'invoice_id.required' => 'The Invoice number field is required.',
            'payment_id.required' => 'The Payment Id field is required.',
            'payerID.required' => 'The Payer Id field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),], 422);
        }
        $invoice = Invoice::where('invoice_key', $request->get('invoice_id'))->first();
        if (!$invoice) {
            return response()->json(['errors' => 'Oops! Invoice not found.'], 404);
        }

        $brand = Brand::where('brand_key', $invoice['brand_key'])->first();
        if (!$brand) {
            return response()->json(['errors' => 'Oops! Brand not found.'], 404);
        }

        $invoice->status = 'paid';
        $invoice->payment_id = $request->get('payment_id');
        $invoice->save();

        $clientData = Client::where('id', $invoice['clientid'])->first();
        if (!$clientData) {
            return response()->json(['errors' => 'Oops! Client not found.'], 404);
        }

        /** Calling Ip Response Function */
        $ipResponse = $this->getIpResponse();

        $payment_data = [
            'team_key' => $invoice['team_key'],
            'brand_key' => $invoice['brand_key'],
            'creatorid' => $invoice['creatorid'],
            'agent_id' => $invoice['agent_id'],
            'clientid' => $invoice['clientid'],
            'invoice_id' => $request['invoice_id'],
            'project_id' => $invoice['project_id'],
            'name' => trim($clientData['name']),
            'email' => $clientData['email'],
            'phone' => $clientData['phone'],
            'address' => '',
            'amount' => $invoice['total_amount'], /** Todo */
            /** Payment Must be dynamic from response */
            'payment_status' => '1',
            'authorizenet_transaction_id' => $request['payment_id'],
            'payment_gateway' => "Paypal",
            'auth_id' => $request['invoice_id'],
            'response_code' => 1,
            'message_code' => 1,
            'payment_notes' => $invoice['invoice_descriptione'],
            'sales_type' => $invoice['sales_type'],
            'merchant_id' => 1,
            // 'card_type' => '',
            // 'card_name'=>'',
            // 'card_number'=>'',
            // 'card_exp_month'=>'',
            // 'card_exp_year'=>'',
            // 'card_cvv'=>'',
            'ip' => $ipResponse['ip'],
            'city' => $ipResponse['city'],
            'state' => $ipResponse['state'],
            'country' => $ipResponse['country'],

        ];
        $payment = Payment::create($payment_data);
        if (!$payment) {
            return response()->json(['errors' => 'Oops! There is an issue with the payment.'], 404);
        }
        // Create PaymentTransactionsLog
        $trans_log = PaymentTransactionsLog::create([
            'team_key' => $invoice['team_key'],
            'brand_key' => $invoice['brand_key'],
            'clientid' => $invoice['clientid'],
            'invoiceid' => $invoice['invoice_key'],
            'projectid' => $invoice['project_id'],
            'amount' => $invoice['total_amount'] != 0 ? $invoice['total_amount'] : $invoice['final_amount'],
            'response_code' => 1,
            'message_code' => 1,
            'response_reason' => 'This transaction has been approved.',
            'payment_gateway' => 4,/** 4 = Paypal */
        ]);
        if (!$trans_log) {
            return response()->json(['errors' => 'Oops! There is an issue with the payment transaction.'], 404);
        }
        return response()->json([
            'InvoiceId' => $request['invoice_id'],
            'brand_url' => $brand->brand_url,
            'resultCode' => 1,
            'message' => 'This transaction has been approved.',
            'status' => 'paid',
            'code' => 1,
        ], 200);
    }


    public function crm_api_expigate_update(Request $request)
    {
        $rules = [
            'invoice_id' => 'required|int',
        ];
        $messages = [
            'invoice_id.required' => 'Invoice Number is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),], 422);
        }
        /** Get Invoice Details */
        $invoice = Invoice::where('invoice_key', $request['invoice_id'])->first();
        if (!$invoice) {
            return response()->json(['error' => 'Oops! The Invoice details not found.'], 404);
        }
        /** Get Client Details */
        $clientData = Client::where('id', $invoice['clientid'])->first();
        if (!$clientData) {
            return response()->json(['error' => 'Oops! The Client details not found. There is an issue with the invoice.'], 404);
        }
        /** Get Brand Details */
        $brand = Brand::where('brand_key', $invoice['brand_key'])->first();
        if (!$brand) {
            return response()->json(['error' => 'Oops! The Brand details not found. There is an issue with the invoice.'], 404);
        }

        $invoice->status = 'paid';
        $invoice->payment_id = (string)$request['payment_id'];
        $invoice->save();

        /** Calling Ip Response Function */
        $ipResponse = $this->getIpResponse();

        $payment_data = [
            'team_key' => $invoice['team_key'],
            'brand_key' => $invoice['brand_key'],
            'creatorid' => $invoice['creatorid'],
            'agent_id' => $invoice['agent_id'],
            'clientid' => $invoice['clientid'],
            'invoice_id' => $request['invoice_id'],
            'project_id' => $invoice['project_id'],
            'name' => trim($clientData['name']),
            'email' => $clientData['email'],
            'phone' => $clientData['phone'],
            'address' => '',
            'amount' => $invoice['total_amount'], /** Todo */
            /** Payment Must be dynamic from response */
            'payment_status' => '1',
            'authorizenet_transaction_id' => $request['payment_id'],
            'payment_gateway' => "Expigate",
            'auth_id' => $request['invoice_id'],
            'response_code' => 1,
            'message_code' => 1,
            'payment_notes' => $invoice['invoice_descriptione'],
            'sales_type' => $invoice['sales_type'],
            'merchant_id' => 1,
            // 'card_type' => '',
            // 'card_name'=>'',
            // 'card_number'=>'',
            // 'card_exp_month'=>'',
            // 'card_exp_year'=>'',
            // 'card_cvv'=>'',
            'ip' => $ipResponse['ip'],
            'city' => $ipResponse['city'],
            'state' => $ipResponse['state'],
            'country' => $ipResponse['country'],

        ];
        Payment::create($payment_data);


        // Create PaymentTransactionsLog
        $trans_log = PaymentTransactionsLog::create([
            'team_key' => $invoice['team_key'],
            'brand_key' => $invoice['brand_key'],
            'clientid' => $invoice['clientid'],
            'invoiceid' => $invoice['invoice_key'],
            'projectid' => $invoice['project_id'],
            'amount' => $invoice['total_amount'] != 0 ? $invoice['total_amount'] : $invoice['final_amount'],
            'response_code' => 1,
            'message_code' => 1,
            'response_reason' => 'This transaction has been approved.',
            'payment_gateway' => 2,/** 2 = Expigate */
        ]);

        return response()->json([
            'InvoiceId' => $request['invoice_id'],
            'brand_url' => $brand->brand_url,
            'resultCode' => 1,
            'message' => 'This transaction has been approved.',
            'status' => 'paid',
            'code' => 1,
        ], 200);
    }


}
