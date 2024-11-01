<?php

namespace App\Http\Controllers\Admincontroller\Team;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\Team\TargetModel as TeamTargetModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;

class TargetController extends Controller
{
    public function index(Request $request)
    {
        $query = TeamTargetModel::query();
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
        $team_targets = $query->get();
        $teams = Team::where('status', '1')->get();
        return view('admin.team.target.index', compact('team_targets', 'teams'));
    }

    /**
     * @param Request $request
     * @param $team_target
     * @return void
     */
    public function extracted(Request $request, $team_target): void
    {
        $team_target->team_key = $request->input('team_key');
        $team_target->amount = $request->input('amount');
        $team_target->month = $request->input('month');
        $team_target->year = $request->input('year');
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
                "amount.required" => "The Target Amount field is required.",
                "amount.numeric" => "The Target Amount must be a number.",
                "amount.min" => "The Target Amount must be at least 0.",
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
            $existingRecord = TeamTargetModel::where('team_key', $request->team_key)
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->first();
//
//            if ($existingRecord) {
//                return response()->json(['errors' => ['year' => ['Target for this team already exists for the specified month and year.']], 'data' => ['id' => $existingRecord->id]], 422);
//            }
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $team_target = new TeamTargetModel();
            $this->extracted($request, $team_target);
            if ($team_target->save()) {
                $team_target->load([
                    'getTeam' => function ($query) {
                        $query->select('id', 'team_key', 'name');
                    },
                ]);
                return response()->json(['status' => 1, 'success' => 'Record created successfully.', 'data' => $team_target]);
            }
            return response()->json(['error' => 'Failed to create record.'], 400);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Team\TargetModel $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        try {
            $record = TeamTargetModel::where('id', $id)->where('status', 1)->first();
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
     * @param \App\Models\Team\TargetModel $id
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
                "amount.required" => "The Target Amount field is required.",
                "amount.numeric" => "The Target Amount must be a number.",
                "amount.min" => "The Target Amount must be at least 0.",
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
            $existingRecord = TeamTargetModel::where('team_key', $request->team_key)
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->where('id', '!=', $id)
                ->first();

//            if ($existingRecord) {
//                return response()->json(['errors' => ['year' => ['Target for this team already exists for the specified month and year.']], 'data' => ['id' => $existingRecord->id]], 422);
//            }
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $team_target = TeamTargetModel::where('id', $id)->first();
            if (!$team_target) {
                return response()->json(['error' => 'Oops! Record not found.'], 404);
            }
            $this->extracted($request, $team_target);
            if ($team_target->save()) {
                $team_target->load([
                    'getTeam' => function ($query) {
                        $query->select('id', 'team_key', 'name');
                    },
                ]);
                return response()->json(['status' => 1, 'success' => 'Record updated successfully.', 'data' => $team_target]);
            }
            return response()->json(['error' => 'Failed to create record.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Team\TargetModel $team_target
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $team_target = TeamTargetModel::find($id);
            if (!$team_target) {
                return response()->json(['error' => 'Record not found'], 404);
            }
            $team_target->delete();
            return response()->json(['success' => 'Record deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
