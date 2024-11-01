<?php

namespace App\Http\Controllers\Admincontroller;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodExpigate;
use App\Models\SplitPayment;
use http\Env\Response;
use Illuminate\Http\Request;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Lead;
use App\Models\User;
use App\Models\Team;
use App\Models\Brand;
use Carbon\Carbon;
use App\Models\AssignBrand;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Project;
use App\Models\PaymentTransactionsLog;
use Illuminate\Support\Facades\Validator;


class adminInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
//    public function index()
//    {
//        $invoiceData = array();
//        $invoices = Invoice::orderBy('created_at', 'desc')->Paginate(15);
//        $teams = Team::where('status', '1')->get();
//        $brands = Brand::where('status', '1')->orderby('name', 'Asc')->get();
//
//        foreach ($invoices as $invoice) {
//            $client_id = $invoice->clientid;
//            $brandKey = $invoice->brand_key;
//            $agent = $invoice->agent_id;
//
//            $client_name = Client::where('id', $client_id)->value('name');
//            $invoice['clientName'] = $client_name;
//
//            $brand = Brand::where('brand_key', $brandKey)->first();
//
//            $invoice['brandName'] = $brand->name;
//            $invoice['brandUrl'] = $brand->brand_url;
//
//            $invoice['agentName'] = User::where(['id' => $agent, 'status' => 1])->value('name');
//
//            array_push($invoiceData, $invoice);
//        }
//
//        //count Upsale
//        $upsale = Invoice::where(['sales_type' => 'Upsale', 'status' => 1])->sum('final_amount');
//        settype($upsale, "integer");
//        //count Fresh
//        $fresh = Invoice::where(['sales_type' => 'Fresh', 'status' => 1])->sum('final_amount');
//        settype($fresh, "integer");
//        //total Paid
//        $totalPaid = Invoice::where('status', 1)->sum('final_amount');
//        settype($totalPaid, "integer");
//        //total unPaid
//        $totalUnpaid = Invoice::where('status', 0)->sum('final_amount');
//        settype($totalUnpaid, "integer");
//
//        $invoiceStatistics = ['upsale' => thousand_format($upsale), 'fresh' => thousand_format($fresh), "totalPaid" => thousand_format($totalPaid), "totalunpaid" => thousand_format($totalUnpaid)];
//
//        $members = User::where('status', 1)->where('type', '!=', 'client')->orderBy('type', 'asc')->get();
//
//        //Team Brand
//        $dataBrand = AssignBrand::all();
//        $teamBrand = array();
//
//        foreach ($dataBrand as $a) {
//            $brand_key = $a->brand_key;
//            $assingBrands = Brand::where('brand_key', $brand_key)->get();
//            foreach ($assingBrands as $brand) {
//                $a['brandKey'] = $brand->brand_key;
//                $a['brandName'] = $brand->name;
//
//                array_push($teamBrand, $a);
//            }
//        }
//        return view('admin.invoice.index', compact('invoiceData', 'invoiceStatistics', 'teams', 'brands', 'members', 'teamBrand', 'invoices'));
//    }
    public function index(Request $request)
    {
        $result = $this->getData($request, new Invoice());
        $invoices = $result['data'];
        $fromDate = $result['fromDate'];
        $toDate = $result['toDate'];
        $teamKey = $result['teamKey'];
        $brandKey = $result['brandKey'];
        $teams = Team::where('status', '1')->get();
        $brands = Brand::where('status', '1')->orderby('name', 'Asc')->get();
        $invoiceStatistics = [
            'upsale' => thousand_format(Invoice::where(['sales_type' => 'Upsale', 'status' => 1])->sum('final_amount')),
            'fresh' => thousand_format(Invoice::where(['sales_type' => 'Fresh', 'status' => 1])->sum('final_amount')),
            'totalPaid' => thousand_format(Invoice::where('status', 1)->sum('final_amount')),
            'totalUnpaid' => thousand_format(Invoice::where('status', 0)->sum('final_amount')),
        ];
        $members = User::where('status', 1)->where('type', '!=', 'client')->orderBy('type')->get();

        $payment_method_authorize = PaymentMethod::where('status', 1)->get();
        $payment_method_expigate = PaymentMethodExpigate::where('status', 1)->get();
        $teamBrand = AssignBrand::all();
        return view('admin.invoice.index', compact('payment_method_authorize', 'payment_method_expigate', 'fromDate', 'toDate', 'teamKey', 'brandKey', 'invoices', 'invoiceStatistics', 'teams', 'brands', 'members', 'teamBrand', 'invoices'));
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
        //

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Invoice|Invoice[]|\Illuminate\Http\Response|\LaravelIdea\Helper\App\Models\_IH_Invoice_C
     */
    public function edit($id)
    {
        return Invoice::find($id);
    }

    public function admin_create_invoice(Request $request)
    {
        $rules = [
            'taxable' => 'required_if:taxable,1',
//            'split_tax' => 'sometimes|integer|in:0,1',
            'tax' => 'required_if:taxable,1',
            'taxAmount' => 'required_if:taxable,1',
            'total_amount' => 'required_if:taxable,1',
//            'is_split' => 'sometimes|integer|in:0,1',
            'is_merchant_handling_fee' => 'sometimes|integer|in:0,1',
//            'split_merchant_handling_fee' => 'sometimes|integer|in:0,1',
            'merchant_handling_fee' => 'required_if:is_merchant_handling_fee,1',
            'client_secret' => 'required_if:parent_id,0|nullable|string',

        ];
        $messages = [
            'taxable.required_if' => 'The Taxable field is required when Tax is enabled.',
            'taxable.in' => 'The Taxable field must be either Yes or No.',
//            'split_tax.integer' => 'The Split Tax field must be an integer.',
//            'split_tax.in' => 'The Split Tax field must be either Yes or No.',

            'tax.required_if' => 'The Tax field is required when Tax is enabled.',
            'taxAmount.required_if' => 'The Tax Amount field is required when Taxable is enabled.',
            'total_amount.required_if' => 'The Total Amount field is required when Taxable is enabled.',
            'is_split.required' => 'The Split Payment field is required.',
            'is_split.integer' => 'The Split Payment field must be an integer.',
            'is_split.in' => 'The Split Payment field must be either Yes or No.',

            'is_merchant_handling_fee.sometimes' => 'The Merchant Handling Fee field is optional.',
            'is_merchant_handling_fee.integer' => 'The Merchant Handling Fee field must be an integer.',
            'is_merchant_handling_fee.in' => 'The Merchant Handling Fee field must be either Yes or No.',

//            'split_merchant_handling_fee.sometimes' => 'The Split Merchant Handling Fee field is optional.',
//            'split_merchant_handling_fee.integer' => 'The Split Merchant Handling Fee field must be an integer.',
//            'split_merchant_handling_fee.in' => 'The Split Merchant Handling Fee field must be either Yes or No.',

            'merchant_handling_fee.required_if' => 'The Merchant Handling Fee field is required when Enable Merchant Handling Fee is selected.',

        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        $creatorid = Auth::user()->id;
//        $is_split = $request->get('is_split', 0);

        $is_tax = $request->get('taxable', 0);
//        $split_tax = $request->get('split_tax',0);

//        $is_merchant_handling_fee = $request->get('is_merchant_handling_fee', 0); /** Temporary Disabled */
        $is_merchant_handling_fee = 0;

//        $split_merchant_handling_fee = $request->get('split_merchant_handling_fee', 0);
        $merchant_handling_fee = $request->get('merchant_handling_fee', 0);

        $teamKey = $request->get('team_key');
        $client_exists = Client::where('email', $request->get('email'))->first();
        $final_amount = $value = $request->get('value');

        if ($is_merchant_handling_fee == 1) {
            $value += $merchant_handling_fee;
        }
        if ($is_tax == 1) {
            $tax_percentage = $request->get('tax');
            $tax_amount = round(($value * $tax_percentage) / 100);
            $total_amount = number_format(round($tax_amount) + $value, 2, '.', '');
        } else {
            $tax_percentage = '0';
            $tax_amount = '0.00';
            $total_amount = $request->get('value');
        }
        if ($client_exists) {
            $project = Project::create([
                'team_key' => $request->get('team_key'),
                'brand_key' => $request->get('brand_key'),
                'creatorid' => $creatorid,
                'clientid' => $client_exists->id,
                'agent_id' => $request->get('agent_id'),
                'asigned_id' => '0',
                'category_id' => '1',
                'project_title' => $request->get('project_title'),
                'project_description' => $request->get('description'),
                'project_status' => '1',
                'project_progress' => '1',
                'project_cost' => $request->get('value')
            ]);

            $projectID = $project->id;

            $invoice_key = random_int(11, 99) . substr(time(), 1, 3) . random_int(11, 99) . substr(time(), 8, 2);
            $invoice = Invoice::create([
                'invoice_num' => 'INV-' . random_int(100000, 999999),
                'invoice_key' => $invoice_key,
                'team_key' => $teamKey,
                'brand_key' => $request->get('brand_key'),
                'creatorid' => $creatorid,
                'clientid' => $client_exists->id,
                'agent_id' => $request->get('agent_id'),
                'due_date' => $request->get('due_date'),
                'invoice_descriptione' => $request->get('description'),
                'sales_type' => $request->get('sales_type'),
                'status' => 'due',
                'project_id' => $projectID,
                'cur_symbol' => $request->get('cur_symbol'),
                'final_amount' => $final_amount,
                'is_tax' => $is_tax,
//                'split_tax' => $split_tax,
                'tax_percentage' => $tax_percentage,
                'tax_amount' => $tax_amount,
                'total_amount' => $total_amount,
                'creator_role' => 'ADM',
//                'is_split' => $is_split,
                'is_merchant_handling_fee' => $is_merchant_handling_fee,
//                'split_merchant_handling_fee' => $split_merchant_handling_fee,
                'merchant_handling_fee' => $merchant_handling_fee,
            ]);
//            if ($total_amount > 3 && $is_split == 1) {
//                SplitPayment::create([
//                    'invoice_id' => $invoice_key,
//                    'amount' => 2,
//                ]);
//                SplitPayment::create([
//                    'invoice_id' => $invoice_key,
//                    'amount' => 1,
//                ]);
//            }
        } else {

            $client = Client::create([
                'team_key' => $teamKey,
                'brand_key' => $request->get('brand_key'),
                'creatorid' => $creatorid,
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'agent_id' => $request->get('agent_id'),
                'client_created_from_leadid' => 0,
                'client_description' => "",
                'address' => "",
                'status' => '1'
            ]);

            $clientID = $client->id;

            $users = User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'password' => Hash::make('12345678'),
                'type' => 'client',
                'team_key' => $teamKey,
                'clientid' => $clientID
            ]);

            if ($clientID) {
                $project = Project::create([
                    'team_key' => $teamKey,
                    'brand_key' => $request->get('brand_key'),
                    'creatorid' => $creatorid,
                    'clientid' => $clientID,
                    'agent_id' => $request->get('agent_id'),
                    'asigned_id' => '0',
                    'category_id' => '1',
                    'project_title' => $request->get('project_title'),
                    'project_description' => "",
                    'project_status' => '1',
                    'project_progress' => '1',
                    'project_cost' => $request->get('value')

                ]);
            }
            $projectID = $project->id;

            $invoice_key = random_int(11, 99) . substr(time(), 1, 3) . random_int(11, 99) . substr(time(), 8, 2);
            $invoice = Invoice::create([
                'invoice_num' => 'INV-' . random_int(100000, 999999),
                'invoice_key' => $invoice_key,
                'team_key' => $teamKey,
                'brand_key' => $request->get('brand_key'),
                'creatorid' => $creatorid,
                'clientid' => $clientID,
                'agent_id' => $request->get('agent_id'),
                'final_amount' => $final_amount,
                'due_date' => $request->get('due_date'),
                'sales_type' => $request->get('sales_type'),
                'status' => 'due',
                'project_id' => $projectID,
                'invoice_descriptione' => $request->get('description'),
                'cur_symbol' => $request->get('cur_symbol'),
                'is_tax' => $is_tax,
                'tax_percentage' => $tax_percentage,
                'tax_amount' => $tax_amount,
                'total_amount' => $total_amount,
                'creator_role' => 'ADM',
                'is_merchant_handling_fee' => $is_merchant_handling_fee,
                'merchant_handling_fee' => $merchant_handling_fee,

//                'is_split' => $is_split,
            ]);
//            if ($total_amount > 3 && $is_split == 1) {
//                SplitPayment::create([
//                    'invoice_id' => $invoice_key,
//                    'amount' => 2,
//                ]);
//                SplitPayment::create([
//                    'invoice_id' => $invoice_key,
//                    'amount' => 1,
//                ]);
//            }
            //$invoiceData =  array_merge($client->toArray(),$invoice->toArray());

            //Notification::send($client, new invoiceNotification($invoiceData));
        }
        unset($client_exists);

        return response()->json([
            'message' => 'Invoice successfully Created!'
        ], 200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'edit_taxable' => 'required_if:edit_taxable,1',
            'edit_tax' => 'required_if:edit_taxable,1',
            'edit_taxAmount' => 'required_if:edit_taxable,1',
            'edit_total_amount' => 'required_if:edit_taxable,1',
//            'edit_is_split' => 'required|integer|in:0,1',
        ];
        $messages = [
            'edit_tax.required_if' => 'The Tax field is required when Taxable is enabled.',
            'edit_taxAmount.required_if' => 'The Tax Amount field is required when Taxable is enabled.',
            'edit_total_amount.required_if' => 'The Total Amount field is required when Taxable is enabled.',
            'edit_is_split.required' => 'The Split Payment field is required.',
            'edit_is_split.integer' => 'The Split Payment field must be an integer.',
            'edit_is_split.in' => 'The Split Payment field must be either Yes or No.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        if ($request->get('edit_taxable') == 1) {
            $value = $request->get('value');
            $tax_percentage = $request->edit_tax;
            $tax_amount = round(($value * $tax_percentage) / 100);
            $total_amount = number_format(round($tax_amount) + $value, 2, '.', '');

        } else {
            $tax_percentage = '0';
            $tax_amount = '0.00';
            $total_amount = $request->get('value');
        }

        $invoice = Invoice::find($id);
        if (!$invoice) {
            return response()->json(['errors' => 'Oops! Invoice not found.'], 404);
        }
        /**Initializing for split condition*/
        $invoice_key = $invoice->invoice_key;
//        $is_split = $request->get('edit_is_split');

        $invoice->brand_key = $request->brand_key;
        $invoice->agent_id = $request->agent_id;
        $invoice->sales_type = $request->sales_type;
        $invoice->final_amount = $request->value;
        $invoice->due_date = $request->due_date;
        $invoice->status = 'due';
        $invoice->invoice_descriptione = $request->description;
        $invoice->cur_symbol = $request->edit_cur_symbol;
        $invoice->tax_percentage = $tax_percentage;
        $invoice->tax_amount = $tax_amount;
        $invoice->total_amount = $total_amount;
//        $invoice->is_split = $is_split;
        $invoice->save();

//        if ($invoice->status == "due") {
//            /** 0 = No (Complete payment) */
//            /** Now first we have to check split payment condition if is split == no then we have to delete record of split payments*/
//            if ($is_split == 0) {
//                /** Deleting both split payment records */
//                SplitPayment::where('invoice_id', $invoice_key)->delete();
//            } else {
//                /** Else split payment condition will be true ,and we have to create split payment records*/
//
//                /** Check if there are any soft-deleted split payment records */
//                $trashedSplitRecords = SplitPayment::onlyTrashed()->where('invoice_id', $invoice_key)->get();
//                /** Then we have to check record already exists or not*/
//                $split_records = SplitPayment::where('invoice_id', $invoice_key)->get();
//
//                if ($trashedSplitRecords->isNotEmpty()) {
//                    SplitPayment::onlyTrashed()->where('invoice_id', $invoice_key)->restore();
//                } elseif ($split_records->isEmpty()) {
//                    /** If not then we have to check minimum total amount , which will be greater than "3" */
//                    if ($total_amount > 3) {
//                        /**Creating first split payment*/
//                        SplitPayment::create(['invoice_id' => $invoice_key, 'amount' => 2,]);
//                        /**Creating second split payment*/
//                        SplitPayment::create(['invoice_id' => $invoice_key, 'amount' => 1,]);
//                    } else {
//                        /** Returning error response if total amount is less than "3" */
//                        return response()->json(['error' => 'Invoice total amount is less then the limit.']);
//                    }
//                }
//                /** Note : If split payment is dynamic please create condition for that e.g. = split payment 1st amount and 2nd amount  */
//            }
//        }

        return $invoice;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Invoice::find($id)->delete();
    }


    // created by Shabir
    public function team_Invoices(Request $request)
    {

        $searchText = $request->search;

        $invoiceData = array();

        if ($searchText == 0) {
            $teamInvoice = Invoice::orderBy('created_at', 'desc')->get();
        } else {
            $teamInvoice = Invoice::where('team_key', $searchText)->orderBy('created_at', 'desc')->get();
        }

        $html = "";
        $html .= '<thead><tr><th>ID #</th><th>Invoice #</th><th>Date</th><th>Brand</th><th>Agent</th><th>Name</th><th>Amount</th><th>Sales Type</th><th data-breakpoints="sm xs">Due Date</th><th data-breakpoints="xs md">Status</th><th data-breakpoints="sm xs md">Action</th></tr></thead>';

        $html .= '<tbody>';

        foreach ($teamInvoice as $invoice) {

            $client_id = $invoice->clientid;
            $brandKey = $invoice->brand_key;
            $agent = $invoice->agent_id;

            $client_name = Client::where('id', $client_id)->value('name');
            $invoice['clientName'] = $client_name;

            $brand = Brand::where('brand_key', $brandKey)->first();
            $invoice['brandName'] = $brand->name;
            $invoice['brandUrl'] = $brand->brand_url;

            $invoice['agentName'] = User::where(['id' => $agent, 'status' => 1])->value('name');


            array_push($invoiceData, $invoice);

            $html .= '<tr>';
            $html .= '<td class="align-middle">' . $invoice->id . '</td>';
            $html .= '<td class="align-middle">' . $invoice->invoice_num . '</td>';

            $html .= '<td>' . $invoice->created_at->format('j F, Y') . "<br>" . $invoice->created_at->format('h:i:s A') . "<br>" . $invoice->created_at->diffForHumans() . '</td>';

            $html .= '<td>' . $invoice->brandName . '</td>';
            $html .= '<td>' . $invoice->agentName . '</td>';
            $html .= '<td> <a class="text-warning" href="' . route('clientadmin.show', $invoice->clientid) . '">' . $invoice->clientName . '</a></td>';
            $html .= '<td class="align-middle">Amount: $' . $invoice->final_amount . "<br>Tax " . $invoice->tax_percentage . '% : $' . $invoice->tax_amount . 'Net Amount: ' . $invoice->total_amount . '</td>';

            $received_amount = $invoice->cur_symbol . " " . ($invoice->total_amount - 3) . ($invoice->status == 'paid' ? " Paid" : ' Unpaid') . " <br> ";
            if (isset($invoice->splitPayments) && $invoice->splitPayments->count() > 0) {
                foreach ($invoice->splitPayments as $spKey => $skVal) {
                    $received_amount .= $invoice->cur_symbol . " " . $skVal->amount . " " . (($skVal->status == 1) ? 'Paid' : 'Unpaid');
                    $received_amount .= ' <br> ';
                }
            }
            $html .= '<td class="align-middle  text-nowrap">' . $received_amount . '</td>';


            $html .= '<td class="align-middle">' . $invoice->sales_type . '</td>';

            $now = \Carbon\Carbon::now();

            if ($invoice->due_date >= $now or $invoice->status == 'paid') {
                $color = 'success';
            } else {
                $color = 'danger';
            }
            $html .= '<td class="align-middle"><span class="badge badge-' . $color . ' rounded-pill xtext-' . $color . '">' . \Carbon\Carbon::parse($invoice->due_date)
                    ->format('d/m/Y') . '</span> </td> ';

            if ($invoice->status == 'draft') {
                $invoicestatus = '<span class="badge bg-grey rounded-pill">Draft</span>';
            } elseif ($invoice->status == 'due') {

                $invoicestatus = '<span class="badge bg-amber rounded-pill">Due</span>';

            } elseif ($invoice->status == 'refund') {
                $invoicestatus = '<span class="badge bg-pink rounded-pill">Refund</span>';
            } elseif ($invoice->status == 'chargeback') {
                $invoicestatus = '<span class="badge bg-red rounded-pill">Charge Back</span>';
            } else {
                $invoicestatus = '<span class="badge badge-success rounded-pill">Paid</span>';
            }
            $html .= '<td class="align-middle">' . $invoicestatus . '</td>';
            $html .= '<td class="align-middle">';
            $html .= '<button Title="Copy Invoice URL"  id="' . $invoice->brandUrl . 'checkout?invoicekey=' . $invoice->invoice_key . '" class="btn badge-success btn-sm btn-round copy-url"><i class="zmdi zmdi-copy"></i></button>';
            if (isset($invoice->getBrand) && $invoice->getBrand->is_paypal == 1) {
                $html .= '<button Title="Copy Paypal Invoice URL"  id="' . $invoice->brandUrl . 'checkout/paypal.php?invoicekey=' . $invoice->invoice_key . '" class="btn badge-paypal btn-sm btn-round copy-url"><i class="zmdi zmdi-copy"></i></button>';
            }
            $html .= ' <a title="Change Status" data-id="" data-type="confirm" href="javascript:void(0);" class="btn btn-info btn-sm btn-round statusChange" data-toggle="modal" data-target="#changeStatusModal"><i class="zmdi zmdi-settings"></i></a>';
            if ($invoice->status == 'paid' && (Auth::guard('admin')->user()->type == 'super' || Auth::guard('admin')->user()->type == 'admin')) {
                $html .= '<button data-id = "' . $invoice->id . '" title = "payments"
                                                class="btn badge-money btn-sm btn-round viewPaymentInvoice"
                                                data-toggle = "modal"
                                                data-target = "#viewPaymentInvoiceModal" ><i class="zmdi zmdi-money" ></i >
                                        </button >';
            }
            if (Auth::guard('admin')->user()->type == 'super') {
                $html .= '<a title="Delete" data-id="' . $invoice->id . '" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton"><i class="zmdi zmdi-delete"></i></a>';
            }
            $html .= '</td>';
            $html .= '</tr>';

        }

        $html .= '</tbody>';
        return $html;
    }


    // created by Shabir
    public function brand_Invoicess(Request $request)
    {

        $searchText = $request->search;

        $invoiceData = array();

        if ($searchText == '0') {
            $brandInvoice = Invoice::orderBy('id', 'desc')->get();
        } else {
            $brandInvoice = Invoice::where('brand_key', $searchText)->orderBy('id', 'desc')
                ->get();
        }

        $html = "";
        $html .= '<thead><tr><th>ID #</th><th>Invoice #</th><th>Date</th><th>Brand</th><th>Agent</th><th>Name</th><th>Amount</th><th>Received Amount</th><th>Sales Type</th><th data-breakpoints="sm xs">Due Date</th><th data-breakpoints="xs md">Status</th><th data-breakpoints="sm xs md">Action</th></tr></thead>';

        $html .= '<tbody>';

        foreach ($brandInvoice as $invoice) {
            $client_id = $invoice->clientid;
            $brandKey = $invoice->brand_key;
            $agent = $invoice->agent_id;

            $client_name = Client::where('id', $client_id)->value('name');
            $invoice['clientName'] = $client_name;

            $brand = Brand::where('brand_key', $brandKey)->first();
            $invoice['brandName'] = $brand->name;
            $invoice['brandUrl'] = $brand->brand_url;

            $invoice['agentName'] = User::where(['id' => $agent, 'status' => 1])->value('name');


            array_push($invoiceData, $invoice);

            $html .= '<tr>';
            $html .= '<td class="align-middle">' . $invoice->id . '</td>';
            $html .= '<td class="align-middle">' . $invoice->invoice_num . '</td>';

            $html .= '<td>' . $invoice->created_at->format('j F, Y') . "<br>" . $invoice->created_at->format('h:i:s A') . "<br>" . $invoice->created_at->diffForHumans() . '</td>';

            $html .= '<td>' . $invoice->brandName . '</td>';
            $html .= '<td>' . $invoice->agentName . '</td>';
            $html .= '<td> <a class="text-warning" href="' . route('clientadmin.show', $invoice->clientid) . '">' . $invoice->clientName . '</a></td>';
            $html .= '<td class="align-middle">Amount: $' . $invoice->final_amount . "<br>Tax " . $invoice->tax_percentage . '% : $' . $invoice->tax_amount . 'Net Amount: ' . $invoice->total_amount . '</td>';

            $received_amount = $invoice->cur_symbol . " " . ($invoice->total_amount - 3) . ($invoice->status == 'paid' ? " Paid" : ' Unpaid') . " <br> ";
            if (isset($invoice->splitPayments) && $invoice->splitPayments->count() > 0) {
                foreach ($invoice->splitPayments as $spKey => $skVal) {
                    $received_amount .= $invoice->cur_symbol . " " . $skVal->amount . " " . (($skVal->status == 1) ? 'Paid' : 'Unpaid');
                    $received_amount .= ' <br> ';
                }
            }
            $html .= '<td class="align-middle  text-nowrap">' . $received_amount . '</td>';

            $html .= '<td class="align-middle">' . $invoice->sales_type . '</td>';

            $now = \Carbon\Carbon::now();

            if ($invoice->due_date >= $now or $invoice->status == 'paid') {
                $color = 'success';
            } else {
                $color = 'danger';
            }
            $html .= '<td class="align-middle"><span class="badge badge-' . $color . ' rounded-pill xtext-' . $color . '">' . \Carbon\Carbon::parse($invoice->due_date)
                    ->format('d/m/Y') . '</span> </td> ';

            if ($invoice->status == 'draft') {
                $invoicestatus = '<span class="badge bg-grey rounded-pill">Draft</span>';
            } elseif ($invoice->status == 'due') {

                $invoicestatus = '<span class="badge bg-amber rounded-pill">Due</span>';

            } elseif ($invoice->status == 'refund') {
                $invoicestatus = '<span class="badge bg-pink rounded-pill">Refund</span>';
            } elseif ($invoice->status == 'chargeback') {
                $invoicestatus = '<span class="badge bg-red rounded-pill">Charge Back</span>';
            } else {
                $invoicestatus = '<span class="badge badge-success rounded-pill">Paid</span>';
            }
            $html .= '<td class="align-middle">' . $invoicestatus . '</td>';
            $html .= '<td class="align-middle text-nowrap">';
            $html .= '<button Title="Copy Invoice URL"  id="' . $invoice->brandUrl . 'checkout?invoicekey=' . $invoice->invoice_key . '" class="btn badge-success btn-sm btn-round copy-url"><i class="zmdi zmdi-copy"></i></button>';
            if (isset($invoice->getBrand) && $invoice->getBrand->is_paypal == 1) {
                $html .= '<button Title="Copy Paypal Invoice URL"  id="' . $invoice->brandUrl . 'checkout/paypal.php?invoicekey=' . $invoice->invoice_key . '" class="btn badge-paypal btn-sm btn-round copy-url"><i class="zmdi zmdi-copy"></i></button>';
            }
            $html .= ' <a title="Change Status" data-id="" data-type="confirm" href="javascript:void(0);" class="btn btn-info btn-sm btn-round statusChange" data-toggle="modal" data-target="#changeStatusModal"><i class="zmdi zmdi-settings"></i></a>';
            if ($invoice->status == 'paid' && (Auth::guard('admin')->user()->type == 'super' || Auth::guard('admin')->user()->type == 'admin')) {
                $html .= '<button data-id = "' . $invoice->id . '" title = "payments"
                                                class="btn badge-money btn-sm btn-round viewPaymentInvoice"
                                                data-toggle = "modal"
                                                data-target = "#viewPaymentInvoiceModal" ><i class="zmdi zmdi-money" ></i >
                                        </button >';

            }
            if (Auth::guard('admin')->user()->type == 'super') {
                $html .= '<a title="Delete" data-id="' . $invoice->id . '" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton"><i class="zmdi zmdi-delete"></i></a>';
            }
            $html .= '</td>';
            $html .= '</tr>';


        }

        $html .= '</tbody>';
        return $html;
    }

    public
    function brandteamAgent(Request $request)
    {

        $searchText = $request->search;

        $members = User::where(['team_key' => $searchText, 'status' => 1])->where('type', '!=', 'client')->orderBy('type', 'asc')->get();
        return $members;
    }

