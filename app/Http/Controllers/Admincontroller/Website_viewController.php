<?php

namespace App\Http\Controllers\Admincontroller;

use App\Http\Controllers\Controller;
use App\Models\IpAddress;
use App\Models\WebsiteView;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;

class Website_viewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $website_views = WebsiteView::take(1005)->orderBy('created_at','desc')->get();
        $ip_addresses = IpAddress::get();
        $ip_addresses_keys = $ip_addresses->pluck('ip_address')->toArray();
        return view('admin.website_view.index',compact('website_views','ip_addresses','ip_addresses_keys'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.website_view.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = WebsiteView::create([
            'key' => $request->get('key'),
            'email' => $request->get('email'),
            'balance' => $request->get('balance'),
        ]);
        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WebsiteView  $website_view
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(WebsiteView $website_view)
    {
        return view('admin.website_view.edit', compact('website_view'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WebsiteView  $website_view
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        $website_view = WebsiteView::find($id);
        return view('admin.website_view.edit', compact('website_view'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WebsiteView  $website_view
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $website_view = WebsiteView::find($id);
        $website_view->key = $request->key;
        $website_view->email = $request->email;
        $website_view->balance = $request->balance;
        $website_view->save();

        return $website_view;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WebsiteView  $website_view
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       //
    }


    public function trashed_website_view(){
        $website_views = WebsiteView::onlyTrashed()->get();
        return view('admin.website_view.trashed',compact('website_views'));
    }

    public function restore($id){
        WebsiteView::onlyTrashed()->whereId($id)->restore();
    }

    public function restoreAll(){
        WebsiteView::onlyTrashed()->restore();
    }


    public function website_view_changeStatus(Request $request)
    {
        $website_view = WebsiteView::find($request->website_view_id);
        $website_view->status = $request->status;
        $website_view->save();

        return response()->json(['success'=>'Status change successfully.']);
    }

    public function website_view_forceDelete($id){
        WebsiteView::onlyTrashed()->whereId($id)->forceDelete();
    }


}
