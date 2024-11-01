<?php

namespace App\Http\Controllers\Usercontroller;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Client;
use App\Models\Lead;
use App\Models\User;
use App\Models\Brand;
use App\Models\AssignBrand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\File;
use App\Models\Comment;
use Carbon\Carbon;
use Validator;


class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function filterProjects(Request $request, $model)
    {
        if ($request->search_box != null) {
            $model->where(function ($query) use ($request) {
                $query->where('project_title', 'LIKE', "%$request->search_box%")
                    ->orWhereHas('getStatus', function ($subquery) use ($request) {
                        $subquery->where('status', 'LIKE', "%$request->search_box%");
                    });
            });
        }
        if ($request->project_client_filter != 0) {
            $model->where('clientid', $request->project_client_filter);
        }
        if ($request->project_agent_filter != 0) {
            $model->where('agent_id', $request->project_agent_filter);
        }
        if ($request->project_status != 0) {
            $model->where('project_status', $request->project_status);
        }
        return $model;
    }

    private function formatProjectData($projects, $gridView = true)
    {
        $all_projects = '';
        $projectsData = array();
        foreach ($projects as $project) {

            $brand = Brand::where('brand_key', $project->brand_key)->value('name');
            $project['brandName'] = $brand;

            $agent = User::where('id', $project->agent_id)->first();
            $project['agentName'] = $agent->name ?? '';
            $project['agentDesignation'] = $agent->designation ?? '';
            $project['agentImage'] = $agent->image ?? '';

            $pm = User::where('id', $project->asigned_id)->first();
            if ($pm) {
                $project['pmName'] = $pm->name;
                $project['pmDesignation'] = $pm->designation;
                $project['pmImage'] = $pm->image;
            } else {
                $project['pmName'] = '---';
                $project['pmDesignation'] = '----';
            }

            $status = ProjectStatus::where('id', $project->project_status)->first();
            $project['status'] = $status->status ?? '';
            $project['statusColor'] = $status->status_color ?? '';
            if ($gridView) {
                $all_projects .= '<div class="col-md-4">
                                    <div class="card author-box card-info">
                                                        <div class="card-body">
                                                            <div class="card-header-action float-right">
                                                                <div class="row align-items-center">
                                                                    <div class="col-3 p-0">
                                                                        <div class="dropdown card-widgets">
                                                                            <a href="#" class="btn dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="zmdi zmdi-settings"></i></a>
                                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                                <a data-id="' . $project->id . '" data-table-id="1" class="dropdown-item has-icon modal-edit-project-ajax editproject"
                                                                                   data-toggle="modal" data-target="#EditProjecteModal" href="javascript:void(0);" title="Edit"><i class="zmdi zmdi-edit"></i>Edit</a>
                                                                                <a class="dropdown-item has-icon delete-project-alert" href="#" data-project_id="1011"><i class="zmdi zmdi-delete"></i>Delete</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <div class="card-widgets mt-2">
                                                                            <i class="zmdi zmdi-star-outline text-golden"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="author-box-name">
                                                                <a href="' . route('project.new.detail', $project->id) . '">' . $project->project_title . '</a>
                                                            </div>
                                                            <div class="author-box-job">
                                                                <div class="badge badge-' . (isset($project->getStatus) ? $project->getStatus->status_color : "info") . ' projects-badge">' . (isset($project->getStatus) ? $project->getStatus->status : "") . '</div>
                                                            </div>
                                                            <div class="author-box-description">
                                                                <p>' . $project->decription . '</p>
                                                            </div>
                                                            <div class="mb-2 mt-3">
                                                                <span class="pr-2 mb-2 d-inline-block">
                                                                    <i class="zmdi zmdi-format-list-bulleted text-muted"></i>
                                                                    <b>0</b> Tasks
                                                                </span>
                                                                <span class="mb-2 d-inline-block">
                                                                    <i class="zmdi zmdi-comments text-muted"></i>
                                                                    <b>0</b> Comments
                                                                </span>
                                                                <div class="w-100 d-sm-none"></div>
                                                                <div class="float-right mt-sm-0 mt-3">
                                                                    <a href="' . route('project.new.detail', $project->id) . '"
                                                                       class="btn btn-sm btn-primary no-shadow">Details
                                                                        <i class="zmdi zmdi-chevron-right"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2 mt-3">
                                                                <span class="pr-2 mb-2 d-inline-block">
                                                                    <i class="zmdi zmdi-calendar text-muted"></i>
                                                                    <b>Start Date: </b>
                                                                    <span class="text-primary">' . ($project->project_date_start ? carbon::parse($project->project_date_start)->format('d-M-Y') : "Not available") . '</span>
                                                                </span>
                                                                <span class="mb-2 d-inline-block">
                                                                    <b>Due Date: </b>
                                                                    <span class="text-primary">' . ($project->project_date_due ? carbon::parse($project->project_date_due)->format('d-M-Y') : "Not available") . '</span>
                                                                </span>
                                                                <div class="w-100 d-sm-none"></div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-4"><span> Priority</span></div>
                                                                <div class="col-md-8">
                                                                    <span class="badge badge-' . ($project->priority === 1 ? "info" : ($project->priority === 2 ? "warning" : ($project->priority === 3 ? "danger" : "secondary"))) . '">' . ($project->priority === 1 ? "Low" : ($project->priority === 2 ? "Medium" : ($project->priority === 3 ? "High" : "Unknown"))) . ' </span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <div class="row">
                                                                    <div class="col-4">
                                                                        <span> Days till DueDate </span>
                                                                    </div>
                                                                    <div class="col-8">
                                                                        <span class="' . $project->remainingDays()['badgeClass'] . ' badge-pill">' . $project->remainingDays()['message'] . '</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <h6 class="mt-1">Clients</h6>
                                                                    <a href="#">
                                                                        <figure class="avatar avatar-md" data-toggle="tooltip" data-title="' . (isset($project->getClient) ? $project->getClient->name : "") . '"><img alt="image" src="' . (isset($project->getClientUser) && $project->getClientUser->image && file_exists(public_path('assets/images/profile_images/') . $project->getClientUser->image) ? asset('assets/images/profile_images/' . $project->getClientUser->image) : asset('assets/images/no-results-found.png')) . '" class="rounded-circle"/></figure>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-footer pt-0">
                                                            <h6>Tasks Insights</h6>
                                                            <div class="progress">
                                                                <div class="progress-bar progress-bar-striped bg-dark"
                                                                     role="progressbar" data-width="100%"
                                                                     aria-valuenow="100"
                                                                     aria-valuemin="0" aria-valuemax="100"
                                                                     style="width: 100%">No tasks assigned
                                                                 </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
            } else {
                $projectsData[] = $project;
            }
        }
        if ($gridView) {
            return $all_projects;
        }
        return $projectsData;
    }

    public function load_more_projects(Request $request)
    {
        $limit = 10;
        $team_key = Auth::user()->team_key;
        $type = Auth::user()->type;
        $offset = (int)$request->offset;
        $projectsQuery = Project::where('team_key', $team_key);
        if ($type == 'ppc' || $type == 'qa') {
            $projectsQuery = Project::query();
        }
        $this->filterProjects($request, $projectsQuery);
        $totalProjectsCount = $projectsQuery->count();
        $projects = $projectsQuery->skip($offset)->take($limit)->get();
        $all_projects = $this->formatProjectData($projects);
        $response = [
            'total_projects' => $totalProjectsCount,
            'received_projects' => count($projects),
            'all_projects' => $all_projects,
        ];
        return response()->json($response);
    }

    private function getTeamBrands($team_key)
    {
        $teamBrand = [];
        AssignBrand::where('team_key', $team_key)->get()->each(function ($a) use (&$teamBrand) {
            $brand_key = $a->brand_key;
            Brand::where('brand_key', $brand_key)->get()->each(function ($brand) use (&$a, &$teamBrand) {
                $a['brandKey'] = $brand->brand_key;
                $a['brandName'] = $brand->name;
                $teamBrand[] = $a;
            });
        });
        return $teamBrand;
    }

