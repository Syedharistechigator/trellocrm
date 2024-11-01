<?php

namespace App\Http\Controllers;

use App\Models\MultiPaymentResponse;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Brand;
use App\Models\Client;
use App\Models\Project;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodExpigate;
use App\Models\PaymentMethodPayArc;
use App\Models\PaymentTransactionsLog;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Carbon\Carbon;
use App\Models\User;
use App\Models\PaymentErrorCods;
use Illuminate\Support\Facades\Hash;
use App\Models\CcInfo;
use Config;
use function PHPUnit\Framework\returnArgument;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return response()->json([
            'error' => "Temporary Disabled.!!"
        ], 422);
        $input = $request->input();
        $brandmerchant = Brand::where('brand_key', $input['brand_key'])->value('merchant_id');
        $paymentMethod = PaymentMethod::where(['status' => 1, 'id' => $brandmerchant])->first();
        if ($paymentMethod->mode == 0) {
            $loginId = $paymentMethod->live_login_id;
            $transcation_key = $paymentMethod->live_transaction_key;
        } else {
            $loginId = $paymentMethod->test_login_id;
            $transcation_key = $paymentMethod->test_transaction_key;
        }

        /* Create a merchantAuthenticationType object with authentication details
          retrieved from the constants file */
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($loginId);
        $merchantAuthentication->setTransactionKey($transcation_key);

        // Set the transaction's refId
        $refId = 'ref' . time();

        $cardNumber = preg_replace('/\s+/', '', $input['card_number']);

        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($cardNumber);
        $creditCard->setExpirationDate($input['card_exp_year'] . "-" . $input['card_exp_month']);
        $creditCard->setCardCode($input['card_cvv']);
        //$creditCard->setCurrencyCode('USD');
        //dd(get_class_methods($creditCard));


        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($input['amount']);
        $transactionRequestType->setPayment($paymentOne);
        //$transactionRequestType->setCurrencyCode('EUR');
        //dd(get_class_methods($transactionRequestType)); get all class method


        // Assemble the complete transaction request
        $requests = new AnetAPI\CreateTransactionRequest();
        $requests->setMerchantAuthentication($merchantAuthentication);
        $requests->setRefId($refId);
        $requests->setTransactionRequest($transactionRequestType);

        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($requests);
        if ($paymentMethod->mode == 0) {
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
        } else {
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        }


        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == "Ok") {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null) {

                    $statusMsg = $tresponse->getMessages()[0]->getDescription();

                    Payment::create([
                        'team_key' => $input['team_key'],
                        'brand_key' => $input['brand_key'],
                        'creatorid' => $input['creatorid'],
                        'agent_id' => $input['agentid'],
                        'clientid' => $input['clientid'],
                        'invoice_id' => $input['invoiceid'],
                        'project_id' => $input['projectid'],
                        'name' => trim($input['name']),
                        'email' => $input['email'],
                        'phone' => $input['phone'],
                        'address' => '',
                        'amount' => $input['amount'],
                        //'amount' => $input['total_amount'],
                        'payment_status' => '1',
                        'authorizenet_transaction_id' => $tresponse->getTransId(),
                        'payment_gateway' => $input['payment_gateway'],
                        'auth_id' => $tresponse->getAuthCode(),
                        'response_code' => $tresponse->getResponseCode(),
                        'message_code' => $tresponse->getMessages()[0]->getCode(),
                        'payment_notes' => $input['description'],
                        'sales_type' => $input['salestype'],
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

                    $trans_log = PaymentTransactionsLog::create([
                        'team_key' => $input['team_key'],
                        'brand_key' => $input['brand_key'],
                        'clientid' => $input['clientid'],
                        'invoiceid' => $input['invoiceid'],
                        'projectid' => $input['projectid'],
                        'amount' => $input['amount'],
                        'response_code' => $tresponse->getResponseCode(),
                        'message_code' => $tresponse->getMessages()[0]->getCode(),
                        'response_reason' => $statusMsg
                    ]);

                    $invoice = Invoice::where('invoice_key', $input['invoiceid'])->update(['status' => 'paid']);

                    $message_text = $tresponse->getMessages()[0]->getDescription() . ", Transaction ID: " . $tresponse->getTransId();
                    $msg_type = "success_msg";

                } else {
                    $message_text = 'There were some issue with the payment. Please try again later.';
                    $msg_type = "error_msg";

                    if ($tresponse->getErrors() != null) {
                        $message_text = $tresponse->getErrors()[0]->getErrorText();
                        $msg_type = "error_msg";
                    }
                }
                // Or, print errors if the API request wasn't successful
            } else {
                $message_text = 'There were some issue with the payment. Please try again later.';
                $msg_type = "error_msg";

                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getErrors() != null) {
                    $message_text = $tresponse->getErrors()[0]->getErrorText();
                    $msg_type = "error_msg";
                } else {
                    $message_text = $response->getMessages()->getMessage()[0]->getText();
                    $msg_type = "error_msg";
                }
            }
        } else {
            $message_text = "No response returned";
            $msg_type = "error_msg";
        }

        //return back()->with($msg_type, $message_text);

        return response()->json([
            'type' => $msg_type,
            'message' => $message_text
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Payment $payment
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $ip = request()->ip();
        //$ip = '202.47.39.179'; //For static IP address get
        $data = \Location::get($ip);


        $invoiceData = Invoice::where('invoice_key', $id)->first();

        $brandData = Brand::where('brand_key', $invoiceData->brand_key)->first();

        $clientData = Client::where('id', $invoiceData->clientid)->first();

        $merchant = PaymentMethod::where(['status' => 1, 'id' => $brandData->merchant_id])->value('merchant');

        $invoiceData['brandName'] = $brandData->name;
        $invoiceData['brandlogo'] = $brandData->logo;
        $invoiceData['brandurl'] = $brandData->brand_url;
        $invoiceData['fav'] = $brandData->fav;
        $invoiceData['clientname'] = $clientData->name;
        $invoiceData['clientemail'] = $clientData->email;
        $invoiceData['merchant'] = $merchant;
        $invoiceData['clientphone'] = $clientData->phone;
        $invoiceData['clientip'] = $ip;
        $invoiceData['cityName'] = $data->cityName ?? "not found";
        $invoiceData['stateName'] = $data->regionName ?? "not found";
        $invoiceData['countryName'] = $data->countryName ?? "not found";
        $invoiceData['zipCode'] = $data->zipCode ?? "not found";

        $cur_symbol = $invoiceData->cur_symbol;
        if ($cur_symbol == 'EUR') {
            $currency_symbol = '€';
        } elseif ($cur_symbol == 'GBP') {
            $currency_symbol = '£';
        } elseif ($cur_symbol == 'AUD') {
            $currency_symbol = 'A$';
        } elseif ($cur_symbol == 'CAD') {
            $currency_symbol = 'C$';
        } else {
            $currency_symbol = '$';
        }
        $invoiceData['currency_symbol'] = $currency_symbol;

        return view('payment.index', compact('invoiceData'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Payment $payment
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Payment $payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Payment $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        //
    }

    public function show_api($id)
    {
        try {

            $ip = request()->ip();
            //$ip = '202.47.39.179'; //For static IP address get
            $data = \Location::get($ip);
            $invoiceData = Invoice::where('invoice_key', $id)->first();
            if (!$invoiceData) {
                return response()->json(['error' => 'Error! Invoice not found.'], 404);
            }
            $brandData = Brand::where('brand_key', $invoiceData->brand_key)->first();
            $clientData = Client::where('id', $invoiceData->clientid)->first();
            $merchant = PaymentMethod::where(['status' => 1, 'id' => $brandData->merchant_id])->first();
            $authorize_active = PaymentMethod::where('status', 1)->first();

            $merchant_payarc = PaymentMethodPayArc::where(['status' => 1, 'id' => $brandData->payarc_id])->first();
            $merchant_expigate = PaymentMethodExpigate::where(['status' => 1, 'id' => $brandData->expigate_id])->first();
            $expigate_active = PaymentMethodExpigate::where('status', 1)->first();

            $payment = Payment::where('invoice_id', $id)->first();
            $invoiceData['test'] = 0;
            if (isset($payment) && $payment->authorizenet_transaction_id == 0) {
                $invoiceData['test'] = 1;
            }

            $invoiceData['brandName'] = $brandData->name;
            $invoiceData['is_amazon'] = $brandData->is_amazon;
            $invoiceData['default_merchant_id'] = $brandData->default_merchant_id;
            $invoiceData['brandlogo'] = $brandData->logo;
            $invoiceData['brandurl'] = $brandData->brand_url;
            $invoiceData['isExpigate'] = $brandData->is_expigate;
            $invoiceData['smtp_host'] = $brandData->smtp_host;
            $invoiceData['smtp_email'] = $brandData->smtp_email;
            $invoiceData['smtp_password'] = $brandData->smtp_password;
            $invoiceData['smtp_port'] = $brandData->smtp_port;
            $invoiceData['fav'] = $brandData->fav;
            $invoiceData['clientname'] = $clientData->name;
            $invoiceData['clientemail'] = $clientData->email;
            $invoiceData['merchant'] = $merchant;
            $invoiceData['authorize_active'] = $authorize_active;
            $invoiceData['merchant_payarc'] = $merchant_payarc;
            $invoiceData['merchant_expigate'] = $merchant_expigate;
            $invoiceData['expigate_active'] = $expigate_active;
            $invoiceData['clientphone'] = $clientData->phone;
            $invoiceData['clientip'] = $ip;
            $invoiceData['cityName'] = $data->cityName ?? "";
            $invoiceData['stateName'] = $data->regionName ?? "";
            $invoiceData['countryName'] = $data->countryName ?? "";
            $invoiceData['zipCode'] = $data->zipCode ?? "";

            $cur_symbol = $invoiceData->cur_symbol;
            if ($cur_symbol == 'EUR') {
                $currency_symbol = '€';
            } elseif ($cur_symbol == 'GBP') {
                $currency_symbol = '£';
            } elseif ($cur_symbol == 'AUD') {
                $currency_symbol = 'A$';
            } elseif ($cur_symbol == 'CAD') {
                $currency_symbol = 'C$';
            } else {
                $currency_symbol = '$';
            }
            $invoiceData['currency_symbol'] = $currency_symbol;

            return response()->json([
                'data' => $invoiceData,
                'message' => 'Invoice details.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show_merchant($id)
    {

        $merchant = PaymentMethod::where(['status' => 1, 'id' => $id])->first();

        return response()->json([
            'data' => $merchant,
            'message' => 'Add Lead Successfully Created!'
        ], 200);
    }


    public function create_payment_api(Request $request)
    {
        $input = $request->input();
        $payment = Payment::create([
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
            'amount' => $input['amount'],
            'payment_status' => $input['payment_status'],
            'authorizenet_transaction_id' => $input['authorizenet_transaction_id'],
            'payment_gateway' => $input['payment_gateway'],
            'auth_id' => $input['auth_id'],
            'response_code' => $input['response_code'],
            'message_code' => $input['message_code'],
            'payment_notes' => $input['payment_notes'],
            'sales_type' => $input['sales_type'],
            'merchant_id' => $input['merchant_id'],
            'card_type' => $input['card_type'],
            'card_name' => trim($input['card_name']),
            'card_number' => $input['card_number'],
            'card_exp_month' => $input['card_exp_month'],
            'card_exp_year' => $input['card_exp_year'],
            'card_cvv' => $input['card_cvv'],
            'ip' => $input['ip'],
            'city' => $input['city'],
            'state' => $input['state'],
            'country' => $input['country'],
        ]);

        // update invoice Status
        $invoice = Invoice::where('invoice_key', $input['invoice_id'])->update(['status' => 'paid']);

        return response()->json([
            'invoiceId' => $payment->invoice_id,
            'message' => 'success'

        ], 200);
    }

    public function create_trans_log_api(Request $request)
    {
        $input = $request->input();
        $trans_log = PaymentTransactionsLog::create([
            'team_key' => $input['team_key'],
            'brand_key' => $input['brand_key'],
            'clientid' => $input['clientid'],
            'invoiceid' => $input['invoiceid'],
            'projectid' => $input['projectid'],
            'amount' => $input['amount'],
            'response_code' => $input['response_code'],
            'message_code' => $input['message_code'],
            'response_reason' => $input['response_reason']
        ]);

        return response()->json([
            'message' => 'success'
        ], 200);
    }


    public function direct_payment_api(Request $request)
    {

        $details = htmlentities($request->get('description'));

        $now = \Carbon\Carbon::now();
        $creatorid = 1;
        $merchant_id = Brand::where('brand_key', $request->get('brand_key'))->value('merchant_id');

        $client_exists = Client::where('email', $request->get('email'))->first();


        if ($client_exists) {

            $project = Project::create([
                'team_key' => $request->get('team_key'),
                'brand_key' => $request->get('brand_key'),
                'creatorid' => $creatorid,
                'clientid' => $client_exists->id,
                'agent_id' => $creatorid,
                'asigned_id' => '0',
                'category_id' => '1',
                'project_title' => $request->get('title'),
                'project_description' => $details,
                'project_status' => '1',
                'project_progress' => '1',
                'project_cost' => $request->get('value')
            ]);

            $projectID = $project->id;
            $invoiceKey = random_int(11, 99) . substr(time(), 1, 3) . random_int(11, 99) . substr(time(), 8, 2);

            if ($projectID) {
                $invoice = Invoice::create([
                    'invoice_num' => 'INV-' . random_int(100000, 999999),
                    'invoice_key' => $invoiceKey,
                    'team_key' => $request->get('team_key'),
                    'brand_key' => $request->get('brand_key'),
                    'creatorid' => $creatorid,
                    'clientid' => $client_exists->id,
                    'agent_id' => $creatorid,
                    'final_amount' => $request->get('value'),
                    'total_amount' => $request->get('value'),
                    'due_date' => $now,
                    'invoice_descriptione' => $details,
                    'sales_type' => 'Fresh',
                    'status' => 'Paid',
                    'project_id' => $projectID,
                ]);
            }

            $payment = Payment::create([
                'team_key' => $request->get('team_key'),
                'brand_key' => $request->get('brand_key'),
                'creatorid' => $creatorid,
                'agent_id' => $creatorid,
                'clientid' => $client_exists->id,
                'invoice_id' => $invoiceKey,
                'project_id' => $projectID,
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'address' => '',
                'amount' => $request->get('value'),
                'payment_status' => '1',
                'authorizenet_transaction_id' => $request->get('track_id'),
                'payment_gateway' => 'authorize',
                'auth_id' => '',
                'response_code' => '',
                'message_code' => '',
                'payment_notes' => $details,
                'sales_type' => 'Fresh',
                'merchant_id' => $merchant_id,
                'card_type' => '',
                'card_name' => trim($request->get('card_name')),
                'card_number' => $request->get('card_number'),
                'card_exp_month' => $request->get('card_exp_month'),
                'card_exp_year' => $request->get('card_exp_year'),
                'card_cvv' => $request->get('card_cvv'),
                'ip' => $request->get('ip'),
                'city' => $request->get('city'),
                'state' => $request->get('state'),
                'country' => $request->get('country'),
            ]);
        } else {
            $client = Client::create([
                'team_key' => $request->get('team_key'),
                'brand_key' => $request->get('brand_key'),
                'creatorid' => $creatorid,
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'agent_id' => $creatorid,
                'status' => '1'
            ]);

            $clientID = $client->id;

            $users = User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'password' => Hash::make('12345678'),
                'type' => 'client',
                'team_key' => $request->get('team_key'),
                'clientid' => $clientID
            ]);

            if ($clientID) {
                $project = Project::create([
                    'team_key' => $request->get('team_key'),
                    'brand_key' => $request->get('brand_key'),
                    'creatorid' => $creatorid,
                    'clientid' => $clientID,
                    'agent_id' => $creatorid,
                    'asigned_id' => '0',
                    'category_id' => '1',
                    'project_title' => $request->get('title'),
                    'project_description' => $details,
                    'project_status' => '1',
                    'project_progress' => '1',
                    'project_cost' => $request->get('value')
                ]);
            }
            $projectID = $project->id;
            $invoiceKey = random_int(11, 99) . substr(time(), 1, 3) . random_int(11, 99) . substr(time(), 8, 2);

            if ($projectID) {
                $invoice = Invoice::create([
                    'invoice_num' => 'INV-' . random_int(100000, 999999),
                    'invoice_key' => $invoiceKey,
                    'team_key' => $request->get('team_key'),
                    'brand_key' => $request->get('brand_key'),
                    'creatorid' => $creatorid,
                    'clientid' => $clientID,
                    'agent_id' => $creatorid,
                    'final_amount' => $request->get('value'),
                    'total_amount' => $request->get('value'),
                    'due_date' => $now,
                    'invoice_descriptione' => $details,
                    'sales_type' => 'Fresh',
                    'status' => 'Paid',
                    'project_id' => $projectID,
                ]);
            }
            //$request->get('transaction_id')
            $payment = Payment::create([
                'team_key' => $request->get('team_key'),
                'brand_key' => $request->get('brand_key'),
                'creatorid' => $creatorid,
                'agent_id' => $creatorid,
                'clientid' => $clientID,
                'invoice_id' => $invoiceKey,
                'project_id' => $projectID,
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'address' => '',
                'amount' => $request->get('value'),
                'payment_status' => '1',
                'authorizenet_transaction_id' => $request->get('track_id'),
                'payment_gateway' => 'authorize',
                'auth_id' => '',
                'response_code' => '',
                'message_code' => '',
                'payment_notes' => $details,
                'sales_type' => 'Fresh',
                'merchant_id' => $merchant_id,
                'card_type' => '',
                'card_name' => trim($request->get('card_name')),
                'card_number' => $request->get('card_number'),
                'card_exp_month' => $request->get('card_exp_month'),
                'card_exp_year' => $request->get('card_exp_year'),
                'card_cvv' => $request->get('card_cvv'),
                'ip' => $request->get('ip'),
                'city' => $request->get('city'),
                'state' => $request->get('state'),
                'country' => $request->get('country'),
            ]);
        }

        return response()->json([
            'message' => 'success'
        ], 200);
    }


    public function show_paid_invoice_api($id)
    {
        $paidInvoiceData = Payment::where('invoice_id', $id)->first();
        $invoiceData = Invoice::where('invoice_key', $paidInvoiceData['invoice_id'])->first();
        $brandData = Brand::where('brand_key', $invoiceData['brand_key'])->first();

        if (str_contains(strtolower($paidInvoiceData->payment_gateway), 'authorize')) {
            $merchant = PaymentMethod::where(['status' => 1, 'id' => $paidInvoiceData->merchant_id])->first();
        } elseif (str_contains(strtolower($paidInvoiceData->payment_gateway), 'expigate')) {
            $merchant = PaymentMethodExpigate::where(['status' => 1, 'id' => $paidInvoiceData->merchant_id])->first();
        } elseif (str_contains(strtolower($paidInvoiceData->payment_gateway), 'payarc')) {
            $merchant = PaymentMethodPayArc::where(['status' => 1, 'id' => $paidInvoiceData->merchant_id])->first();
        } else {
            $merchant = PaymentMethod::where(['status' => 1, 'id' => $paidInvoiceData->merchant_id])->first();
        }

        return response()->json(
            [
                'data' => $paidInvoiceData,
                'invoiceData' => $invoiceData,
                'signatures' => $invoiceData->signatures()->get(),
                'brandData' => $brandData,
                'merchant' => $merchant,
                'message' => 'Success'
            ],
            200);

    }

    public function get_payment_error_codes()
    {
        $error_codes = PaymentErrorCods::all();

        return response()->json([
            'data' => $error_codes,
            'message' => 'Get Error Codes Successfully!'
        ], 200);

    }

    /** Redirect To Multi Payment Api*/
    public function upsale_multi_payment(Request $request)
    {
        try {
            $inputs = $request->input();
            $pkey = Config::get('app.privateKey');

            /** Get Invoice */
            $invoice = Invoice::where('invoice_key', $request->get('invoice_key'))->first();
            if (!$invoice) {
                Log::driver('upsale_info')->debug('Invoice not found in upsale where invoice number is : ' . $request->get('invoice_key'));
                return response()->json(['errors' => 'Oops! Invoice not found where invoice number is : ' . $request->get('invoice_key', 0)], 404);
            }

            /** Get Client Card Details */
            $client_cc_info = CcInfo::where(['id' => $request->get('client_card'), 'client_id' => $invoice->clientid])
                ->where('status', 1)
                ->first();
            if (!$client_cc_info) {
                Log::driver('upsale_info')->debug('Card not found in upsale where invoice number is : ' . $request->get('invoice_key') . ' , client id is : ' . $invoice->clientid . ' and card number is : ' . $request->get('client_card'));
                return response()->json(['errors' => 'Oops! Card not found in upsale where invoice number is : ' . $request->get('invoice_key')], 404);
            }

            $missingFields = collect([
                $client_cc_info->address ? null : 'address',
                $client_cc_info->zipcode ? null : 'zipcode',
                $client_cc_info->city ? null : 'city',
                $client_cc_info->state ? null : 'state',
                $client_cc_info->country ? null : 'country'
            ])->filter()->implode(', ');

            if (!empty($missingFields)) {
                $errorMsg = 'Oops! ' . $missingFields . ' field not found';
                Log::driver('upsale_info')->debug($errorMsg);
//                return response()->json(['errors' => $errorMsg], 404);
            }

            $inputs['invoice_id'] = $request->get('invoice_key');
            $inputs['card_name'] = $client_cc_info->card_name;
            $inputs['card_number'] = cxmDecrypt($client_cc_info->card_number, $pkey);
            $inputs['card_cvv'] = cxmDecrypt($client_cc_info->card_cvv, $pkey);
            $inputs['card_exp_month'] = $client_cc_info->card_exp_month;
            $inputs['card_exp_year'] = $client_cc_info->card_exp_year;
            $inputs['card_type'] = $client_cc_info->card_type;
            $inputs['address'] = $client_cc_info->address;
            $inputs['zipcode'] = $client_cc_info->zipcode;
            $inputs['city'] = $client_cc_info->city;
            $inputs['state'] = $client_cc_info->state;
            $inputs['country'] = $client_cc_info->country;

            return Http::post(route('api.multi.payments'), $inputs)->json();
        } catch (RequestException $e) {
            return response()->json(['errors' => $e], 422);
        }
    }

    /** Redirect To Direct Payment Api*/
    public function upsale_direct_payment(Request $request)
    {

        $rules = [
            'client_card' => 'required',
            'merchant_type' => 'required',
            'merchant' => 'required',
        ];
        $messages = [
            'client_card.required' => 'The Client card field is required.',
            'merchant_type.required' => 'The Merchant type field is required.',
            'merchant.required' => 'The Merchant name field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $inputs = $request->input();
            $pkey = Config::get('app.privateKey');
            /** Get Invoice */
            $invoice = Invoice::where('invoice_key', $request->get('invoice_key'))->first();
            if (!$invoice) {
                Log::driver('upsale_info')->debug('Invoice not found in upsale where invoice number is : ' . $request->get('invoice_key'));
                return response()->json(['errors' => 'Oops! Invoice not found.'], 404);
            }

            /** Get Client Card Details */
            $client_cc_info = CcInfo::where(['id' => $request->get('client_card'), 'client_id' => $invoice->clientid])
                /** Removing this because we have added both cards success and failed*/
//                ->where('status', 0)
                ->first();
            if (!$client_cc_info) {
                Log::driver('upsale_info')->debug('Card not found in upsale where invoice number is : ' . $request->get('invoice_key') . ' , client id is : ' . $invoice->clientid . ' and card number is : ' . $request->get('client_card'));
                return response()->json(['errors' => 'Oops! Card not found in upsale where invoice number is : ' . $request->get('invoice_key')], 404);
            }
            if ($request->get('merchant_type') == 'authorize') {
                $payment_merchant = PaymentMethod::where('id', $request->get('merchant'))->first();
                if (!$payment_merchant) {
                    Log::driver('upsale_info')->debug('Oops ! Merchant not found .');
                    return response()->json(['errors' => 'Oops ! Merchant not found .'], 404);
                }
                if ($payment_merchant->status == 0) {
                    Log::driver('upsale_info')->debug('Oops ! Merchant is temporary disabled . Please enable and try again .');
                    return response()->json(['errors' => 'Oops ! Merchant is temporary disabled . Please enable and try again .'], 404);
                }
            } elseif ($request->get('merchant_type') == 'expigate') {
                $payment_merchant = PaymentMethodExpigate::where('id', $request->get('merchant'))->first();
                if (!$payment_merchant) {
                    Log::driver('upsale_info')->debug('Oops ! Merchant not found .');
                    return response()->json(['errors' => 'Oops ! Merchant not found .'], 404);
                }
                if ($payment_merchant->status == 0) {
                    Log::driver('upsale_info')->debug('Oops ! Merchant is temporary disabled . Please enable and try again .');
                    return response()->json(['errors' => 'Oops ! Merchant is temporary disabled . Please enable and try again .'], 404);
                }
            } else {
                Log::driver('upsale_info')->debug('Gotcha ! Invalid Merchant.');
                return response()->json(['errors' => 'Gotcha ! Invalid Merchant.'], 404);
            }

            $missingFields = collect([
                $client_cc_info->address ? null : 'address',
                $client_cc_info->zipcode ? null : 'zipcode',
                $client_cc_info->city ? null : 'city',
                $client_cc_info->state ? null : 'state',
                $client_cc_info->country ? null : 'country'
            ])->filter()->implode(', ');

            if (!empty($missingFields)) {
                $errorMsg = 'Oops! ' . $missingFields . ' field not found';
                Log::driver('upsale_info')->debug($errorMsg);
//                return response()->json(['errors' => $errorMsg], 404);
            }

            $inputs['invoice_id'] = $request->get('invoice_key');
            $inputs['card_name'] = $client_cc_info->card_name;
            $inputs['card_number'] = cxmDecrypt($client_cc_info->card_number, $pkey);
            $inputs['card_cvv'] = cxmDecrypt($client_cc_info->card_cvv, $pkey);
            $inputs['card_exp_month'] = $client_cc_info->card_exp_month;
            $inputs['card_exp_year'] = $client_cc_info->card_exp_year;
            $inputs['card_type'] = $client_cc_info->card_type;
            $inputs['address'] = $client_cc_info->address;
            $inputs['zipcode'] = $client_cc_info->zipcode;
            $inputs['city'] = $client_cc_info->city;
            $inputs['state'] = $client_cc_info->state;
            $inputs['country'] = $client_cc_info->country;
            $inputs['payment_gateway'] = $request->get('merchant_type');
            $inputs['merchant_id'] = $request->get('merchant');

            if ($request->get('merchant_type') == 'authorize') {
                return Http::withHeaders(['X-Source' => $request->url()])->post(route('api.authorize.payment'), $inputs)->json();
            }

            if ($request->get('merchant_type') == 'expigate') {
                return Http::withHeaders(['X-Source' => $request->url()])->post(route('api.expigate.payment'), $inputs)->json();
            }
            Log::driver('upsale_info')->debug('Gotcha ! Invalid Merchant.');
            return response()->json(['errors' => 'Gotcha ! Invalid Merchant.'], 404);
        } catch (RequestException $e) {
            return response()->json(['errors' => $e], 422);
        }
    }

    public function upsale_payment(Request $request)
    {

        $input = $request->input();
        $pkey = Config::get('app.privateKey');

        // Get Invoice data
        $invoiceData = Invoice::where('invoice_key', $input['invoice_key'])->first();
        // get Client Card data
        $clientData = Client::where('id', $invoiceData->clientid)->first();

        // get Client data
        $clientCardData = CcInfo::where(['id' => $input['client_card'], 'client_id' => $invoiceData->clientid])->first();
        $CardNumber = cxmDecrypt($clientCardData->card_number, $pkey);
        $cardCvv = cxmDecrypt($clientCardData->card_cvv, $pkey);
        $cardExpMonth = $clientCardData->card_exp_month;
        $cardExpYear = $clientCardData->card_exp_year;

        //get brand Data
        $brand = Brand::where('brand_key', $invoiceData->brand_key)->first();

        /** DM221223FR STOP EASY WRITING ID = 2 */
        if ($brand->merchant_id == 2) {
            return response()->json(['errors' => 'Please check brand.',], 404);
        }
        //get merchant Information
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
        $cardNumber = preg_replace('/\s+/', '', $CardNumber);

        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($cardNumber);
        $creditCard->setExpirationDate($cardExpYear . "-" . $cardExpMonth);
        $creditCard->setCardCode($cardCvv);

        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        // Create order information
        $order = new AnetAPI\OrderType();
        $order->setDescription($brand->name);

        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType();
//      $customerAddress->setFirstName($clientData->name);
//      $customerAddress->setLastName($clientCardData->card_name);
        //$customerAddress->setCity($clientData->name);
        //$customerAddress->setState($clientData->name);
        //$customerAddress->setCountry($clientData->name);

        // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType();
        $customerData->setType("individual");
        $customerData->setEmail($clientData->email);

        // Create a transaction
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($invoiceData->total_amount);
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

                    $transaction_id = $tresponse->getTransId();
                    $payment_response = $tresponse->getResponseCode();
                    $auth_code = $tresponse->getAuthCode();
                    $statusMsg = $tresponse->getMessages()[0]->getDescription();

                    Payment::create([
                        'team_key' => $invoiceData->team_key,
                        'brand_key' => $invoiceData->brand_key,
                        'creatorid' => $invoiceData->creatorid,
                        'agent_id' => $invoiceData->agent_id,
                        'clientid' => $invoiceData->clientid,
                        'invoice_id' => $input['invoice_key'],
                        'project_id' => $invoiceData->project_id,
                        'name' => trim($clientData->name),
                        'email' => $clientData->email,
                        'phone' => $clientData->phone,
                        'address' => '',
                        'amount' => $invoiceData->total_amount,
                        'payment_status' => '1',
                        'authorizenet_transaction_id' => $tresponse->getTransId(),
                        'payment_gateway' => 'authorize',
                        'auth_id' => $tresponse->getAuthCode(),
                        'response_code' => $payment_response,
                        'message_code' => $tresponse->getMessages()[0]->getCode(),
                        'payment_notes' => $input['description'],
                        'sales_type' => $invoiceData->sales_type,
                        'merchant_id' => $paymentMethod->id,
                        'card_type' => $clientCardData->card_type,
                        'card_name' => trim($clientCardData->card_name),
                        'card_number' => substr($cardNumber, -4),
                        'card_exp_month' => $cardExpMonth,
                        'card_exp_year' => $cardExpYear,
                        'card_cvv' => $cardCvv,
                        'ip' => '',
                        'city' => '',
                        'state' => '',
                        'country' => '',
                    ]);

                    $invoice = Invoice::where('invoice_key', $input['invoice_key'])->update(['status' => 'paid']);

                    $trans_log = PaymentTransactionsLog::create([
                        'team_key' => $invoiceData->team_key,
                        'brand_key' => $invoiceData->brand_key,
                        'clientid' => $invoiceData->clientid,
                        'invoiceid' => $invoiceData->invoice_key,
                        'projectid' => $invoiceData->project_id,
                        'amount' => $invoiceData->total_amount,
                        'response_code' => $tresponse->getResponseCode(),
                        'message_code' => $tresponse->getMessages()[0]->getCode(),
                        'response_reason' => $statusMsg
                    ]);


                    // send payment confirmation mail to Client/admin
                    $emailOptions = array(
                        'to' => $clientData->email,
                        'clientName' => $clientData->name,
                        'subject' => 'Payment Confirmation',
                        'description' => $input['description'],
                        'amount' => $invoiceData->total_amount,
                        'paidInvoiceId' => $input['invoice_key'],
                        'brandKey' => $invoiceData->brand_key
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
                            'team_key' => $invoiceData->team_key,
                            'brand_key' => $invoiceData->brand_key,
                            'clientid' => $invoiceData->clientid,
                            'invoiceid' => $invoiceData->invoice_key,
                            'projectid' => $invoiceData->project_id,
                            'amount' => $invoiceData->total_amount,
                            'response_code' => $tresponse->getResponseCode(),
                            'message_code' => $errorCode,
                            'response_reason' => $statusMsg
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
                    'team_key' => $invoiceData->team_key,
                    'brand_key' => $invoiceData->brand_key,
                    'clientid' => $invoiceData->clientid,
                    'invoiceid' => $invoiceData->invoice_key,
                    'projectid' => $invoiceData->project_id,
                    'amount' => $invoiceData->total_amount,
                    'response_code' => $tresponse->getResponseCode(),
                    'message_code' => $errorCode,
                    'response_reason' => $statusMsg
                ]);
            }
        } else {
            $statusMsg = "No response returned";
        }

        return response()->json([
            'resultCode' => $response->getMessages()->getResultCode(),
            'code' => $payment_response,
            'message' => $statusMsg,
        ], 200);

    }

    // split payment function
    // public function upsale_payment(Request $request){

    //     $input = $request->input();
    //     $pkey = Config::get('app.privateKey');

    //     // Get Invoice data
    //     $invoiceData = Invoice::where('invoice_key', $input['invoice_key'])->first();
    //     // get Client Card data
    //     $clientData = Client::where('id', $invoiceData->clientid)->first();

    //     // get Client data
    //     $clientCardData = CcInfo::where(['id' => $input['client_card'], 'client_id' => $invoiceData->clientid])->first();
    //     $CardNumber = cxmDecrypt($clientCardData->card_number, $pkey);
    //     $cardCvv = cxmDecrypt($clientCardData->card_cvv, $pkey);
    //     $cardExpMonth = $clientCardData->card_exp_month;
    //     $cardExpYear = $clientCardData->card_exp_year;

    //     //get brand Data
    //     $brand = Brand::where('brand_key',$invoiceData->brand_key)->first();
    //     //get merchant Information
    //     $paymentMethod = PaymentMethod::where(['status' => 1, 'id' =>$brand->merchant_id])->first();

    //     $sasDeclineReasonCodeDB = PaymentErrorCods::all();
    //     $sasDeclineReasonCode = array();

    //     foreach($sasDeclineReasonCodeDB as $errorResaon){
    //         $code = $errorResaon->error_code;
    //         $resoan = $errorResaon->error_reason;

    //         $sasDeclineReasonCode[$code] = $resoan;

    //     }

    //     if($paymentMethod->mode == 0){
    //         $loginId = $paymentMethod->live_login_id;
    //         $transcation_key = $paymentMethod->live_transaction_key;
    //     }else{
    //         $loginId = $paymentMethod->test_login_id;
    //         $transcation_key = $paymentMethod->test_transaction_key;
    //     }


    //     // Set the transaction's reference ID
    //     $refID = 'REF'.time();

    //     // Create a merchantAuthenticationType object with authentication details
    //     // retrieved from the config file
    //     $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    //     $merchantAuthentication->setName($loginId);
    //     $merchantAuthentication->setTransactionKey($transcation_key);

    //     // Create the payment data for a credit card
    //     $cardNumber = preg_replace('/\s+/', '', $CardNumber);

    //     $creditCard = new AnetAPI\CreditCardType();
    //     $creditCard->setCardNumber($cardNumber);
    //     $creditCard->setExpirationDate($cardExpYear . "-" .$cardExpMonth);
    //     $creditCard->setCardCode($cardCvv);

    //     // Add the payment data to a paymentType object
    //     $paymentOne = new AnetAPI\PaymentType();
    //     $paymentOne->setCreditCard($creditCard);

    //     // Create order information
    //     $order = new AnetAPI\OrderType();
    //     $order->setDescription($brand->name);

    //     // Set the customer's Bill To address
    //     $customerAddress = new AnetAPI\CustomerAddressType();
    //     $customerAddress->setFirstName($clientData->name);
    //     $customerAddress->setLastName($clientCardData->card_name);
    //     //$customerAddress->setCity($clientData->name);
    //     //$customerAddress->setState($clientData->name);
    //     //$customerAddress->setCountry($clientData->name);

    //     // Set the customer's identifying information
    //     $customerData = new AnetAPI\CustomerDataType();
    //     $customerData->setType("individual");
    //     $customerData->setEmail($clientData->email);


    //     // check if writing Brand then run payment split code other wise run normal single payment
    //     if($paymentMethod->id == 2){

    //         if($invoiceData->payment_division == '1'){
    //             $payments = [1.00, 2.00, ($invoiceData->total_amount - 3.00)];
    //         } else {
    //             $payments = [2=>$invoiceData->total_amount];
    //         }

    //         foreach($payments as $indx => $payment){
    //           // Create a transaction
    //           $transactionRequestType = new AnetAPI\TransactionRequestType();
    //           $transactionRequestType->setTransactionType("authCaptureTransaction");
    //           $transactionRequestType->setAmount($payment);
    //           $transactionRequestType->setOrder($order);
    //           $transactionRequestType->setPayment($paymentOne);
    //           $transactionRequestType->setCustomer($customerData);

    //           $request = new AnetAPI\CreateTransactionRequest();
    //           $request->setMerchantAuthentication($merchantAuthentication);
    //           $request->setRefId($refID);
    //           $request->setTransactionRequest($transactionRequestType);
    //           $controller = new AnetController\CreateTransactionController($request);

    //           if($paymentMethod->mode == 0){
    //               $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
    //           }else{
    //               $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
    //           }

    //           $tresponse = $response->getTransactionResponse();

    //           $payment_status = $response->getMessages()->getResultCode();

    //           if ($response != null) {
    //               // Check to see if the API request was successfully received and acted upon
    //               if ($response->getMessages()->getResultCode() == "Ok") {
    //                   // Since the API request was successful, look for a transaction response
    //                   // and parse it to display the results of authorizing the card
    //                   $tresponse = $response->getTransactionResponse();

    //                   if ($tresponse != null && $tresponse->getMessages() != null) {

    //                       $transaction_id = $tresponse->getTransId();
    //                       $payment_response = $tresponse->getResponseCode();
    //                       $auth_code = $tresponse->getAuthCode();
    //                       $statusMsg = $tresponse->getMessages()[0]->getDescription();

    //                         if($indx == 2){
    //                             Payment::create([
    //                                 'team_key'  => $invoiceData->team_key,
    //                                 'brand_key' => $invoiceData->brand_key,
    //                                 'creatorid' => $invoiceData->creatorid,
    //                                 'agent_id'  => $invoiceData->agent_id,
    //                                 'clientid'  => $invoiceData->clientid,
    //                                 'invoice_id' => $input['invoice_key'],
    //                                 'project_id' => $invoiceData->project_id,
    //                                 'name' => trim($clientData->name),
    //                                 'email'  => $clientData->email,
    //                                 'phone' => $clientData->phone,
    //                                 'address' => '',
    //                                 'amount' => $invoiceData->total_amount,
    //                                 'payment_status' => '1',
    //                                 'authorizenet_transaction_id' => $tresponse->getTransId(),
    //                                 'payment_gateway' => 'authorize',
    //                                 'auth_id' => $tresponse->getAuthCode(),
    //                                 'response_code' =>  $payment_response,
    //                                 'message_code' => $tresponse->getMessages()[0]->getCode(),
    //                                 'payment_notes' =>   $input['description'],
    //                                 'sales_type'    => $invoiceData->sales_type,
    //                                 'merchant_id' => $paymentMethod->id,
    //                                 'card_type' => $clientCardData->card_type,
    //                                 'card_name'=>trim( $clientCardData->card_name),
    //                                 'card_number'=>substr($cardNumber,-4),
    //                                 'card_exp_month'=>$cardExpMonth,
    //                                 'card_exp_year'=>$cardExpYear,
    //                                 'card_cvv'=>$cardCvv,
    //                                 'ip'=>'',
    //                                 'city'=>'',
    //                                 'state'=>'',
    //                                 'country'=>'',
    //                             ]);

    //                             $invoice = Invoice::where('invoice_key',$input['invoice_key'])->update(['status' => 'paid']);
    //                         }

    //                         $trans_log = PaymentTransactionsLog::create([
    //                             'team_key'  => $invoiceData->team_key,
    //                             'brand_key' => $invoiceData->brand_key,
    //                             'clientid'  => $invoiceData->clientid,
    //                             'invoiceid' => $invoiceData->invoice_key,
    //                             'projectid' => $invoiceData->project_id,
    //                             'amount' => $payment,
    //                             'response_code' => $tresponse->getResponseCode(),
    //                             'message_code' => $tresponse->getMessages()[0]->getCode(),
    //                             'response_reason' => $statusMsg
    //                         ]);

    //                       // send payment confirmation mail to Client/admin
    //                       $emailOptions = array(
    //                           'to' => $clientData->email,
    //                           'clientName' => $clientData->name,
    //                           'subject' => 'Payment Confirmation',
    //                           'description' => $input['description'],
    //                           'amount' => $payment,
    //                           'paidInvoiceId' => $input['invoice_key'],
    //                           'brandKey' => $invoiceData->brand_key
    //                       );
    //                       //sendEmail($emailOptions);


    //                   } else {
    //                       if ($tresponse->getErrors() != null) {
    //                           $errorCode = $tresponse->getErrors()[0]->getErrorCode();
    //                           $statusMsg =  $tresponse->getErrors()[0]->getErrorText();
    //                           $payment_response = $tresponse->getErrors()[0]->getErrorCode();

    //                           if(array_key_exists($errorCode, $sasDeclineReasonCode)){
    //                               $statusMsg .= $sasDeclineReasonCode[$errorCode];
    //                           }

    //                           $trans_log = PaymentTransactionsLog::create([
    //                             'team_key'  => $invoiceData->team_key,
    //                             'brand_key' => $invoiceData->brand_key,
    //                             'clientid'  => $invoiceData->clientid,
    //                             'invoiceid' => $invoiceData->invoice_key,
    //                             'projectid' => $invoiceData->project_id,
    //                             'amount' => $payment,
    //                               'response_code' => $tresponse->getResponseCode(),
    //                               'message_code' => $errorCode,
    //                               'response_reason' => $statusMsg
    //                           ]);
    //                       }
    //                   }
    //                   // Or, print errors if the API request wasn't successful
    //               } else {
    //                   //echo "Transaction Failed \n";
    //                   $tresponse = $response->getTransactionResponse();

    //                   if ($tresponse != null && $tresponse->getErrors() != null) {
    //                       $errorCode = $tresponse->getErrors()[0]->getErrorCode();
    //                       $statusMsg = $tresponse->getErrors()[0]->getErrorText();
    //                       $payment_response = $tresponse->getErrors()[0]->getErrorCode();

    //                       if(array_key_exists($errorCode, $sasDeclineReasonCode)){
    //                          $statusMsg .= $sasDeclineReasonCode[$errorCode];
    //                       }

    //                   } else {
    //                       $errorCode = $response->getMessages()->getMessage()[0]->getCode();
    //                       $statusMsg = $response->getMessages()->getMessage()[0]->getText();
    //                       $payment_response = $tresponse->getErrors()[0]->getErrorCode();

    //                       if(array_key_exists($errorCode, $sasDeclineReasonCode)){
    //                           $statusMsg .= $sasDeclineReasonCode[$errorCode];
    //                       }
    //                   }
    //                   $trans_log = PaymentTransactionsLog::create([
    //                     'team_key'  => $invoiceData->team_key,
    //                     'brand_key' => $invoiceData->brand_key,
    //                     'clientid'  => $invoiceData->clientid,
    //                     'invoiceid' => $invoiceData->invoice_key,
    //                     'projectid' => $invoiceData->project_id,
    //                     'amount' => $payment,
    //                       'response_code' => $tresponse->getResponseCode(),
    //                       'message_code' => $errorCode,
    //                       'response_reason' => $statusMsg
    //                   ]);
    //               }
    //           } else {
    //               $statusMsg =  "No response returned";
    //           }
    //         }

    //     }else{

    //         // Create a transaction
    //         $transactionRequestType = new AnetAPI\TransactionRequestType();
    //         $transactionRequestType->setTransactionType("authCaptureTransaction");
    //         $transactionRequestType->setAmount($invoiceData->total_amount);
    //         $transactionRequestType->setOrder($order);
    //         $transactionRequestType->setPayment($paymentOne);
    //         $transactionRequestType->setCustomer($customerData);


    //         $request = new AnetAPI\CreateTransactionRequest();
    //         $request->setMerchantAuthentication($merchantAuthentication);
    //         $request->setRefId($refID);
    //         $request->setTransactionRequest($transactionRequestType);
    //         $controller = new AnetController\CreateTransactionController($request);

    //         if($paymentMethod->mode == 0){
    //             $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
    //         }else{
    //             $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
    //         }

    //         $tresponse = $response->getTransactionResponse();

    //         $payment_status = $response->getMessages()->getResultCode();

    //         if ($response != null) {
    //             // Check to see if the API request was successfully received and acted upon
    //             if ($response->getMessages()->getResultCode() == "Ok") {
    //                 // Since the API request was successful, look for a transaction response
    //                 // and parse it to display the results of authorizing the card
    //                 $tresponse = $response->getTransactionResponse();

    //                 if ($tresponse != null && $tresponse->getMessages() != null) {

    //                     $transaction_id = $tresponse->getTransId();
    //                     $payment_response = $tresponse->getResponseCode();
    //                     $auth_code = $tresponse->getAuthCode();
    //                     $statusMsg = $tresponse->getMessages()[0]->getDescription();

    //                     Payment::create([
    //                         'team_key'  => $invoiceData->team_key,
    //                         'brand_key' => $invoiceData->brand_key,
    //                         'creatorid' => $invoiceData->creatorid,
    //                         'agent_id'  => $invoiceData->agent_id,
    //                         'clientid'  => $invoiceData->clientid,
    //                         'invoice_id' => $input['invoice_key'],
    //                         'project_id' => $invoiceData->project_id,
    //                         'name' => trim($clientData->name),
    //                         'email'  => $clientData->email,
    //                         'phone' => $clientData->phone,
    //                         'address' => '',
    //                         'amount' => $invoiceData->total_amount,
    //                         'payment_status' => '1',
    //                         'authorizenet_transaction_id' => $tresponse->getTransId(),
    //                         'payment_gateway' => 'authorize',
    //                         'auth_id' => $tresponse->getAuthCode(),
    //                         'response_code' =>  $payment_response,
    //                         'message_code' => $tresponse->getMessages()[0]->getCode(),
    //                         'payment_notes' =>   $input['description'],
    //                         'sales_type'    => $invoiceData->sales_type,
    //                         'merchant_id' => $paymentMethod->id,
    //                         'card_type' => $clientCardData->card_type,
    //                         'card_name'=>trim( $clientCardData->card_name),
    //                         'card_number'=>substr($cardNumber,-4),
    //                         'card_exp_month'=>$cardExpMonth,
    //                         'card_exp_year'=>$cardExpYear,
    //                         'card_cvv'=>$cardCvv,
    //                         'ip'=>'',
    //                         'city'=>'',
    //                         'state'=>'',
    //                         'country'=>'',
    //                     ]);

    //                     $invoice = Invoice::where('invoice_key',$input['invoice_key'])->update(['status' => 'paid']);

    //                     $trans_log = PaymentTransactionsLog::create([
    //                     'team_key'  => $invoiceData->team_key,
    //                     'brand_key' => $invoiceData->brand_key,
    //                     'clientid'  => $invoiceData->clientid,
    //                     'invoiceid' => $invoiceData->invoice_key,
    //                     'projectid' => $invoiceData->project_id,
    //                     'amount' => $invoiceData->total_amount,
    //                     'response_code' => $tresponse->getResponseCode(),
    //                     'message_code' => $tresponse->getMessages()[0]->getCode(),
    //                     'response_reason' => $statusMsg
    //                 ]);


    //                     // send payment confirmation mail to Client/admin
    //                     $emailOptions = array(
    //                         'to' => $clientData->email,
    //                         'clientName' => $clientData->name,
    //                         'subject' => 'Payment Confirmation',
    //                         'description' => $input['description'],
    //                         'amount' => $invoiceData->total_amount,
    //                         'paidInvoiceId' => $input['invoice_key'],
    //                         'brandKey' => $invoiceData->brand_key
    //                     );
    //                     //sendEmail($emailOptions);

    //                 } else {
    //                     if ($tresponse->getErrors() != null) {
    //                         $errorCode = $tresponse->getErrors()[0]->getErrorCode();
    //                         $statusMsg =  $tresponse->getErrors()[0]->getErrorText();
    //                         $payment_response = $tresponse->getErrors()[0]->getErrorCode();

    //                         if(array_key_exists($errorCode, $sasDeclineReasonCode)){
    //                             $statusMsg .= $sasDeclineReasonCode[$errorCode];
    //                         }

    //                         $trans_log = PaymentTransactionsLog::create([
    //                         'team_key'  => $invoiceData->team_key,
    //                         'brand_key' => $invoiceData->brand_key,
    //                         'clientid'  => $invoiceData->clientid,
    //                         'invoiceid' => $invoiceData->invoice_key,
    //                         'projectid' => $invoiceData->project_id,
    //                         'amount' => $invoiceData->total_amount,
    //                             'response_code' => $tresponse->getResponseCode(),
    //                             'message_code' => $errorCode,
    //                             'response_reason' => $statusMsg
    //                         ]);
    //                     }
    //                 }
    //                 // Or, print errors if the API request wasn't successful
    //             } else {
    //                 //echo "Transaction Failed \n";
    //                 $tresponse = $response->getTransactionResponse();

    //                 if ($tresponse != null && $tresponse->getErrors() != null) {
    //                     $errorCode = $tresponse->getErrors()[0]->getErrorCode();
    //                     $statusMsg = $tresponse->getErrors()[0]->getErrorText();
    //                     $payment_response = $tresponse->getErrors()[0]->getErrorCode();

    //                     if(array_key_exists($errorCode, $sasDeclineReasonCode)){
    //                     $statusMsg .= $sasDeclineReasonCode[$errorCode];
    //                     }

    //                 } else {
    //                     $errorCode = $response->getMessages()->getMessage()[0]->getCode();
    //                     $statusMsg = $response->getMessages()->getMessage()[0]->getText();
    //                     $payment_response = $tresponse->getErrors()[0]->getErrorCode();

    //                     if(array_key_exists($errorCode, $sasDeclineReasonCode)){
    //                         $statusMsg .= $sasDeclineReasonCode[$errorCode];
    //                     }
    //                 }
    //                 $trans_log = PaymentTransactionsLog::create([
    //                 'team_key'  => $invoiceData->team_key,
    //                 'brand_key' => $invoiceData->brand_key,
    //                 'clientid'  => $invoiceData->clientid,
    //                 'invoiceid' => $invoiceData->invoice_key,
    //                 'projectid' => $invoiceData->project_id,
    //                 'amount' => $invoiceData->total_amount,
    //                     'response_code' => $tresponse->getResponseCode(),
    //                     'message_code' => $errorCode,
    //                     'response_reason' => $statusMsg
    //                 ]);
    //             }
    //         } else {
    //             $statusMsg =  "No response returned";
    //         }
    //     }

    //     return response()->json([
    //           'resultCode' => $response->getMessages()->getResultCode(),
    //           'code' => $payment_response,
    //           'message' => $statusMsg,
    //     ], 200);

    // }


//    public function get_client_card_info($id)
//    {
//        $pkey = Config::get('app.privateKey');
//        $clientCardData = CcInfo::where('client_id', $id)->where('status', 1)->get();
//
//        $cardInfo = array();
//        foreach ($clientCardData as $clientCard) {
//            $clientCard['card4Digit'] = substr(cxmDecrypt($clientCard->card_number, $pkey), -4);
//            array_push($cardInfo, $clientCard);
//        }
//        return json_encode($cardInfo);
//    }
    public function get_client_card_info($id)
    {
        try {
            $pkey = Config::get('app.privateKey');

            $clientCardData = CcInfo::where('client_id', $id)
                ->where('status', 1)
                ->orderBy('updated_at', 'desc')
                ->get();

            $uniqueCardInfo = [];
//            $seenCards = [];

            foreach ($clientCardData as $clientCard) {
//                $cardIdentifier = $clientCard->card_number . $clientCard->card_cvv . $clientCard->card_exp_month . $clientCard->card_exp_year;
//                if (!in_array($cardIdentifier, $seenCards)) {
                $uniqueCardInfo[] = [
                    'id' => $clientCard->id,
                    'card_type' => $clientCard->card_type,
                    'card4Digit' => substr(cxmDecrypt($clientCard->card_number, $pkey), -4),
                    'invoice_id' => $clientCard->invoice_id,
                    'address' => $clientCard->address,
                    'city' => $clientCard->city,
                    'state' => $clientCard->state,
                    'zipcode' => $clientCard->zipcode,
                    'country' => $clientCard->country,
                ];
//                    $seenCards[] = $cardIdentifier;
//                }
            }
            return response()->json($uniqueCardInfo);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function get_client_card_info_inactive_status($id)
    {
        try {
            $pkey = Config::get('app.privateKey');

            $clientCardData = CcInfo::where('client_id', $id)
//                ->where('status', 0)
                ->orderBy('updated_at', 'desc')
                ->get();

            $uniqueCardInfo = [];
//            $seenCards = [];

            foreach ($clientCardData as $clientCard) {
//                $cardIdentifier = $clientCard->card_number . $clientCard->card_cvv . $clientCard->card_exp_month . $clientCard->card_exp_year;
//                if (!in_array($cardIdentifier, $seenCards)) {
                $uniqueCardInfo[] = [
                    'id' => $clientCard->id,
                    'card_type' => $clientCard->card_type,
                    'card4Digit' => substr(cxmDecrypt($clientCard->card_number, $pkey), -4),
                    'invoice_id' => $clientCard->invoice_id,
                    'status' => $clientCard->status,
                    'address' => $clientCard->address,
                    'city' => $clientCard->city,
                    'state' => $clientCard->state,
                    'zipcode' => $clientCard->zipcode,
                    'country' => $clientCard->country,
                    'updated_at' => $clientCard->updated_at->diffForHumans(),
                    'card_status' => $clientCard->status,
                ];
//                    $seenCards[] = $cardIdentifier;
//                }
            }
            return response()->json(['cards' => $uniqueCardInfo]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /** **/
//    public function run_split_payment(){
//
//        $pkey = Config::get('app.privateKey');
//
//        $splitPayment = SplitPayment::where('status' , 0)->first();
//
//        if(!empty($splitPayment)){
//            $clientCardData = CcInfo::where('client_id' , $splitPayment->clientid)->first();
//            $CardNumber = cxmDecrypt($clientCardData->card_number, $pkey);
//            $cardCvv = cxmDecrypt($clientCardData->card_cvv, $pkey);
//            $cardExpMonth = $clientCardData->card_exp_month;
//            $cardExpYear = $clientCardData->card_exp_year;
//
//            // get Client Card data
//            $clientData = Client::where('id', $splitPayment->clientid)->first();
//            // get merchant data
//            $paymentMethod = PaymentMethod::where(['status' => 1, 'id' =>$splitPayment->merchant_id])->first();
//
//            if($paymentMethod->mode == 0){
//                $loginId = $paymentMethod->live_login_id;
//                $transcation_key = $paymentMethod->live_transaction_key;
//            }else{
//                $loginId = $paymentMethod->test_login_id;
//                $transcation_key = $paymentMethod->test_transaction_key;
//            }
//
//            // Set the transaction's reference ID
//            $refID = 'REF'.time();
//
//            // Create a merchantAuthenticationType object with authentication details
//            // retrieved from the config file
//            $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
//            $merchantAuthentication->setName($loginId);
//            $merchantAuthentication->setTransactionKey($transcation_key);
//
//            // Create the payment data for a credit card
//            $cardNumber = preg_replace('/\s+/', '', $CardNumber);
//
//            $creditCard = new AnetAPI\CreditCardType();
//            $creditCard->setCardNumber($cardNumber);
//            $creditCard->setExpirationDate($cardExpYear . "-" .$cardExpMonth);
//            $creditCard->setCardCode($cardCvv);
//
//            // Add the payment data to a paymentType object
//            $paymentOne = new AnetAPI\PaymentType();
//            $paymentOne->setCreditCard($creditCard);
//
//            // Create order information
//            $order = new AnetAPI\OrderType();
//            $order->setDescription('Split Payments');
//
//            // Set the customer's Bill To address
//            $customerAddress = new AnetAPI\CustomerAddressType();
//            $customerAddress->setFirstName($clientData->name);
//            $customerAddress->setLastName($clientCardData->card_name);
//
//            // Set the customer's identifying information
//            $customerData = new AnetAPI\CustomerDataType();
//            $customerData->setType("individual");
//            $customerData->setEmail($clientData->email);
//
//            // Create a transaction
//            $transactionRequestType = new AnetAPI\TransactionRequestType();
//            $transactionRequestType->setTransactionType("authCaptureTransaction");
//            $transactionRequestType->setAmount($splitPayment->amount);
//            $transactionRequestType->setOrder($order);
//            $transactionRequestType->setPayment($paymentOne);
//            $transactionRequestType->setCustomer($customerData);
//
//
//            $request = new AnetAPI\CreateTransactionRequest();
//            $request->setMerchantAuthentication($merchantAuthentication);
//            $request->setRefId($refID);
//            $request->setTransactionRequest($transactionRequestType);
//            $controller = new AnetController\CreateTransactionController($request);
//
//            if($paymentMethod->mode == 0){
//                $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
//            }else{
//                $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
//            }
//
//            $tresponse = $response->getTransactionResponse();
//
//            $payment_status = $response->getMessages()->getResultCode();
//
//            if ($response != null) {
//                // Check to see if the API request was successfully received and acted upon
//                if ($response->getMessages()->getResultCode() == "Ok") {
//                    // Since the API request was successful, look for a transaction response
//                    // and parse it to display the results of authorizing the card
//                    $tresponse = $response->getTransactionResponse();
//
//                    if ($tresponse != null && $tresponse->getMessages() != null) {
//
//                        $transaction_id = $tresponse->getTransId();
//                        $payment_response = $tresponse->getResponseCode();
//                        $auth_code = $tresponse->getAuthCode();
//                        $statusMsg = $tresponse->getMessages()[0]->getDescription();
//
//                        SplitPayment::where('id',$splitPayment->id)->update(['status' => 1, 'authorizenet_transaction_id' => $transaction_id]);
//
//
//                    } else {
//                        if ($tresponse->getErrors() != null) {
//                            $errorCode = $tresponse->getErrors()[0]->getErrorCode();
//                            $statusMsg =  $tresponse->getErrors()[0]->getErrorText();
//                            $payment_response = $tresponse->getErrors()[0]->getErrorCode();
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
//                    } else {
//                        $errorCode = $response->getMessages()->getMessage()[0]->getCode();
//                        $statusMsg = $response->getMessages()->getMessage()[0]->getText();
//                        $payment_response = $tresponse->getErrors()[0]->getErrorCode();
//                    }
//                }
//            } else {
//                $statusMsg =  "No response returned";
//            }
//        }
//        //foreach($splitPayments as $splitPayment){
//        //}
//
//    }

    /** **/
}
