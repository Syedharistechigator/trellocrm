<?php

namespace App\Http\Controllers\Admincontroller;

use App\Http\Controllers\Controller;
use App\Models\BoardList;
use App\Models\BoardListCard;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::get();
        return view('admin.department.index', compact('departments'));
    }

    /**
     * @param Request $request
     * @param $department
     * @return void
     */
    public function extracted(Request $request, $department): void
    {
        $department->name = $request->input('name');
        $newOrder = $request->input('order');

        $position = (int)$newOrder;
        if ($department->order != $newOrder) {
            $departments = Department::where('order', '>=', $newOrder)->where(function ($q) use ($department) {
                if (isset($department->id)) {
                    $q->where('id', '!=', $department->id);
                }
            })
                ->orderBy('order')
                ->get();
            foreach ($departments as $dept) {
                ++$position;
                $dept->order = $position;
                $dept->save();
            }
        }

        $department->order = $newOrder;
        $department->status = $request->input('status');
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
                "name" => "required",
                "order" => "required|integer",
                "status" => "required|in:0,1",
            ];
            $messages = [
                "name.required" => "The name field is required.",
                "order.required" => "The order field is required.",
                "order.integer" => "The order field must be type of number.",
                'status.required' => 'The status field is required.',
                'status.in' => 'The status field must be either "Active" or "InActive".',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $department = new Department();
            $order_change = false;
            if ($request->has('order') && $request->get('order') != null && $request->get('order') != $department->order && $request->get('order') <= Department::get()->max('order')) {
                $order_change = true;
            }
            $this->extracted($request, $department);
            if ($department->save()) {
                return response()->json(['status' => 1, 'success' => 'Record created successfully.', 'data' => $department, 'order_change' => $order_change]);
            }
            return response()->json(['error' => 'Failed to create record.'], 400);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Department $record
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        try {
            $record = Department::where('id', $id)->first();
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
     * @param \App\Models\Department $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $rules = [
                "name" => "required",
                "order" => "required|integer",
                "status" => "required|in:0,1",
            ];
            $messages = [
                "name.required" => "The name field is required.",
                "order.required" => "The order field is required.",
                "order.integer" => "The order field must be type of number.",
                'status.required' => 'The status field is required.',
                'status.in' => 'The status field must be either "Active" or "InActive".',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $department = Department::where('id', $id)->first();
            if (!$department) {
                return response()->json(['error' => 'Oops! Record not found.'], 404);
            }
            $order_change = false;
            if ($request->has('order') && $request->get('order') != null && $request->get('order') != $department->order) {
                $order_change = true;
            }
            $this->extracted($request, $department);
            if ($department->save()) {
                return response()->json(['status' => 1, 'success' => 'Record updated successfully.', 'data' => $department, 'order_change' => $order_change]);
            }
            return response()->json(['error' => 'Failed to create record.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Department $department
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $department = Department::find($id);
            if (!$department) {
                return response()->json(['error' => 'Record not found'], 404);
            }
            $department->delete();
            return response()->json(['success' => 'Record deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function change_status(Request $request)
    {
        $rules = [
            "id" => "required|integer",
            "status" => "required|in:0,1",
        ];
        $messages = [
            'status.required' => 'The status field is required.',
            'status.in' => 'The status field must be either "Active" or "InActive".',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $department = Department::find($request->id);
        $department->status = $request->status;
        $department->save();
        return response()->json(['success' => 'Status change successfully.']);
    }

    public function get_board_lists($id)
    {
        try {
            if (!$id) {
                return response()->json(['error' => 'Record not found.', 'status' => 0], 404);
            }
            $board_lists = BoardList::with('getDepartment')
                ->where(function ($query) use ($id) {
                    if ($id > 0) {
                        $query->whereHas('getDepartment', function ($q) use ($id) {
                            $q->where('departments.id', $id);
                        });
                    }
                })->get()->map(function ($board_list) {
                    return [
                        'id' => $board_list->id,
                        'data' => $board_list->title,
                    ];
                });
            return response()->json(['success' => 'Record fetched', 'board_lists' => $board_lists, 'status' => 1]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
