<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Invoice;
use App\Models\MultiPaymentResponse;
use App\Models\Payment;
use App\Models\PaymentMethodExpigate;
use App\Models\Client;
use App\Models\CcInfo;
use App\Models\PaymentTransactionsLog;
use gwapi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApiExpigatePaymentController extends Controller
{

//    public function process_payment(Request $request)
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
////            'address' => 'required',
////            'zipcode' => 'required',
////            'city' => 'required',
////            'state' => 'required',
////            'country' => 'required',
//        ];
//        /** Defining rules message to show validation messages */
//        $messages = [
//            'invoice_id.required' => 'The Invoice number field is required.',
//            'card_number.required' => 'The Card number field is required.',
//            'card_number.min' => 'The Card number should not be less than 15 digits.',
//            'card_exp_month.required' => 'The Expiry month field is required.',
//            'card_exp_year.required' => 'The Expiry year field is required.',
//            'card_cvv.required' => 'The CVV  number field is required.',
////            'card_cvv.integer' => 'The CVV number must be in numbers.',
//        ];
//        /** Validating through Validator Library */
//        $validator = Validator::make($request->all(), $rules, $messages);
//        /** Return errors if validator fails */
//        if ($validator->fails()) {
//            return response()->json(['errors' => $validator->errors(),], 422);
//        }
//        try {
//
//            /** Fetching invoice to get invoice details */
//            $invoice = Invoice::where('invoice_key', $request->input('invoice_id'))->first();
//            /** Returning error if invocie not found */
//            if (!$invoice) {
//                return response()->json(['errors' => 'Oops! Invoice not found in ep where invoice number is :' . $request->input('invoice_id', 0)], 404);
//            }
//            /** Fetching invoice to get invoice details */
//            $client = Client::where('id', $invoice->clientid)->first();
//            /** Returning error if invocie not found */
//            if (!$client) {
//                return response()->json(['errors' => 'Oops! Client not found.'], 404);
//            }
//            /** We will update customer ip*/
//            if ($request->has('customer_ip') && $request->get('customer_ip') !== null) {
//                $client->ip_address = request()->get('customer_ip');
//                $client->save();
//            }
//            /** Now confirming if payment was already done */
//            $invoice_payment = Payment::where('invoice_id', $invoice->invoice_key)->where('amount', $invoice->total_amount)->first();
//            if ($invoice->status == "paid" && $invoice_payment) {
//                return response()->json(['errors' => 'Oops! Payment was already paid.'], 404);
//            }
//            /** Now we have to fetch brand through invoice so that we will get Payment Merchant which we need to process payment */
//            $brand = Brand::where('brand_key', $invoice->brand_key)->first();
//            /** Returning error if brand not found , there must be issue with the invoice */
//            if (!$brand) {
//                return response()->json(['errors' => 'Brand not found'], 404);
//            }
//            /** Calling Ip Response Function */
//            $ipResponse = $this->getIpResponse();
//
//            $pkey = Config::get('app.privateKey');
//            // Create the payment data for a credit card
//            $cardNumber = preg_replace('/\s+/', '', $request->card_number);
//            $cardNumberEncrypt = cxmEncrypt($cardNumber, $pkey);
//            $cardCvv = cxmEncrypt($request->card_cvv, $pkey);
//
//            $cc_info = new CcInfo();
//            $cc_info->invoice_id = $invoice->invoice_key;
//            $cc_info->payment_gateway = 2;
//            /** 2 = Expigate for both (Expigate And Amazon)*/
//            $cc_info->client_id = $invoice->clientid;
//            $cc_info->card_name = $request->get('card_name');
//            $cc_info->card_type = $request->get('card_type');
//            $cc_info->card_number = $cardNumberEncrypt;
//            $cc_info->card_exp_month = $request->get('card_exp_month');
//            $cc_info->card_exp_year = $request->get('card_exp_year');
//            $cc_info->card_cvv = $cardCvv;
//
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
//
//            $cc_info->save();
//            /** Card disabled on by default updated by migration 2024_01_03_085515_change_status_default_zero_to_cc_infos_table.php*/
//            /** Card will be enabled after successful*/
//
//            Log::driver('s_info')->debug('Expigate  => details = ' . json_encode($cc_info));
//
//            /** Initializing GwApi Payment Gateway Class Or Library */
//            $gw = new GwApi();
//            $expigate_id = $request->get('merchant_id') ?? $brand->expigate_id;
//
//            /** Fetching the payment merchant using the brand key, which we have already obtained in the previous step */
//            $paymentMethodExpigate = PaymentMethodExpigate::where(['status' => 1, 'id' => $expigate_id])->first();
//            /** For secure purpose there is no default merchant */
////            /** If payment merchant not found we will use alternate way to get payment */
////            if (!$paymentMethodExpigate) {
////                /** Here you can add your default expigate_id */
////                $paymentMethodExpigate = PaymentMethodExpigate::where(['status' => 1, 'live_login_id' => 'vca7Eaarsb5cR662akeBvryKt82M3e37'])->first();
////            }
//            if (!$paymentMethodExpigate) {
//                return response()->json(['errors' => 'Payment merchant not found'], 404);
//            }
//            if ($paymentMethodExpigate->mode == 0) {
//                // $loginId = $paymentMethodExpigate->live_login_id;
//                $transcation_key = $paymentMethodExpigate->live_transaction_key;
//            } else {
//                // $loginId = $paymentMethodExpigate->test_login_id;
//                $transcation_key = $paymentMethodExpigate->test_transaction_key;
//            }
//            $gw->setLogin($transcation_key);
//
//            /** Set billing and shipping information */
//            $this->setBillingAndShipping($gw, $client, $request);
//
//            // Set order information
//            $this->setOrder($gw, $invoice);
//
//            // Extract the year for card expiration
//            $year = strlen($request->input('card_exp_year')) > 2 ? substr($request->input('card_exp_year'), -2) : $request->input('card_exp_year');
//
//            if ($paymentMethodExpigate->payment_url !== null) {
//                $payment_url = $paymentMethodExpigate->payment_url;
//            } elseif ($paymentMethodExpigate->name === "Amazon") {
//                $payment_url = "https://merchantstronghold.transactiongateway.com/api/transact.php";
//            } elseif ($paymentMethodExpigate->name === "Expigate") {
//                $payment_url = "https://secure.expigate.com/api/transact.php";
//            } else {
//                $payment_url = null;
//            }
//            $payment_amount = $invoice->total_amount;
//
//            // Process the payment
//            $response = $this->processPaymentRequest($gw, $request, $year, $invoice, $brand->is_amazon, $payment_url, $payment_amount);
////            $payment_amount = $invoice->total_amount != 0 ? $invoice->total_amount : number_format($invoice->final_amount + $invoice->tax_amount, 2, '.', '');
//
//            if (isset($response['response_code']) && $response['response_code'] == '100') {
//                $payment_res = [
//                    'team_key' => $invoice->team_key,
//                    'brand_key' => $invoice->brand_key,
//                    'creatorid' => $invoice->creatorid,
//                    'agent_id' => $invoice->agent_id,
//                    'clientid' => $invoice->clientid,
//                    'invoice_id' => $invoice->invoice_key,
//                    'project_id' => $invoice->project_id,
//                    'name' => trim($client->name),
//                    'email' => $client->email,
//                    'phone' => $client->phone,
//                    'address' => '',
//                    'amount' => $payment_amount,
//                    /** Todo */
//                    /** Payment Must be dynamic from response */
//                    'payment_status' => '1',
//                    'authorizenet_transaction_id' => $response['transactionid'],
//                    'payment_response' => json_encode($response),
//                    'payment_gateway' => 'Expigate',
//                    'auth_id' => $response['authcode'],
//                    'response_code' => $response['response_code'],
//                    'message_code' => $response['response'],
//                    'payment_notes' => $invoice->invoice_descriptione,
//                    'sales_type' => $invoice->sales_type,
//                    'merchant_id' => $paymentMethodExpigate->id,
//                    'card_type' => $request->card_type,
//                    'card_name' => trim($request->card_name),
//                    'card_number' => substr($request->card_number, -4),
//                    'card_exp_month' => $request->card_exp_month,
//                    'card_exp_year' => $request->card_exp_year,
//                    'card_cvv' => $request->card_cvv,
//                    'ip' => $ipResponse['ip'],
//                    'city' => $ipResponse['city'],
//                    'state' => $ipResponse['state'],
//                    'country' => $ipResponse['country'],
//
//                ];
//
//
//                Payment::create($payment_res);
//
//                $invoice->update(['status' => 'paid', 'received_amount' => $payment_amount]);
//
//                /** Card enabled on successful*/
//                $cc_info->status = 1;
//                $cc_info->save();
//
//                $paymentMethodExpigate->increment('cap_usage', $payment_amount);
//            }
//
//            $logData = [
//                'team_key' => $invoice->team_key,
//                'brand_key' => $invoice->brand_key,
//                'clientid' => $invoice->clientid,
//                'invoiceid' => $invoice->invoice_key,
//                'merchant_id' => $paymentMethodExpigate->id,
//                'projectid' => $invoice->project_id,
//                'amount' => $payment_amount,
//                'response_code' => $response['response_code'] ?? "",
//                'message_code' => $response['response'] ?? "",
//                'response_reason' => $response['responsetext'] ?? "",
//                'payment_gateway' => 2, /** 2 = Expigate */
//                'address' => $request->get('address'),
//                'zipcode' => $request->get('zipcode'),
//                'city' => $request->get('city'),
//                'state' => $request->get('state'),
//                'country' => $request->get('country'),
//            ];
//
//            $trans_log = PaymentTransactionsLog::create($logData);
//
//            $process_from_mode = $request->get('process_from_mode', 2);
//
//            if (Str::contains($request->url(), 'expigate-process-payment') && !(Str::contains($request->url(), 'multi-payments')) && $process_from_mode == 2) {
//                $inputs = $request->input();
//                $pkey = Config::get('app.privateKey');
//                $card_number_enc = cxmEncrypt($request->card_number, $pkey);
//                $cvv_enc = cxmEncrypt($request->card_cvv, $pkey);
//                $inputs['payment_gateway'] = $paymentMethodExpigate->name;
//                $inputs['merchant_id'] = $paymentMethodExpigate->id;
//                $inputs['merchant_name'] = $paymentMethodExpigate->merchant;
//                $inputs['ip'] = request()->ip() ?? 'unknown';
//                $form_inputs = $inputs;
//                $form_inputs['card_number'] = $card_number_enc;
//                $form_inputs['card_cvv'] = $cvv_enc;
//
//                $payment_process_from = [
//                    strtolower($paymentMethodExpigate->name) => [
//                        trim(str_replace(' ', '_', $paymentMethodExpigate->merchant)) => [
//                            'payment_gateway' => $request->payment_gateway == 'amazon' ? "amazon" : "expigate",
//                            'payment_url' => $payment_url,
//                            'response' => $response,
//                            'InvoiceId' => $invoice->invoice_key,
//                            'PaymentAmount' => $payment_amount,
//                            'resultCode' => $response['response_code'] ?? "",
//                            'code' => $response['response'] ?? "",
////                            'keys' => $transcation_key,
////                'paymentMethodExpigate' => $paymentMethodExpigate,
//                            'message' => $response['responsetext'] ?? "",
//                            'merchant_id' => $paymentMethodExpigate->id,
//                            'merchant_name' => $paymentMethodExpigate->merchant,
//                            'mode' => $request->header('X-Source') ? $request->header('X-Source') : $request->url(),
//                        ],
//                    ],
//                ];
//
//                $multi_payment_response = MultiPaymentResponse::create([
//                    'invoice_id' => $inputs['invoice_id'],
//                    'response' => json_encode($response ?? null),
//                    'payment_gateway' => strtolower($paymentMethodExpigate->name),
//                    'payment_process_from' => json_encode($payment_process_from),
//                    'response_status' => 200,
//                    'form_inputs' => json_encode($form_inputs),
//                    'controlling_code' => 'single',
//                ]);
//            }
//
//            return response()->json([
//                'payment_gateway' => $request->payment_gateway == 'amazon' ? "amazon" : "expigate",
//                'payment_url' => $payment_url,
//                'response' => $response,
//                'InvoiceId' => $invoice->invoice_key,
//                'PaymentAmount' => $payment_amount,
//                'resultCode' => $response['response_code'] ?? "",
//                'code' => $response['response'] ?? "",
////                'keys' => $transcation_key,
////                'paymentMethodExpigate' => $paymentMethodExpigate,
//                'message' => $response['responsetext'] ?? "",
//                'merchant_id' => $paymentMethodExpigate->id,
//                'merchant_name' => trim(str_replace(' ', '_', $paymentMethodExpigate->merchant)),
//                'mode' => $request->header('X-Source') ? $request->header('X-Source') : $request->url(),
//            ], 200);
//        } catch (\Exception $e) {
//            return response()->json([
//                'errors' => $e->getMessage(),
//            ], 422);
//        }
//    }

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
//            'address' => 'required',
//            'zipcode' => 'required',
//            'city' => 'required',
//            'state' => 'required',
//            'country' => 'required',
        ];
        /** Defining rules message to show validation messages */
        $messages = [
            'invoice_id.required' => 'The Invoice number field is required.',
            'card_number.required' => 'The Card number field is required.',
            'card_number.min' => 'The Card number should not be less than 15 digits.',
            'card_exp_month.required' => 'The Expiry month field is required.',
            'card_exp_year.required' => 'The Expiry year field is required.',
            'card_cvv.required' => 'The CVV  number field is required.',
//            'card_cvv.integer' => 'The CVV number must be in numbers.',
        ];
        /** Validating through Validator Library */
        $validator = Validator::make($request->all(), $rules, $messages);
        /** Return errors if validator fails */
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),], 422);
        }
        try {

            /** Fetching invoice to get invoice details */
            $invoice = Invoice::where('invoice_key', $request->input('invoice_id'))->first();
            /** Returning error if invocie not found */
            if (!$invoice) {
                return response()->json(['errors' => 'Oops! Invoice not found in ep where invoice number is :' . $request->input('invoice_id', 0)], 404);
            }
            /** Fetching invoice to get invoice details */
            $client = Client::where('id', $invoice->clientid)->first();
            /** Returning error if invocie not found */
            if (!$client) {
                return response()->json(['errors' => 'Oops! Client not found.'], 404);
            }
            /** We will update customer ip*/
            if ($request->has('customer_ip') && $request->get('customer_ip') !== null) {
                $client->ip_address = request()->get('customer_ip');
                $client->save();
            }

            $payment_amount = $total_amount = $invoice->total_amount;
            $merchant_handling_fee = $invoice->merchant_handling_fee;
            $tax_paid = $merchant_handling_fee_paid = $tax_curl = $merchant_handling_fee_curl = 0;
            $tax_amount = $invoice->tax_amount;

            if ($invoice->is_merchant_handling_fee == 1 && $merchant_handling_fee > 0) {
                $total_amount -= $merchant_handling_fee;
                if ($invoice->is_tax == 1) {
                    $total_amount -= $tax_amount;
                }
            }
            if ($payment_amount != $total_amount) {
                $payment_amount = $total_amount;
            }
            /** Now confirming if payment was already done */
            $invoice_payment = Payment::where('invoice_id', $invoice->invoice_key)->where('amount', $payment_amount)->first();
            if ($invoice->status == "paid" && $invoice_payment && $invoice->is_merchant_handling_fee == 0) {
                return response()->json(['errors' => 'Oops! Payment was already paid.'], 404);
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

            /** Now we have to fetch brand through invoice so that we will get Payment Merchant which we need to process payment */
            $brand = Brand::where('brand_key', $invoice->brand_key)->first();
            /** Returning error if brand not found , there must be issue with the invoice */
            if (!$brand) {
                return response()->json(['errors' => 'Brand not found'], 404);
            }
            /** Calling Ip Response Function */
            $ipResponse = $this->getIpResponse();

            $pkey = Config::get('app.privateKey');
            // Create the payment data for a credit card
            $cardNumber = preg_replace('/\s+/', '', $request->card_number);
            $cardNumberEncrypt = cxmEncrypt($cardNumber, $pkey);
            $cardCvv = cxmEncrypt($request->card_cvv, $pkey);

            $cc_info = new CcInfo();
            $cc_info->invoice_id = $invoice->invoice_key;
            $cc_info->payment_gateway = 2;
            /** 2 = Expigate for both (Expigate And Amazon)*/
            $cc_info->client_id = $invoice->clientid;
            $cc_info->card_name = $request->get('card_name');
            $cc_info->card_type = $request->get('card_type');
            $cc_info->card_number = $cardNumberEncrypt;
            $cc_info->card_exp_month = $request->get('card_exp_month');
            $cc_info->card_exp_year = $request->get('card_exp_year');
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

            Log::driver('s_info')->debug('Expigate  => details = ' . json_encode($cc_info));

            /** Initializing GwApi Payment Gateway Class Or Library */
            $gw = new GwApi();
            $expigate_id = $request->get('merchant_id') ?? $brand->expigate_id;

            /** Fetching the payment merchant using the brand key, which we have already obtained in the previous step */
            $paymentMethodExpigate = PaymentMethodExpigate::where(['status' => 1, 'id' => $expigate_id])->first();
            /** For secure purpose there is no default merchant */
//            /** If payment merchant not found we will use alternate way to get payment */
//            if (!$paymentMethodExpigate) {
//                /** Here you can add your default expigate_id */
//                $paymentMethodExpigate = PaymentMethodExpigate::where(['status' => 1, 'live_login_id' => 'vca7Eaarsb5cR662akeBvryKt82M3e37'])->first();
//            }
            if (!$paymentMethodExpigate) {
                return response()->json(['errors' => 'Payment merchant not found'], 404);
            }
            if ($paymentMethodExpigate->mode == 0) {
                // $loginId = $paymentMethodExpigate->live_login_id;
                $transcation_key = $paymentMethodExpigate->live_transaction_key;
            } else {
                // $loginId = $paymentMethodExpigate->test_login_id;
                $transcation_key = $paymentMethodExpigate->test_transaction_key;
            }
            $gw->setLogin($transcation_key);

            /** Set billing and shipping information */
            $this->setBillingAndShipping($gw, $client, $request);

            // Set order information
            $this->setOrder($gw, $invoice);

            // Extract the year for card expiration
            $year = strlen($request->input('card_exp_year')) > 2 ? substr($request->input('card_exp_year'), -2) : $request->input('card_exp_year');

            if ($paymentMethodExpigate->payment_url !== null) {
                $payment_url = $paymentMethodExpigate->payment_url;
            } elseif ($paymentMethodExpigate->name === "Amazon") {
                $payment_url = "https://merchantstronghold.transactiongateway.com/api/transact.php";
            } elseif ($paymentMethodExpigate->name === "Expigate") {
                $payment_url = "https://secure.expigate.com/api/transact.php";
            } else {
                $payment_url = null;
            }

            // Process the payment
            $response = $this->processPaymentRequest($gw, $request, $year, $invoice, $brand->is_amazon, $payment_url, $payment_amount);
//            $payment_amount = $invoice->total_amount != 0 ? $invoice->total_amount : number_format($invoice->final_amount + $invoice->tax_amount, 2, '.', '');

            if (isset($response['response_code']) && $response['response_code'] == '100') {
                $payment_res = [
                    'team_key' => $invoice->team_key,
                    'brand_key' => $invoice->brand_key,
                    'creatorid' => $invoice->creatorid,
                    'agent_id' => $invoice->agent_id,
                    'clientid' => $invoice->clientid,
                    'invoice_id' => $invoice->invoice_key,
                    'project_id' => $invoice->project_id,
                    'name' => trim($client->name),
                    'email' => $client->email,
                    'phone' => $client->phone,
                    'address' => '',
                    'amount' => $payment_amount,
                    /** Todo */
                    /** Payment Must be dynamic from response */
                    'payment_status' => '1',
                    'authorizenet_transaction_id' => $response['transactionid'],
                    'payment_response' => json_encode($response),
                    'payment_gateway' => 'Expigate',
                    'auth_id' => $response['authcode'],
                    'response_code' => $response['response_code'],
                    'message_code' => $response['response'],
                    'payment_notes' => $invoice->invoice_descriptione,
                    'sales_type' => $invoice->sales_type,
                    'merchant_id' => $paymentMethodExpigate->id,
                    'card_type' => $request->card_type,
                    'card_name' => trim($request->card_name),
                    'card_number' => substr($request->card_number, -4),
                    'card_exp_month' => $request->card_exp_month,
                    'card_exp_year' => $request->card_exp_year,
                    'card_cvv' => $request->card_cvv,
                    'ip' => $ipResponse['ip'],
                    'city' => $ipResponse['city'],
                    'state' => $ipResponse['state'],
                    'country' => $ipResponse['country'],

                ];


                Payment::create($payment_res);
                $invoice_data = ['status' => 'paid', 'received_amount' => $invoice->received_amount + $payment_amount];
                if ($tax_paid == 1) {
                    $invoice_data['tax_paid'] = 1;
                }
                if ($merchant_handling_fee_paid == 1) {
                    $invoice_data['merchant_handling_fee_paid'] = 1;
                }
                $update_invoice = $invoice->update($invoice_data);

                /** Card enabled on successful*/
                $cc_info->status = 1;
                $cc_info->save();

                $paymentMethodExpigate->increment('cap_usage', $payment_amount);
            }

            $logData = [
                'team_key' => $invoice->team_key,
                'brand_key' => $invoice->brand_key,
                'clientid' => $invoice->clientid,
                'invoiceid' => $invoice->invoice_key,
                'merchant_id' => $paymentMethodExpigate->id,
                'projectid' => $invoice->project_id,
                'amount' => $payment_amount,
                'response_code' => $response['response_code'] ?? "",
                'message_code' => $response['response'] ?? "",
                'response_reason' => $response['responsetext'] ?? "",
                'payment_gateway' => 2, /** 2 = Expigate */
                'address' => $request->get('address'),
                'zipcode' => $request->get('zipcode'),
                'city' => $request->get('city'),
                'state' => $request->get('state'),
                'country' => $request->get('country'),
            ];

            $trans_log = PaymentTransactionsLog::create($logData);

            $process_from_mode = $request->get('process_from_mode', 2);

            if (Str::contains($request->url(), 'expigate-process-payment') && !(Str::contains($request->url(), 'multi-payments')) && $process_from_mode == 2) {
                $inputs = $request->input();
                $pkey = Config::get('app.privateKey');
                $card_number_enc = cxmEncrypt($request->card_number, $pkey);
                $cvv_enc = cxmEncrypt($request->card_cvv, $pkey);
                $inputs['payment_gateway'] = $paymentMethodExpigate->name;
                $inputs['merchant_id'] = $paymentMethodExpigate->id;
                $inputs['merchant_name'] = $paymentMethodExpigate->merchant;
                $inputs['ip'] = request()->ip() ?? 'unknown';
                $form_inputs = $inputs;
                $form_inputs['card_number'] = $card_number_enc;
                $form_inputs['card_cvv'] = $cvv_enc;

                $payment_process_from = [
                    strtolower($paymentMethodExpigate->name) => [
                        trim(str_replace(' ', '_', $paymentMethodExpigate->merchant)) => [
                            'payment_gateway' => $request->payment_gateway == 'amazon' ? "amazon" : "expigate",
                            'payment_url' => $payment_url,
                            'response' => $response,
                            'InvoiceId' => $invoice->invoice_key,
                            'PaymentAmount' => $payment_amount,
                            'resultCode' => $response['response_code'] ?? "",
                            'code' => $response['response'] ?? "",
//                            'keys' => $transcation_key,
//                'paymentMethodExpigate' => $paymentMethodExpigate,
                            'message' => $response['responsetext'] ?? "",
                            'merchant_id' => $paymentMethodExpigate->id,
                            'merchant_name' => $paymentMethodExpigate->merchant,
                            'mode' => $request->header('X-Source') ? $request->header('X-Source') : $request->url(),
                        ],
                    ],
                ];

                $multi_payment_response = MultiPaymentResponse::create([
                    'invoice_id' => $inputs['invoice_id'],
                    'response' => json_encode($response ?? null),
                    'payment_gateway' => strtolower($paymentMethodExpigate->name),
                    'payment_process_from' => json_encode($payment_process_from),
                    'response_status' => 200,
                    'form_inputs' => json_encode($form_inputs),
                    'controlling_code' => 'single',
                ]);
            }

            return response()->json([
                'payment_gateway' => $request->payment_gateway == 'amazon' ? "amazon" : "expigate",
                'payment_url' => $payment_url,
                'response' => $response,
                'InvoiceId' => $invoice->invoice_key,
                'PaymentAmount' => $payment_amount,
                'resultCode' => $response['response_code'] ?? "",
                'code' => $response['response'] ?? "",
//                'keys' => $transcation_key,
//                'paymentMethodExpigate' => $paymentMethodExpigate,
                'message' => $response['responsetext'] ?? "",
                'merchant_id' => $paymentMethodExpigate->id,
                'merchant_name' => trim(str_replace(' ', '_', $paymentMethodExpigate->merchant)),
                'mode' => $request->header('X-Source') ? $request->header('X-Source') : $request->url(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage(),
            ], 422);
        }
    }

    private function setBillingAndShipping($gw, $client, $request_inputs)
    {
        $billingData = [
            'firstname' => $request_inputs->card_name,
            'lastname' => '',
            'company' => '',
            'address1' => preg_replace('/[^\w\s]/', '', substr($request_inputs->get('address'), 0, 50)) . " " . preg_replace('/[^\w\s]/', '', substr($request_inputs->get('country'), 0, 25)),
            'address2' => '',
            'city' => preg_replace('/[^\w\s]/', '', substr($request_inputs->get('city'), 0, 25)),
            'state' => preg_replace('/[^\w\s]/', '', substr($request_inputs->get('state'), 0, 25)),
            'zip' => preg_replace('/[^\w\s]/', '', substr($request_inputs->get('zipcode'), 0, 10)),
            'country' => preg_replace('/[^\w\s]/', '', substr($request_inputs->get('country'), 0, 25)),
//            'phone' => preg_replace('/\D/', '', $client->phone),
            'phone' => "",
            'fax' => '',
            'email' => $client->email,
            'website' => '',
        ];
        $shippingData = [
            'firstname' => $request_inputs->card_name,
            'lastname' => '',
            'company' => '',
            'address1' => preg_replace('/[^\w\s]/', '', substr($request_inputs->get('address'), 0, 50)) . " " . preg_replace('/[^\w\s]/', '', substr($request_inputs->get('country'), 0, 25)),
            'address2' => '',
            'city' => preg_replace('/[^\w\s]/', '', substr($request_inputs->get('city'), 0, 25)),
            'state' => preg_replace('/[^\w\s]/', '', substr($request_inputs->get('state'), 0, 25)),
            'zip' => preg_replace('/[^\w\s]/', '', substr($request_inputs->get('zipcode'), 0, 10)),
            'country' => preg_replace('/[^\w\s]/', '', substr($request_inputs->get('country'), 0, 25)),
            'email' => $client->email,
        ];

        $gw->setBilling($billingData);
        $gw->setShipping($shippingData);
    }

    private function setOrder($gw, $invoice)
    {
        $orderDescription = '';
        $gw->setOrder(
            $invoice->invoice_key, /** Provide a proper order ID*/
            $orderDescription,
            0, /** Provide proper tax */
            0, /** Provide proper shipping */
            '', /** Provide a proper PO number */
            request()->ip() // Replace with the actual IP address
        );
    }

    private function processPaymentRequest($gw, $request, $year, $invoice, $is_amazon, $payment_url, $payment_amount = 0)
    {
        return $gw->doSale($payment_amount, $request->input('card_number'), sprintf('%02d', $request->input('card_exp_month')) . $year, $request->input('card_cvv'), $is_amazon, $payment_url);
    }
}
