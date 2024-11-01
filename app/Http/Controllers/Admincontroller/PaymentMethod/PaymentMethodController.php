<?php

namespace App\Http\Controllers\Admincontroller\PaymentMethod;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;


class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $payment_methods = array();
        foreach (PaymentMethod::where('id', '!=', 5)->get() as $payment) {
            $monthPayment = Payment::where(['payment_status' => 1, 'payment_gateway' => "authorize", 'merchant_id' => $payment->id])
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('amount');
            $payment['paymentMonth'] = (int)$monthPayment;
            $payment_methods[] = $payment;
        }

        return view('admin.payment-method.authorize.index', compact('payment_methods'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.payment-method.authorize.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {


            $rules = [
                'name' => 'required|min:3',
                'email' => 'required|email',
                'live_login_id' => 'required|max:255',
                'live_transaction_key' => 'required',
                'capacity' => 'required|numeric',
                'amount_limit' => 'required|numeric',
                'currency' => 'required',
                'environment' => 'required|in:0,1',
                'gateway_id' => 'required',
                'mmid' => 'required',
            ];

            $messages = [
                'name.required' => 'Merchant field is required',
                'name.min' => 'Merchant must be at least :min characters',
                'email.required' => 'Email field is required',
                'email.email' => 'Please provide a valid email address',
                'live_login_id.required' => 'Login ID field is required',
                'live_transaction_key.required' => 'Transaction Key field is required',
                'amount_limit.required' => 'Amount Limit field is required',
                'amount_limit.numeric' => 'Amount Limit must be a number',
                'capacity.required' => 'Capacity field is required',
                'capacity.numeric' => 'Capacity must be a number',
                'currency.required' => 'Currency field is required',
                'environment.required' => 'Environment field is required',
                'environment.in' => 'Invalid value for Environment',
                'gateway_id.required' => 'Gateway field is requried.',
                'mmid.required' => 'MMID field is requried.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $payment_method = PaymentMethod::create([
                'name' => 'Authorize.Net',
                'email' => $request->email,
                'test_login_id' => $request->get('test_login_id', '4nV8fPcR8f2'),
                'test_transaction_key' => $request->get('test_transaction_key', '45kBec6CU5S9ug2L'),
                'live_login_id' => $request->live_login_id,
                'live_transaction_key' => $request->live_transaction_key,
                'currency' => $request->currency,
                'evnironment' => $request->get('environment', 1) == 0 ? "Production" : 'Sandbox',
                'mode' => $request->get('environment', 1) == 0 ? 0 : 1,
                'status' => 1,
                'merchant' => $request->name,
                'capacity' => $request->capacity,
                'limit' => $request->amount_limit,
                'gateway_id' => $request->gateway_id,
                'mmid' => $request->mmid,
            ]);
            return response()->json(['success' => 'Payment Method created successfully!', 'data' => $payment_method]);
        } catch (QueryException|\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\PaymentMethod $paymentMethod
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        try {
            $method = PaymentMethod::findOrFail($id);
            return view('admin.payment-method.authorize.edit', compact('method'));
        } catch (\Exception $e) {
            return back()->with(['error' => 'Record not found'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\PaymentMethod $paymentMethod
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        try {
            $method = PaymentMethod::findOrFail($id);
            return view('admin.payment-method.authorize.edit', compact('method'));
        } catch (\Exception $e) {
            return back()->with(['error' => 'Record not found'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PaymentMethod $paymentMethod
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {

        try {

            $rules = [
                'name' => 'required|min:3',
                'email' => 'required|email',
                'login_id' => 'required|max:255',
                'transaction_key' => 'required',
                'capacity' => 'required|numeric',
                'amount_limit' => 'required|numeric',
                'gateway_id' => 'required',
                'mmid' => 'required',
            ];

            $messages = [
                'name.required' => 'Merchant field is required',
                'name.min' => 'Merchant must be at least :min characters',
                'email.required' => 'Email field is required',
                'email.email' => 'Please provide a valid email address',
                'login_id.required' => 'Login ID field is required',
                'transaction_key.required' => 'Transaction Key field is required',
                'capacity.required' => 'Capacity field is required',
                'capacity.numeric' => 'Capacity must be a number',
                'amount_limit.required' => 'Amount Limit field is required',
                'amount_limit.numeric' => 'Amount Limit must be a number',
                'gateway_id.required' => 'Gateway field is requried.',
                'mmid.required' => 'MMID field is requried.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $paymentMethod = PaymentMethod::find($id);
            $paymentMethod->merchant = $request->name;
            $paymentMethod->email = $request->email;
            $paymentMethod->live_login_id = $request->login_id;
            $paymentMethod->live_transaction_key = $request->transaction_key;
            $paymentMethod->capacity = $request->capacity;
            $paymentMethod->gateway_id = $request->gateway_id;
            $paymentMethod->mmid = $request->mmid;
            $paymentMethod->save();
            return response()->json(['success' => 'Payment Method updated successfully!', 'data' => $paymentMethod]);
        } catch (QueryException|\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\PaymentMethod $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\PaymentMethod $paymentMethod
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeMode(Request $request)
    {
        $payment_method = PaymentMethod::find($request->method_id);
        $payment_method->mode = $request->mode;
        $payment_method->save();

        return response()->json(['success' => 'Mode change successfully.']);
    }

    public function changeStatus(Request $request)
    {
        $payment_method = PaymentMethod::find($request->method_id);
        $payment_method->status = $request->status;
        $payment_method->save();

        return response()->json(['success' => 'Mode change successfully.']);
    }

    public function changeAuthorization(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'method_id' => 'required|integer|exists:payment_methods,id',
                'authorization' => 'required|boolean',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $paymentMethod = PaymentMethod::where('id', $request->get('method_id'))->first();
            if (!$paymentMethod) {
                return response()->json(['error' => 'Payment method not found.'], 404);
            }

            $paymentMethod->authorization = $request->authorization;
            $paymentMethod->save();
            return response()->json(['success' => 'Authorization changed successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to change authorization.'], 500);
        }
    }


    public function get_merchants()
    {
        $payment_methods = PaymentMethod::all();

        return response()->json([
            'data' => $payment_methods,
            'message' => 'Add Lead Successfully Created!'
        ], 200);

    }


    //get all held trans
    public function get_held_trans_list($id)
    {

        $method = PaymentMethod::find($id);

        $loginId = $method->live_login_id;
        $transKey = $method->live_transaction_key;

        //    $loginId = '5KP3u95bQpv';
        //    $transKey = '346HZ32z3fP4hTG2';

        /* Create a merchantAuthenticationType object with authentication details
        retrieved from the constants file */
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($loginId);
        $merchantAuthentication->setTransactionKey($transKey);

        // Set the transaction's refId
        $refId = 'ref' . time();

        $request = new AnetAPI\GetUnsettledTransactionListRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setStatus("pendingApproval");


        $controller = new AnetController\GetUnsettledTransactionListController($request);

        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
        //SANDBOX
        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
            //dd(get_class_methods($response->getTransactions()[0]));
            if (null != $response->getTransactions()) {
                $held_trans = $response->getTransactions();
            } else {
                $held_trans = "No suspicious transactions for the merchant." . "\n";
            }
        }

        return view('admin.payment-method.authorize.heldtrans', compact('held_trans'));
    }

    //approve held tras.
    public function approved_all_held_trans($id)
    {

        $method = PaymentMethod::find($id);

        $loginId = $method->live_login_id;
        $transKey = $method->live_transaction_key;

        //$loginId = '5KP3u95bQpv';
        //$transKey = '346HZ32z3fP4hTG2';


        /* Create a merchantAuthenticationType object with authentication details
        retrieved from the constants file */
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($loginId);
        $merchantAuthentication->setTransactionKey($transKey);

        // Set the transaction's refId
        $refId = 'ref' . time();

        $request = new AnetAPI\GetUnsettledTransactionListRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setStatus("pendingApproval");


        $controller = new AnetController\GetUnsettledTransactionListController($request);

        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);

        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
            if (null != $response->getTransactions()) {
                foreach ($response->getTransactions() as $tx) {

                    $refId = 'ref' . time();

                    //create a transaction
                    $transactionRequestType = new AnetAPI\HeldTransactionRequestType();
                    $transactionRequestType->setAction("approve"); //other possible value: decline
                    $transactionRequestType->setRefTransId($tx->getTransId());


                    $request = new AnetAPI\UpdateHeldTransactionRequest();
                    $request->setMerchantAuthentication($merchantAuthentication);
                    $request->setHeldTransactionRequest($transactionRequestType);
                    $controller = new AnetController\UpdateHeldTransactionController($request);
                    $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);

                }
            }
        }

        return redirect()->route('admin.payment.method.authorize.held.transaction', $id);
    }


}
