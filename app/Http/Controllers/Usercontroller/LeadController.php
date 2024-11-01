<?php

namespace App\Http\Controllers\Usercontroller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Lead;
use App\Models\User;
use App\Models\Team;
use App\Models\Brand;
use App\Models\AssignBrand;
use App\Models\LeadStatus;
use App\Models\LeadAssign;
use DB;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{
    // Lead Index
//    public function index()
//    {
//        $team_key =  Auth::user()->team_key;
//        $type = Auth::user()->type;
//        $leadsStatus = LeadStatus::all();
//
////        dd(Auth::user());
//
//
//
//        $agentSales = User::where(['team_key' => $team_key , 'status' => 1])->where('type','!=','client')->get();
//        $teams = Team::where('status','1')->get();
//
//        //Team Brands
//        if($type == 'ppc'){
//            $AssignBrand = AssignBrand::all();
//        }else{
//            $AssignBrand = AssignBrand::where('team_key', $team_key)->get();
//        }
//
//        $team_brand = array();
//        $team_brands_id = array();
//        foreach($AssignBrand as $a){
//            $brand_key =  $a->brand_key;
//            $brands = Brand::where('brand_key',$brand_key)->get();
//            foreach($brands as $brand){
//                $a['brand_name'] = $brand->name;
//                array_push($team_brand,$a);
//                array_push($team_brands_id,$brand->brand_key);
//            }
//        }
//
//        //Leads data
//        // if($type == 'ppc'){
//        //     $leads = Lead::all();
//        // } else{
//        //     $leads = Lead::where('team_key' , $id)->orderBy('id', 'desc')->get();
//        // }
//        if($type == 'ppc'){
//            $leads = Lead::orderBy('id', 'desc')->Paginate(15);
//        } else{
//            $leads = Lead::whereIn('brand_key' ,$team_brands_id)->orderBy('id', 'desc')->Paginate(15);
//        }
//
//
//        $leadsdata = array();
//        foreach($leads as $lead){
//
//            $brandKey = $lead->brand_key;
//            $statusId = $lead->status;
//            $brandName = DB::table('brands')->where('brand_key', $brandKey)->value('name');
//            $lead['brandName'] = $brandName;
//
//            $leadStatus =  LeadStatus::where('id',$statusId)->first();
//            $lead['status'] = $leadStatus->status;
//            $lead['statusColor'] = $leadStatus->leadstatus_color;
//
//            $assingLead = LeadAssign::where('leadid',$lead->id)->value('userid');
//            $agentName = User::where(['id' => $assingLead , 'status' => 1])->value('name');
//            $lead['assignAgent'] = $agentName;
//
//            array_push($leadsdata,$lead);
//        }
//
//        return view('lead.index',compact('leadsdata','team_brand','leadsStatus','agentSales','teams','leads'));
//    }

    public function index(Request $request)
    {
        $team_key = Auth::user()->team_key;
        $leadsStatus = LeadStatus::all();
        if (Auth::user()->type === 'ppc') {
            $assignedTeams = auth()->user()->assigned_teams ?? [];
            if (auth()->user()->team_key && !in_array(auth()->user()->team_key, $assignedTeams)) {
                $assignedTeams[] = auth()->user()->team_key;
            }
            $agentSales = User::whereIn('team_key', $assignedTeams)->where('status', 1)->where('type', '!=', 'client')->get();
            /** Team Brands */
            $assign_brands = AssignBrand::whereIn('team_key', $assignedTeams)->whereHas('getBrandWithOutTrashed')->get();
            $teams = Team::whereIn('team_key', $assignedTeams)->where('status', '1')->get();
        } else {
            $agentSales = User::where('team_key', $team_key)->where('status', 1)->where('type', '!=', 'client')->get();
            /** Team Brands */
            $assign_brands = AssignBrand::where('team_key', $team_key)->whereHas('getBrandWithOutTrashed')->get();
            $teams = Team::where('status', '1')->get();
        }
        $leadType = 'all-leads';
        $query = new Lead();
        if ($request->has('listType') && $request->get('listType', 'my-leads') !== null && $request->get('listType') === "my-leads") {
            if ($request->get('listType') === 'my-leads') {
                $leadIds = LeadAssign::where('userid', Auth::user()->id)->get()->pluck('leadid')->toArray();
                $query = $query->whereIn('id', $leadIds);
                $leadType = 'my-leads';
            }
        }
        $result = $this->userDataFilter($request, $query, $assign_brands);
        $brandKey = $result['brandKey'];
        $fromDate = $result['fromDate'];
        $toDate = $result['toDate'];
        $leads = $result['data'];
        return view('lead.index', compact('leadType', 'fromDate', 'toDate', 'brandKey', 'assign_brands', 'leadsStatus', 'agentSales', 'teams', 'leads'));
    }

    public function create_lead(Request $request)
    {
        $rules = [
            'team_key' => 'required',
            'brand_key' => 'required',
            'title' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'details' => '',
            'source' => 'required',
            'value' => 'required',
        ];
        $messages = [

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
        return response()->json([
            'data' => Lead::create([
                'team_key' => $teamKey,
                'brand_key' => $request->get('brand_key'),
                'title' => $request->get('title'),
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'details' => '',
                'source' => $request->get('source'),
                'value' => $request->get('value')
            ]),
            'message' => 'Add Lead Successfully Created!'
        ], 200);
    }

    public function show($id)
    {
        $lead = Lead::find($id);
        $lead->view = 1;
        $lead->save();
        if (Auth::user()->type === 'ppc') {
            $brandTeam = AssignBrand::where('brand_key', $lead->brand_key)->where('team_key', $lead->team_key)->whereHas('getBrandWithOutTrashed')->first();
            $agentSales = [];
            if ($brandTeam) {
                $agentSales = User::where('team_key', $brandTeam->team_key)->where('status', 1)->where('type', '!=', 'client')->get();
            }
        } else {
            $agentSales = User::where(['team_key' => Auth::user()->team_key, 'status' => 1])->where('type', '!=', 'client')->get();
        }

        $brandData = Brand::where('brand_key', $lead->brand_key)->first();

        $leadStatus = LeadStatus::where('id', $lead->status)->first();
        $lead['statusName'] = $leadStatus->status;
        $lead['statusColor'] = $leadStatus->leadstatus_color;

        $lead['brandName'] = $brandData->name;
        $lead['brandUrl'] = $brandData->brand_url;

        return view('lead.show', compact('lead', 'agentSales'));
    }

    //brand Lead
    public function team_brands(Request $request)
    {
        $id = Auth::user()->team_key;
        $searchText = $request->search;

        $leads = array();

        if ($searchText == 0) {
            $teamLeads = Lead::where('team_key', $id)->get();
        } else {
            $teamLeads = Lead::where('brand_key', $searchText)->get();
        }

        $html = "";
        $html .= '<thead><tr><th>ID#</th><th>Title</th><th>Contact</th><th>Assigned</th><th>Date</th><th>Brand</th><th>Value</th><th class="text-center">Status</th><th>Action</th></tr></thead>';

        $html .= '<tbody>';

        foreach ($teamLeads as $lead) {

            $brandKey = $lead->brand_key;
            $statusId = $lead->status;

            $brandName = DB::table('brands')->where('brand_key', $brandKey)->value('name');
            $lead['brandName'] = $brandName;

            $leadStatus = LeadStatus::where('id', $statusId)->first();
            $lead['status'] = $leadStatus->status;
            $lead['statusColor'] = $leadStatus->leadstatus_color;

            $assingLead = LeadAssign::where('leadid', $lead->id)->value('userid');
            $agentName = User::where(['id' => $assingLead, 'status' => 1])->value('name');
            $lead['assignAgent'] = $agentName;

            array_push($leads, $lead);

            $sty = "";

            if ($lead->status == 'new') {
                $sty = "class='badge badge-info '";
            } else {
                $sty = "class='badge badge-success'";
            }

            $html .= '<tr>';
            $html .= '<td>' . $lead->id . '</td>';
            $html .= '<td class="align-middle"><a class="text-info" href="' . route('leadshow', $lead->id) . '"><span class="zmdi zmdi-open-in-new"></span> ' . $lead->title . '</a></td>';
            $html .= '<td>' . $lead->name . '</td>';
            $html .= '<td>' . $lead->assignAgent . '</td>';
            $html .= '<td>' . $lead->created_at->format('j F, Y') . '</td>';
            $html .= '<td>' . $lead->brandName . '</td>';
            $html .= '<td>$' . $lead->value . '.00</td>';
            $html .= '<td class="text-center"><span class="badge badge-' . $lead->statusColor . ' rounded-pill">' . $lead->status . '</span></td>';
            $html .= '<td>';

            $html .= '<a title="Assing to Agent" data-id="' . $lead->id . '" data-type="confirm" href="assign-agent" class="btn btn-info btn-sm btn-round cxm-assing" data-toggle="modal" data-target="#assignLead"><span class="zmdi zmdi-account-add"></span></a>';

            if ($lead->view == '0') {
                $html .= '<a href="javascript:void(0);" class="btn btn-danger btn-sm btn-round" title="Viewed"><i class="zmdi zmdi-close"></i></a>';
            } else {
                $html .= '<a href="javascript:void(0);" class="btn btn-success btn-sm btn-round" title="Not View"><i class="zmdi zmdi-check"></i></a>';
            }

            $html .= '<a title="View" href="' . route('leadshow', $lead->id) . '" class="btn btn-primary btn-sm btn-round"><i class="zmdi zmdi-eye"></i></a>';

            $html .= '<a title="Change Status" data-id="' . $lead->id . '" data-type="confirm" href="javascript:void(0);" class="btn bg-orange btn-sm btn-round statusChange" data-toggle="modal" data-target="#changeStatusModal"><span class="zmdi zmdi-settings"></span></a>';
            $html .= '<a title="Comments" data-id="' . $lead->id . '" href="#" class="btn btn-neutral btn-sm btn-round LeadComments" data-toggle="modal" data-target="#cxmCommentsModal"><i class="zmdi zmdi-comments text-info"></i></a>';
            $html .= '</td>';
            $html .= '</tr>';

        }

        $html .= '</tbody>';
        return $html;
    }

    public function monthly_lead(Request $request)
    {

        $id = Auth::user()->team_key;

        $searchText = $request->search;
        $data = explode("-", $searchText);

        $leads = array();

        // if(!empty($data)){
        //     $teamLeads = Lead::all();
        // }else{
        $teamLeads = Lead::where('team_key', $id)
            ->whereMonth('created_at', $data[1])
            ->whereYear('created_at', $data[0])
            ->orderBy('created_at', 'desc')->get();
        //}


        $html = "";
        $html .= '<thead><tr><th>ID#</th><th>Title</th><th>Contact</th><th>Assigned</th><th>Date</th><th>Brand</th><th>Value</th><th class="text-center">Status</th><th>Action</th></tr></thead>';

        $html .= '<tbody>';

        foreach ($teamLeads as $lead) {

            $brandKey = $lead->brand_key;
            $statusId = $lead->status;

            $brandName = DB::table('brands')->where('brand_key', $brandKey)->value('name');
            $lead['brandName'] = $brandName;

            $leadStatus = LeadStatus::where('id', $statusId)->first();
            $lead['status'] = $leadStatus->status;
            $lead['statusColor'] = $leadStatus->leadstatus_color;

            array_push($leads, $lead);

            $html .= '<tr>';
            $html .= '<td>' . $lead->id . '</td>';
            $html .= '<td class="align-middle"><a class="text-info" href="' . route('leadshow', $lead->id) . '"><span class="zmdi zmdi-open-in-new"></span> ' . $lead->title . '</a></td>';
            $html .= '<td>' . $lead->name . '</td>';
            $html .= '<td>Assign Agent</td>';
            $html .= '<td>' . $lead->created_at->format('j F, Y') . '</td>';
            $html .= '<td>' . $lead->brandName . '</td>';
            $html .= '<td>$' . $lead->value . '.00</td>';
            $html .= '<td class="text-center"><span class="badge badge-' . $lead->statusColor . ' rounded-pill">' . $lead->status . '</span></td>';
            $html .= '<td>';

            $html .= '<a title="Assing to Agent" data-id="' . $lead->id . '" data-type="confirm" href="assign-agent" class="btn btn-info btn-sm btn-round cxm-assing" data-toggle="modal" data-target="#assignLead"><span class="zmdi zmdi-account-add"></span></a>';

            if ($lead->view == '0') {
                $html .= '<a href="javascript:void(0);" class="btn btn-danger btn-sm btn-round" title="Viewed"><i class="zmdi zmdi-close"></i></a>';
            } else {
                $html .= '<a href="javascript:void(0);" class="btn btn-success btn-sm btn-round" title="Not View"><i class="zmdi zmdi-check"></i></a>';
            }

            $html .= '<a title="View" href="' . route('leadshow', $lead->id) . '" class="btn btn-primary btn-sm btn-round"><i class="zmdi zmdi-eye"></i></a>';

            $html .= '<a title="Change Status" data-id="' . $lead->id . '" data-type="confirm" href="javascript:void(0);" class="btn bg-orange btn-sm btn-round statusChange" data-toggle="modal" data-target="#changeStatusModal"><span class="zmdi zmdi-settings"></span></a>';
            $html .= '<a title="Comments" data-id="' . $lead->id . '" href="#" class="btn btn-neutral btn-sm btn-round LeadComments" data-toggle="modal" data-target="#cxmCommentsModal"><i class="zmdi zmdi-comments text-info"></i></a>';
            $html .= '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        return $html;
    }

    public function leadStatus(Request $request)
    {

        $leadId = $request->lead_id;
        $status = $request->LeadStatus;

        $lead = Lead::find($leadId);
        $lead->status = $status;
        $lead->save();

        return $lead;
    }


    public function assinglead(Request $request)
    {

        $leadId = $request->lead_id;
        $agent_id = $request->agenti_id;

        $assigned = LeadAssign::create([
            'leadid' => $leadId,
            'userid' => $agent_id,
        ]);

        return $assigned;
    }

    public function my_lead()
    {
        $id = Auth::user()->id;

        $html = "";
        $html .= '<thead><tr><th>ID#</th><th>Title</th><th>Contact</th><th>Assigned</th><th>Date</th><th data-breakpoints="sm xs">Brand</th><th data-breakpoints="sm xs">Value</th><th class="text-center" data-breakpoints="xs md">Status</th><th data-breakpoints="sm xs md">Action</th></tr></thead>';

        $html .= '<tbody>';

        $myLeaddata = array();
        $leadAssigned = LeadAssign::where('userid', $id)->get();

        foreach ($leadAssigned as $lead) {
            $data = Lead::where('id', $lead->leadid)->first();
            $lead['id'] = $data->id;
            $lead['title'] = $data->title;
            $lead['name'] = $data->name;
            $lead['date'] = $data->created_at;
            $lead['value'] = $data->value;
            $lead['view'] = $data->view;

            $leadStatus = LeadStatus::where('id', $data->status)->first();
            $lead['status'] = $leadStatus->status;
            $lead['statusColor'] = $leadStatus->leadstatus_color;

            $brandName = DB::table('brands')->where('brand_key', $data->brand_key)->value('name');
            $lead['brandName'] = $brandName;

            array_push($myLeaddata, $lead);

            $html .= '<tr>';
            $html .= '<td>' . $lead->id . '</td>';
            $html .= '<td class="align-middle"><a class="text-info" href="' . route('leadshow', $lead->id) . '"><span class="zmdi zmdi-open-in-new"></span> ' . $lead->title . '</a></td>';
            $html .= '<td>' . $lead->name . '</td>';
            $html .= '<td>Assign Agent</td>';
            $html .= '<td>' . $lead->created_at->format('j F, Y') . '</td>';
            $html .= '<td>' . $lead->brandName . '</td>';
            $html .= '<td>$' . $lead->value . '.00</td>';
            $html .= '<td class="text-center"><span class="badge badge-' . $lead->statusColor . ' rounded-pill">' . $lead->status . '</span></td>';
            $html .= '<td>';

            $html .= '<a title="Assing to Agent" data-id="' . $lead->id . '" data-type="confirm" href="assign-agent" class="btn btn-info btn-sm btn-round cxm-assing" data-toggle="modal" data-target="#assignLead"><span class="zmdi zmdi-account-add"></span></a>';

            if ($lead->view == '0') {
                $html .= '<a href="javascript:void(0);" class="btn btn-danger btn-sm btn-round" title="Viewed"><i class="zmdi zmdi-close"></i></a>';
            } else {
                $html .= '<a href="javascript:void(0);" class="btn btn-success btn-sm btn-round" title="Not View"><i class="zmdi zmdi-check"></i></a>';
            }

            $html .= '<a title="View" href="' . route('leadshow', $lead->id) . '" class="btn btn-primary btn-sm btn-round"><i class="zmdi zmdi-eye"></i></a>';

            $html .= '<a title="Change Status" data-id="' . $lead->id . '" data-type="confirm" href="javascript:void(0);" class="btn bg-orange btn-sm btn-round statusChange" data-toggle="modal" data-target="#changeStatusModal"><span class="zmdi zmdi-settings"></span></a>';
            $html .= '<a title="Comments" data-id="' . $lead->id . '" href="#" class="btn btn-neutral btn-sm btn-round LeadComments" data-toggle="modal" data-target="#cxmCommentsModal"><i class="zmdi zmdi-comments text-info"></i></a>';
            $html .= '</td>';
            $html .= '</tr>';

        }

        $html .= '</tbody>';
        return $html;
    }


    public function teamBrands($id)
    {
        $assign_brands = [];
        if (auth()->user()->type === 'ppc') {
            $assignedTeams = auth()->user()->assigned_teams ?? [];
            if (auth()->user()->team_key && !in_array(auth()->user()->team_key, $assignedTeams)) {
                $assignedTeams[] = auth()->user()->team_key;
            }

            if (in_array($id,$assignedTeams)) {
                $assign_brands = AssignBrand::where('team_key', $id)->whereHas('getBrandWithOutTrashed')->get();
            }
        } else {
            $assign_brands = AssignBrand::where('team_key', $id)->whereHas('getBrandWithOutTrashed')->get();
        }

        $team_brand = array();
        foreach ($assign_brands as $a) {
            $brand_key = $a->brand_key;
            foreach (Brand::where('brand_key', $brand_key)->get() as $brand) {
                $a['brand_name'] = $brand->name;
                $team_brand[] = $a;
            }
        }

        return $team_brand;
    }


}
