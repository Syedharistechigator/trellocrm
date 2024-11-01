<?php

namespace App\Http\Controllers\Admincontroller;

use App\Http\Controllers\Controller;
use App\Models\BoardList;
use App\Models\BoardListTeam;
use App\Models\Department;
use App\Models\Team;
use App\Rules\TeamKeyExists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BoardListController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $department_id = $request->has('departmentKey') && $request->get('departmentKey') !== "undefined" ? $request->get('departmentKey') : 0;
        $board_lists = BoardList::with('getDepartment')
            ->where(function ($query) use ($department_id) {
                if ($department_id > 0) {
                    $query->whereHas('getDepartment', function ($q) use ($department_id) {
                        $q->where('departments.id', $department_id);
                    });
                }
            })->get();
        $departments = Department::where('status', '1')->get();
        return view('admin.board-list.index', compact('board_lists', 'departments' ,'department_id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
//        $teams = Team::where('status', '1')->get();
        $departments = Department::where('status', 1)->get();
        return view('admin.board-list.create', compact('departments'));
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
                'title' => 'required',
//                'team_key' => ['required', 'array', new TeamKeyExists],
                'position' => 'required|integer|min:0|max:65535',
            ];
            $messages = [
//                'team_key.required' => 'The Team field is required.',
//                'team_key.array' => 'The Team field must be an array.',
                'position.integer' => 'The position must be an integer.',
                'position.min' => 'The position must be at least 0.',
                'position.max' => 'The position must be at most 65,535.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list = new BoardList();
            $board_list->title = $request->get('title');
            $board_list->position = $request->get('position');
//            $board_list->department = $request->get('department');
            if ($board_list->save()) {
                if ($request->filled('department')) {
                    $board_list->setDepartment()->sync($request->get('department'));
                } else {
                    $board_list->setDepartment()->detach();
                }

//                $selectedTeamKeys = $request->get('team_key');
//                if (in_array(0, $selectedTeamKeys)) {
//                    $selectedTeamKeys = Team::pluck('team_key')->toArray();
//                }
//                $board_list->setteams()->sync($selectedTeamKeys);
                return response()->json(['success' => 'created successfully']);
            }
            throw new \RuntimeException('Failed to create Board list record.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\BoardList $board_list
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show($id)
    {
        $board_list = BoardList::find($id);
        $departments = Department::where('status', 1)->get();
        return view('admin.board-list.edit', compact('board_list', 'departments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\BoardList $board_list
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        $board_list = BoardList::find($id);
        if (!$board_list) {
            abort(404, 'Board List not found.');
        }
        $departments = Department::where('status', 1)->get();
//        $teams = Team::where('status', '1')->get();
        return view('admin.board-list.edit', compact('board_list', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\BoardList $board_list
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'title' => 'required',
//                'team_key' => 'required',
                'position' => 'required|integer|min:0|max:65535',
            ];
            $messages = [
//                'team_key.required' => 'The Team field is required.',
                'position.integer' => 'The position must be an integer.',
                'position.min' => 'The position must be at least 0.',
                'position.max' => 'The position must be at most 65,535.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list = BoardList::find($id);

            if (!$board_list) {
                return response()->json(['error' => 'Board List not found'], 404);
            }
            $board_list->title = $request->get('title');
            $board_list->position = $request->get('position');
            if ($board_list->save()) {
                if ($request->filled('department')) {
                    $board_list->setDepartment()->sync($request->get('department'));
                } else {
                    $board_list->setDepartment()->detach();
                }
//                $selectedTeamKeys = $request->get('team_key');
//                if (in_array(0, $selectedTeamKeys)) {
//                    $selectedTeamKeys = Team::pluck('team_key')->toArray();
//                }
//                $board_list->setteams()->sync($selectedTeamKeys);
                return response()->json(['success' => 'Board List updated successfully']);
            }
            throw new \RuntimeException('Failed to create board list record.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\BoardList $board_list
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $board_list = BoardList::find($id);
            if (!$board_list) {
                return response()->json(['error' => 'Board List not found'], 404);
            }
            $board_list->delete();
            return response()->json(['success' => 'Board List deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function trashed()
    {
        $board_lists = BoardList::onlyTrashed()->get();
        return view('admin.board-list.trashed', compact('board_lists'));
    }


    public function restore($id)
    {
        try {
            BoardList::onlyTrashed()->whereId($id)->restore();
            return response()->json(['success' => 'Board List restored successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function restore_all()
    {
        try {
            BoardList::onlyTrashed()->restore();
            return response()->json(['success' => 'All trashed Board Lists restored successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function force_delete($id)
    {
        try {
            $boardList = BoardList::onlyTrashed()->find($id);
            if (!$boardList) {
                return response()->json(['error' => 'Board List not found.'], 404);
            }
            $boardList->forceDelete();
            $boardList->getBoardListTeams()->delete();
            return response()->json(['success' => 'Board List permanently deleted.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function change_status(Request $request)
    {
        $board_list = BoardList::find($request->board_list_id);
        $board_list->status = $request->status;
        $board_list->save();
        return response()->json(['success' => 'Status change successfully.']);
    }
}
