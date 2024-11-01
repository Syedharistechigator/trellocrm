<?php

namespace App\Http\Controllers\Admincontroller\Team;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\Team\SpendingModel as TeamSpendingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SpendingController extends Controller
{

    public function index(Request $request)
    {
        $query = TeamSpendingModel::query();
        if ($request->filled('teamKey')) {
            $query->where('team_key', $request->get('teamKey'));
        }
        if ($request->filled('month')) {
            $monthNumeric = array_search(Str::title($request->month), config('app.months'));
            $query->where('month', $monthNumeric + 1);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }
        $team_spendings = $query->get();
        $teams = Team::where('status', '1')->get();
        return view('admin.team.spending.index', compact('team_spendings', 'teams'));
    }

    /**
     * @param Request $request
     * @param $team_spending
     * @return void
     */
    public function extracted(Request $request, $team_spending): void
    {
        $team_spending->team_key = $request->input('team_key');
        $team_spending->accounts = $request->input('accounts');
        $team_spending->amount = $request->input('amount');
        $team_spending->limit = $request->input('limit');
        $team_spending->month = $request->input('month');
        $team_spending->year = $request->input('year');
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
                "team_key" => "required|exists:teams,team_key",
                "accounts" => "required|numeric|min:0",
                "amount" => "required|numeric|min:0",
                "limit" => "numeric|min:0",
                "month" => "required|integer|min:1|max:12",
                "year" => [
                    "required",
                    "integer",
                    "min:2021",
                    "max:2030",
                ],
            ];
            $messages = [
                "team_key.required" => "The Team field is required.",
                "team_key.exists" => "The selected Team is invalid.",
                "amount.required" => "The Target Amount field is required.",
                "amount.numeric" => "The Target Amount must be a number.",
                "amount.min" => "The Target Amount must be at least 0.",
                "limit.numeric" => "The Limit must be a number.",
                "limit.min" => "The Limit must be at least 0.",
                "month.required" => "Please select a month.",
                "month.integer" => "The selected Month is invalid.",
                "month.min" => "The selected Month is invalid.",
                "month.max" => "The selected Month is invalid.",
                "year.required" => "Please select a year.",
                "year.integer" => "The selected Year is invalid.",
                "year.min" => "The selected Year must be the current year or later.",
                "year.max" => "The selected Year is too far in the future.",
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $team_spending = new TeamSpendingModel();
            $this->extracted($request, $team_spending);
            if ($team_spending->save()) {
                $team_spending->load([
                    'getTeam' => function ($query) {
                        $query->select('id', 'team_key', 'name');
                    },
                ]);
                return response()->json(['status' => 1, 'success' => 'Record created successfully.', 'data' => $team_spending]);
            }
            return response()->json(['error' => 'Failed to create record.'], 400);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Team\SpendingModel $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        try {
            $record = TeamSpendingModel::where('id', $id)->where('status', 1)->first();
            if (!$record) {
                return response()->json(['error' => 'Oops! Record not found.'], 404);
            }
            $teams = Team::where('status', '1')->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'data' => $user->name,
                ];
            });
            return response()->json(['status' => 1, 'success' => 'Fetched successfully', 'data' => $record, 'teams' => $teams]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Team\SpendingModel $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $rules = [
                "team_key" => "required|exists:teams,team_key",
                "accounts" => "required|numeric|min:0",
                "amount" => "required|numeric|min:0",
                "limit" => "numeric|min:0",
                "month" => "required|integer|min:1|max:12",
                "year" => [
                    "required",
                    "integer",
                    "min:2021",
                    "max:2030",
                ],
            ];
            $messages = [
                "team_key.required" => "The Team field is required.",
                "team_key.exists" => "The selected Team is invalid.",
                "amount.required" => "The Target Amount field is required.",
                "amount.numeric" => "The Target Amount must be a number.",
                "amount.min" => "The Target Amount must be at least 0.",
                "limit.numeric" => "The Limit must be a number.",
                "limit.min" => "The Limit must be at least 0.",
                "month.required" => "Please select a month.",
                "month.integer" => "The selected Month is invalid.",
                "month.min" => "The selected Month is invalid.",
                "month.max" => "The selected Month is invalid.",
                "year.required" => "Please select a year.",
                "year.integer" => "The selected Year is invalid.",
                "year.min" => "The selected Year must be the current year or later.",
                "year.max" => "The selected Year is too far in the future.",
                "year.unique" => "Target for this team already exists for the specified month and year."
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $team_spending = TeamSpendingModel::where('id', $id)->first();
            if (!$team_spending) {
                return response()->json(['error' => 'Oops! Record not found.'], 404);
            }
            $this->extracted($request, $team_spending);
            if ($team_spending->save()) {
                $team_spending->load([
                    'getTeam' => function ($query) {
                        $query->select('id', 'team_key', 'name');
                    },
                ]);
                return response()->json(['status' => 1, 'success' => 'Record updated successfully.', 'data' => $team_spending]);
            }
            return response()->json(['error' => 'Failed to create record.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Team\SpendingModel $team_spending
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $team_spending = TeamSpendingModel::find($id);
            if (!$team_spending) {
                return response()->json(['error' => 'Record not found'], 404);
            }
            $team_spending->delete();
            return response()->json(['success' => 'Record deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
