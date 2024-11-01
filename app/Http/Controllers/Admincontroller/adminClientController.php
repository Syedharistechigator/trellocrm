<?php

namespace App\Http\Controllers\Admincontroller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Add_phones;
use App\Models\Brand;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use App\Models\ProjectStatus;
use App\Models\Payment;
use App\Models\AssignBrand;
use App\Models\ProjectCategory;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client as GuzzleClient;


class adminClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clients = Client::all();
        return view('admin.client.index', compact('clients'));
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
        $client = Client::find($id);
        $client_add_phones = Add_phones::where('client_id', $id)->get();
        $client['brandLogo'] = Brand::where('brand_key', $client->brand_key)->value('logo');
        //client Invoice Data
        $invoices = array();
        $invoiceAmount = Invoice::where('clientid', $id)->sum('final_amount');
        $invoicesClient = Invoice::where('clientid', $id)->get();
        foreach ($invoicesClient as $inv) {

            $projectName = Project::where('id', $inv->project_id)->value('project_title');
            $inv['ProjectName'] = $projectName;

            $brand = Brand::where('brand_key', $inv->brand_key)->first();
            $inv['brandName'] = $brand->name ?? null;
            $inv['brandUrl'] = $brand->brand_url ?? null;

            array_push($invoices, $inv);
        }

        //Client Projects
        $completeProject = Project::where(['clientid' => $id, 'project_status' => 5])->count();
        $openProject = Project::where(['clientid' => $id, 'project_status' => 2])->count();

        $clientProjects_yy = Project::where('clientid', $id)->get();

        $clientProjects = array();

        foreach ($clientProjects_yy as $yy) {
            $brand = Brand::where('brand_key', $yy->brand_key)->value('name');
            $yy['brandName'] = $brand;

            $agent = User::where('id', $yy->agent_id)->first();
            $yy['agentName'] = $agent->name;
            $yy['agentDesignation'] = $agent->designation;
            $yy['agentImage'] = $agent->image;

            $status = ProjectStatus::where('id', $yy->project_status)->first();
            $yy['status'] = $status->status;
            $yy['statusColor'] = $status->status_color;

            array_push($clientProjects, $yy);
        }

        //Clients Payments
        $payments = array();
        $clientspayments = Payment::where('clientid', $id)->get();
        $totalPayment = Payment::where('clientid', $id)->sum('amount');

        foreach ($clientspayments as $pay) {
            $projectName = Project::where('id', $pay->project_id)->value('project_title');
            $pay['ProjectName'] = $projectName;
            array_push($payments, $pay);
        }

        //Project Status
        $projectStatus = ProjectStatus::all();

        $data = AssignBrand::all();
        $teamBrand = array();

        foreach ($data as $a) {
            $brand_key = $a->brand_key;
            $brands = Brand::where('brand_key', $brand_key)->get();
            foreach ($brands as $brand) {
                $a['brandKey'] = $brand->brand_key;
                $a['brandName'] = $brand->name;
                array_push($teamBrand, $a);
            }
        }


        //Team Members
        $members = User::where(['team_key' => $client->team_key, 'status' => 1])
            ->where('type', '!=', 'client')->orderBy('type', 'asc')->get();
        //Project Category
        $projectCategories = ProjectCategory::all();

        $client_call_logs = DB::table('call_logs')->where('client_id', $id)->get();
        return view('admin.client.profile', compact('projectCategories', 'client', 'invoices', 'members', 'invoiceAmount', 'teamBrand', 'payments', 'totalPayment', 'completeProject', 'openProject', 'clientProjects', 'projectStatus', 'client_add_phones', 'client_call_logs'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function client_add_phone(Request $request)
    {
        // Validate the request
        $request->validate([
            'client_id' => 'required',
            'phone' => 'required',
        ]);

        // Insert into the add_phones table
        Add_phones::create([
            'client_id' => $request->input('client_id'),
            'phone' => $request->input('phone'),
        ]);

        // You can return a response or redirect as needed
        return response()->json(['message' => 'Phone # added successfully']);
    }

    public function client_destroy_phone(Add_phones $id)
    {
        $id->delete();
        return redirect()->back()
            ->with('success', 'Phone # deleted successfully');
    }

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
}
