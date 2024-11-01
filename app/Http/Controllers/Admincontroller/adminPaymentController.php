<?php

namespace App\Http\Controllers\Admincontroller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Lead;
use App\Models\User;
use App\Models\Payment;
use App\Models\Brand;
use App\Models\Team;
use App\Models\Refund;
use App\Models\AssignBrand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Project;
use Illuminate\Support\Facades\Validator;

class adminPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $result = $this->getData($request, new Payment());
        $payments = $result['data'];
        $fromDate = $result['fromDate'];
        $toDate = $result['toDate'];
        $teamKey = $result['teamKey'];
        $brandKey = $result['brandKey'];
        $teams = Team::where('status', '1')->get();
        $brands = Brand::where('status', '1')->orderby('name', 'Asc')->get();
        $members = User::where('status', 1)->where('type', '!=', 'client')->orderBy('type')->get();
        $assign_brands = AssignBrand::all();
        return view('admin.payment.index',compact('payments','assign_brands','fromDate','toDate','teamKey','brandKey','teams','brands','members'));
    }

    public function old_index()
    {
//        $paymentData = array();
        $payments = Payment::orderBy('created_at', 'desc')->get();
        $teams = Team::where('status', '1')->get();
        $brands = Brand::where('status', '1')->get();


        foreach ($payments as $payment) {
            $teamKey = $payment->team_key;
            $brandKey = $payment->brand_key;

            $payment['teamName'] = Team::where('team_key', $teamKey)->value('name');

            $payment['brandName'] = Brand::where('brand_key', $brandKey)->value('name');

            array_push($paymentData, $payment);
        }

        //Team Brand
        $data = AssignBrand::all();

        $teamBrand = array();

        foreach ($data as $a) {
            $brand_key = $a->brand_key;
            $brands = Brand::where('brand_key', $brand_key)->get();
            foreach ($brands as $brand) {
                $a['brandKey'] = $brand->brand_key;
                $a['brandName'] = $brand->name;
                $teamBrand[] = $a;
            }
        }

        //Agent  Members
        $members = User::where('status', 1)
            ->where('type', '!=', 'client')->orderBy('type', 'asc')->get();


        return view('admin.payment.index', compact('brands', 'teams', 'teamBrand', 'members', 'payments'));
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
        return Payment::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //

    }

    public function refund_payment(Request $request)
    {
        $rules = [
            'reason'=>'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $paymentId = $request->get('payment_id');
        $type = $request->get('type');

        Refund::create(['team_key' => $request->get('team_key'), 'brand_key' => $request->get('brand_key'), 'agent_id' => $request->get('agent_id'), 'client_id' => $request->get('client_id'), 'invoice_id' => $request->get('invoice_id'), 'payment_id' => $paymentId, 'authorizenet_transaction_id' => $request->get('auth_transaction_id'), 'amount' => $request->get('amount'), 'reason' => $request->get('reason'), 'type' => $type, 'qa_approval' => 0]);

        if ($type == "refund") {
            $paymentStatus = 2;
        } else {
            $paymentStatus = 3;
        }
        $payment = Payment::find($paymentId);
        $payment->payment_status = $paymentStatus;
        $payment->save();

    }

    public function team_Payment(Request $request)
    {

        $searchText = $request->search;

        $paymentData = array();

        if ($searchText == 0) {
            $teamPayment = Payment::orderBy('id', 'desc')->get();
        } else {
            $teamPayment = Payment::where('team_key', $searchText)->orderBy('id', 'desc')
                ->get();
        }

        $html = "";
        $html .= '<thead><tr><th>ID #</th><th>Team</th><th>Brand</th><th>Client</th><th>Amount</th><th>Payment Gateway</th><th>Transaction ID</th><th data-breakpoints="sm xs">Payment Date</th><th class="text-center" data-breakpoints="xs md">Status</th><th class="text-center" data-breakpoints="xs md">Compliance<br>Varified</th><th class="text-center" data-breakpoints="xs md">Operation<br>Varified</th><th class="text-center" data-breakpoints="sm xs md">Action</th></tr></thead>';

        $html .= '<tbody>';

        foreach ($teamPayment as $payments) {
            $teamKey = $payments->team_key;
            $brandKey = $payments->brand_key;
            $client_id = $payments->clientid;

            $client_name = Client::where('id', $client_id)->value('name');
            $payments['clientName'] = $client_name;

            $team_name = Team::where('team_key', $teamKey)->value('name');
            $payments['teamName'] = $team_name;

            $brand_name = Brand::where('brand_key', $brandKey)->value('name');
            $payments['brandName'] = $brand_name;

            array_push($paymentData, $payments);

            $html .= '<tr>';
            $html .= '<td class="align-middle">' . $payments->id . '</td>';
            $html .= '<td class="align-middle">' . $payments->teamName . '</td>';
            $html .= '<td class="align-middle">' . $payments->brandName . '</td>';
            $html .= '<td> <a class="text-warning" href="#">' . $payments->clientName . '</a></td>';
            $html .= '<td class="align-middle">$' . $payments->amount . '</td>';
            $html .= '<td class="align-middle">' . $payments->payment_gateway . '</td>';
            $html .= '<td class="align-middle">' . $payments->authorizenet_transaction_id . '</td>';
            $html .= '<td class="align-middle">' . \Carbon\Carbon::parse($payments->created_at)
                    ->format('d/m/Y') . '</td>';

            if ($payments->payment_status == 1) {
                $payment_status = '<span class="badge badge-success rounded-pill">Success</span>';
            } elseif ($payments->payment_status == 2) {
                $payment_status = '<span class="badge badge-warning rounded-pill">Refund</span>';
            } else {
                $payment_status = '<span class="badge badge-danger rounded-pill">Charge Back</span>';
            }
            $html .= '<td class="align-middle">' . $payment_status . '</td>';

            if ($payments->compliance_verified == 1) {
                $compliance_verified = '<i class="zmdi zmdi-check-circle text-success" title="Active"></i>';
            } else {
                $compliance_verified = '<i class="zmdi zmdi-close-circle text-danger" title="Inactive"></i>';
            }
            $html .= '<td class="align-middle">' . $compliance_verified . '</td>';

            if ($payments->head_verified == 1) {
                $head_verified = '<i class="zmdi zmdi-check-circle text-success" title="Active"></i>';
            } else {
                $head_verified = '<i class="zmdi zmdi-close-circle text-danger" title="Inactive"></i>';
            }
            $html .= '<td class="align-middle">' . $head_verified . '</td>';

            $html .= '<td class="text-center align-middle">
                                <button data-id="' . $payments->id . '" title="Refund" type="button" class="btn btn-warning btn-sm btn-round cxm-btn-refund"><i class="zmdi zmdi-replay"></i></button>
                            </td>';

            $html .= '</tr>';

        }

        $html .= '</tbody>';
        return $html;
    }

    public function brand_Payment(Request $request)
    {

        $searchText = $request->search;

        $paymentData = array();

        if ($searchText == 0) {
            $teamPayment = Payment::orderBy('id', 'desc')->get();
        } else {
            $teamPayment = Payment::where('brand_key', $searchText)->orderBy('id', 'desc')
                ->get();
        }

        $html = "";
        $html .= '<thead><tr><th>ID #</th><th>Team</th><th>Brand</th><th>Client</th><th>Amount</th><th>Payment Gateway</th><th>Transaction ID</th><th data-breakpoints="sm xs">Payment Date</th><th class="text-center" data-breakpoints="xs md">Status</th><th class="text-center" data-breakpoints="xs md">Compliance<br>Varified</th><th class="text-center" data-breakpoints="xs md">Operation<br>Varified</th><th class="text-center" data-breakpoints="sm xs md">Action</th></tr></thead>';

        $html .= '<tbody>';

        foreach ($teamPayment as $payments) {
            $teamKey = $payments->team_key;
            $brandKey = $payments->brand_key;
            $client_id = $payments->clientid;

            $client_name = Client::where('id', $client_id)->value('name');
            $payments['clientName'] = $client_name;

            $team_name = Team::where('team_key', $teamKey)->value('name');
            $payments['teamName'] = $team_name;

            $brand_name = Brand::where('brand_key', $brandKey)->value('name');
            $payments['brandName'] = $brand_name;

            array_push($paymentData, $payments);

            $html .= '<tr>';
            $html .= '<td class="align-middle">' . $payments->id . '</td>';
            $html .= '<td class="align-middle">' . $payments->teamName . '</td>';
            $html .= '<td class="align-middle">' . $payments->brandName . '</td>';
            $html .= '<td> <a class="text-warning" href="#">' . $payments->clientName . '</a></td>';
            $html .= '<td class="align-middle">$' . $payments->amount . '</td>';
            $html .= '<td class="align-middle">' . $payments->payment_gateway . '</td>';
            $html .= '<td class="align-middle">' . $payments->authorizenet_transaction_id . '</td>';
            $html .= '<td class="align-middle">' . \Carbon\Carbon::parse($payments->created_at)
                    ->format('d/m/Y') . '</td>';

            if ($payments->payment_status == 1) {
                $payment_status = '<span class="badge badge-success rounded-pill">Success</span>';
            } elseif ($payments->payment_status == 2) {
                $payment_status = '<span class="badge badge-warning rounded-pill">Refund</span>';
            } else {
                $payment_status = '<span class="badge badge-danger rounded-pill">Charge Back</span>';
            }
            $html .= '<td class="align-middle">' . $payment_status . '</td>';

            if ($payments->compliance_verified == 1) {
                $compliance_verified = '<i class="zmdi zmdi-check-circle text-success" title="Active"></i>';
            } else {
                $compliance_verified = '<i class="zmdi zmdi-close-circle text-danger" title="Inactive"></i>';
            }
            $html .= '<td class="align-middle">' . $compliance_verified . '</td>';

            if ($payments->head_verified == 1) {
                $head_verified = '<i class="zmdi zmdi-check-circle text-success" title="Active"></i>';
            } else {
                $head_verified = '<i class="zmdi zmdi-close-circle text-danger" title="Inactive"></i>';
            }
            $html .= '<td class="align-middle">' . $head_verified . '</td>';

            $html .= '<td class="text-center align-middle">
                                <button data-id="' . $payments->id . '" title="Refund" type="button" class="btn btn-warning btn-sm btn-round cxm-btn-refund"><i class="zmdi zmdi-replay"></i></button>
                            </td>';

            $html .= '</tr>';

        }

        $html .= '</tbody>';
        return $html;
    }


    public function show_payment_refund()
    {
        $refunds = Refund::orderBy('created_at', 'desc')->get();
        return view('admin.payment.refund', compact('refunds'));
    }

    public function paymentTeamAgent($id)
    {
        return User::where(['team_key' => $id, 'status' => 1])->where('type', '!=', 'client')->orderBy('type', 'asc')->get();
    }

    // admin Add Direct Payment

    /**Dm=> Note change in this function will also reflect in admincontroller/WirePaymentController -> payment_approval*/
    public function admin_direct_payment(Request $request)
    {
        try {
            $creatorid = Auth::user()->id;

            $client_exists = Client::where('email', $request->get('email'))->first();

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
                $invoiceKey = random_int(11, 99) . substr(time(), 1, 3) . random_int(11, 99) . substr(time(), 8, 2);

                if ($projectID) {
                    $invoice = Invoice::create([
                        'invoice_num' => 'INV-' . random_int(100000, 999999),
                        'invoice_key' => $invoiceKey,
                        'team_key' => $request->get('team_key'),
                        'brand_key' => $request->get('brand_key'),
                        'creatorid' => $creatorid,
                        'clientid' => $client_exists->id,
                        'agent_id' => $request->get('agent_id'),
                        'final_amount' => $request->get('value'),
                        'total_amount' => $request->get('value'),
                        'received_amount' => $request->get('value'),
                        'due_date' => $request->get('due_date'),
                        'invoice_descriptione' => $request->get('description'),
                        'sales_type' => $request->get('sales_type'),
                        'status' => 'Paid',
                        'project_id' => $projectID,
                        'payment_gateway' => $request->get('merchant'),
                    ]);
                }

                //$request->get('transaction_id')
                $payment = Payment::create([
                    'team_key' => $request->get('team_key'),
                    'brand_key' => $request->get('brand_key'),
                    'creatorid' => $creatorid,
                    'actor_id' => $creatorid,
                    'actor_type' => get_class(Auth::guard('admin')->user() ?? ""),
                    'agent_id' => $request->get('agent_id'),
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
                    'payment_gateway' => $request->get('merchant'),
                    'auth_id' => '',
                    'response_code' => '',
                    'message_code' => '',
                    'payment_notes' => $request->get('description'),
                    'sales_type' => $request->get('sales_type'),
                    'actor_id' => $request->get('actor_id',auth()->user()->id),
                    'actor_type' => $request->get('actor_type',get_class(Auth::guard('admin')->user() ?? "")),
                ]);
            } else {

                $client = Client::create([
                    'team_key' => $request->get('team_key'),
                    'brand_key' => $request->get('brand_key'),
                    'creatorid' => $creatorid,
                    'name' => $request->get('name'),
                    'email' => $request->get('email'),
                    'phone' => $request->get('phone'),
                    'agent_id' => $request->get('agent_id'),
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
                        'agent_id' => $request->get('agent_id'),
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
                $invoiceKey = random_int(11, 99) . substr(time(), 1, 3) . random_int(11, 99) . substr(time(), 8, 2);

                if ($projectID) {
                    $invoice = Invoice::create([
                        'invoice_num' => 'INV-' . random_int(100000, 999999),
                        'invoice_key' => $invoiceKey,
                        'team_key' => $request->get('team_key'),
                        'brand_key' => $request->get('brand_key'),
                        'creatorid' => $creatorid,
                        'clientid' => $clientID,
                        'agent_id' => $request->get('agent_id'),
                        'final_amount' => $request->get('value'),
                        'due_date' => $request->get('due_date'),
                        'invoice_descriptione' => $request->get('description'),
                        'sales_type' => $request->get('sales_type'),
                        'status' => 'Paid',
                        'project_id' => $projectID,
                    ]);
                }

                //$request->get('transaction_id')
                $payment = Payment::create([
                    'team_key' => $request->get('team_key'),
                    'brand_key' => $request->get('brand_key'),
                    'creatorid' => $creatorid,
                    'agent_id' => $request->get('agent_id'),
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
                    'payment_gateway' => $request->get('merchant'),
                    'auth_id' => '',
                    'response_code' => '',
                    'message_code' => '',
                    'payment_notes' => $request->get('description'),
                    'sales_type' => $request->get('sales_type'),
                    'actor_id' => $request->get('actor_id',auth()->user()->id),
                    'actor_type' => $request->get('actor_type',get_class(Auth::guard('admin')->user() ?? "")),
                ]);
            }
            return response()->json(['success' => 'succeeded.!'], 200);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => 'Response error'], 500);
        }
    }

    // admin add client/project payment

    public function admin_create_payment(Request $request)
    {
        $creatorid = Auth::user()->id;
        $teamKey = $request->get('team_key');
        $invoiceKey = random_int(11, 99) . substr(time(), 1, 3) . random_int(11, 99) . substr(time(), 8, 2);
        $clientId = $request->get('client_id');

        $clientInfo = Client::where('id', $clientId)->first();

        $invoice = Invoice::create([
            'invoice_num' => 'INV-' . random_int(100000, 999999),
            'invoice_key' => $invoiceKey,
            'team_key' => $teamKey,
            'brand_key' => $request->get('brand_key'),
            'creatorid' => $creatorid,
            'clientid' => $clientId,
            'agent_id' => $request->get('agent_id'),
            'final_amount' => $request->get('value'),
            'due_date' => $request->get('due_date'),
            'sales_type' => $request->get('sales_type'),
            'project_id' => $request->get('project_id'),
            'status' => 'Paid',
            'invoice_descriptione' => $request->get('description')
        ]);

        $payment = Payment::create([
            'team_key' => $teamKey,
            'brand_key' => $request->get('brand_key'),
            'creatorid' => $creatorid,
            'agent_id' => $request->get('agent_id'),
            'clientid' => $clientId,
            'invoice_id' => $invoiceKey,
            'project_id' => $request->get('project_id'),
            'name' => $clientInfo->name,
            'email' => $clientInfo->email,
            'phone' => $clientInfo->phone,
            'address' => '',
            'amount' => $request->get('value'),
            'payment_status' => '1',
            'authorizenet_transaction_id' => $request->get('track_id'),
            'payment_gateway' => $request->get('merchant'),
            'auth_id' => '',
            'response_code' => '',
            'message_code' => '',
            'payment_notes' => $request->get('description'),
            'sales_type' => $request->get('sales_type'),
        ]);

        return $invoice;

    }

    public function unsettled_payments(Request $request)
    {
        $query = new Payment();
        $query = $query->where('payment_gateway','authorize')->where('merchant_id','!=',0)->whereNotIn('settlement',['settled successfully']);
        $result = $this->getData($request, $query);
        $unsettled_payments = $result['data'];
        $fromDate = $result['fromDate'];
        $toDate = $result['toDate'];
        $teamKey = $result['teamKey'];
        $brandKey = $result['brandKey'];
        $teams = Team::where('status', '1')->get();
        $brands = Brand::where('status', '1')->orderby('name', 'Asc')->get();
        return view('admin.payment.unsettled-payments.index',compact('unsettled_payments','fromDate','toDate','teamKey','brandKey','teams','brands'));
    }
}