//        $cardNumber = cxmDecrypt($request->card_number, $pkey);
//        $card_cvv = cxmDecrypt($request->card_cvv, $pkey);
    public function new_index(Request $request)
    {
        $clientId = $request->get('project_client_filter', 0);
        $agentId = $request->get('project_agent_filter', 0);
        $statusId = $request->get('project_status', 0);

        $team_key = Auth::user()->team_key;
        $type = Auth::user()->type;
        $teamClients = Client::where('team_key', $team_key)->get();
        $projectCategories = ProjectCategory::all();
        $teamBrand = $this->getTeamBrands($team_key);
        /**Team Members*/
        $members = User::where([
            'team_key' => $team_key,
            'status' => 1,
            'type' => 'staff',
            //'staff_division'=>'agent'
        ])->orderBy('type')->get();
        $accountManager = '';
        $projectsQuery = Project::where('team_key', $team_key);
        if ($type == 'ppc' || $type == 'qa') {
            $projectsQuery = Project::query();
        }
        $this->filterProjects($request, $projectsQuery);
        $projects = $projectsQuery->get();
        $projectsData = $this->formatProjectData($projects, false);

        $projectStatus = ProjectStatus::all();
        return view('project.new-index', compact('clientId', 'agentId', 'statusId', 'teamClients', 'teamBrand', 'members', 'projectCategories', 'projectsData', 'projectStatus', 'accountManager'));
    }

    public function new_detail($id)
    {


        $userType = Auth::user()->type;
        $project = Project::find($id);

        $clientInfo = Client::where('id', $project->clientid)->first();
        $project['clientId'] = $clientInfo->id;
        $project['clientName'] = $clientInfo->name;

        $projectStatus = ProjectStatus::where('id', $project->project_status)->first();
        $project['projectStatus'] = $projectStatus->status;
        $project['projectStatusColor'] = $projectStatus->status_color;

        $project['category'] = ProjectCategory::where('id', $project->category_id)->value('name');
        $project['projectAgent'] = User::where('id', $project->agent_id)->value('pseudo_name');
        $project['projectManager'] = User::where('id', $project->asigned_id)->value('pseudo_name');


        //project invoices
        $projectInvoices = array();
        $projectInv = Invoice::where('project_id', $id)->get();

        foreach ($projectInv as $proInv) {
            $brand = Brand::where('brand_key', $proInv->brand_key)->first();

            $proInv['brandName'] = $brand->name;
            $proInv['brandUrl'] = $brand->brand_url;
            array_push($projectInvoices, $proInv);
        }
        $projectInvoicesAmount = Invoice::where('project_id', $id)->sum('final_amount');

        //project payments
        $projectPayments = Payment::where('project_id', $id)->get();

        $projectPaymentsAmount = Payment::where('project_id', $id)->sum('amount');
        //project files
        if ($userType == 'client') {
            $projectFiles = File::where(['projectid' => $id, 'visibility_client' => '1'])->get();
        } else {
            $projectFiles = File::where('projectid', $id)->get();
        }

        $data = AssignBrand::where('team_key', $project->team_key)->get();
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
        $members = User::where(['team_key' => $project->team_key, 'status' => 1])
            ->where('type', '!=', 'client')->orderBy('type', 'asc')->get();


        $projectComments = Comment::where('projectid', $id)->get();
        $comments = array();
        foreach ($projectComments as $comment) {
            $comment['creatorName'] = User::where('id', $comment->creatorid)->value('pseudo_name');
            $comment['clientName'] = Client::where('id', $comment->clientid)->value('name');
            array_push($comments, $comment);
        }

        return view('project.new-detail', compact('comments', 'project', 'projectInvoices', 'projectPayments', 'members', 'teamBrand', 'projectInvoicesAmount', 'projectPaymentsAmount', 'projectFiles'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    /**Create Project and also need to create client and user if client is new*/
    public function new_store(Request $request)
    {
        $creatorid = Auth::user()->id;
        $teamKey = Auth::user()->team_key;
        $type = $request->get('type');
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|after:today',
            'due_date' => 'required|date|after:start_date',
            'type' => 'required',
            'name' => 'required_if:type,==,new',
            'email' => 'required_if:type,==,new',
            'phone' => 'required_if:type,==,new',
            'client_id' => 'required_if:type,==,existing',
            'brand_key' => 'required',
            'agent_id' => 'required',
            'category_id' => 'required',
            'title' => 'required',
            'description' => 'required',
            'priority_id' => 'required',
        ], [
            'start_date.required' => 'The Start date is required.',
            'start_date.date' => 'The Start date must be a valid date.',
            'start_date.after' => 'The Start date must be after today.',
            'due_date.required' => 'The Due date is required.',
            'due_date.date' => 'The Due date must be a valid date.',
            'due_date.after' => 'The Due date must be after the start date.',
            'type.required' => 'The Client field is required.',
            'name.required_if' => 'The Name field is required when the client is new.',
            'email.required_if' => 'The Email field is required when the client is new.',
            'email.email' => 'Please provide a valid email address.',
            'phone.required_if' => 'The Phone field is required when the client is new.',
            'client_id.required_if' => 'The Client field is required when the client is existing.',
            'brand_key.required' => 'The Brand name field is required.',
            'agent_id.required' => 'The Sales agent name field is required.',
            'title.required' => 'The Project title field is required.',
            'description.required' => 'The Project Description field is required.',
            'priority_id.required' => 'The Project priority field is required.',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $clientId = $request->get('client_id');
        if ($type === 'new') {
            $client = Client::create([
                'team_key' => $teamKey,
                'brand_key' => $request->get('brand_key'),
                'creatorid' => $creatorid,
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'address' => '',
                'status' => 1,
                'agent_id' => $request->get('agent_id')
            ]);
            $clientId = $client->id;
            User::create([
                'team_key' => $teamKey,
                'name' => $request->get('name'),
                'designation' => '',
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'password' => Hash::make('12345678'),
                'type' => 'client',
                'clientid' => $clientId,
                'target' => '0',
                'image' => '',
                'status' => '1',
                'login_access' => '0'
            ]);
        }
        Project::create([
            'team_key' => $teamKey,
            'brand_key' => $request->get('brand_key'),
            'creatorid' => $creatorid,
            'clientid' => $clientId,
            'agent_id' => $request->get('agent_id'),
            'asigned_id' => '0',
            'category_id' => $request->get('category_id'),
            'project_title' => $request->get('title'),
            'project_date_start' => $request->get('start_date'),
            'project_date_due' => $request->get('due_date'),
            'project_description' => $request->get('description'),
            'project_status' => '1',
            'project_cost' => $request->get('project_cost', 0),
            'project_progress' => '1',
            'priority' => $request->get('priority_id', 1),
        ]);
        return response()->json(['success' => 'Added new records.']);

    }
        public function show_new($id)
    {

        $userType = Auth::user()->type;
        $project = Project::find($id);

        $clientInfo = Client::where('id', $project->clientid)->first();
        $project['clientId'] = $clientInfo->id;
        $project['clientName'] = $clientInfo->name;

        $projectStatus = ProjectStatus::where('id', $project->project_status)->first();
        $project['projectStatus'] = $projectStatus->status;
        $project['projectStatusColor'] = $projectStatus->status_color;

        $project['category'] = ProjectCategory::where('id', $project->category_id)->value('name');
        $project['projectAgent'] = User::where('id', $project->agent_id)->value('pseudo_name');
        $project['projectManager'] = User::where('id', $project->asigned_id)->value('pseudo_name');


        //project invoices
        $projectInvoices = array();
        $projectInv = Invoice::where('project_id', $id)->get();

        foreach ($projectInv as $proInv) {
            $brand = Brand::where('brand_key', $proInv->brand_key)->first();

            $proInv['brandName'] = $brand->name;
            $proInv['brandUrl'] = $brand->brand_url;
            array_push($projectInvoices, $proInv);
        }
        $projectInvoicesAmount = Invoice::where('project_id', $id)->sum('final_amount');

        //project payments
        $projectPayments = Payment::where('project_id', $id)->get();

        $projectPaymentsAmount = Payment::where('project_id', $id)->sum('amount');
        //project files
        if ($userType == 'client') {
            $projectFiles = File::where(['projectid' => $id, 'visibility_client' => '1'])->get();
        } else {
            $projectFiles = File::where('projectid', $id)->get();
        }

        $data = AssignBrand::where('team_key', $project->team_key)->get();
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
        $members = User::where(['team_key' => $project->team_key, 'status' => 1])
            ->where('type', '!=', 'client')->orderBy('type', 'asc')->get();


        $projectComments = Comment::where('projectid', $id)->get();
        $comments = array();
        foreach ($projectComments as $comment) {
            $comment['creatorName'] = User::where('id', $comment->creatorid)->value('pseudo_name');
            $comment['clientName'] = Client::where('id', $comment->clientid)->value('name');
            array_push($comments, $comment);
        }

        return view('project.new-detail-project', compact('comments', 'project', 'projectInvoices', 'projectPayments', 'members', 'teamBrand', 'projectInvoicesAmount', 'projectPaymentsAmount', 'projectFiles'));

    }
    public function index()
    {
        $id = Auth::user()->team_key;
        $type = Auth::user()->type;
        $agentId = Auth::user()->id;
        $staff_Div = Auth::user()->staff_division;

        //Team Client
        $teamClients = Client::where('team_key', $id)->get();
        //Project Category
        $projectCategories = ProjectCategory::all();

        //Team Brand
        $data = AssignBrand::where('team_key', $id)->get();

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
        $members = User::where([
            'team_key' => $id,
            'status' => 1,
            'type' => 'staff',
            //'staff_division'=>'agent'
        ])->orderBy('type', 'asc')->get();


        // Team Account Manager
        // $accountManager = User::where(
        //     [
        //         'team_key' => $id,
        //         'status' => 1,
        //         'type' => 'staff',
        //         'staff_division'=>'Account Manager'
        //     ])->orderBy('type', 'asc')->get();
        $accountManager = '';

        $projectsData = array();
        // if($type == 'staff'){
        //     $projects = Project::where(['team_key' =>$id,'agent_id'=>$agentId])->get();
        // }elseif($type == 'hob' or $type == 'qa' ){
        //     $projects = Project::all();
        // }else{
        //     $projects = Project::where('team_key',$id)->get();
        // }

        if ($type == 'ppc' or $type == 'qa') {
            $projects = Project::all();
        } else {
            $projects = Project::where('team_key', $id)->get();
        }

        foreach ($projects as $yy) {
            $cName = Client::where('id', $yy->id)->value('name');
            $yy['clientName'] = $cName;

            $brand = Brand::where('brand_key', $yy->brand_key)->value('name');
            $yy['brandName'] = $brand;

            $agent = User::where('id', $yy->agent_id)->first();
            $yy['agentName'] = $agent->name??"";
            $yy['agentDesignation'] = $agent->designation??"";
            $yy['agentImage'] = $agent->image??"";

            $pm = User::where('id', $yy->asigned_id)->first();
            if ($pm) {
                $yy['pmName'] = $pm->name;
                $yy['pmDesignation'] = $pm->designation;
                $yy['pmImage'] = $pm->image;
            } else {
                $yy['pmName'] = '---';
                $yy['pmDesignation'] = '----';
            }
            $status = ProjectStatus::where('id', $yy->project_status)->first();
            $yy['status'] = $status->status;
            $yy['statusColor'] = $status->status_color;

            array_push($projectsData, $yy);
        }

        $projectStatus = ProjectStatus::all();
        return view('project.index', compact('teamClients', 'teamBrand', 'members', 'projectCategories', 'projectsData', 'projectStatus', 'accountManager'));
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
        $creatorid = Auth::user()->id;
        $teamKey = Auth::user()->team_key;
        $type = $request->get('type');

        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|after:today',
            'due_date' => 'required|date|after:start_date'
        ]);


        if ($validator->passes()) {
            if ($type == 'new') {
                $client = Client::create([
                    'team_key' => $teamKey,
                    'brand_key' => $request->get('brand_key'),
                    'creatorid' => $creatorid,
                    'name' => $request->get('first_name'),
                    'email' => $request->get('email'),
                    'phone' => $request->get('phone'),
                    'address' => '',
                    'status' => 1,
                    'agent_id' => $request->get('agent_id')
                ]);

                $clientId = $client->id;

                $user = User::create([
                    'team_key' => $teamKey,
                    'name' => $request->get('first_name'),
                    'designation' => '',
                    'email' => $request->get('email'),
                    'phone' => $request->get('phone'),
                    'password' => Hash::make('12345678'),
                    'type' => 'client',
                    'clientid' => $clientId,
                    'target' => '0',
                    'image' => '',
                    'status' => '1',
                    'login_access' => '0'
                ]);

                $project = Project::create([
                    'team_key' => $teamKey,
                    'brand_key' => $request->get('brand_key'),
                    'creatorid' => $creatorid,
                    'clientid' => $clientId,
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
            } else {
                $clientId = $request->get('client_id');

                $project = Project::create([
                    'team_key' => $teamKey,
                    'brand_key' => $request->get('brand_key'),
                    'creatorid' => $creatorid,
                    'clientid' => $clientId,
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
            }
            return response()->json(['success' => 'Added new records.']);
        }

        return response()->json(['error' => $validator->errors()->all()]);
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $userType = Auth::user()->type;
        $project = Project::find($id);

        $clientInfo = Client::where('id', $project->clientid)->first();
        $project['clientId'] = $clientInfo->id;
        $project['clientName'] = $clientInfo->name;

        $projectStatus = ProjectStatus::where('id', $project->project_status)->first();
        $project['projectStatus'] = $projectStatus->status;
        $project['projectStatusColor'] = $projectStatus->status_color;

        $project['category'] = ProjectCategory::where('id', $project->category_id)->value('name');
        $project['projectAgent'] = User::where('id', $project->agent_id)->value('pseudo_name');
        $project['projectManager'] = User::where('id', $project->asigned_id)->value('pseudo_name');


        //project invoices
        $projectInvoices = array();
        $projectInv = Invoice::where('project_id', $id)->get();

        foreach ($projectInv as $proInv) {
            $brand = Brand::where('brand_key', $proInv->brand_key)->first();

            $proInv['brandName'] = $brand->name;
            $proInv['brandUrl'] = $brand->brand_url;
            array_push($projectInvoices, $proInv);
        }
        $projectInvoicesAmount = Invoice::where('project_id', $id)->sum('final_amount');

        //project payments
        $projectPayments = Payment::where('project_id', $id)->get();

        $projectPaymentsAmount = Payment::where('project_id', $id)->sum('amount');
        //project files
        if ($userType == 'client') {
            $projectFiles = File::where(['projectid' => $id, 'visibility_client' => '1'])->get();
        } else {
            $projectFiles = File::where('projectid', $id)->get();
        }

        $data = AssignBrand::where('team_key', $project->team_key)->get();
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
        $members = User::where(['team_key' => $project->team_key, 'status' => 1])
            ->where('type', '!=', 'client')->orderBy('type', 'asc')->get();


        $projectComments = Comment::where('projectid', $id)->get();
        $comments = array();
        foreach ($projectComments as $comment) {
            $comment['creatorName'] = User::where('id', $comment->creatorid)->value('pseudo_name');
            $comment['clientName'] = Client::where('id', $comment->clientid)->value('name');
            array_push($comments, $comment);
        }

        return view('project.detail', compact('comments', 'project', 'projectInvoices', 'projectPayments', 'members', 'teamBrand', 'projectInvoicesAmount', 'projectPaymentsAmount', 'projectFiles'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Project $project
     * @return Project|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Http\Response|\LaravelIdea\Helper\App\Models\_IH_Project_QB|object
     */
    public function edit($id)
    {
        return Project::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $project = Project::find($id);
        if ($request->has('client_id')) {
            $project->clientid = $request->get('client_id');
        }
        $project->brand_key = $request->brand_key;
        $project->agent_id = $request->agent_id;
        $project->category_id = $request->category_id;
        $project->project_title = $request->title;
        $project->project_description = $request->description;
        $project->project_date_start = $request->start_date;
        $project->project_date_due = $request->due_date;
        $project->project_cost = $request->project_cost;
        if ($request->has('priority_id')) {
            $project->priority = $request->get('priority_id', 1);
        }
        if ($request->has('status_id')) {
            $project->project_status = $request->get('status_id', 1);
        }
        $project->save();

        return $project;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        //
    }

    public function projectStatus(Request $request)
    {

        $projectId = $request->project_id;
        $status = $request->projectStatus;

        $project = Project::find($projectId);
        $project->project_status = $status;
        $project->save();

        return $project;
    }

    public function projectManager(Request $request)
    {

        $projectId = $request->project_id;
        $projectManager = $request->projectManager;

        $project = Project::find($projectId);
        $project->asigned_id = $projectManager;
        $project->save();

        return $project;
    }


    public function projectDetailsUpdate(Request $request)
    {

        $projectId = $request->project_id;
        $details = $request->ProjectDetails;

        $project = Project::find($projectId);
        $project->project_description = $details;
        $project->save();

        return $project;
    }

    public function create_project_invoice(Request $request)
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

        $invoice = Invoice::create([
            'invoice_num' => 'INV-' . random_int(100000, 999999),
            'invoice_key' => random_int(11,99).substr(time(), 1, 3).random_int(11,99).substr(time(), 8, 2),
            'team_key' => $teamKey,
            'brand_key' => $request->get('brand_key'),
            'creatorid' => $creatorid,
            'clientid' => $request->get('client_id'),
            'agent_id' => $request->get('agent_id'),
            'due_date' => $request->get('due_date'),
            'sales_type' => $request->get('sales_type'),
            'project_id' => $request->get('project_id'),
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

    public function upload_project_file(Request $request)
    {

        $file = $request->file('upload_file');

        $creatorid = Auth::user()->id;
        $teamKey = Auth::user()->team_key;
        $clientId = $request->get('client_id');
        $projectId = $request->get('project_id');
        $filePath = time() . '.' . $file->getClientOriginalExtension();
        $fileresource_type = $request->get('fileresource_type');


        $upload_file = File::create([
            'team_key' => $teamKey,
            'brand_key' => $request->get('brand_key'),
            'creatorid' => $creatorid,
            'clientid' => $request->get('client_id'),
            'projectid' => $request->get('project_id'),
            'filename' => $filePath,
            'extension' => $file->getClientOriginalExtension(),
            'size' => $file->getSize(),
            'type' => $file->getMimeType(),
            'thumbname' => $file->getClientOriginalName(),
            'fileresource_type' => $fileresource_type,
            'visibility_client' => ($fileresource_type == 'client') ? '1' : '0'
        ]);

        //Move Uploaded File
        $destinationPath = 'uploads';
        $file->move($destinationPath, $filePath);

        return $upload_file;
    }

    public function delete_file($id)
    {
        File::find($id)->delete();
        return true;

    }

    public function create_project_comment(Request $request)
    {
        $creatorid = Auth::user()->id;
        $type = Auth::user()->type;
        $list_html = "";
        $comment = Comment::create([
            'creatorid' => $creatorid,
            'clientid' => $request->get('client_id'),
            'projectid' => $request->get('project_id'),
            'comment_text' => $request->get('comment'),
            'type' => ($type == 'client') ? 'client' : 'staff'
        ]);

        $comment['creatorName'] = User::where('id', $creatorid)->value('pseudo_name');
        $comment['clientName'] = Client::where('id', $request->get('client_id'))->value('name');

        $list_html .= '<li class="right">
                    <img src="';
        $list_html .= url("assets/images/xs/avatar3.jpg");
        $list_html .= '" class="rounded-circle" alt="">
                        <ul class="list-unstyled chat_info">
                            <li><small>';
        $list_html .= $comment->creatorName;
        $list_html .= ' ';
        $list_html .= $comment->created_at->diffForHumans();
        $list_html .= '</small></li>
                                        <li><span class="message">';
        $list_html .= $comment->comment_text;
        $list_html .= '</span></li>
                            </ul>
                    </li>';

        return $list_html;
    }

    //Client Views Functions

    public function client_projects()
    {

        $loginClientId = Auth::user()->clientid;
        $projectsData = Project::where('clientid', $loginClientId)->get();
        $projects = array();

        foreach ($projectsData as $yy) {
            $status = ProjectStatus::where('id', $yy->project_status)->first();
            $yy['status'] = $status->status;
            $yy['statusColor'] = $status->status_color;

            array_push($projects, $yy);
        }


        return view('project.clientproject', compact('projects'));

    }

    public function visibilityFilestatus(Request $request)
    {

        $fileData = File::where('id', $request->file_id)->first();

        File::where('id', $fileData->id)->update(
            array('visibility_client' => $request->status,
            ));

        return response()->json(['success' => 'Status change successfully.']);
    }

    public function all_comments(Request $request)
    {
        $id = $request->projectId;
        $projectAllComments = Comment::where('projectid', $id)->get();

        $cxm_comments_html = '<li>&nbsp;</li>';
        foreach ($projectAllComments as $comment) {
            $comment['creatorName'] = User::where('id', $comment->creatorid)->value('pseudo_name');
            $comment['clientName'] = Client::where('id', $comment->clientid)->value('name');

            $cxmChatClass = (Auth::user()->id == $comment->creatorid) ? 'right' : 'left';

            $cxm_comments_html .= '<li class="' . $cxmChatClass . '">';
            $cxm_comments_html .= '<img src="' . url("assets/images/xs/avatar3.jpg") . '" class="rounded-circle" alt="Avatar">';
            $cxm_comments_html .= '<ul class="list-unstyled chat_info">';
            $cxm_comments_html .= '<li><small>' . $comment->creatorName . ' ' . $comment->created_at->diffForHumans() . '</small></li>';
            $cxm_comments_html .= '<li><span class="message">' . $comment->comment_text . '</span></li>';
            $cxm_comments_html .= '</ul>';
            $cxm_comments_html .= '</li>';
        }

        return $cxm_comments_html;
    }


    public function account_manager_projects()
    {

        $creatorid = Auth::user()->id;
        $teamKey = Auth::user()->team_key;

        //Team Client
        $teamClients = Client::where('team_key', $teamKey)->get();
        //Project Category
        $projectCategories = ProjectCategory::all();

        //Team Brand
        $data = AssignBrand::where('team_key', $teamKey)->get();

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
        $members = User::where([
            'team_key' => $teamKey,
            'status' => 1,
            'type' => 'staff',
            'staff_division' => 'agent'
        ])->orderBy('type', 'asc')->get();


        // Team Account Manager
        $accountManager = User::where(
            [
                'team_key' => $teamKey,
                'status' => 1,
                'type' => 'staff',
                'staff_division' => 'Account Manager'
            ])->orderBy('type', 'asc')->get();

        //Account Manager Project
        $projects = Project::where(['team_key' => $teamKey, 'asigned_id' => $creatorid])->get();
        $projectsData = array();
        foreach ($projects as $yy) {
            $cName = Client::where('id', $yy->id)->value('name');
            $yy['clientName'] = $cName;

            $brand = Brand::where('brand_key', $yy->brand_key)->value('name');
            $yy['brandName'] = $brand;

            $agent = User::where('id', $yy->agent_id)->first();
            $yy['agentName'] = $agent->name;
            $yy['agentDesignation'] = $agent->designation;
            $yy['agentImage'] = $agent->image;


            $status = ProjectStatus::where('id', $yy->project_status)->first();
            $yy['status'] = $status->status;
            $yy['statusColor'] = $status->status_color;

            array_push($projectsData, $yy);

        }
        $projectStatus = ProjectStatus::all();
        return view('project.am_project', compact('teamClients', 'teamBrand', 'members', 'projectCategories', 'projectsData', 'projectStatus', 'accountManager'));

    }

    public function create_project_payment(Request $request)
    {
        $creatorid = Auth::user()->id;
        $teamKey = Auth::user()->team_key;
        $invoiceKey = random_int(11,99).substr(time(), 1, 3).random_int(11,99).substr(time(), 8, 2);
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


}
