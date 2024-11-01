<?php

namespace App\Http\Controllers\Admincontroller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\Brand;
use App\Models\User;
use App\Models\AssignBrand;
use App\Models\Spending;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Carbon\Carbon;
use App\Models\Payment;

use DB;

class SpendingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
     
         $brand = Brand::where('status',1)->get();
         
         $teams = Team::where('status',1)->get();
         $spending = Spending::latest('updated_at')->get();
        
        return view('admin.spending.index',compact('brand','teams','spending'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    { 
        //
        
        // echo 2;
        // return response()->json($request);
        // exit;
        try{
            
            $spending = Spending::create([       
                // 'brand_key' => $request->get('brand_key'),
                'team_key' => $request->get('team_key'),
                // 'spending_date' => $request->get('spending_date'),
                // 'platform' => $request->get('spending_platform'),
                // 'amount' => $request->get('spending_amount'),
            ]);
            return response()->json(['success' => 'Response error'], 201); 

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
        // dd($request);
        

        
        // return $spending;
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $team = Spending::create([       
            'brand_key' => $request->get('brand_key'),
            'team_key' => $request->get('team_key'),
            'spending_date' => $request->get('spending_date'),
            'platform' => $request->get('spending_platform'),
            'amount' => $request->get('spending_amount'),
        ]);
        return $team;
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
        $spendingData = Spending::where('id',$id)->first();
        $brand = Brand::where('status',1)->get();
        $team = Team::where('status',1)->get();
        return view('admin.spending.edit',compact('spendingData','brand','team'));
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
         $spendingData = Spending::find($id);
         $spendingData->team_key= $request->team_key;
         $spendingData->brand_key =$request->brand_key;
         $spendingData->spending_date = $request->spending_date;
         $spendingData->platform = $request->spending_platform;
         $spendingData->amount = $request->spending_amount;
         $spendingData->save();
         return $spendingData;            
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      Spending::find($id)->delete();
      return true; 
    }
}