// old code
// public function admin_create_invoice(Request $request)
// {

//     $creatorid  = Auth::user()->id;
//     $clientId = $request->get('client_id');
//     $teamKey    = $request->get('team_key');

//     if($request->get('taxable') == 1){
//         $tax_percentage = $request->get('tax');
//         $tax_amount = $request->get('taxAmount');
//         $total_amount = $request->get('total_amount');
//     }else{
//         $tax_percentage = '0';
//         $tax_amount = '0.00';
//         $total_amount = $request->get('value');
//     }

//     if($clientId > 0)
//     {
//         $clientData = Client::where('id',$clientId)->first();

//         $invoice = Invoice::create([
//             'invoice_num'   => 'INV-'.random_int(100000, 999999),
//             'invoice_key'   => random_int(100000, 999999),
//             'team_key'      => $teamKey,
//             'brand_key'     => $request->get('brand_key'),
//             'creatorid'     => $creatorid,
//             'clientid'      => $clientId,
//             'agent_id'      => $request->get('agent_id'),
//             'final_amount'  => $request->get('value'),
//             'due_date'      => $request->get('due_date'),
//             'sales_type'    => $request->get('sales_type'),
//             'status'        => 'due',
//             'project_id'    => $request->get('project_id'),
//             'invoice_descriptione'    => $request->get('description'),
//             'cur_symbol'    => $request->get('cur_symbol'),
//             'tax_percentage' => $tax_percentage,
//             'tax_amount' => $tax_amount,
//             'total_amount' => $total_amount,
//             'creator_role' => 'ADM'
//         ]);

