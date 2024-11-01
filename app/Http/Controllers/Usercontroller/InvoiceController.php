<?php

namespace App\Http\Controllers\Usercontroller;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Lead;
use App\Models\SplitPayment;
use App\Models\Team;
use App\Models\User;
use App\Models\Brand;
use App\Models\Project;
use App\Models\AssignBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\invoiceNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;
use DB;
use Illuminate\Support\Facades\Validator;


class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
//    public function index()
//    {
//        $id = Auth::user()->team_key;
//        $type = Auth::user()->type;
//        $staff_Div = Auth::user()->staff_division;
//        $agentId = Auth::user()->id;
//
//        //Team Client
//        $teamClients = Client::where('team_key', $id)->get();
//
//        //Team Brand
//        if ($type == 'ppc') {
//            $data = AssignBrand::all();
//        } else {
//            $data = AssignBrand::where('team_key', $id)->get();
//        }
//
//        $teamBrand = array();
//
//        foreach ($data as $a) {
//            $brand_key = $a->brand_key;
//            $brands = Brand::where('brand_key', $brand_key)->get();
//            foreach ($brands as $brand) {
//                $a['brandKey'] = $brand->brand_key;
//                $a['brandName'] = $brand->name;
//                array_push($teamBrand, $a);
//            }
//        }
//
//        //Team Members
//
//        $members = User::where(['team_key' => $id, 'status' => 1])->where('type', '!=', 'client')->orderBy('type', 'asc')->get();
//
//
//        // Team Invoice
//        $invoiceData = array();
//
//        // if($type == 'staff'){
//        //     $invoices = Invoice::where(['team_key'=>$id,'agent_id'=>$agentId])->get();
//        // }elseif($type == 'hob' or $type == 'qa'){
//        //     $invoices = Invoice::all();
//        // }else{
//        //     $invoices = Invoice::where('team_key' , $id )->get();
//        // }
//
//        // if($type == 'ppc' or $type == 'qa'){
//        //     $invoices = Invoice::all();
//        // }else{
//        //     $invoices = Invoice::where('team_key' , $id )->get();
//        // }
//
//        if ($type == 'ppc' or $type == 'qa') {
//            $invoices = Invoice::orderBy('id', 'desc')->Paginate(15);
//        } else {
//            //$invoices = Invoice::where('team_key' , $id )->get();
////            $invoices = Invoice::where('team_key', $id)->orderBy('id', 'desc')->Paginate(15);
//
//            $brandKeys = array_column($teamBrand, 'brandKey');
//            $invoices = Invoice::whereIn('brand_key', $brandKeys)
//                ->orderBy('id', 'desc')
//                ->Paginate(15);
//        }
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
//            $cur_symbol = $invoice->cur_symbol;
//            if ($cur_symbol == 'EUR') {
//                $currency_symbol = '€';
//            } elseif ($cur_symbol == 'GBP') {
//                $currency_symbol = '£';
//            } elseif ($cur_symbol == 'AUD') {
//                $currency_symbol = 'A$';
//            } elseif ($cur_symbol == 'CAD') {
//                $currency_symbol = 'C$';
//            } else {
//                $currency_symbol = '$';
//            }
//            $invoice['currency_symbol'] = $currency_symbol;
//
//            array_push($invoiceData, $invoice);
//        }
//
//        //count Upsale
//        $upsale = Invoice::where(['team_key' => $id, 'sales_type' => 'Upsale', 'status' => 'paid'])->sum('final_amount');
//        settype($upsale, "integer");
//        //count Fresh
//        $fresh = Invoice::where(['team_key' => $id, 'sales_type' => 'Fresh', 'status' => 'paid'])->sum('final_amount');
//        settype($fresh, "integer");
//        //total Paid
//        $totalPaid = Invoice::where(['team_key' => $id, 'status' => 'paid'])->sum('final_amount');
//        settype($totalPaid, "integer");
//        //total unPaid
//        $totalUnpaid = Invoice::where(['team_key' => $id, 'status' => 'due'])->sum('final_amount');
//        settype($totalUnpaid, "integer");
//
//        $invoiceStatistics = [
//            'upsale' => thousand_format($upsale),
//            'fresh' => thousand_format($fresh),
//            "totalPaid" => thousand_format($totalPaid),
//            "totalunpaid" => thousand_format($totalUnpaid)
//        ];
//
//
//        return view('invoice.index', compact('invoiceData', 'teamClients', 'teamBrand', 'members', 'invoiceStatistics', 'invoices'));
//    }
    public function index(Request $request)
    {
        $team_key = Auth::user()->team_key;
        $type = Auth::user()->type;
        $teams = [];
        /** Team Brands */
        if ($type === 'ppc') {
            $assignedTeams = auth()->user()->assigned_teams ?? [];
            if (auth()->user()->team_key && !in_array(auth()->user()->team_key, $assignedTeams)) {
                $assignedTeams[] = auth()->user()->team_key;
            }
            $assign_brands = AssignBrand::whereIn('team_key', $assignedTeams)->whereHas('getBrandWithOutTrashed')->get();
            //Team Members
            $members = User::whereIn('team_key', $assignedTeams)->where('status', 1)->where('type', '!=', 'client')->get();
            $teams = Team::whereIn('team_key', $assignedTeams)->where('status', '1')->get();
        } else {
            $assign_brands = AssignBrand::whereHas('getBrandWithOutTrashed')->where('team_key', $team_key)->get();
            //Team Members
            $members = User::where('team_key', $team_key)->where('status', 1)->where('type', '!=', 'client')->get();
        }
        //Team Client
        $teamClients = Client::whereIn('brand_key', $assign_brands->pluck('brand_key'))->get();


        $query = new Invoice();
        if ($type === 'tm-ppc') {
            $query = $query->where('team_key', $team_key)->where('sales_type', 'Fresh');
        }
        $result = $this->userDataFilter($request, $query, $assign_brands);
        $brandKey = $result['brandKey'];
        $fromDate = $result['fromDate'];
        $toDate = $result['toDate'];
        $invoices = $result['data'];
        $currencySymbols = ['EUR' => '€', 'GBP' => '£', 'AUD' => 'A$', 'CAD' => 'C$',];
        $invoiceStatistics = [
            'upsale' => thousand_format((int)Invoice::where(['team_key' => $team_key, 'sales_type' => 'Upsale', 'status' => 'paid'])->sum('final_amount')),
            'fresh' => thousand_format((int)Invoice::where(['team_key' => $team_key, 'sales_type' => 'Fresh', 'status' => 'paid'])->sum('final_amount')),
            "totalPaid" => thousand_format((int)Invoice::where(['team_key' => $team_key, 'status' => 'paid'])->sum('final_amount')),
            "totalunpaid" => thousand_format((int)Invoice::where(['team_key' => $team_key, 'status' => 'due'])->sum('final_amount'))
        ];

        return view('invoice.index', compact('currencySymbols', 'teams', 'brandKey', 'fromDate', 'toDate', 'assign_brands', 'teamClients', 'members', 'invoiceStatistics', 'invoices'));
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $rules = [
            'taxable' => 'required_if:taxable,1',
            'tax' => 'required_if:taxable,1',
            'taxAmount' => 'required_if:taxable,1',
            'total_amount' => 'required_if:taxable,1',
        ];
        $messages = [
            'tax.required_if' => 'The Tax field is required when Taxable is enabled.',
            'taxAmount.required_if' => 'The Tax Amount field is required when Taxable is enabled.',
            'total_amount.required_if' => 'The Total Amount field is required when Taxable is enabled.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        if (Auth::user()->type === 'ppc') {
            $assignedTeams = auth()->user()->assigned_teams ?? [];
            if (auth()->user()->team_key && !in_array(auth()->user()->team_key, $assignedTeams)) {
                $assignedTeams[] = auth()->user()->team_key;
            }
            $teamKey = $request->get('team_key');

            if (!in_array($teamKey, $assignedTeams)) {
                return response()->json([
                    'errors' => 'Please select a valid team.',
                ], 400);
            }
            /** Team Brands */
            $assign_brands = AssignBrand::where('team_key', $teamKey)->whereHas('getBrandWithOutTrashed')->get()->pluck('brand_key')->toArray();
            if (!in_array($request->get('brand_key'), $assign_brands)) {
                return response()->json([
                    'errors' => 'Brand not assigned to this team.',
                ], 400);
            }
        } else {
            $teamKey = auth()->user()->team_key;
            /** Team Brands */
            $assign_brands = AssignBrand::where('team_key', $teamKey)->whereHas('getBrandWithOutTrashed')->get()->pluck('brand_key')->toArray();
            if (!in_array($request->get('brand_key'), $assign_brands)) {
                return response()->json([
                    'errors' => 'Oops! Brand not assigned to your team.',
                ], 400);
            }
        }

        $creatorid = Auth::user()->id;

        $client_exists = Client::where('email', $request->get('email'))->first();


        if ($request->get('taxable') == 1) {
            $tax_percentage = $request->get('tax');
            $tax_amount = $request->get('taxAmount');
            $total_amount = number_format((float)(($request->get('value') * $tax_percentage) / 100) + $request->get('value'), 2, '.', '');
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
                'agent_id' => $request->get('agent'),
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
                'team_key' => $request->get('team_key'),
                'brand_key' => $request->get('brand_key'),
                'creatorid' => $creatorid,
                'clientid' => $client_exists->id,
                'agent_id' => $request->get('agent'),
                'due_date' => $request->get('due_date'),
                'invoice_descriptione' => $request->get('description'),
                'sales_type' => $request->get('sales_type', 'Fresh'),
                'status' => 'due',
                'project_id' => $projectID,
                'cur_symbol' => $request->get('cur_symbol'),
                'final_amount' => $request->get('value'),
                'tax_percentage' => $tax_percentage,
                'tax_amount' => $tax_amount,
                'total_amount' => $total_amount,
//                'is_split' => $request->get('is_split'),
                'is_split' => 0,
            ]);
