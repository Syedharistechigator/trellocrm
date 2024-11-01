<?php

namespace App\Http\Controllers\Admincontroller;

use App\Http\Controllers\Controller;
use App\Models\User_info_api;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;

class User_info_apiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_info_apis = User_info_api::all();
        // dd($user_info_apis);
        return view('admin.user_info_api.index',compact('user_info_apis'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.user_info_api.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = User_info_api::create([
            'key' => $request->get('key'),
            'email' => $request->get('email'),
            'balance' => $request->get('balance'),
        ]);
        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\user_info_api  $user_info_api
     * @return \Illuminate\Http\Response
     */
    public function show(User_info_api $user_info_api)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\user_info_api  $user_info_api
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user_info_api = User_info_api::find($id);
        return view('admin.user_info_api.edit', compact('user_info_api'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\user_info_api  $user_info_api
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $user_info_api = User_info_api::find($id);
        $user_info_api->key = $request->key;
        $user_info_api->email = $request->email;
        $user_info_api->balance = $request->balance;
        $user_info_api->save();

        return $user_info_api;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\user_info_api  $user_info_api
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User_info_api::find($id)->delete();
    }


    public function trashed_user_info_api(){
        $user_info_apis = User_info_api::onlyTrashed()->get();
        return view('admin.user_info_api.trashed',compact('user_info_apis'));
    }

    public function restore($id){
        User_info_api::onlyTrashed()->whereId($id)->restore();
    }

    public function restoreAll(){
        User_info_api::onlyTrashed()->restore();
    }


    public function user_info_api_changeStatus(Request $request)
    {
        $user_info_api = User_info_api::find($request->user_info_api_id);
        $user_info_api->status = $request->status;
        $user_info_api->save();

        return response()->json(['success'=>'Status change successfully.']);
    }

    public function user_info_api_forceDelete($id){
        User_info_api::onlyTrashed()->whereId($id)->forceDelete();
    }


}
