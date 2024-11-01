<?php

namespace App\Http\Controllers\Admincontroller;

use App\Http\Controllers\Controller;
use App\Models\LeadStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeadStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $statuses = LeadStatus::all();
        return view('admin.leadstatus.index', compact('statuses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                "status" => "required|string",
                "status_color" => "required|in:default,primary,success,info,warning,danger,dark",
            ];
            $messages = [
                "status.required" => "The Lead status field is required",
                "status_color.in" => "The Lead status color field must be Default , Primary , Success , Info , Warning , Danger , Dark.",
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors(),], 422);
            }
            $lead_status = new LeadStatus();
            $lead_status->status = $request->get('status', 'default');
            $lead_status->leadstatus_color = $request->get('status_color', 'default');
            if ($lead_status->save()) {
                return response()->json(['status' => 1, 'success' => 'Record created successfully.', 'data' => $lead_status]);
            }
            return response()->json(['error' => 'Failed to create record.'], 400);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\LeadStatus $leadStatus
     * @return \Illuminate\Http\Response
     */
    public function show(LeadStatus $leadStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\LeadStatus $leadStatus
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($leadStatus)
    {
        try {
            $record = LeadStatus::where('id', $leadStatus)->first();
            if (!$record) {
                return response()->json(['error' => 'Oops! Record not found.'], 404);
            }
            return response()->json(['status' => 1, 'success' => 'Fetched successfully', 'data' => $record]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\LeadStatus $leadStatus
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $leadStatus)
    {
        try {
            $rules = [
                "status" => "required|string",
                "status_color" => "required|in:default,primary,success,info,warning,danger,dark",
            ];
            $messages = [
                "status.required" => "The Lead status field is required",
                "status_color.in" => "The Lead status color field must be Default , Primary , Success , Info , Warning , Danger , Dark.",
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors(),], 422);
            }
            $lead_status = LeadStatus::where('id', $leadStatus)->first();
            if (!$lead_status) {
                return response()->json(['error' => 'Oops! Record not found.'], 404);
            }
            $lead_status->status = $request->get('status', 'default');
            $lead_status->leadstatus_color = $request->get('status_color', 'default');
            if ($lead_status->save()) {
                return response()->json(['status' => 1, 'success' => 'Record created successfully.', 'data' => $lead_status]);
            }
            return response()->json(['error' => 'Failed to create record.'], 400);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\LeadStatus $leadStatus
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        LeadStatus::find($id)->delete();
    }
}
