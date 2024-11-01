<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CcInfo;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\MultiPaymentResponse;
use App\Models\Payment;
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

class ApiAuthorizePaymentController extends Controller
{
    public function process_payment(Request $request)
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

//
            if ($paymentMethod->mode === 0) {
                $loginId = $paymentMethod->live_login_id;
                $transcation_key = $paymentMethod->live_transaction_key;
                $environment = ANetEnvironment::PRODUCTION;

            } else {
                $loginId = $paymentMethod->test_login_id;
                $transcation_key = $paymentMethod->test_transaction_key;
                $environment = ANetEnvironment::SANDBOX;
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
            if ($request->get('address') !== null) {
                $cc_info->address = $request->get('address');
            }
            if ($request->get('zipcode') !== null) {
                $cc_info->zipcode = $request->get('zipcode');
            }
            if ($request->get('city') !== null) {
                $cc_info->city = $request->get('city');
            }
            if ($request->get('state') !== null) {
                $cc_info->state = $request->get('state');
            }
            if ($request->get('country') !== null) {
                $cc_info->country = $request->get('country');
            }
            $cc_info->save();

            /** Card disabled on by default updated by migration 2024_01_03_085515_change_status_default_zero_to_cc_infos_table.php*/
            /** Card will be enabled after successful*/

            Log::driver('s_info')->debug('Authorize => details = ' . json_encode($cc_info));


            // Ensure customer profile exists
            $customerProfileResponse = $this->ensureCustomerProfile($clientData, $merchantAuthentication, $request, $environment);

            // Ensure payment profile exists
            $paymentProfileResponse = $this->ensurePaymentProfile($clientData, $customerProfileResponse['customerProfileId'] ?? null, $merchantAuthentication, $request, $environment);

            // Create a transaction
            $transactionRequestType = new AnetAPI\TransactionRequestType();
            $transactionRequestType->setTransactionType("authCaptureTransaction");
            $transactionRequestType->setAmount($payment_amount);

            $customerProfilePaymentType = new AnetAPI\CustomerProfilePaymentType();
            $customerProfilePaymentType->setCustomerProfileId($customerProfileResponse['customerProfileId'] ?? null);

            $paymentProfile = new AnetAPI\PaymentProfileType();
            $paymentProfile->setPaymentProfileId($paymentProfileResponse['paymentProfileId'] ?? null);
            $customerProfilePaymentType->setPaymentProfile($paymentProfile);

            $transactionRequestType->setProfile($customerProfilePaymentType);


            // Create order information
            $order = new AnetAPI\OrderType();
            $order->setInvoiceNumber($invoice->invoice_key);
            $order->setDescription($brand->name);

            // Set the customer's identifying information
            $customerData = new AnetAPI\CustomerDataType();
            $customerData->setId($clientData['id']);
            $customerData->setType("individual");
            $customerData->setEmail($clientData['email']);


            $transactionRequestType->setOrder($order);
            $transactionRequestType->setCustomer($customerData);
//            $transactionRequestType->setBillTo($customerAddress);
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
            $response = (new AnetController\CreateTransactionController($trans_request))->executeWithApiResponse($environment);

            $tresponse = $response->getTransactionResponse();
            $payment_status = $response->getMessages()->getResultCode();

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

//                        $update_invoice = Invoice::where('invoice_key', $input['invoice_id'])->update(['status' => 'paid', 'received_amount' => $payment_amount]);
                        $update_invoice = $invoice->update(['status' => 'paid', 'received_amount' => $payment_amount]);

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
                            'customerProfileResponse' => $customerProfileResponse,
                            'paymentProfileResponse' => $paymentProfileResponse,
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
                'resp' => $response,
                't_resp' => $tresponse,
                'customerProfileResponse' => $customerProfileResponse,
                'paymentProfileResponse' => $paymentProfileResponse,
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

    private function ensureCustomerProfile($clientData, $merchantAuthentication, $request, $environment)
    {
        $getRequest = new AnetAPI\GetCustomerProfileRequest();
        $getRequest->setMerchantAuthentication($merchantAuthentication);
//        $getRequest->setCustomerProfileId($clientData->authorize_profile_id);
//        $getRequest->setCustomerProfileId(921462189);
//        $getRequest->setEmail($clientData->email);
        $getRequest->setEmail($request->get('email'));
        $getRequest->setIncludeIssuerInfo(true);

        $response = (new AnetController\GetCustomerProfileController($getRequest))->executeWithApiResponse($environment);
        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
            return [
                'success' => true,
                'customerProfileId' => $response->getProfile()->getCustomerProfileId(),
                'response' => $response,
            ];
        }

        $customerProfile = new AnetAPI\CustomerProfileType();
        $customerProfile->setDescription("Customer for " . $clientData->name);
//        $customerProfile->setEmail($clientData->email);
        $customerProfile->setEmail($request->get('email'));
        $customerProfile->setMerchantCustomerId($clientData->id);

        $createRequest = new AnetAPI\CreateCustomerProfileRequest();
        $createRequest->setMerchantAuthentication($merchantAuthentication);
        $createRequest->setProfile($customerProfile);

        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType();
        $customerAddress->setFirstName($request->get('card_name'));
//          $customerAddress->setLastName($inputlastName);
        foreach (['address' => 60, 'zipcode' => 20,
//                         'city' => 40,  'state' => 40,
                     'country' => 60] as $field => $maxLength) {
            if (($value = $request->get($field)) !== null) {
                $method = 'set' . ucfirst($field);
                if ($field === 'zipcode') {
                    $method = 'setZip';
                }
                $customerAddress->$method(preg_replace('/[^\w\s]/', '', substr($value, 0, $maxLength)));
            }
        }

        $customerAddress->setEmail($clientData['email']);
//            $customerAddress->setPhoneNumber($clientData['phone']);

        $createResponse = (new AnetController\CreateCustomerProfileController($createRequest))->executeWithApiResponse($environment);
        if (($createResponse != null) && ($createResponse->getMessages()->getResultCode() == "Ok")) {
            return [
                'success' => true,
                'customerProfileId' => $createResponse->getCustomerProfileId(),
                'response' => $createResponse,
            ];
        }

        throw new \Exception('Failed to create customer profile.');
    }

