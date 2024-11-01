<?php

namespace App\Http\Controllers\Admincontroller\Team;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\Team\CarryForwardModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;

class CarryForwardController extends Controller
{

    public function index(Request $request)
    {
        $query = CarryForwardModel::query();
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
        $carry_forwards = $query->get();
        $teams = Team::where('status', '1')->get();
        return view('admin.team.carry-forward.index', compact('carry_forwards', 'teams'));
    }

    /**
     * @param Request $request
     * @param $carry_forward
     * @return void
     */
    public function extracted(Request $request, $carry_forward): void
    {
        $carry_forward->team_key = $request->input('team_key');
        $carry_forward->amount = $request->input('amount');
        $carry_forward->month = $request->input('month');
        $carry_forward->year = $request->input('year');
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
                "amount.required" => "The Carry Forward Amount field is required.",
                "amount.numeric" => "The Carry Forward Amount must be a number.",
                "amount.min" => "The Carry Forward Amount must be at least 0.",
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
            $existingRecord = CarryForwardModel::where('team_key', $request->team_key)
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->first();
//
//            if ($existingRecord) {
//                return response()->json(['errors' => ['year' => ['Carry Forward for this team already exists for the specified month and year.']], 'data' => ['id' => $existingRecord->id]], 422);
//            }
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $carry_forward = new CarryForwardModel();
            $this->extracted($request, $carry_forward);
            if ($carry_forward->save()) {
                $carry_forward->load([
                    'getTeam' => function ($query) {
                        $query->select('id', 'team_key', 'name');
                    },
                ]);
                return response()->json(['status' => 1, 'success' => 'Record created successfully.', 'data' => $carry_forward]);
            }
            return response()->json(['error' => 'Failed to create record.'], 400);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Team\CarryForwardModel $record
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        try {
            $record = CarryForwardModel::where('id', $id)->where('status', 1)->first();
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
     * @param \App\Models\Team\CarryForwardModel $id
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
                "amount.required" => "The Carry Forward Amount field is required.",
                "amount.numeric" => "The Carry Forward Amount must be a number.",
                "amount.min" => "The Carry Forward Amount must be at least 0.",
                "month.required" => "Please select a month.",
                "month.integer" => "The selected Month is invalid.",
                "month.min" => "The selected Month is invalid.",
                "month.max" => "The selected Month is invalid.",
                "year.required" => "Please select a year.",
                "year.integer" => "The selected Year is invalid.",
                "year.min" => "The selected Year must be the current year or later.",
                "year.max" => "The selected Year is too far in the future.",
                "year.unique" => "Carry Forward for this team already exists for the specified month and year."
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            $existingRecord = CarryForwardModel::where('team_key', $request->team_key)
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->where('id', '!=', $id)
                ->first();

//            if ($existingRecord) {
//                return response()->json(['errors' => ['year' => ['Carry Forward for this team already exists for the specified month and year.']], 'data' => ['id' => $existingRecord->id]], 422);
//            }
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $carry_forward = CarryForwardModel::where('id', $id)->first();
            if (!$carry_forward) {
                return response()->json(['error' => 'Oops! Record not found.'], 404);
            }
            $this->extracted($request, $carry_forward);
            if ($carry_forward->save()) {
                $carry_forward->load([
                    'getTeam' => function ($query) {
                        $query->select('id', 'team_key', 'name');
                    },
                ]);
                return response()->json(['status' => 1, 'success' => 'Record updated successfully.', 'data' => $carry_forward]);
            }
            return response()->json(['error' => 'Failed to create record.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Team\CarryForwardModel $carry_forward
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $carry_forward = CarryForwardModel::find($id);
            if (!$carry_forward) {
                return response()->json(['error' => 'Record not found'], 404);
            }
            $carry_forward->delete();
            return response()->json(['success' => 'Record deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
