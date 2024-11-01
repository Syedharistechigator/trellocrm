<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CcInfo;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\MultiPaymentResponse;
use App\Models\Payment;
use App\Models\PaymentMethodPayArc;
use App\Models\PaymentTransactionsLog;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/** Package */

use GuzzleHttp\Client as GuzzleClient;

/** Package Exception */

use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use Config;
use Illuminate\Support\Str;

class ApiPayarcPaymentController extends Controller
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
                return response()->json(['errors' => 'Oops! Invoice not found.'], 404);
            }
            /** Fetching invoice to get invoice details */
            $client = Client::where('id', $invoice->clientid)->first();
            /** Returning error if invocie not found */
            if (!$client) {
                return response()->json(['errors' => 'Oops! Client not found.'], 404);
            }
            /** Now confirming if payment was already done */
            $invoice_payment = Payment::where('invoice_id', $invoice->invoice_key)->where('amount', $invoice->total_amount)->first();
            if ($invoice->status == "paid" && $invoice_payment) {
                return response()->json(['errors' => 'Oops! Payment was already paid.'], 404);
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
            $cc_info->payment_gateway = 3; /** PayArc */
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

            Log::driver('s_info')->debug('PayArc    => details = ' . json_encode($cc_info));

            /** Initializing Client Library for curl */
            $GuzzleClient = new GuzzleClient();
            /** Initializing url for curl */
            if ($_SERVER['SERVER_NAME'] === 'development.tgcrm.net' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
                $url = 'https://testapi.payarc.net/v1/charges';
                /** Here you can add your default test payarc_id */
                $defaultPaymentMethodPayarcId = 1;
                $defaultTransactionKey = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI0NTA1IiwianRpIjoiNmUzMTlkMWJiY2U5MmViYTljOTk4NzExYzRmZWRhMTJlODM4NTczZWI5NGJmMGNlM2E3YzhhMjA4M2M3MzkyMTAyNmI0MTQxNmVjYzhmYzAiLCJpYXQiOjE2OTg4NjU3MDMsIm5iZiI6MTY5ODg2NTcwMywiZXhwIjoxODU2NTQ1NzAzLCJzdWIiOiI4NTI2MTY5Iiwic2NvcGVzIjoiKiJ9.uOiB9VZOSagqhEQTcOlvkr34A3voO9NQ1uojeGofw9CXHMMl7_vtd-dW6F5wUMpwZ0CEnt7zVxG09fT5B45OyLE4UouLOFs9FvVO1vkVQiMpfoZbr8SW8voWZGB__yLVgrm0CotV8bypYrDlxwQF58axEQSv3AfrbCBX-RoU745t7CghMy9yYjf4lIRVO6D6SdAMf-TQ0AM6hmVIGx81kuDS0vj9gbJqcTxHZDZv7-JxiPbY2QeBBEZ__BnO4d3cvRDBUrjGmMkZGwPXRHXiKcCj95rsrqaP8HxSXltMSbMj0TbkRHWk_aTZh7Yu-0DsHyrtHxrt15oPxA4pPC0qxBuEM3rgwUJZzMa8R46PXuPzS-gJ0n77JLcAH9EGR6Pe9bTk3ikqwJ30dmfYa0UDv-hbs0XIp3gokJ0A-j_Iv230F7RLU5R8ltwhFx2jHGpWB8vK1AI6qiszuLgEXyLgFCfIchATDGdJGYu75N0C2membVxmAUulyd8YepJThEEdSCt45cNrneQYDVQ7-6uTwuh8Zk6j37xTXyfJzM1N2YWfd59J46cYoG-Bk5E5LoXqYszM2JeoTn7CUKYKYWdoLYoq_-oPlcrsiHOQVwgN606XYvBt4gGLqhp9nlBzlzLGgc71VZe6p7xekgJ5TdZrtMpncpB1H-WtOcOGbMBUhQk';
            } else {
                $url = 'https://api.payarc.net/v1/charges';
                /** Here you can add your default live payarc_id */
                $defaultPaymentMethodPayarcId = 1;
                $defaultTransactionKey = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxMzIwNCIsImp0aSI6IjcwZmFlYTBmOTkwMjFmZGZjMWQyMmRlMzQwZTY3ZGIyNDNiYWJiZTYyMWViZTllMjhkYzU1N2YzYmM0MWZhNjE3OWE5YmRhYWZmZTExM2JmIiwiaWF0IjoxNjk3NTc1MTMxLCJuYmYiOjE2OTc1NzUxMzEsImV4cCI6MTg1NTI1NTEzMSwic3ViIjoiMjQ0MjIiLCJzY29wZXMiOiIqIn0.GUK913_LSI9A7Q6n3maoevS2rRrBOozanJo-2WrRFP3DlAyPTyYsLWK_Ii477SsuAZKIkeE7WCW4iN_vJIOh7IptZOqgU082y9tcmDLlngtpkXUsaJLjk0GRRuW2b4ialdsRpDAwPtn58HuAYp3DtAjoNuqWb1Gps1-dHrv65q3IGPfpbzl0oBaN3m_LK5XDvZlc5RsWv4iCeB-2-hu6i6EyOsrfvA52ge099nBijciPqhVDe4jtCHRarTW6m_i7DOv8NL0FsNxTH0AnYIoujcFQ0fwtqNr3ZGaUFkqKuGrt_maqV2IR7qSnVMPJhnWKYvuwuTt5xHo-VSlyuJQXCTVwRLlqoktEmlIz_7CFdAmjURfoq55nraQIyrm4UBGOsqnbF3M3zlBnJluguvLVc-kNUIPNSUG4b2xpqYafGGQNKpZBR762MAAJmdHDjwYJt_QBs3XwRv43TAio3AYz4nGoCvDNlcwaYEq74CZ3t-uzIa6XiDXWpQ89MP7CKXleP4Rrvt9kAoRObZ_IU-O8hD_b04lQMa_cjxTirq6WpTjcqqTFVzHfVCeJPOyYYFmMMa0Z8viOZM0EPT4O8B-MBc1yFsfD080maRZuoAWor8ZzbmGSczMtOEw-T8jlXtqCh8CRNWwwuR_9FkRFVI64LVHGXy29JKVf1M9RS0l2yw0';
            }
            /** Fetching the payment merchant using the brand key, which we have already obtained in the previous step */
            $paymentMethodPayarc = PaymentMethodPayArc::where(['status' => 1, 'id' => $brand->payarc_id])->first();
            if ($paymentMethodPayarc && $paymentMethodPayarc->mode == 0) {
                $paymentMethodPayarcId = $paymentMethodPayarc->id;
                $transaction_key = $paymentMethodPayarc->live_transaction_key;
            } elseif ($paymentMethodPayarc && $paymentMethodPayarc->mode == 1) {
                $url = 'https://testapi.payarc.net/v1/charges';
                $paymentMethodPayarcId = $paymentMethodPayarc->id;
                $transaction_key = $paymentMethodPayarc->test_transaction_key;
            } else {
                /** If payment merchant not found we will use alternate way to get payment */
                $paymentMethodPayarcId = $defaultPaymentMethodPayarcId;
                $transaction_key = $defaultTransactionKey;
            }

            /** Set variable for payment amount*/
            $payment_amount = $invoice['total_amount'] != 0 ? $invoice['total_amount'] : $invoice['final_amount'];

            /** Initializing header for curl & token will be dynamic form PaymentMethodPayArc*/
            $headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => "Bearer {$transaction_key}",
            ];
            $data = [
                'amount' => $payment_amount * 100,
                'currency' => 'usd',
                'card_holder_name' => substr(preg_replace('/[^A-Za-z\s]/', '', $request->card_name), 0, 30),
                'email' => $client->email,
                'phone_number' => preg_replace('/\D/', '', substr($client->phone, -11)),
                'card_number' => $request->card_number,
                'cvv' => $request->card_cvv,
                'exp_month' => sprintf("%02d", $request->card_exp_month),
                'exp_year' => $request->card_exp_year,
                'statement_description' => Str::limit($brand->name, $limit = 22, $end = '...'),

                /** Add fields to send payarc if needed*/
                /** (customer id / token id / card details) only one */
//            'customer_id' => '4DPNMVjxxKpxVnjA',
//            'token_id' => 'YmNlwYYlELLEEwEq',

                /**Address*/
                'address_line1' => preg_replace('/[^\w\s]/', '', substr($request->get('address'), 0, 200)) . " " . preg_replace('/[^\w\s]+|\s+/', '', substr($request->get('country'), 0, 50)),
//            'address_line2' => '2nd Floor',
                'zip' => preg_replace('/[^\w\s]/', '', substr($request->get('zipcode'), 0, 10)),
                'city' => preg_replace('/[^\w\s]/', '', substr($request->get('city'), 0, 50)),
                'state' => preg_replace('/[^\w\s]/', '', substr($request->get('state'), 0, 50)),
                'country' => preg_replace('/[^\w\s]+|\s+/', '', substr($request->get('country'), 0, 50)),


//            'capture' => 1,
//            'card_level' => 'LEVEL2',
//            'sales_tax' => 1,
//            'terminal_id' => '',/**FROM DASHBOARD*/
//            'tip_amount' => 1,
//            'purchase_order' => 'PO123',
//            'order_date' => '2020-01-31',
//            'customer_ref_id' => 'CRID123',
//            'ship_to_zip' => '123',
//            'amex_descriptor' => 'AD123',
//            'supplier_reference_number' => 'SRN12',
//            'tax_amount' => 1,
//            'tax_category' => 'SERVICE',
//            'customer_vat_number' => 'CVN123',
//            'summary_commodity_code' => 'SCC1',
//            'shipping_charges' => 999,
//            'duty_charges' => 999,
//            'ship_from_zip' => '123',
//            'destination_country_code' => 'DCC',
//            'tax_type' => 'VAT',
//            'vat_invoice' => 'VI12',
//            'tax_rate' => 3,
//            'surcharge' => 3,
//            'avs_parameters' => 1,
//            'bin_location_parameters' => 1,
//            'bin_card_type_parameters' => 1,
//            'metadata' => '{"FullCustomerName" : " John Smith", "CustomerID" : "APDKVpN4AxpDVNxn"}',
//            'do_not_send_email_to_customer' => 'yes',
//            'do_not_send_sms_to_customer' => 'yes',
            ];

            $options = [
                'headers' => $headers,
                'form_params' => $data,
            ];

            try {
                $response = $GuzzleClient->post($url, $options);
                $responseData = json_decode($response->getBody()->getContents(), true)['data'] ?? "";
                $statusCode = $response->getStatusCode();
                if ($statusCode == 201 && isset($responseData) && isset($responseData['tsys_response_code']) && $responseData['tsys_response_code'] == 'A0000') {
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
                        'amount' => $responseData['amount'] / 100,
                        'payment_status' => '1',
                        'authorizenet_transaction_id' => $responseData['id'],
                        'payment_gateway' => 'Payarc',
                        'auth_id' => $responseData['auth_code'],
                        'payment_response' => json_encode($responseData),
                        'response_code' => $responseData['host_response_code'],
                        'message_code' => $responseData['host_response_code'],
                        'payment_notes' => $invoice->invoice_descriptione,
                        'sales_type' => $invoice->sales_type,
                        'merchant_id' => $paymentMethodPayarc->id,
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

                    $invoice->update(['status' => 'paid', 'received_amount' => $payment_amount]);

                    /** Card enabled on successful*/
                    $cc_info->status = 1;
                    $cc_info->save();
                }
                $logData = [
                    'team_key' => $invoice->team_key,
                    'brand_key' => $invoice->brand_key,
                    'clientid' => $invoice->clientid,
                    'invoiceid' => $invoice->invoice_key,
                    'merchant_id' => $paymentMethodPayarcId,
                    'projectid' => $invoice->project_id,
                    'amount' => $payment_amount,
                    'response_code' => $responseData['host_response_code'],
                    'message_code' => $responseData['host_response_code'],
                    'response_reason' => 'This transaction has been approved.',
                    'payment_gateway' => 3, /** 3 = Payarc */
                    'address' => $request->get('address'),
                    'zipcode' => $request->get('zipcode'),
                    'city' => $request->get('city'),
                    'state' => $request->get('state'),
                    'country' => $request->get('country'),
                ];
                PaymentTransactionsLog::create($logData);

                if (Str::contains($request->url(), 'payarc-process-payment')) {
                    $inputs = $request->input();
                    $pkey = Config::get('app.privateKey');
                    $card_number_enc = cxmEncrypt($request->card_number, $pkey);
                    $cvv_enc = cxmEncrypt($request->card_cvv, $pkey);
                    $inputs['payment_gateway'] = 'payarc';
                    $inputs['merchant_id'] = $paymentMethodPayarc->id;
                    $inputs['merchant_name'] = $paymentMethodPayarc->merchant;
                    $inputs['ip'] = request()->ip() ?? 'unknown';
                    $form_inputs = $inputs;
                    $form_inputs['card_number'] = $card_number_enc;
                    $form_inputs['card_cvv'] = $cvv_enc;
                    $payment_process_from['payarc'][$paymentMethodPayarc->merchant] = [$responseData];

                    $payment_process_from = [
                        'payarc' => [
                            str_replace(' ', '_', $paymentMethodPayarc->merchant) => [
                                'response' => $responseData,
                                'InvoiceId' => $invoice->invoice_key,
                                'PaymentAmount' => $payment_amount,
                                'resultCode' => $responseData['tsys_response_code'],
                                'code' => $responseData['host_response_code'],
                                'message' => $responseData['host_response_message'],
                                'merchant_id' => $paymentMethodPayarc->id,
                                'mode' => $request->header('X-Source') ? $request->header('X-Source') : $request->url(),
                            ],
                        ],
                    ];
                    $multi_payment_response =  MultiPaymentResponse::create([
                        'invoice_id' => $inputs['invoice_id'],
                        'response' => json_encode($responseData ?? null),
                        'payment_gateway' => $inputs['payment_gateway'],
                        'payment_process_from' => json_encode($payment_process_from),
                        'response_status' => 200,
                        'form_inputs' => json_encode($form_inputs),
                        'controlling_code' => 'single',
                    ]);
                }
                return response()->json([
                    'response' => $responseData,
                    'InvoiceId' => $invoice->invoice_key,
                    'PaymentAmount' => $payment_amount,
                    'resultCode' => $responseData['tsys_response_code'],
                    'code' => $responseData['host_response_code'],
                    'message' => $responseData['host_response_message'],
                    'merchant_id' => $paymentMethodPayarc->id,
                    'mode' => $request->header('X-Source') ? $request->header('X-Source') : $request->url(),
                ], 200);

            } catch (GuzzleClientException $e) {
                $statusCode = $e->getResponse()->getStatusCode();
                $responseBody = $e->getResponse()->getBody()->getContents();
                $errorData = json_decode($responseBody, true);
                $reasonCodeMapping = [
                    '0' => 'CVV2 verification failed',
                    '00.1' => 'Invalid Card',
                    '00.2' => 'The given data was invalid.',
                    '00.3' => 'Unauthenticated.',
                    '10.1' => 'EMV Liability Shift Counterfeit Fraud',
                    '10.2' => 'EMV Liability Shift Non-Counterfeit Fraud',
                    '10.3' => 'Other Fraud – Card Present Environment',
                    '10.4' => 'Other Fraud – Card Absent Environment',
                    '10.5' => 'Visa Fraud Monitoring Program',
                    '11.1' => 'Card Recovery Bulletin or Exception File',
                    '11.2' => 'Declined Authorization',
                    '11.3' => 'No Authorization',
                    '12.1' => 'Late Presentment',
                    '12.2' => 'Incorrect Transaction Code',
                    '12.3' => 'Incorrect Currency',
                    '12.4' => 'Incorrect Transaction Account Number',
                    '12.5' => 'Incorrect Transaction Amount',
                    '12.6' => 'Duplicate Processing or Paid by Other Means',
                    '12.7' => 'Invalid Data',
                    '13.1' => 'Services Not Provided or Merchandise Not Received',
                    '13.2' => 'Cancelled Recurring Transaction',
                    '13.3' => 'Not as Described or Defective Merchandise/Services',
                    '13.4' => 'Counterfeit Merchandise',
                    '13.5' => 'Misrepresentation of the Purchased Good and/or Service',
                    '13.6' => 'Credit Not Processed',
                    '13.7' => 'Cancelled Merchandise/Services',
                    '13.8' => 'Original Credit Transaction Not Accepted',
                    '13.9' => 'Non-Receipt of Cash or Load Transaction Value at ATM',
                ];
                $message_code = 00.00;
                $errorMessage = "Invalid Card.";
                if (isset($errorData['message'])) {
                    $errorMessage = $errorData['message'];
                    $message_code = array_search($errorMessage, $reasonCodeMapping);
                }
                if (isset($errorData['errors'])) {
                    $firstErrorKey = array_key_first($errorData['errors']);
                    $errorMessage = $errorData['errors'][$firstErrorKey][0];
                }
                if (isset($errorData['error'])) {
                    $errorMessage = $errorData['error'];
                }
                $failedTransactionLog = [
                    'team_key' => $invoice->team_key,
                    'brand_key' => $invoice->brand_key,
                    'clientid' => $invoice->clientid,
                    'invoiceid' => $invoice->invoice_key,
                    'merchant_id' => $paymentMethodPayarcId,
                    'projectid' => $invoice->project_id,
                    'amount' => $payment_amount,
                    'response_code' => $statusCode,
                    'message_code' => $message_code,
                    'response_reason' => $errorMessage,
                    'payment_gateway' => 3, /** 3 = Payarc */
                    'address' => $request->get('address'),
                    'zipcode' => $request->get('zipcode'),
                    'city' => $request->get('city'),
                    'state' => $request->get('state'),
                    'country' => $request->get('country'),
                ];
                PaymentTransactionsLog::create($failedTransactionLog);

                return response()->json(['error' => $errorData, 'message' => $errorMessage], $statusCode);
            } catch (\Exception $e) {
                return response()->json(['status_code' => 500, 'message' => 'Internal server error.', 'error' => $e->getMessage()]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage(),
            ], 422);
        }
    }

}
