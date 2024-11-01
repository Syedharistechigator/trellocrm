<?php

namespace App\Http\Controllers\Admincontroller\Team;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\Team\FixedCostingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FixedCostingController extends Controller
{

    public function index(Request $request)
    {
        $query = FixedCostingModel::query();
        if ($request->filled('teamKey')) {
            $query->where('team_key', $request->get('teamKey'));
        }
        if ($request->filled('month')) {
            $monthNumeric = array_search(Str::title($request->month), config('app.months'));
            $query->where('month', $monthNumeric+1);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }
        $fixed_costings = $query->get();
        $teams = Team::where('status', '1')->get();
        return view('admin.team.fixed-costing.index', compact('fixed_costings', 'teams'));
    }

    /**
     * @param Request $request
     * @param $fixed_costing
     * @return void
     */
    public function extracted(Request $request, $fixed_costing): void
    {
        $fixed_costing->team_key = $request->input('team_key');
        $fixed_costing->amount = $request->input('amount');
        $fixed_costing->month = $request->input('month');
        $fixed_costing->year = $request->input('year');
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
                "amount" => "required|numeric|min:0",
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
                "amount.required" => "The Fixed Costing Amount field is required.",
                "amount.numeric" => "The Fixed Costing Amount must be a number.",
                "amount.min" => "The Fixed Costing Amount must be at least 0.",
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
            $existingRecord = FixedCostingModel::where('team_key', $request->team_key)
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->first();
//
//            if ($existingRecord) {
//                return response()->json(['errors' => ['year' => ['Fixed Costing for this team already exists for the specified month and year.']], 'data' => ['id' => $existingRecord->id]], 422);
//            }
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $fixed_costing = new FixedCostingModel();
            $this->extracted($request, $fixed_costing);
            if ($fixed_costing->save()) {
                $fixed_costing->load([
                    'getTeam' => function ($query) {
                        $query->select('id', 'team_key', 'name');
                    },
                ]);
                return response()->json(['status' => 1, 'success' => 'Record created successfully.', 'data' => $fixed_costing]);
            }
            return response()->json(['error' => 'Failed to create record.'], 400);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Team\FixedCostingModel $record
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        try {
            $record = FixedCostingModel::where('id', $id)->where('status', 1)->first();
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
     * @param \App\Models\Team\FixedCostingModel $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $rules = [
                "team_key" => "required|exists:teams,team_key",
                "amount" => "required|numeric|min:0",
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
                "amount.required" => "The Fixed Costing Amount field is required.",
                "amount.numeric" => "The Fixed Costing Amount must be a number.",
                "amount.min" => "The Fixed Costing Amount must be at least 0.",
                "month.required" => "Please select a month.",
                "month.integer" => "The selected Month is invalid.",
                "month.min" => "The selected Month is invalid.",
                "month.max" => "The selected Month is invalid.",
                "year.required" => "Please select a year.",
                "year.integer" => "The selected Year is invalid.",
                "year.min" => "The selected Year must be the current year or later.",
                "year.max" => "The selected Year is too far in the future.",
                "year.unique" => "Fixed Costing for this team already exists for the specified month and year."
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            $existingRecord = FixedCostingModel::where('team_key', $request->team_key)
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->where('id', '!=', $id)
                ->first();

//            if ($existingRecord) {
//                return response()->json(['errors' => ['year' => ['Fixed Costing for this team already exists for the specified month and year.']], 'data' => ['id' => $existingRecord->id]], 422);
//            }
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $fixed_costing = FixedCostingModel::where('id', $id)->first();
            if (!$fixed_costing) {
                return response()->json(['error' => 'Oops! Record not found.'], 404);
            }
            $this->extracted($request, $fixed_costing);
            if ($fixed_costing->save()) {
                $fixed_costing->load([
                    'getTeam' => function ($query) {
                        $query->select('id', 'team_key', 'name');
                    },
                ]);
                return response()->json(['status' => 1, 'success' => 'Record updated successfully.', 'data' => $fixed_costing]);
            }
            return response()->json(['error' => 'Failed to create record.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Team\FixedCostingModel $fixed_costing
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $fixed_costing = FixedCostingModel::find($id);
            if (!$fixed_costing) {
                return response()->json(['error' => 'Record not found'], 404);
            }
            $fixed_costing->delete();
            return response()->json(['success' => 'Record deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
