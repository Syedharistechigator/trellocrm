<?php

namespace App\Http\Controllers;

use App\Models\AssignBrand;
use App\Models\Brand;
use App\Models\CcInfo;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentErrorCods;
use App\Models\PaymentMethod;
use App\Models\PaymentTransactionsLog;
use App\Models\SplitPayment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

/** Authorize Packages */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Carbon\Carbon;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $key = 0x270F;

    public function decryptV1($encryptedData)
    {
        return (int)str_replace(['DM', 'CARD'], "", base64_decode($encryptedData . str_repeat('=', strlen($encryptedData) % 4)));
    }
    public function encrypt_2($board_list_card_id)
    {
        return rtrim(base64_encode("DM" . $board_list_card_id . "CARD"), '=');
    }
    public function encryptId($data)
    {
        $encrypted = '';
        for ($i = 0; $i < strlen($data); $i++) {
            $encrypted .= chr(ord($data[$i]) ^ $this->key);
        }
        return rtrim(base64_encode($encrypted), '=');
    }

    public function decryptId($encryptedData)
    {
        $decoded = base64_decode($encryptedData . str_repeat('=', strlen($encryptedData) % 4));
        $decrypted = '';
        for ($i = 0; $i < strlen($decoded); $i++) {
            $decrypted .= chr(ord($decoded[$i]) ^ $this->key);
        }
        return $decrypted;
    }

    public function encrypt(Request $request, $id)
    {
        return response()->json($this->encryptId($id));
    }

    public function decrypt(Request $request, $id)
    {
        return response()->json($this->decryptId($id));
    }

    public function getIpResponse()
    {
        $tokens = [
            '478789134a7b9f',
            'c4d5bd23f6904c',
            '12b59c8b5bf82e',
            'f19afe426ebfa7',
            '590a01c8690db0',
            '0aaa18feea61f7',
            'f37b2121d2944b',
            'ff661bbe09498d',
        ];
        shuffle($tokens);

        foreach ($tokens as $token) {
            $curl = curl_init();
            if ($_SERVER['REMOTE_ADDR'] == '::1' || $_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
                curl_setopt($curl, CURLOPT_URL, "https://ipinfo.io/" . $_SERVER['REMOTE_ADDR'] . "/json?token=" . $token);
            } else {
                curl_setopt($curl, CURLOPT_URL, "https://ipinfo.io/" . $_SERVER['REMOTE_ADDR'] . "/json?token=" . $token);
            }
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $ipResponse = curl_exec($curl);
            $httpStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $ipResponse = (array)json_decode($ipResponse);
            if (!empty($ipResponse) && $httpStatusCode != 429) {
                $ipResponse['valid_token'] = $token;
                break;
            }

            if ($ipResponse && isset($ipResponse['status']) && $ipResponse['status'] == 429) {
                $ipResponse['expire_tokens'] = $token;
            } else {
                Log::error('Token ' . $token . ' failed with status code ' . $httpStatusCode);
            }
        }

        if ($ipResponse && isset($ipResponse['status']) && $ipResponse['status'] == 429) {
            Log::error('All Tokens Expired ');

            $ipResponse['ip'] = "All Tokens Expired";
            $ipResponse['city'] = "All Tokens Expired";
            $ipResponse['state'] = "All Tokens Expired";
            $ipResponse['country'] = "All Tokens Expired";
            $ipResponse['postal'] = "All Tokens Expired";
            $ipResponse['all_token_expire'] = true;
        }
        $ipResponse['all_token_expire'] = $ipResponse['all_token_expire'] ?? false;
        $ipResponse['ip'] = $ipResponse['ip'] ?? null;
        $ipResponse['city'] = $ipResponse['city'] ?? null;
        $ipResponse['state'] = $ipResponse['region'] ?? $ipResponse['state'] ?? null;
        $ipResponse['country'] = $ipResponse['country'] ?? null;
        $ipResponse['postal'] = $ipResponse['postal'] ?? null;
        $ipResponse['valid_token'] = $ipResponse['valid_token'] ?? null;
        $ipResponse['expire_tokens'] = $ipResponse['expire_tokens'] ?? [];
        return $ipResponse;
    }

    public function pay_now_split_payments_global($id)
    {
        $pkey = Config::get('app.privateKey');
        $ipResponse = $this->getIpResponse();

        $splitPayment = SplitPayment::where('id', $id)->first();
        if (!$splitPayment) {
            return response()->json(['error' => 'Oops! Payment not found.'], 400);
        }
        $invoiceData = Invoice::where('invoice_key', $splitPayment->invoice_id)->first();
        if (!$invoiceData) {
            return response()->json(['error' => 'Oops! Previous invoice not found.'], 422);
        }
        if ($invoiceData->status != 'paid') {
            return response()->json(['error' => 'Oops! Previous invoice status is ' . $invoiceData->status . "."], 422);
        }
        if ($splitPayment->status == 1) {
            return response()->json(['error' => 'Oops! Payment was already paid.'], 422);
        }
        if (isset($splitPayment->getInvoice) && $splitPayment->getInvoice->id) {


            $clientData = Client::where('id', $invoiceData->clientid)->first();
            $brandData = Brand::where('brand_key', $invoiceData->brand_key)->first();
            $paymentMethod = PaymentMethod::where(['status' => 1, 'id' => $brandData->merchant_id])->first();
            $clientCardData = CcInfo::where('client_id', $invoiceData->clientid)->first();

            if (!$clientCardData) {
                return response()->json(['invoice id' => $splitPayment->invoice_id, 'split id' => $id, 'client id' => $invoiceData->clientid, 'error' => 'Oops! Client card details not found.'], 422);
            }

            $sasDeclineReasonCodeDB = PaymentErrorCods::all();
            $sasDeclineReasonCode = array();
            foreach ($sasDeclineReasonCodeDB as $errorReason) {
                $code = $errorReason->error_code;
                $reason = $errorReason->error_reason;
                $sasDeclineReasonCode[$code] = $reason;
            }

            $CardNumber = cxmDecrypt($clientCardData->card_number, $pkey);
            $cardCvv = cxmDecrypt($clientCardData->card_cvv, $pkey);
            $cardExpMonth = $clientCardData->card_exp_month;
            $cardExpYear = $clientCardData->card_exp_year;

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
            $cardNumber = preg_replace('/\s+/', '', $CardNumber);

            $creditCard = new AnetAPI\CreditCardType();
            $creditCard->setCardNumber($cardNumber);
            $creditCard->setExpirationDate($cardExpYear . "-" . $cardExpMonth);
            $creditCard->setCardCode($cardCvv);

            // Add the payment data to a paymentType object
            $paymentType = new AnetAPI\PaymentType();
            $paymentType->setCreditCard($creditCard);

            $order = new AnetAPI\OrderType();
            $order->setDescription($brandData->name);

            // Set the customer's Bill To address
            $customerAddress = new AnetAPI\CustomerAddressType();
            $customerAddress->setFirstName($clientData->name);
            $customerAddress->setLastName($clientCardData->card_name);
            /**Need to add in client table
             * $customerAddress->setCity($city);
             * $customerAddress->setState($state);
             * $customerAddress->setCountry($country);
             */
            // Set the customer's identifying information
            $customerData = new AnetAPI\CustomerDataType();
            $customerData->setType("individual");
            $customerData->setEmail($clientData->email);

            // Create a transaction
            $transactionRequestType = new AnetAPI\TransactionRequestType();
            $transactionRequestType->setTransactionType("authCaptureTransaction");
            $transactionRequestType->setAmount($splitPayment->amount);
            $transactionRequestType->setOrder($order);
            $transactionRequestType->setPayment($paymentType);
            $transactionRequestType->setCustomer($customerData);

            $transactionRequest = new AnetAPI\CreateTransactionRequest();
            $transactionRequest->setMerchantAuthentication($merchantAuthentication);
            $transactionRequest->setRefId($refID);
            $transactionRequest->setTransactionRequest($transactionRequestType);
            $controller = new AnetController\CreateTransactionController($transactionRequest);

            if ($paymentMethod->mode == 0) {
                $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
            } else {
                $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
            }
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

                        SplitPayment::where('id', $splitPayment->id)->update(['status' => 1, 'transaction_id' => $transaction_id]);

                        Payment::create([
                            'team_key' => $invoiceData->team_key,
                            'brand_key' => $invoiceData->brand_key,
                            'creatorid' => $invoiceData->creatorid,
                            'agent_id' => $invoiceData->agent_id,
                            'clientid' => $invoiceData->clientid,
                            'invoice_id' => $invoiceData->invoice_key,
                            'project_id' => $invoiceData->project_id,
                            'name' => $clientData->name,
                            'email' => $clientData->email,
                            'phone' => $clientData->phone,
                            'address' => '',
                            'amount' => $splitPayment->amount,
                            'payment_status' => '1',
                            'authorizenet_transaction_id' => $transaction_id,
                            'payment_response' => json_encode($response),
                            'transaction_response' => json_encode($tresponse),
                            'payment_gateway' => $invoiceData->payment_gateway,
                            'auth_id' => $tresponse->getAuthCode(),
                            'response_code' => $payment_response,
                            'message_code' => $tresponse->getMessages()[0]->getCode(),
                            'payment_notes' => 'Paid with one click',
                            'sales_type' => $invoiceData->sales_type,
                            'merchant_id' => $paymentMethod->id,
                            'card_type' => $clientCardData->card_type,
                            'card_name' => $clientCardData->card_name,
                            'card_number' => substr($CardNumber, -4),
                            'card_exp_month' => $cardExpMonth,
                            'card_exp_year' => $cardExpYear,
                            'card_cvv' => $cardCvv,
                            'ip' => request()->ip(),
                            'city' => $ipResponse['city'],
                            'state' => $ipResponse['state'],
                            'country' => $ipResponse['country'],
                        ]);

                        $invoiceData->received_amount += $splitPayment->amount;
                        $invoiceData->save();

                        PaymentTransactionsLog::create([
                            'team_key' => $invoiceData->team_key,
                            'brand_key' => $invoiceData->brand_key,
                            'clientid' => $invoiceData->clientid,
                            'invoiceid' => $invoiceData->invoice_key,
                            'projectid' => $invoiceData->project_id,
                            'amount' => $splitPayment->amount,
                            'response_code' => $tresponse->getResponseCode(),
                            'message_code' => $tresponse->getMessages()[0]->getCode(),
                            'response_reason' => $statusMsg
                        ]);
                        $emailOptions = array(
                            'to' => $clientData->email,
                            'clientName' => $clientData->name,
                            'subject' => 'Payment Confirmation',
                            'description' => 'Sorry for any inconvenience',
                            'amount' => $splitPayment->amount,
                            'paidInvoiceId' => $invoiceData->invoice_key,
                            'brandKey' => $invoiceData->brand_key,
                        );
                        //sendEmail($emailOptions);
                        $status = 'success';
                        $statusCode = 200;
                    } else {
                        if ($tresponse->getErrors() != null) {
                            $errorCode = $tresponse->getErrors()[0]->getErrorCode();
                            $statusMsg = $tresponse->getErrors()[0]->getErrorText();
                            $payment_response = $tresponse->getErrors()[0]->getErrorCode();

                            if (array_key_exists($errorCode, $sasDeclineReasonCode)) {
                                $statusMsg .= $sasDeclineReasonCode[$errorCode];
                            }

                            $trans_log = PaymentTransactionsLog::create([
                                'team_key' => $invoiceData->team_key,
                                'brand_key' => $invoiceData->brand_key,
                                'clientid' => $invoiceData->clientid,
                                'invoiceid' => $invoiceData->invoice_key,
                                'projectid' => $invoiceData->project_id,
                                'amount' => $splitPayment->amount,
                                'response_code' => $tresponse->getResponseCode(),
                                'message_code' => $errorCode,
                                'response_reason' => $statusMsg
                            ]);
                        }

                        $status = 'error';
                        $statusCode = 500;
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
                    PaymentTransactionsLog::create([
                        'team_key' => $invoiceData->team_key,
                        'brand_key' => $invoiceData->brand_key,
                        'clientid' => $invoiceData->clientid,
                        'invoiceid' => $invoiceData->invoice_key,
                        'projectid' => $invoiceData->project_id,
                        'amount' => $splitPayment->amount,
                        'response_code' => $tresponse->getResponseCode(),
                        'message_code' => $errorCode,
                        'response_reason' => $statusMsg
                    ]);
                    $status = 'error';
                    $statusCode = 500;
                }
            } else {
                $statusMsg = "No response returned";
                $status = 'error';
                $statusCode = 500;
            }

            /** Response */
            return response()->json([
                'InvoiceId' => $invoiceData->invoice_key,
                'SplitId' => $id,
                'resultCode' => $response->getMessages()->getResultCode(),
                'status' => $status,
                'code' => $payment_response,
                'message' => $statusMsg,
                'resp' => $response,
                't_resp' => $tresponse
            ], $statusCode);

        }

        return response()->json(['error' => 'Oops! Client not found.'], 422);
    }

    public function getData(Request $request, $model, $empty_check = true)
    {
        $dateRange = $request->input('dateRange', now()->format('Y-m-d') . ' - ' . now()->addDay()->format('Y-m-d'));
        $dateParts = explode(' - ', $dateRange);
        $fromDate = $dateParts[0] ?? now()->format('Y-m-d');
        $toDate = $dateParts[1] ?? now()->addDay()->format('Y-m-d');
        $teamKey = $request->has('teamKey') && $request->input('teamKey') !== 'undefined' ? $request->input('teamKey', 0) : null;
        $brandKey = $request->has('brandKey') && $request->input('brandKey') !== 'undefined' ? $request->input('brandKey', 0) : null;
        $model = $model->orderBy('created_at', 'desc');

        $query = clone $model;
        $query2 = clone $model;
        if ($fromDate && $toDate && ($fromDate != $toDate)) {
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        } else {
            $query->whereDate('created_at', today());
        }
        if ($teamKey > 0 && !$brandKey) {
            $query->where('team_key', $teamKey);
        } elseif (!$teamKey && $brandKey > 0) {
            $query->where('brand_key', $brandKey);
        } elseif ($teamKey > 0 && $brandKey > 0) {
            $query->where('team_key', $teamKey)->where('brand_key', $brandKey);
        }
        $data = $query->get();

        if ($data->isEmpty() && $empty_check) {
            $latestDateRecord = $model
                ->when($teamKey > 0, function ($query) use ($teamKey) {
                    return $query->where('team_key', $teamKey);
                })
                ->when($brandKey > 0, function ($query) use ($brandKey) {
                    return $query->where('brand_key', $brandKey);
                })
                ->whereDate('created_at', '<', today())
                ->orderBy('created_at', 'desc')
                ->first();
            if ($latestDateRecord) {
                $fromDate = $toDate = $latestDateRecord->created_at->format('Y-m-d');
                $data = $query2
                    ->when($teamKey > 0, function ($query) use ($teamKey) {
                        return $query->where('team_key', $teamKey);
                    })
                    ->when($brandKey > 0, function ($query) use ($brandKey) {
                        return $query->where('brand_key', $brandKey);
                    })
                    ->whereDate('created_at', $latestDateRecord->created_at->format('Y-m-d'))
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }
        return [
            'data' => $data,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'teamKey' => $teamKey,
            'brandKey' => $brandKey,
        ];
    }

    public function userDataFilter(Request $request, $model, $assign_brands)
    {
        if (auth()->user()->type === 'ppc') {
            $assign_brands = AssignBrand::whereIn('team_key', auth()->user()->assigned_teams ?? [])->whereHas('getBrandWithOutTrashed')->get();
        }
        $dateRange = $request->input('dateRange', now()->subDays(15)->format('Y-m-d') . ' - ' . now()->addDay()->format('Y-m-d'));
        $dateParts = explode(' - ', $dateRange);
        $fromDate = $dateParts[0] ?? now()->format('Y-m-d');
        $toDate = $dateParts[1] ?? now()->addDay()->format('Y-m-d');

        $brandKey = $request->input('brandKey', 0);
        $query = $model->orderBy('created_at', 'desc');

        if ($fromDate && $toDate && ($fromDate != $toDate)) {
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        } else {
            $query->whereDate('created_at', today());
        }
        $brand_keys = $assign_brands->pluck('brand_key')->toArray();
        if ($brandKey > 0) {
            if (in_array($request->get('brandKey'), $brand_keys)) {
                $query->where('brand_key', $brandKey);
            }
        } else {
//            if (auth()->user()->type != 'ppc') {
            $query->whereIn('brand_key', $brand_keys);
//            }
        }

        $data = $query->get();
//        if ($data->isEmpty()) {
//            $latestDateRecordQuery = $model->whereDate('created_at', '<', today())->orderBy('created_at', 'desc');
//            if (Auth::user()->type !== 'ppc' && count($brand_keys) > 0) {
//                $latestDateRecordQuery->whereIn('brand_key', $brand_keys);
//            }
//            $latestDateRecord = $latestDateRecordQuery->first();
//            if ($latestDateRecord) {
//                $data = $model->whereDate('created_at', $latestDateRecord->created_at->format('Y-m-d'));
//                if (Auth::user()->type !== 'ppc' && count($brand_keys) > 0) {
//                    $data->whereIn('brand_key', $brand_keys);
//                }
//                $data = $data->orderBy('id', 'desc')->take(100)->get();
//            }
//        }
//
//        $fromDate = $data->min('created_at')->format('Y-m-d');
//        $toDate = $data->max('created_at')->format('Y-m-d');
        return [
            'data' => $data,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'brandKey' => $brandKey,
        ];
    }

}