//            $is_split = $request->get('is_split');
            $is_split = 0;
            if ($total_amount > 3 && $is_split == 1) {
                SplitPayment::create([
                    'invoice_id' => $invoice_key,
                    'amount' => 2,
                ]);
                SplitPayment::create([
                    'invoice_id' => $invoice_key,
                    'amount' => 1,
                ]);
            }
            //$additionalData = ['name' => $client_exists->name];

            // $invoiceData =  array_merge($invoice->toArray(),$additionalData);

            // Notification::send($client_exists, new invoiceNotification($invoiceData));

        } else {

            $client = Client::create([
                'team_key' => $request->get('team_key'),
                'brand_key' => $request->get('brand_key'),
                'creatorid' => $creatorid,
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'agent_id' => $request->get('agent'),
                'client_created_from_leadid' => $request->get('lead_id'),
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
                'team_key' => $request->get('team_key'),
                'clientid' => $clientID
            ]);

            if ($clientID) {
                $project = Project::create([
                    'team_key' => $request->get('team_key'),
                    'brand_key' => $request->get('brand_key'),
                    'creatorid' => $creatorid,
                    'clientid' => $clientID,
                    'agent_id' => $request->get('agent'),
                    'asigned_id' => '0',
                    'category_id' => '1',
                    'project_title' => $request->get('project_title'),
                    'project_description' => $request->get('description'),
                    'project_status' => '1',
                    'project_progress' => '1',
                    'project_cost' => $request->get('value')

                ]);
            }
            $projectID = $project->id;
            if ($projectID) {
                $invoice_key = random_int(11, 99) . substr(time(), 1, 3) . random_int(11, 99) . substr(time(), 8, 2);
                $invoice = Invoice::create([
                    'invoice_num' => 'INV-' . random_int(100000, 999999),
                    'invoice_key' => $invoice_key,
                    'team_key' => $request->get('team_key'),
                    'brand_key' => $request->get('brand_key'),
                    'creatorid' => $creatorid,
                    'clientid' => $clientID,
                    'agent_id' => $request->get('agent'),
                    'final_amount' => $request->get('value'),
                    'due_date' => $request->get('due_date'),
                    'invoice_descriptione' => $request->get('description'),
                    'sales_type' => $request->get('sales_type', 'Fresh'),
                    'status' => 'due',
                    'project_id' => $projectID,
                    'cur_symbol' => $request->get('cur_symbol'),
                    'tax_percentage' => $tax_percentage,
                    'tax_amount' => $tax_amount,
                    'total_amount' => $total_amount,
//                'is_split' => $request->get('is_split'),
                    'is_split' => 0,
                ]);
//            $is_split = $request->get('is_split');
                $is_split = 0;
                if ($total_amount > 3 && $is_split == 1) {
                    SplitPayment::create([
                        'invoice_id' => $invoice_key,
                        'amount' => 2,
                    ]);
                    SplitPayment::create([
                        'invoice_id' => $invoice_key,
                        'amount' => 1,
                    ]);
                }
            }

            //$invoiceData =  array_merge($client->toArray(),$invoice);

            //Notification::send($client, new invoiceNotification($invoiceData));
        }


        $leadStatus = Lead::find($request->get('lead_id'));
        $leadStatus->status = 2;
        $leadStatus->value = $request->get('value');
        $leadStatus->save();

        return response()->json([
            'message' => 'Create Client Successfully!',
            'invoice_key' => $invoice->invoice_key,
            'brand_url' => $request->get('brand_url'),
        ], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Invoice $invoice
     * @return Invoice|Invoice[]|\Illuminate\Http\Response|\LaravelIdea\Helper\App\Models\_IH_Invoice_C
     */
    public function edit($id)
    {
        return Invoice::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'edit_taxable' => 'required_if:edit_taxable,1',
            'edit_tax' => 'required_if:edit_taxable,1',
            'edit_taxAmount' => 'required_if:edit_taxable,1',
            'edit_total_amount' => 'required_if:edit_taxable,1',
            'edit_is_split' => 'required|integer|in:0,1',
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
        $is_split = $request->get('edit_is_split');

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
        $invoice->is_split = $is_split;
        $invoice->save();

        if ($invoice->status == "due") {
            /** 0 = No (Complete payment) */
            /** Now first we have to check split payment condition if is split == no then we have to delete record of split payments*/
            if ($is_split == 0) {
                /** Deleting both split payment records */
                SplitPayment::where('invoice_id', $invoice_key)->delete();
            } else {
                /** Else split payment condition will be true ,and we have to create split payment records*/

                /** Check if there are any soft-deleted split payment records */
                $trashedSplitRecords = SplitPayment::onlyTrashed()->where('invoice_id', $invoice_key)->get();
                /** Then we have to check record already exists or not*/
                $split_records = SplitPayment::where('invoice_id', $invoice_key)->get();

                if ($trashedSplitRecords->isNotEmpty()) {
                    SplitPayment::onlyTrashed()->where('invoice_id', $invoice_key)->restore();
                } elseif ($split_records->isEmpty()) {
                    /** If not then we have to check minimum total amount , which will be greater than "3" */
                    if ($total_amount > 3) {
                        /**Creating first split payment*/
                        SplitPayment::create(['invoice_id' => $invoice_key, 'amount' => 2,]);
                        /**Creating second split payment*/
                        SplitPayment::create(['invoice_id' => $invoice_key, 'amount' => 1,]);
                    } else {
                        /** Returning error response if total amount is less than "3" */
                        return response()->json(['error' => 'Invoice total amount is less then the limit.']);
                    }
                }
                /** Note : If split payment is dynamic please create condition for that e.g. = split payment 1st amount and 2nd amount  */
            }
        }

        return $invoice;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        //
    }


    public function create_invoice(Request $request)
    {
        $rules = [
            'value' => 'required',
            'taxable' => 'required_if:taxable,1',
            'tax' => 'required_if:taxable,1',
            'taxAmount' => 'required_if:taxable,1',
            'total_amount' => 'required_if:taxable,1',
            'is_split' => 'required|integer|in:0,1',
            'brand_key' => 'required',
            'agent_id' => 'required',
        ];
        $messages = [
            'brand_key.required' => 'The Brand field is required.',
            'agent_id.required' => 'The Agent field is required.',
            'value.required' => 'The Amount field is required.',
            'tax.required_if' => 'The Tax field is required when Taxable is enabled.',
            'taxAmount.required_if' => 'The Tax Amount field is required when Taxable is enabled.',
            'total_amount.required_if' => 'The Total Amount field is required when Taxable is enabled.',
            'is_split.required' => 'The Split Payment field is required.',
            'is_split.integer' => 'The Split Payment field must be an integer.',
            'is_split.in' => 'The Split Payment field must be either Yes or No.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $creatorid = Auth::user()->id;
            $clientID = $request->get('client_id');
            if (Auth::user()->type === 'ppc') {
                $assignedTeams = auth()->user()->assigned_teams ?? [];
                if (auth()->user()->team_key && !in_array(auth()->user()->team_key, $assignedTeams)) {
                    $assignedTeams[] = auth()->user()->team_key;
                }
                $teamKey = $request->get('team_key');

                if (!in_array($teamKey, $assignedTeams)) {
                    return response()->json([
                        'errors' => 'Please select a valid team.',
                    ], 400);
                }
                /** Team Brands */
                $assign_brands = AssignBrand::where('team_key', $teamKey)->where('brand_key', $request->get('brand_key'))->whereHas('getBrandWithOutTrashed')->get()->pluck('brand_key')->toArray();
                if (!in_array($request->get('brand_key'), $assign_brands)) {
                    return response()->json([
                        'errors' => 'Brand not assigned to this team.',
                    ], 400);
                }
            } else {
                $teamKey = auth()->user()->team_key;
                /** Team Brands */
                $assign_brands = AssignBrand::where('team_key', $teamKey)->where('brand_key', $request->get('brand_key'))->whereHas('getBrandWithOutTrashed')->get()->pluck('brand_key')->toArray();
                if (!in_array($request->get('brand_key'), $assign_brands)) {
                    return response()->json([
                        'errors' => 'Oops! Brand not assigned to your team.',
                    ], 400);
                }
            }
            $is_split = $request->get('is_split');
            if ($request->get('taxable') == 1) {
                if ($request->get('tax') && $request->get('value')) {
                    $value = $request->get('value');
                    $tax_percentage = $request->get('tax');
                    $tax_amount = round(($value * $tax_percentage) / 100);
                    $total_amount = number_format(round($tax_amount) + $value, 2, '.', '');
                } elseif ($request->get('taxAmount') && $request->get('value')) {
                    $value = $request->get('value');
                    $tax_amount = $request->get('taxAmount');
                    $tax_percentage = round(($tax_amount / $value) * 100);
                    $total_amount = number_format(round($tax_amount) + $value, 2, '.', '');
                } elseif ($request->get('total_amount') && $request->get('value')) {
                    $value = $request->get('value');
                    $total_amount = $request->get('total_amount');
                    $tax_amount = $total_amount - $value;
                    $tax_percentage = round(($tax_amount / $value) * 100);
                } elseif ($request->get('taxAmount') && $request->get('tax')) {
                    $tax_amount = $request->get('taxAmount');
                    $tax_percentage = $request->get('tax');
                    $value = ($tax_amount / $tax_percentage) * 100;
                    $total_amount = number_format(round($tax_amount) + $value, 2, '.', '');
                } else {
                    $tax_percentage = '0';
                    $tax_amount = '0.00';
                    $value = $request->get('value');
                    $total_amount = number_format($value, 2, '.', '');
                }
            } elseif ($request->get('tax') && $request->get('value')) {
                $value = $request->get('value');
                $tax_percentage = $request->get('tax');
                $tax_amount = round(($value * $tax_percentage) / 100);
                $total_amount = number_format(round($tax_amount) + $value, 2, '.', '');
            } else {
                $tax_percentage = '0';
                $tax_amount = '0.00';
                $value = $request->get('value');
                $total_amount = number_format($value, 2, '.', '');
            }
            if ($clientID != null) {
                $client = Client::where('id', $clientID)->first();
                $projectID = $request->get('project_id');
                if (!$request->get('project_id') || $request->get('project_id') == 'new') {
                    $project = Project::create([
                        'team_key' => $teamKey,
                        'brand_key' => $request->get('brand_key'),
                        'creatorid' => $creatorid,
                        'clientid' => $client->id,
                        'agent_id' => $request->get('agent_id'),
                        'asigned_id' => '0',
                        'category_id' => '1',
                        'project_title' => $request->get('project_title'),
                        'project_description' => "",
                        'project_status' => '1',
                        'project_progress' => '1',
                        'project_cost' => $request->get('value')
                    ]);
                    $projectID = $project->id;
                }
                $additionalData = ['name' => $client->name];
                // $invoiceData =  array_merge($invoice->toArray(),$additionalData);
                //Notification::send($client, new invoiceNotification($invoiceData));

            } else {
                $client_exists = Client::where('email', $request->get('email'))->first();
                if ($client_exists) {
                    $clientID = $client_exists->id;
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
                    User::create([
                        'name' => $request->get('name'),
                        'email' => $request->get('email'),
                        'phone' => $request->get('phone'),
                        'password' => Hash::make('12345678'),
                        'type' => 'client',
                        'team_key' => $teamKey,
                        'clientid' => $clientID
                    ]);
                }
                if (!$clientID) {
                    return response()->json(['error', 'server side error!']);
                }
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
                $projectID = $project->id;
            }
            $invoice_key = random_int(11, 99) . substr(time(), 1, 3) . random_int(11, 99) . substr(time(), 8, 2);
            $invoice = Invoice::create([
                'invoice_num' => 'INV-' . random_int(100000, 999999),
                'invoice_key' => $invoice_key,
                'team_key' => $teamKey,
                'brand_key' => $request->get('brand_key'),
                'creatorid' => $creatorid,
                'clientid' => $clientID,
                'agent_id' => $request->get('agent_id'),
                'final_amount' => $request->get('value'),
                'due_date' => $request->get('due_date'),
                'sales_type' => $request->get('sales_type'),
                'status' => 'due',
                'project_id' => $projectID,
                'invoice_descriptione' => $request->get('description'),
                'cur_symbol' => $request->get('cur_symbol'),
                'tax_percentage' => $tax_percentage,
                'tax_amount' => $tax_amount,
                'total_amount' => $total_amount,
                'payment_gateway' => $request->get('payment_gateway'),
                'is_split' => $is_split,
            ]);
            if ($total_amount > 3 && $is_split == 1) {
                SplitPayment::create([
                    'invoice_id' => $invoice_key,
                    'amount' => 2,
                ]);
                SplitPayment::create([
                    'invoice_id' => $invoice_key,
                    'amount' => 1,
                ]);
            }
            //$invoiceData =  array_merge($client->toArray(),$invoice->toArray());
            //Notification::send($client, new invoiceNotification($invoiceData));

            return response()->json([
                'message' => 'Create Invoice Successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);
            Log::error($e->getMessage());

            return response()->json([
                'message' => 'Please try in shortly!'
            ], 200);
        }
    }


    public function send_invoice_email($id)
    {
        $invoice = Invoice::find($id);

        DB::table('invoices')
            ->where('id', $id)
            ->update(['status' => 'due']);

        $client = Client::find($invoice->clientid);

        $additionalData = ['name' => $client->name];

        $invoiceData = array_merge($invoice->toArray(), $additionalData);

        Notification::send($client, new invoiceNotification($invoiceData));

    }


    public function publish_invoice($id)
    {
        $invoice = Invoice::find($id);

        DB::table('invoices')
            ->where('id', $id)
            ->update(['status' => 'due']);
    }

    public function show_client_projects($id)
    {
        $project = Project::where('clientid', $id)->get();
        return $project;
    }

    /** Client Invoice Index Separate Function*/
    public function show_client_invoice()
    {

        $loginClientId = Auth::user()->clientid;

        // Team Invoice
        $invoiceData = array();
        $invoices = Invoice::where('clientid', $loginClientId)->where('status', '!=', 'draft')->get();

        foreach ($invoices as $invoice) {
            $projectId = $invoice->project_id;

            $invoice['projectTitle'] = Project::where('id', $projectId)->value('project_title');

            array_push($invoiceData, $invoice);
        }

        return view('invoice.clientInvoice', compact('invoiceData'));
    }

    public function qa_invoice_list()
    {

    }

//lead
    public function teamAgent($brand_key = null, $team_key = null)
    {
        if (auth()->user()->type === 'ppc') {
            if (!Brand::where('brand_key', $brand_key)->withoutTrashed()->first()) {
                return response()->json(['status' => 0, 'error' => 'Oops ! Brand not found'], 404);
            }
            $assignedTeams = auth()->user()->assigned_teams ?? [];
            if (auth()->user()->team_key && !in_array(auth()->user()->team_key, $assignedTeams)) {
                $assignedTeams[] = auth()->user()->team_key;
            }
            $brandTeams = AssignBrand::whereIn('team_key', $assignedTeams)->where('brand_key', $brand_key)->whereHas('getBrandWithOutTrashed')->get()->pluck('team_key')->toArray();
            $users = User::whereIn('team_key', $brandTeams)->where('status', 1)->where('type', '!=', 'client')->orderBy('type')->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'data' => $user->name,
                ];
            });
        } else {
            $users = User::where(['team_key' => $team_key, 'status' => 1])->where('type', '!=', 'client')->orderBy('type')->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'data' => $user->name,
                ];
            });
        }
        return response()->json(['status' => 1, 'success' => 'Agents fetched successfully.!', 'users' => $users, 'count' => count($users)]);
    }

    public function teamBrands($id)
    {
        try {
            $team_brands = [];
            if (auth()->user()->type === 'ppc') {
                $assignedTeams = auth()->user()->assigned_teams ?? [];
                if (auth()->user()->team_key && !in_array(auth()->user()->team_key, $assignedTeams)) {
                    $assignedTeams[] = auth()->user()->team_key;
                }
                if (in_array($id, $assignedTeams)) {
                    $team_brands = AssignBrand::where('team_key', $id)->whereHas('getBrandWithOutTrashed')->with('getBrandWithOutTrashed')->get()->map(function ($assign_brand) {
                        return [
                            'id' => $assign_brand->brand_key,
                            'data' => $assign_brand->getBrandWithOutTrashed->name,
                        ];
                    });
                }
            } else {
                $team_brands = AssignBrand::where('team_key', $id)->whereHas('getBrandWithOutTrashed')->whereHas('getBrandWithOutTrashed')->get()->map(function ($assign_brand) {
                    return [
                        'id' => $assign_brand->brand_key,
                        'data' => $assign_brand->getBrandWithOutTrashed->name,
                    ];
                });
            }
            return response()->json(['status' => 1, 'success' => 'Brands fetched successfully.!', 'brands' => $team_brands, 'count' => count($team_brands)]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