//         $additionalData = ['name' => $clientData->name];

//        // $invoiceData =  array_merge($invoice->toArray(),$additionalData);

//         //Notification::send($clientData, new invoiceNotification($invoiceData));

//     }else{

//         $client = Client::create([
//             'team_key'  => $teamKey,
//             'brand_key' => $request->get('brand_key'),
//             'creatorid' => $creatorid,
//             'name'      => $request->get('name'),
//             'email'     => $request->get('email'),
//             'phone'     => $request->get('phone'),
//             'agent_id'  => $request->get('agent_id'),
//             'client_created_from_leadid' => 0,
//             'client_description'  => "",
//             'address' => "",
//             'status' => '1'
//         ]);

//         $clientID = $client->id;

//         $users = User::create([
//             'name'          => $request->get('name'),
//             'email'         => $request->get('email'),
//             'phone'         => $request->get('phone'),
//             'password'      => Hash::make('12345678'),
//             'type'          => 'client',
//             'team_key'      => $teamKey,
//             'clientid'      => $clientID
//         ]);

//         if($clientID){
//             $project = Project::create([
//                 'team_key'  => $teamKey,
//                 'brand_key' => $request->get('brand_key'),
//                 'creatorid' => $creatorid,
//                 'clientid'  => $clientID,
//                 'agent_id'  => $request->get('agent_id'),
//                 'asigned_id' => '0',
//                 'category_id'  => '1',
//                 'project_title'  => $request->get('project_title'),
//                 'project_description'  => "",
//                 'project_status'  => '1',
//                 'project_progress' => '1',
//                 'project_cost' => $request->get('value')

