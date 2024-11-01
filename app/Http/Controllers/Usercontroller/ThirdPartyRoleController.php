<?php

namespace App\Http\Controllers\Usercontroller;

use App\Http\Controllers\Controller;
use App\Models\AssignBrand;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Team;
use App\Models\ThirdPartyRoleModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Validator;

class ThirdPartyRoleController extends Controller
{
    public function index()
    {
        $third_party_roles = ThirdPartyRoleModel::where('status', 1)->where('creator_id', Auth::id())->where('creator_type', get_class(Auth::user() ?? ""))->get();
        $teams = Team::where('status', 1)->get();
        return view('third-party-role.index', compact('third_party_roles', 'teams'));
    }

    public function get_teams_agents_and_clients($team_key)
    {
        try {
            $assign_brands = AssignBrand::whereHas('getBrandWithOutTrashed')->where('team_key', $team_key)->get();
            $paid_invoice_client_ids = Invoice::where('team_key', $team_key)->where('status', 'paid')->get()->pluck('clientid')->toArray();
            $clients = Client::whereIn('brand_key', $assign_brands->pluck('brand_key'))->orWhereIn('id',$paid_invoice_client_ids)->orWhere('team_key',$team_key)->get()->map(function ($client) {
                return [
                    'id' => $client->id,
                    'data' => $client->name . " : " . $client->email . " : " . $client->phone,
                ];
            });
            $users = User::where(['team_key' => $team_key, 'status' => 1])->where('type', '!=', 'client')->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'data' => $user->name,
                ];
            });
            return response()->json(['success' => 'Record fetched', 'users' => $users, 'clients' => $clients, 'status' => 1]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function get_client_paid_invoices($team_key, $client_id)
    {
        try {
            $assign_brands = AssignBrand::whereHas('getBrandWithOutTrashed')->where('team_key', $team_key)->get();
            $paid_invoice_client_ids = Invoice::where('team_key', $team_key)->where('status', 'paid')->get()->pluck('clientid')->toArray();

            if (count($assign_brands) === 0 && !in_array($client_id,$paid_invoice_client_ids)) {
                return response()->json(['error' => 'Oops! Record not found'], 404);
            }
            $client = Client::where('id', $client_id)->where(function($query) use ($team_key,$client_id,$assign_brands,$paid_invoice_client_ids){
                if (!in_array($client_id,$paid_invoice_client_ids)){
                    $query->where('team_key', $team_key)->orWhereIn('brand_key', $assign_brands->pluck('brand_key'));
                }
            })->first();
            if (!$client) {
                return response()->json(['error' => 'Oops! Record not found.'], 404);
            }
            $invoices = Invoice::where('clientid', $client->id)->where('team_key', $team_key)->where('status', 'paid')->get()
                ->map(function ($invoice) {
                    return [
                        'id' => $invoice->id,
                        'invoice_key' => $invoice->invoice_key,
                        'data' => "#" . $invoice->invoice_key . " : $" . $invoice->total_amount . " : " . $invoice->created_at->format('j F, Y h:i:s A') . ' ' . $invoice->created_at->diffForHumans()
                    ];
                });
            return response()->json(['success' => 'Record fetched', 'invoices' => $invoices, 'status' => 1]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function extracted(Request $request, $third_party_role): void
    {
        $third_party_role->team_key = $request->team_key;
        $third_party_role->agent_id = $request->agent_id;
        $third_party_role->client_id = $request->client_id;
        $third_party_role->invoice_id = $request->invoice_id;
        $third_party_role->order_id = $request->order_id;
        $third_party_role->order_status = $request->order_status;
        $third_party_role->description = $request->description;
        $third_party_role->amount = $request->amount;
        $third_party_role->merchant_type = $request->merchant_type;
        $third_party_role->transaction_id = $request->transaction_id;
        $third_party_role->payment_status = $request->payment_status;
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
                "team_key" => "required",
                "agent_id" => "required",
                "client_id" => "required",
                "order_id" => "required",
                "order_status" => "required|in:Order Placed,Shipped,Delivered,On Hold",
                'amount' => 'required|numeric|min:0',
                "merchant_type" => "required|in:4,21",
                "payment_status" => "required|in:0,1,2",
//                "transaction_id" => "required",
            ];
            $messages = [
                "team_key.required" => "The Team field is required.",
                "agent_id.required" => "The Agent field is required.",
                "client_id.required" => "The Client field is required.",
                "order_id.required" => "The Order id field is required.",
                "order_status.required" => "The Order status field is required.",
                "order_status.in" => "The Order status field must be one of: Order Placed, Shipped, Delivered, On Hold.",
                "amount.required" => "The Amount field is required.",
                "merchant_type.required" => "The Merchant field is required.",
                "merchant_type.in" => "The Merchant field must be one of: Paypal, Master Card 0079",
//                "transaction_id.required" => "The Transaction ID field is required.",
                "payment_status.required" => "The Payment status field is required.",
                "payment_status.in" => "The Payment status field must be Pending, In Review, or Completed.",
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $third_party_role = new ThirdPartyRoleModel();
            $third_party_role->creator_id = Auth::id();
            $third_party_role->creator_type = get_class(Auth::user() ?? "");
            $this->extracted($request, $third_party_role);
            if ($third_party_role->save()) {
                $third_party_role->formatted_created_at = Carbon::parse($third_party_role->created_at)->format('j F, Y');
                $third_party_role->load([
                    'getInvoice' => function ($query) {
                        $query->select('id', 'invoice_key', 'invoice_num');
                    },
                    'getTeam' => function ($query) {
                        $query->select('id', 'team_key', 'name');
                    },
                    'getClient' => function ($query) {
                        $query->select('id', 'name');
                    }
                ]);
                return response()->json(['status' => 1, 'success' => 'Record created successfully.', 'data' => $third_party_role]);
            }
            return response()->json(['error' => 'Failed to create record.'], 400);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\CustomerSheet\CustomerSheet $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        try {
            $record = ThirdPartyRoleModel::where('id', $id)->where('status', 1)->first();
            if (!$record) {
                return response()->json(['error' => 'Oops! Record not found.'], 404);
            }
            $record_team_key = $record->team_key;
            $assign_brands = AssignBrand::whereHas('getBrandWithOutTrashed')->where('team_key', $record_team_key)->get();
            if (count($assign_brands) === 0) {
                return response()->json(['error' => 'Oops! Record not found'], 404);
            }
            $users = User::where(['team_key' => $record_team_key, 'status' => 1])->where('type', '!=', 'client')->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'data' => $user->name,
                ];
            });

            $paid_invoice_client_ids = Invoice::where('team_key', $record_team_key)->where('status', 'paid')->get()->pluck('clientid')->toArray();

            $clients = Client::where(function($query) use ($record_team_key, $assign_brands, $paid_invoice_client_ids) {
                $query->where('team_key', $record_team_key)
                    ->orWhereIn('brand_key', $assign_brands->pluck('brand_key'))
                    ->orWhereIn('id', $paid_invoice_client_ids);
            })->get()->map(function ($client) {
                return [
                    'id' => $client->id,
                    'data' => $client->name . " : " . $client->email . " : " . $client->phone,
                ];
            });
            $invoices = Invoice::where('clientid', $record->client_id)->where('team_key', $record_team_key)->where('status', 'paid')->get()
                ->map(function ($invoice) {
                    return [
                        'id' => $invoice->id,
                        'invoice_key' => $invoice->invoice_key,
                        'data' => "#" . $invoice->invoice_key . " : $" . $invoice->total_amount . " : " . $invoice->created_at->format('j F, Y h:i:s A') . ' ' . $invoice->created_at->diffForHumans()
                    ];
                });

            return response()->json(['status' => 1, 'success' => 'Fetched successfully', 'data' => $record, 'users' => $users, 'clients' => $clients, 'invoices' => $invoices]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\CustomerSheet\CustomerSheet $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $rules = [
                "team_key" => "required",
                "agent_id" => "required",
                "client_id" => "required",
                "order_id" => "required",
                "order_status" => "required|in:Order Placed,Shipped,Delivered,On Hold",
                'amount' => 'required|numeric|min:0',
                "merchant_type" => "required|in:4,21",
                "payment_status" => "required|in:0,1,2",
//                "transaction_id" => "required",
            ];
            $messages = [
                "team_key.required" => "The Team field is required.",
                "agent_id.required" => "The Agent field is required.",
                "client_id.required" => "The Client field is required.",
                "order_id.required" => "The Order id field is required.",
                "order_status.required" => "The Order status field is required.",
                "order_status.in" => "The Order status must be one of: Order Placed, Shipped, Delivered, On Hold.",
                "amount.required" => "The Amount field is required.",
                "merchant_type.required" => "The Merchant field is required.",
                "merchant_type.in" => "The Order status must be one of: Paypal, Master Card 0079.",
//                "transaction_id.required" => "The Transaction ID field is required.",
                "payment_status.required" => "The Payment status field is required.",
                "payment_status.in" => "The Payment status field must be Pending, In Review, or Completed.",
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $third_party_role = ThirdPartyRoleModel::where('id', $id)->first();
            if (!$third_party_role) {
                return response()->json(['error' => 'Oops! Record not found.'], 404);
            }
            $this->extracted($request, $third_party_role);
            if ($third_party_role->save()) {
                $third_party_role->formatted_created_at = Carbon::parse($third_party_role->created_at)->format('j F, Y');
                $third_party_role->load([
                    'getInvoice' => function ($query) {
                        $query->select('id', 'invoice_key', 'invoice_num');
                    },
                    'getTeam' => function ($query) {
                        $query->select('id', 'team_key', 'name');
                    },
                    'getClient' => function ($query) {
                        $query->select('id', 'name');
                    }
                ]);
                return response()->json(['status' => 1, 'success' => 'Record updated successfully.', 'data' => $third_party_role]);
            }
            return response()->json(['error' => 'Failed to update record.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
