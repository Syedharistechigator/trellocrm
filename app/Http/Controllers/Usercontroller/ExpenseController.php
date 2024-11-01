<?php

namespace App\Http\Controllers\Usercontroller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\Brand;
use App\Models\Expense;
use App\Models\User;
use App\Models\Project;
use App\Models\AssignBrand;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        //Team Client
        $teams = Team::where('status',1)->get();
        $expenses = Expense::all();

        $members = User::where(['status' => 1, 'type'=>'staff'])->orderBy('type', 'asc')->get();

        return view('expense.index',compact('expenses','teams','members'));
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
        $creatorid = Auth::user()->id;
        $expense = Expense::create([
            'team_key'      => $request->get('team_key'),
            'brand_key'     => $request->get('brand_key'),
            'creator_id'     => $creatorid, 
            'client_id'      => $request->get('client_id'),
            'agent_id'      => $request->get('agent_id'),
            'project_id'    => $request->get('project_id'),    
            'amount'        => $request->get('value'),
            'title'      => $request->get('title'),
            'description'      => $request->get('description'),
            'status'        => 1,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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

    public function show_team_brands($id){
        
        $teamBrands = AssignBrand::where('team_key',$id)->get();
        $teamBrand = array();
 
        foreach($teamBrands as $a){
            $brand_key =  $a->brand_key;
            $brands = Brand::where('brand_key',$brand_key)->get();
            foreach($brands as $brand){
                 $a['brandKey'] = $brand->brand_key;
                 $a['brandName'] = $brand->name;
                 array_push($teamBrand,$a);
            }     
        }

        return $teamBrand;
    }

    public function show_brand_projects($id){
        $project = Project::where('brand_key',$id)->get();
        return $project;
    }

    public function project_detail($id){
        $project_data = Project::find($id);
        return $project_data;
    }


}
