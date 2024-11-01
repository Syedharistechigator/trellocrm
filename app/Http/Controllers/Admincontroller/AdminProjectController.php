<?php

namespace App\Http\Controllers\Admincontroller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
use App\Models\File;
use App\Models\Comment;

class AdminProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projectsData = array();
        $projects = Project::orderBy('id', 'desc')->get();

        foreach($projects as $yy){
            $cName = Client::where('id',$yy->id)->value('name');
            $yy['clientName'] = $cName;

            $brand = Brand::where('brand_key',$yy->brand_key)->value('name');
            $yy['brandName'] = $brand;

            $agent = User::where('id',$yy->agent_id)->first();
            $yy['agentName'] = $agent->name??"";
            $yy['agentDesignation'] = $agent->designation??"";
            $yy['agentImage'] = $agent->image??"";

            $pm = User::where('id',$yy->asigned_id)->first();
            if($pm){
                $yy['pmName'] = $pm->name;
                $yy['pmDesignation'] = $pm->designation;
                $yy['pmImage'] = $pm->image;
            }else{
                $yy['pmName'] = '---';
                $yy['pmDesignation'] = '----';
            }
            $status = ProjectStatus::where('id',$yy->project_status)->first();
            $yy['status'] = $status->status;
            $yy['statusColor'] = $status->status_color;

            array_push($projectsData,$yy);
        }

        return view('admin.project.index',compact('projectsData'));
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $project = Project::find($id);

        $clientInfo = Client::where('id',$project->clientid)->first();
        $project['clientId'] = $clientInfo->id;
        $project['clientName'] = $clientInfo->name;

        $projectStatus = ProjectStatus::where('id',$project->project_status)->first();
        $project['projectStatus'] = $projectStatus->status;
        $project['projectStatusColor'] = $projectStatus->status_color;

        $project['category'] = ProjectCategory::where('id', $project->category_id)->value('name');
        $project['projectAgent'] = User::where('id',$project->agent_id)->value('pseudo_name');
        $project['projectManager'] = User::where('id',$project->asigned_id)->value('pseudo_name');


        //project invoices
        $projectInvoices = Invoice::where('project_id',$id)->get();
        $projectInvoicesAmount = Invoice::where('project_id',$id)->sum('final_amount');

        //project payments
        $projectPayments = Payment::where('project_id',$id)->get();

        $projectPaymentsAmount = Payment::where('project_id',$id)->sum('amount');
        //project files
        $projectFiles = File::where('projectid',$id)->get();

        $data = AssignBrand::where('team_key',$project->team_key)->get();
        $teamBrand = array();

        foreach($data as $a){
            $brand_key =  $a->brand_key;
            $brands = Brand::where('brand_key',$brand_key)->get();
            foreach($brands as $brand){
                $a['brandKey'] = $brand->brand_key;
                $a['brandName'] = $brand->name;
                array_push($teamBrand,$a);
            }
        }

        //Team Members
        $members = User::where(['team_key' => $project->team_key , 'status' => 1])
        ->where('type','!=','client')->orderBy('type', 'asc')->get();


        $projectComments = Comment::where('projectid',$id)->get();
        $comments = array();
        foreach($projectComments as $comment){
            $comment['creatorName'] = User::where('id',$comment->creatorid)->value('pseudo_name');
            $comment['clientName'] =  Client::where('id',$comment->clientid)->value('name');
            array_push($comments,$comment);
        }

        return view('admin.project.detail',compact('comments','project','projectInvoices','projectPayments','members','teamBrand','projectInvoicesAmount','projectPaymentsAmount','projectFiles'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Project|Project[]|\Illuminate\Http\Response|\LaravelIdea\Helper\App\Models\_IH_Project_C
     */
    public function edit($id)
    {
        return Project::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
