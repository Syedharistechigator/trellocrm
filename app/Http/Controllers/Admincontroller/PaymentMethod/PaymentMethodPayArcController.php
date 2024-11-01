<?php

namespace App\Http\Controllers\Admincontroller\PaymentMethod;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentMethodExpigate;
use App\Models\PaymentMethodPayArc;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentMethodPayArcController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $payment_methods = array();
        foreach (PaymentMethodPayArc::get() as $payment_method_payarc) {
            $monthPayment = Payment::where(['payment_status' => 1, 'payment_gateway' => "PayArc", 'merchant_id' => $payment_method_payarc->id])
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at',  Carbon::now()->year)
                ->sum('amount');
            $payment_method_payarc['paymentMonth'] = (int)$monthPayment;
            $payment_methods[] = $payment_method_payarc;
        }
        return view('admin.payment-method.payarc.index', compact('payment_methods'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.payment-method.payarc.create');
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
                'currency' => 'required',
                'environment' => 'required|in:0,1',
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
                'currency.required' => 'Currency field is required',
                'environment.required' => 'Environment field is required',
                'environment.in' => 'Invalid value for Environment',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $payment_method_payarc = PaymentMethodPayArc::create([
                'name' => 'PayArc',
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
                'limit' => $request->amount_limit,
                'capacity' => $request->capacity,
            ]);
            return response()->json(['success' => 'Payment Method PayArc created successfully!', 'data' => $payment_method_payarc]);
        } catch (QueryException|\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\PaymentMethodPayArc $method
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        try {
            $method = PaymentMethodPayArc::findOrFail($id);
            return view('admin.payment-method.payarc.edit', compact('method'));
        } catch (\Exception $e) {
            return back()->with(['error' => 'Record not found'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\PaymentMethodPayArc $method
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        try {
            $method = PaymentMethodPayArc::findOrFail($id);
            return view('admin.payment-method.payarc.edit', compact('method'));
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
                'merchant' => 'required|min:3',
                'email' => 'required|email',
                'login_id' => 'required|max:255',
                'transaction_key' => 'required',
                'capacity' => 'required|numeric',
                'amount_limit' => 'required|numeric',
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
            ];
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $payment_method_payarc = PaymentMethodPayArc::find($id);
            $payment_method_payarc->merchant = $request->merchant;
            $payment_method_payarc->email = $request->email;
            $payment_method_payarc->live_login_id = $request->login_id;
            $payment_method_payarc->live_transaction_key = $request->transaction_key;
            $payment_method_payarc->capacity = $request->capacity;
            $payment_method_payarc->limit = $request->amount_limit;
            $payment_method_payarc->save();

            return response()->json(['success' => 'Payment Method PayArc updated successfully!', 'data' => $payment_method_payarc]);
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
    public function destroy(PaymentMethodPayArc $paymentMethod)
    {
        //
    }


    public function changeMode(Request $request)
    {
        $payment_method = PaymentMethodPayArc::find($request->payarc_id);
        $payment_method->mode = $request->mode;
        $payment_method->save();
        return response()->json(['success' => 'Mode change successfully.']);
    }

    public function changeStatus(Request $request)
    {
        $payment_method = PaymentMethodPayArc::find($request->payarc_id);
        $payment_method->status = $request->status;
        $payment_method->save();

        return response()->json(['success' => 'Mode change successfully.']);
    }

}
