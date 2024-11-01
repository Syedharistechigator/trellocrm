<?php

namespace App\Http\Controllers\Admincontroller\PaymentMethod;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentMethodExpigate;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentMethodExpigateController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $payment_methods = array();
        foreach (PaymentMethodExpigate::get() as $payment_method_expigate) {
            $monthPayment = Payment::where(['payment_status' => 1, 'payment_gateway' => "Expigate", 'merchant_id' => $payment_method_expigate->id])
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at',  Carbon::now()->year)
                ->sum('amount');
            $payment_method_expigate['paymentMonth'] = (int)$monthPayment;
            $payment_methods[] = $payment_method_expigate;
        }
        return view('admin.payment-method.expigate.index', compact('payment_methods'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.payment-method.expigate.create');
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
                'merchant' => 'required|min:3',
                'email' => 'required|email',
                'login_id' => 'required|max:255',
                'transaction_key' => 'required',
                'capacity' => 'required|numeric',
                'amount_limit' => 'required|numeric',
                'payment_url' => 'required|url',
                'currency' => 'required',
                'merchant_type' => 'required|in:0,1',
                'environment' => 'required|in:0,1',
                'gateway_id' => 'required',
                'mmid' => 'required',
            ];

            $messages = [
                'merchant.required' => 'Merchant field is required',
                'merchant.min' => 'Merchant must be at least :min characters',
                'email.required' => 'Email field is required',
                'email.email' => 'Please provide a valid email address',
                'login_id.required' => 'Login ID field is required',
                'transaction_key.required' => 'Transaction Key field is required',
                'capacity.required' => 'Capacity field is required',
                'capacity.numeric' => 'Capacity must be a number',
                'amount_limit.required' => 'Amount Limit field is required',
                'amount_limit.numeric' => 'Amount Limit must be a number',
                'payment_url.required' => 'Payment URL field is required',
                'payment_url.url' => 'Please provide a valid URL for Payment URL',
                'currency.required' => 'Currency field is required',
                'merchant_type.required' => 'Merchant Type field is required',
                'merchant_type.in' => 'Invalid value for Merchant Type',
                'environment.required' => 'Environment field is required',
                'environment.in' => 'Invalid value for Environment',
                'gateway_id.required' => 'Gateway field is requried.',
                'mmid.required' => 'MMID field is requried.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $payment_method_expigate = PaymentMethodExpigate::create([
                'name' => $request->get('merchant_type', 0) == 0 ? "Expigate" : 'Amazon',
                'email' => $request->email,
                'test_login_id' => $request->test_login_id,
                'test_transaction_key' => $request->test_transaction_key,
                'live_login_id' => $request->login_id,
                'live_transaction_key' => $request->transaction_key,
                'currency' => $request->currency,
                'environment' => $request->get('environment', 1) == 0 ? "Production" : 'Sandbox',
                'mode' => $request->get('environment', 1) == 0 ? 0 : 1,
                'status' => 1,
                'merchant' => $request->merchant,
                'capacity' => $request->capacity,
                'limit' => $request->amount_limit,
                'payment_url' => $request->payment_url,
                'gateway_id' => $request->gateway_id,
                'mmid' => $request->mmid,
            ]);
            return response()->json(['success' => 'Payment Method Expigate created successfully!', 'data' => $payment_method_expigate]);
        } catch (QueryException|\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\PaymentMethodExpigate $method
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        try {
            $method = PaymentMethodExpigate::where('name', '!=', 'Amazon')->findOrFail($id);
            return view('admin.payment-method.expigate.edit', compact('method'));
        } catch (\Exception $e) {
            return back()->with(['error' => 'Record not found'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\PaymentMethodExpigate $method
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        try {
            $method = PaymentMethodExpigate::findOrFail($id);
            return view('admin.payment-method.expigate.edit', compact('method'));
        } catch (\Exception $e) {
            return back()->with(['error' => 'Record not found'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PaymentMethodExpigate $payment_method_expigate
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {

            $rules = [
                'merchant' => 'required|min:3',
                'email' => 'required|email',
                'login_id' => 'required|max:255',
                'transaction_key' => 'required',
                'payment_url' => 'required|url',
                'merchant_type' => 'required|in:0,1',
                'capacity' => 'required|numeric',
                'amount_limit' => 'required|numeric',
                'gateway_id' => 'required',
                'mmid' => 'required',
            ];

            $messages = [
                'merchant.required' => 'Merchant field is required',
                'merchant.min' => 'Merchant must be at least :min characters',
                'email.required' => 'Email field is required',
                'email.email' => 'Please provide a valid email address',
                'login_id.required' => 'Login ID field is required',
                'transaction_key.required' => 'Transaction Key field is required',
                'payment_url.required' => 'Payment URL field is required',
                'payment_url.url' => 'Please provide a valid URL for Payment URL',
                'merchant_type.required' => 'Merchant Type field is required',
                'merchant_type.in' => 'Invalid value for Merchant Type',
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
            $payment_method_expigate = PaymentMethodExpigate::find($id);
            $payment_method_expigate->name = $request->get('merchant_type', 0) == 0 ? "Expigate" : 'Amazon';
            $payment_method_expigate->merchant = $request->merchant;
            $payment_method_expigate->email = $request->email;
            $payment_method_expigate->live_login_id = $request->login_id;
            $payment_method_expigate->live_transaction_key = $request->transaction_key;
            $payment_method_expigate->payment_url = $request->payment_url;
            $payment_method_expigate->capacity = $request->capacity;
            $payment_method_expigate->limit = $request->amount_limit;
            $payment_method_expigate->gateway_id = $request->gateway_id;
            $payment_method_expigate->mmid = $request->mmid;
            $payment_method_expigate->save();

            return response()->json(['success' => 'Payment Method Expigate updated successfully!', 'data' => $payment_method_expigate]);
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
    public function destroy(PaymentMethodExpigate $paymentMethod)
    {
        //
    }


    public function changeMode(Request $request)
    {
        $payment_method = PaymentMethodExpigate::find($request->expigate_id);
        $payment_method->mode = $request->mode;
        $payment_method->save();
        return response()->json(['success' => 'Mode change successfully.']);
    }

    public function changeStatus(Request $request)
    {
        $payment_method = PaymentMethodExpigate::find($request->expigate_id);
        $payment_method->status = $request->status;
        $payment_method->save();

        return response()->json(['success' => 'Mode change successfully.']);
    }

}