    private function ensurePaymentProfile($clientData, $CustomerProfileId, $merchantAuthentication, $request, $environment)
    {
        $cardNumber = preg_replace('/\s+/', '', $request->get('card_number'));
        $paymentProfiles = $this->getExistingPaymentProfiles($CustomerProfileId, $merchantAuthentication, $environment);

        if (isset($paymentProfiles)) {
            foreach ($paymentProfiles as $profile) {
                $existingCardNumber = $profile->getPayment()->getCreditCard()->getCardNumber();
                if (substr($existingCardNumber, -4) === substr($cardNumber, -4)) {
                    return [
                        'success' => true,
                        'paymentProfileId' => $profile->getCustomerPaymentProfileId(),
                        'response' => $profile,
                    ];
                }
            }
        }

        return $this->createPaymentProfile($CustomerProfileId, $merchantAuthentication, $request, $environment);
    }

    private function getExistingPaymentProfiles($CustomerProfileId, $merchantAuthentication, $environment)
    {
        $getPaymentProfilesRequest = new AnetAPI\GetCustomerProfileRequest();
        $getPaymentProfilesRequest->setMerchantAuthentication($merchantAuthentication);
        $getPaymentProfilesRequest->setCustomerProfileId($CustomerProfileId);

        $response = (new AnetController\GetCustomerProfileController($getPaymentProfilesRequest))
            ->executeWithApiResponse($environment);

        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
            return $response->getProfile()->getPaymentProfiles();
        }

        throw new \Exception('Failed to fetch existing payment profiles.');
    }

    private function createPaymentProfile($CustomerProfileId, $merchantAuthentication, $request, $environment)
    {
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber(preg_replace('/\s+/', '', $request->get('card_number')));
        $creditCard->setExpirationDate($request->get('card_exp_year') . "-" . $request->get('card_exp_month'));
        $creditCard->setCardCode($request->get('card_cvv'));

        $payment = new AnetAPI\PaymentType();
        $payment->setCreditCard($creditCard);

        // Set the customer's Bill To address
        $billTo = new AnetAPI\CustomerAddressType();
        $billTo->setFirstName($request->get('card_name'));
//          $billTo->setLastName($inputlastName);
        foreach (['address' => 60, 'zipcode' => 20,
//                         'city' => 40,  'state' => 40,
                     'country' => 60] as $field => $maxLength) {
            if (($value = $request->get($field)) !== null) {
                $method = 'set' . ucfirst($field);
                if ($field === 'zipcode') {
                    $method = 'setZip';
                }
                $billTo->$method(preg_replace('/[^\w\s]/', '', substr($value, 0, $maxLength)));
            }
        }

        $billTo->setEmail($request->get('email'));
//            $billTo->setPhoneNumber($clientData['phone']);

        $paymentProfile = new AnetAPI\CustomerPaymentProfileType();
        $paymentProfile->setCustomerType('individual');
        $paymentProfile->setPayment($payment);
        $paymentProfile->setBillTo($billTo);

        $createPaymentProfileRequest = new AnetAPI\CreateCustomerPaymentProfileRequest();
        $createPaymentProfileRequest->setMerchantAuthentication($merchantAuthentication);
        $createPaymentProfileRequest->setCustomerProfileId($CustomerProfileId);
        $createPaymentProfileRequest->setPaymentProfile($paymentProfile);
        $createPaymentProfileRequest->setValidationMode("liveMode");

        $createPaymentProfileResponse = (new AnetController\CreateCustomerPaymentProfileController($createPaymentProfileRequest))
            ->executeWithApiResponse($environment);
        if (($createPaymentProfileResponse != null) && ($createPaymentProfileResponse->getMessages()->getResultCode() == "Ok")) {
            return [
                'success' => true,
                'paymentProfileId' => $createPaymentProfileResponse->getCustomerPaymentProfileId(),
                'response' => $createPaymentProfileResponse,
            ];
        }
        throw new \Exception('Failed to create customer payment profiles..');
    }

}