//             ]);
//         }
//         $projectID = $project->id;

//         $invoice = Invoice::create([
//             'invoice_num'   => 'INV-'.random_int(100000, 999999),
//             'invoice_key'   => random_int(100000, 999999),
//             'team_key'      => $teamKey,
//             'brand_key'     => $request->get('brand_key'),
//             'creatorid'     => $creatorid,
//             'clientid'      => $clientID,
//             'agent_id'      => $request->get('agent_id'),
//             'final_amount'  => $request->get('value'),
//             'due_date'      => $request->get('due_date'),
//             'sales_type'    => $request->get('sales_type'),
//             'status'        => 'due',
//             'project_id'    => $projectID,
//             'invoice_descriptione'    => $request->get('description'),
//             'cur_symbol'    => $request->get('cur_symbol'),
//             'tax_percentage' => $tax_percentage,
//             'tax_amount' => $tax_amount,
//             'total_amount' => $total_amount,
//             'creator_role' => 'ADM'
//         ]);

//         //$invoiceData =  array_merge($client->toArray(),$invoice->toArray());

//         //Notification::send($client, new invoiceNotification($invoiceData));
//     }

//     return response()->json([
//         'message' => 'Create Invoice Successfully!'
//     ], 200);

// }

    public function payment_trans_log($id = null)
    {

        if (!empty($id)) {
            $transLog = PaymentTransactionsLog::where('invoiceid', $id)->orderBy('created_at', 'desc')->get();
        } else {
            $transLog = PaymentTransactionsLog::orderBy('created_at', 'desc')->get();
        }


        return view('admin.invoice.transLog', compact('transLog'));
    }

    public function view_payment_invoice(Invoice $invoice)
    {
        if (isset($invoice->id) && $invoice->id > 0 && isset($invoice->splitPayments) && $invoice->splitPayments->count() > 0) {


            $first_payment = $invoice->cur_symbol . (" " . ($invoice->total_amount - 3)) . " - " . $invoice->status;
            $second_payment = "";
            $third_payment = "";
            if (isset($invoice->splitPayments) && $invoice->splitPayments->count() > 0) {
                $second_payment = $invoice->cur_symbol . ' ' . 2 . " - " . ($invoice->splitPayments[0]->status == 1 ? 'Paid' : 'Unpaid');
                $third_payment = $invoice->cur_symbol . ' ' . 1 . " - " . ($invoice->splitPayments[1]->status == 1 ? 'Paid' : 'Unpaid');
            }
            $data = [
                'first_payment' => $first_payment,
                'second_payment' => $second_payment,
                'third_payment' => $third_payment,
            ];
            return response()->json($data, 200);
        } else {
            return response()->json(['error' => 'not found'], 400);
        }
    }
}

