<?php

namespace App\Http\Controllers\Usercontroller;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Brand;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Team;
use App\Models\ThirdPartyRoleModel;
use App\Models\User;
use App\Models\ProjectStatus;
use App\Models\AssignBrand;
use App\Models\ProjectCategory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $type = Auth::user()->type;
        $agentId = Auth::user()->id;
        $team_key = Auth::user()->team_key;
        if ($type === 'ppc') {
            $assignedTeams = auth()->user()->assigned_teams ?? [];
            if (auth()->user()->team_key && !in_array(auth()->user()->team_key, $assignedTeams)) {
                $assignedTeams[] = auth()->user()->team_key;
            }
            /** Team Brands */
            $assign_brands = AssignBrand::whereIn('team_key', $assignedTeams)->whereHas('getBrandWithOutTrashed')->get();
        } elseif (in_array($type, ['qa', 'third-party-user'])) {
            /** Team Brands */
            $assign_brands = AssignBrand::whereHas('getBrandWithOutTrashed')->get();
        } else {
            /** Team Brands */
            $assign_brands = AssignBrand::where('team_key', $team_key)->whereHas('getBrandWithOutTrashed')->get();
        }
        $query = new Client();
        $result = $this->userDataFilter($request, $query, $assign_brands);
        $brandKey = $result['brandKey'];
        $fromDate = $result['fromDate'];
        $toDate = $result['toDate'];
        $clients = $result['data'];

        return view('client.index', compact('brandKey', 'fromDate', 'toDate', 'clients', 'assign_brands'));
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
        $creatorid = Auth::user()->id;
        $client_exists = Client::where('email', $request->get('email'))->first();

        if ($client_exists) {
            $title = "Error!";
            $message = "Client  Already Exists";
            $status = 'error';
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

            $title = "Good Job!";
            $message = "Client  successfully Created!";
            $status = 'success';
        }

        return response()->json([
            'title' => $title,
            'message' => $message,
            'status' => $status
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Client $client
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($id)
    {
        $team_key = Auth::user()->team_key;
        if (Auth::user()->type === 'ppc') {
            $assignedTeams = auth()->user()->assigned_teams ?? [];
            if (auth()->user()->team_key && !in_array(auth()->user()->team_key, $assignedTeams)) {
                $assignedTeams[] = auth()->user()->team_key;
            }
            /** Team Members */
            $members = User::whereIn('team_key', $assignedTeams)->where('status', 1)->where('type', '!=', 'client')->orderBy('type')->get();
            /** Team Brands */
            $assign_brands = AssignBrand::whereIn('team_key', $assignedTeams)->whereHas('getBrandWithOutTrashed')->get();
        } else {
            /** Team Members */
            $members = User::where('team_key', $team_key)->where('status', 1)->where('type', '!=', 'client')->orderBy('type')->get();
            /** Team Brands */
            $assign_brands = AssignBrand::where('team_key', $team_key)->whereHas('getBrandWithOutTrashed')->get();
        }
        $brand_keys = $assign_brands->pluck('brand_key')->toArray();

        $client = Client::where('id', $id)->whereIn('brand_key', $brand_keys)->first();
        if (!$client) {
            return redirectBackConditional()->withErrors(['error' => 'Oops! Client not found.']);
        }

        /** Client Invoices */
        $invoices = Invoice::where('clientid', $id)->whereIn('brand_key', $brand_keys)->get();

        /** Client Projects */
        $projects = Project::where('clientid', $id)->whereIn('brand_key', $brand_keys)->get();

        /** Client Payments */
        $payments = Payment::where('clientid', $id)->whereIn('brand_key', $brand_keys)->get();

        /** Project Statuses */
        $projectStatus = ProjectStatus::all();

        /** Project Categories */
        $projectCategories = ProjectCategory::all();

        return view('client.profile', compact('projectCategories', 'client', 'invoices', 'members', 'assign_brands', 'payments', 'projects', 'projectStatus'));
    }

//    public function show($id)
//    {
//        $client = Client::find($id);
//        $client['brandLogo'] = Brand::where('brand_key', $client->brand_key)->value('logo');
//
//        //client Invoice Data
//        $invoices = array();
//        $invoiceAmount = Invoice::where('clientid', $id)->sum('final_amount');
//        $invoicesClient = Invoice::where('clientid', $id)->get();
//        foreach ($invoicesClient as $inv) {
//
//            $projectName = Project::where('id', $inv->project_id)->value('project_title');
//            $inv['ProjectName'] = $projectName;
//
//            $brand = Brand::where('brand_key', $inv->brand_key)->first();
//            $inv['brandName'] = $brand->name;
//            $inv['brandUrl'] = $brand->brand_url;
//
//            array_push($invoices, $inv);
//        }
//
//        //Client Projects
//        $completeProject = Project::where(['clientid' => $id, 'project_status' => 5])->count();
//        $openProject = Project::where(['clientid' => $id, 'project_status' => 2])->count();
//
//        $clientProjects_yy = Project::where('clientid', $id)->get();
//
//        $clientProjects = array();
//
//        foreach ($clientProjects_yy as $yy) {
//            $brand = Brand::where('brand_key', $yy->brand_key)->value('name');
//            $yy['brandName'] = $brand;
//
//            $agent = User::where('id', $yy->agent_id)->first();
//            $yy['agentName'] = $agent->name;
//            $yy['agentDesignation'] = $agent->designation;
//            $yy['agentImage'] = $agent->image;
//
//            $status = ProjectStatus::where('id', $yy->project_status)->first();
//            $yy['status'] = $status->status;
//            $yy['statusColor'] = $status->status_color;
//
//            array_push($clientProjects, $yy);
//        }
//
//        //Clients Payments
//        $payments = array();
//        $clientspayments = Payment::where('clientid', $id)->get();
//        $totalPayment = Payment::where('clientid', $id)->sum('amount');
//
//        foreach ($clientspayments as $pay) {
//            $projectName = Project::where('id', $pay->project_id)->value('project_title');
//            $pay['ProjectName'] = $projectName;
//            array_push($payments, $pay);
//        }
//
//        //Project Status
//        $projectStatus = ProjectStatus::all();
//
//        $data = AssignBrand::where('team_key', $client->team_key)->get();
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
//
//        //Team Members
//        $members = User::where(['team_key' => $client->team_key, 'status' => 1])
//            ->where('type', '!=', 'client')->orderBy('type', 'asc')->get();
//
//        //Project Category
//        $projectCategories = ProjectCategory::all();
//
//        return view('client.profile', compact('projectCategories', 'client', 'invoices', 'members', 'invoiceAmount', 'teamBrand', 'payments', 'totalPayment', 'completeProject', 'openProject', 'clientProjects', 'projectStatus'));
//    }

    public function get_spending(Request $request, $id)
    {
        try {
            $team_key = Auth::user()->team_key;
            if (!$team_key && Auth::user()->type != 'third-party-user') {
                return response()->json(['error' => 'Oops! Team not found.'], 404);
            }

            $client = Client::where('id', $id)
                ->where(function ($query) use ($team_key) {
                    if (Auth::user()->type != 'third-party-user') {
                        $query->where('team_key', $team_key);
                    }
                })
                ->where('status', 1)
                ->first();

            if (!$client) {
                return response()->json(['error' => 'Oops! Client not found.'], 404);
            }
            $third_party_roles = ThirdPartyRoleModel::where('status', 1)->where('client_id', $client->id)
                ->where(function ($query) use ($team_key) {
                    if (Auth::user()->type != 'third-party-user') {
                        $query->where('team_key', $team_key);
                    }
                })
                ->get();
            return response()->json(['success' => 'Record fetched', 'data' => $third_party_roles, 'status' => 1]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Client $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Client $client
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Client $client)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Client $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        //
    }

    public function create_client_invoice(Request $request)
    {

        if ($request->get('taxable') == 1) {
            $tax_percentage = $request->get('tax');
            $tax_amount = $request->get('taxAmount');
            $total_amount = $request->get('total_amount');
        } else {
            $tax_percentage = '0';
            $tax_amount = '0.00';
            $total_amount = $request->get('value');
        }

        $creatorid = Auth::user()->id;
        $teamKey = Auth::user()->team_key;

        if ($request->get('project_id') == 'new') {
            $project = Project::create([
                'team_key' => $teamKey,
                'brand_key' => $request->get('brand_key'),
                'creatorid' => $creatorid,
                'clientid' => $request->get('client_id'),
                'agent_id' => $request->get('agent_id'),
                'asigned_id' => '0',
                'category_id' => '1',
                'project_title' => $request->get('project_title'),
                'project_description' => "",
                'project_status' => '1',
                'project_progress' => '1'
            ]);
            $projectID = $project->id;
        } else {
            $projectID = $request->get('project_id');
        }

        $invoice = Invoice::create([
            'invoice_num' => 'INV-' . random_int(100000, 999999),
            'invoice_key' => substr(random_int(100000, 999999) . time(), 0, 9),
            'team_key' => $teamKey,
            'brand_key' => $request->get('brand_key'),
            'creatorid' => $creatorid,
            'clientid' => $request->get('client_id'),
            'agent_id' => $request->get('agent_id'),
            'due_date' => $request->get('due_date'),
            'sales_type' => $request->get('sales_type'),
            'project_id' => $projectID,
            'invoice_descriptione' => $request->get('description'),
            'cur_symbol' => $request->get('cur_symbol'),
            'final_amount' => $request->get('value'),
            'tax_percentage' => $tax_percentage,
            'tax_amount' => $tax_amount,
            'total_amount' => $total_amount,
            'status' => 'due',
        ]);

        return $invoice;

    }

    public function create_client_project(Request $request)
    {
        $creatorid = Auth::user()->id;
        $teamKey = Auth::user()->team_key;

        $project = Project::create([
            'team_key' => $teamKey,
            'brand_key' => $request->get('brand_key'),
            'creatorid' => $creatorid,
            'clientid' => $request->get('client_id'),
            'agent_id' => $request->get('agent_id'),
            'asigned_id' => '0',
            'category_id' => $request->get('category_id'),
            'project_title' => $request->get('title'),
            'project_date_start' => $request->get('start_date'),
            'project_date_due' => $request->get('due_date'),
            'project_description' => $request->get('description'),
            'project_status' => '1',
            'project_cost' => $request->get('project_cost'),
            'project_progress' => '1'
        ]);

        return $project;

    }


}
