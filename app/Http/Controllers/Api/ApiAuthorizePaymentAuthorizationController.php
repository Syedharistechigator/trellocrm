<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CcInfo;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\MultiPaymentResponse;
use App\Models\Payment;
use App\Models\PaymentAuthorization;
use App\Models\PaymentErrorCods;
use App\Models\PaymentMethod;
use App\Models\PaymentTransactionsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Config;

class ApiAuthorizePaymentAuthorizationController extends Controller
{
    public function payment_authorization(Request $request): ?\Illuminate\Http\JsonResponse
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
            $payment_amount = $invoice['total_amount'];
            if ($invoice->is_split == 1) {
                $payment_amount = $invoice['total_amount'] - 3;
            }
            $invoicepayments = Payment::where('invoice_id', $invoice->invoice_key)->where('amount', $payment_amount)->first();
            if ($invoice->status == "paid" && $invoicepayments) {
                return response()->json(['errors' => 'Oops! Payment was already paid.',], 404);
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
                $sasDeclineReasonCode[$code] = $errorResaon->error_reason;
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
            $transactionRequestType->setTransactionType("authOnlyTransaction");
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
            $type_message = 'Failed to authorized payment.';
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
                        $type_message = 'Payment authorized successfully.';
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
                        $update_invoice = $invoice->update(['status' => 'authorized']);
                        $payment_authorization = new PaymentAuthorization();
                        $payment_authorization->invoice_id = $input['invoice_id'];
                        $payment_authorization->client_id = $clientData->id;
                        $payment_authorization->card_id = $cc_info->id;
                        $payment_authorization->payment_gateway = "Authorize";
                        $payment_authorization->merchant_id = $paymentMethod->id;
                        $payment_authorization->transaction_id = $tresponse->getTransId();
                        $payment_authorization->response = json_encode([
                            'payment_gateway' => 'authorize',
                            'InvoiceId' => $input['invoice_id'],
                            'PaidAmount' => $payment_amount,
                            'resultCode' => $response->getMessages()->getResultCode(),
                            'code' => $payment_response,
                            'message' => $statusMsg,
                            'resp' => $response,
                            't_resp' => $tresponse,
                            'merchant_id' => $paymentMethod->id,
                            'update_invoice' => $update_invoice ?? null,
                            'mode' => $mode,
                        ] ?? null, JSON_THROW_ON_ERROR);
                        $payment_authorization->response_status = 200;
                        $payment_authorization->payment_status = 'authorized';
                        $payment_authorization->save();
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
                            'response_reason' => $statusMsg . '( Authorized )',
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
//                        $paymentMethod->increment('cap_usage', $payment_amount);
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
                                'response_reason' => $statusMsg . '( Authorizing )',
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
                        'response_reason' => $statusMsg . '( Authorizing )',
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
                'payment_authorized' => true,
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

    public function payment_capture_authorized_invoice(Request $request): ?\Illuminate\Http\JsonResponse
    {
        /** Defining rules to validate */
        $rules = [
            'id' => 'required|int|exists:invoices,id',
            'key' => 'required|int|exists:invoices,invoice_key',
        ];
        /** Defining rules message to show validation messages */
        $messages = [
            'id.required' => 'The Invoice number field is required.',
            'id.integer' => 'The Invoice number must be integer.',
            'id.exists' => 'The Invoice number is invalid.',
            'key.required' => 'The Invoice key field is required.',
            'key.integer' => 'The Invoice key must be integer.',
            'key.exists' => 'The Invoice key is invalid or not found.',
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
            $invoice_id = $request->get('id');
            $invoice_key = $request->get('key');
            $payment_authorization = PaymentAuthorization::where('invoice_id', $invoice_key)->first();
            if (!$payment_authorization) {
                return response()->json(['errors' => 'Payment was not previously authorized please authorized first.',], 404);
            }
            if ($payment_authorization->payment_status == 'captured') {
                return response()->json(['errors' => 'Payment was already captured.',], 404);
            }
            if (!$payment_authorization->transaction_id) {
                return response()->json(['errors' => 'Oops! Payment transaction id not found.',], 404);
            }
            $merchant_id = $payment_authorization->merchant_id;
            $pkey = Config::get('app.privateKey');
            $invoice = Invoice::where('id', $invoice_id)->where('invoice_key', $invoice_key)->first();
            if (!$invoice) {
                return response()->json(['errors' => 'Invoice not found',], 404);
            }
            $payment_amount = $invoice->total_amount;
            $invoicepayments = Payment::where('invoice_id', $invoice->invoice_key)->where('amount', $payment_amount)->first();
            if ($invoice->status == "paid" && $invoicepayments) {
                return response()->json(['errors' => 'Oops! Payment was already paid.',], 404);
            }
            if ($invoice->status != "authorized") {
                return response()->json(['errors' => 'Oops! Payment was previously not authorized.',], 404);
            }
            $paymentMethod = PaymentMethod::where('id', $merchant_id)->where('status', 1)->first();
            if (!$paymentMethod) {
                return response()->json(['errors' => 'Payment merchant not found',], 404);
            }
            $client = Client::where('id', $invoice->clientid)->first();
            if (!$client) {
                return response()->json(['errors' => 'Client not found.',], 404);
            }
            $cc_info = CcInfo::where('id', $payment_authorization->card_id)->where('client_id', $payment_authorization->client_id)->where('status', 1)->first();
            if (!$cc_info) {
                return response()->json(['errors' => 'Client cc details not found.',], 404);
            }
            /** Calling Ip Response Function */
            $ipResponse = $this->getIpResponse();
            $sasDeclineReasonCodeDB = PaymentErrorCods::all();
            $sasDeclineReasonCode = array();
            foreach ($sasDeclineReasonCodeDB as $errorResaon) {
                $code = $errorResaon->error_code;
                $sasDeclineReasonCode[$code] = $errorResaon->error_reason;
            }
            if ($paymentMethod->mode == 0) {
                $loginId = $paymentMethod->live_login_id;
                $transcation_key = $paymentMethod->live_transaction_key;
            } else {
                $loginId = $paymentMethod->test_login_id;
                $transcation_key = $paymentMethod->test_transaction_key;
            }
            // Create a merchantAuthenticationType object with authentication details
            // retrieved from the config file
            $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
            $merchantAuthentication->setName($loginId);
            $merchantAuthentication->setTransactionKey($transcation_key);
            Log::driver('s_info')->debug('Authorize Capture Authorized payment => details = ' . $invoice->id . " - " . $payment_authorization->id);
            // Create a transaction
            $transactionRequestType = new AnetAPI\TransactionRequestType();
            $transactionRequestType->setTransactionType("priorAuthCaptureTransaction");
            $transactionRequestType->setRefTransId($payment_authorization->transaction_id);
            $transactionRequestType->setAmount($payment_amount);
            $trans_request = new AnetAPI\CreateTransactionRequest();
            $trans_request->setMerchantAuthentication($merchantAuthentication);
            $trans_request->setTransactionRequest($transactionRequestType);
            $controller = new AnetController\CreateTransactionController($trans_request);
            if ($paymentMethod->mode == 0) {
                $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
            } else {
                $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
            }
            /** Type Message */
            $type_message = 'Failed to capture payment.';
            if ($response != null) {
                if ($response->getMessages() && ($response->getMessages()->getResultCode() == "Ok")) {
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
                        /** Type Message */
                        $type_message = 'Payment captured successfully.';
                        $payment_res = [
                            'team_key' => $invoice->team_key,
                            'brand_key' => $invoice->brand_key,
                            'creatorid' => $invoice->creatorid,
                            'agent_id' => $invoice->agent_id,
                            'clientid' => $invoice->clientid,
                            'invoice_id' => $invoice_key,
                            'project_id' => $invoice->project_id,
                            'name' => trim($client->name),
                            'email' => $client->email,
                            'phone' => $client->phone,
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
                            'payment_notes' => $invoice->invoice_descriptione,
                            'sales_type' => $invoice->sales_type,
                            'merchant_id' => $paymentMethod->id,
                            'card_type' => $cc_info->card_type,
                            'card_name' => trim($cc_info->card_name),
                            'card_number' => substr(cxmDecrypt($cc_info->card_number, $pkey), -4),
                            'card_exp_month' => $cc_info->card_exp_month,
                            'card_exp_year' => $cc_info->card_exp_year,
                            'card_cvv' => cxmDecrypt($cc_info->card_cvv, $pkey),
                            'ip' => $ipResponse['ip'] ?? null,
                            'city' => $ipResponse['city'] ?? null,
                            'state' => $ipResponse['state'] ?? null,
                            'country' => $ipResponse['country'] ?? null,
                        ];
                        Payment::create($payment_res);
                        $update_invoice = $invoice->update(['status' => 'paid', 'received_amount' => $payment_amount]);
                        $capture_response = json_encode([
                            'payment_gateway' => 'authorize',
                            'InvoiceId' => $invoice_key,
                            'PaidAmount' => $payment_amount,
                            'resultCode' => $response->getMessages()->getResultCode(),
                            'code' => $payment_response,
                            'message' => $statusMsg,
                            'resp' => $response,
                            't_resp' => $tresponse,
                            'merchant_id' => $paymentMethod->id,
                            'update_invoice' => $update_invoice ?? null,
                            'mode' => $request->url(),
                        ] ?? null, JSON_THROW_ON_ERROR);
                        $payment_authorization->update(['payment_status' => 'captured', 'capture_response' => $capture_response]);
                        PaymentTransactionsLog::create([
                            'team_key' => $invoice->team_key,
                            'brand_key' => $invoice->brand_key,
                            'clientid' => $invoice->clientid,
                            'invoiceid' => $invoice_key,
                            'projectid' => $invoice->project_id,
                            'merchant_id' => $paymentMethod->id,
                            'amount' => $payment_amount,
                            'response_code' => $tresponse ? $tresponse->getResponseCode() : "",
                            'message_code' => $msgCode,
                            'response_reason' => $statusMsg . '( Captured after authorization )',
                            'payment_gateway' => 1, /** 1 = Authorize */
                            'address' => request()->get('address'),
                            'zipcode' => request()->get('zipcode'),
                            'city' => request()->get('city'),
                            'state' => request()->get('state'),
                            'country' => request()->get('country'),
                        ]);
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
                        return response()->json([
                            'success' => $type_message,
                            'transaction_id' => $tresponse->getTransId(),
                            'payment_gateway' => 'authorize',
                            'InvoiceId' => $invoice_key,
                            'PaidAmount' => $payment_amount,
                            'resultCode' => $response->getMessages()->getResultCode(),
                            'code' => $payment_response,
                            'resp' => $response,
                            't_resp' => $tresponse,
                            'merchant_id' => $paymentMethod->id,
                            'update_invoice' => $update_invoice ?? null,
                        ], 200);
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
                            PaymentTransactionsLog::create([
                                'team_key' => $invoice->team_key,
                                'brand_key' => $invoice->brand_key,
                                'clientid' => $invoice->clientid,
                                'invoiceid' => $invoice_key,
                                'projectid' => $invoice->project_id,
                                'merchant_id' => $paymentMethod->id,
                                'amount' => $payment_amount,
                                'response_code' => $tresponse ? $tresponse->getResponseCode() : "",
                                'message_code' => $errorCode,
                                'response_reason' => $statusMsg . '( Capturing after authorization )',
                                'payment_gateway' => 1, /** 1 = Authorize */
                                'address' => request()->get('address'),
                                'zipcode' => request()->get('zipcode'),
                                'city' => request()->get('city'),
                                'state' => request()->get('state'),
                                'country' => request()->get('country'),
                            ]);
                        }
                    }
                    return response()->json(['success' => $statusMsg, 'transaction_id' => $payment_authorization->transaction_id, 'amount' => $payment_amount], 400);
                }
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
                    if (array_key_exists($errorCode, $sasDeclineReasonCode)) {
                        $statusMsg .= $sasDeclineReasonCode[$errorCode];
                    }
                }
                PaymentTransactionsLog::create([
                    'team_key' => $invoice->team_key,
                    'brand_key' => $invoice->brand_key,
                    'clientid' => $invoice->clientid,
                    'invoiceid' => $invoice_key,
                    'projectid' => $invoice->project_id,
                    'merchant_id' => $paymentMethod->id,
                    'amount' => $payment_amount,
                    'response_code' => $tresponse && $tresponse->getResponseCode() ? $tresponse->getResponseCode() : "",
                    'message_code' => $errorCode,
                    'response_reason' => $statusMsg . '( Capturing after authorization )',
                    'payment_gateway' => 1,
                    'address' => request()->get('address'),
                    'zipcode' => request()->get('zipcode'),
                    'city' => request()->get('city'),
                    'state' => request()->get('state'),
                    'country' => request()->get('country'),
                ]);
                return response()->json(['success' => $statusMsg, 'transaction_id' => $payment_authorization->transaction_id, 'amount' => $payment_amount], 400);
            }
            $statusMsg = "No response returned";
            return response()->json(['success' => $statusMsg, 'transaction_id' => $payment_authorization->transaction_id, 'amount' => $payment_amount], 400);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 422);
        }
    }
}
