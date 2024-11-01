<?php

namespace App\Http\Controllers\Admincontroller;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AssignDepartmentBoardList;
use App\Models\BoardList;
use App\Models\BoardListCard;
use App\Models\BoardListTeam;
use App\Models\Client;
use App\Models\Color;
use App\Models\Department;
use App\Models\Team;
use App\Models\User;
use App\Rules\TeamKeyExists;
use App\Traits\BoardListCardCoverImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BoardListCardController extends Controller
{
    use BoardListCardCoverImageTrait;

    /**
     * Display a listing of the resource.
     *
     */
    public function index(Request $request)
    {
        $department_id = $request->has('departmentKey') && $request->get('departmentKey') !== "undefined" ? $request->get('departmentKey') : 0;
        $query = BoardListCard::with('getBoardList.getDepartment')
            ->where(function ($query) use ($department_id) {
                if ($department_id > 0) {
                    $query->whereHas('getBoardList.getDepartment', function ($q) use ($department_id) {
                        $q->where('departments.id', $department_id);
                    });
                }
            });
        $result = $this->getData($request, $query);
        $board_list_cards = $result['data'];
        $departments = Department::where('status', '1')->get();
        $teams = Team::where('status', '1')->get();
        $fromDate = $result['fromDate'];
        $toDate = $result['toDate'];
        $teamKey = $result['teamKey'];
        return view('admin.board-list.cards.index', [
            'board_list_cards' => $board_list_cards,
            'departments' => $departments,
            'departmentKey' => $department_id,
            'teams' => $teams,
            'teamKey' => $teamKey,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'cover_image_url_trait' => [$this, 'cover_image_url_trait']
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\BoardListCard $board_list_card
     */
    public function edit($id)
    {
        $board_list_card = BoardListCard::find($id);
        if (!$board_list_card) {
            abort(404, 'Board List Card not found.');
        }
        $departments = Department::where('status', 1)->get();
        $teams = Team::where('status', '1')->get();
        $clients = Client::where('status', '1')->get();
        $users = User::where('status', 1)->where('type', '!=', 'client')->get();
        $cover_image_url_trait = [$this, 'cover_image_url_trait'];
        return view('admin.board-list.cards.edit', compact('board_list_card', 'departments', 'teams', 'clients', 'users', 'cover_image_url_trait'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'title' => 'required|string',
                'position' => 'required|integer|min:0|max:65535',
                'department' => 'nullable|integer|exists:departments,id',
                'board_list' => 'nullable|integer|exists:board_lists,id',
                'description' => 'nullable|string',
                'cover_background_color' => 'nullable|string',
                'cover_image_size' => 'nullable|integer',
                'priority' => 'nullable|integer',
                'start_date' => 'nullable|date',
                'due_date' => 'nullable|date|after_or_equal:start_date',
                'task_completed' => 'nullable|boolean',
                'status' => 'nullable|string',
            ];
            $messages = [
                'position.integer' => 'The position must be an integer.',
                'position.min' => 'The position must be at least 0.',
                'position.max' => 'The position must be at most 65,535.',
                'due_date.after_or_equal' => 'The due date must be on or after the start date.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::where('id', $id)->first();
            if (!$board_list_card) {
                return response()->json(['error' => 'Oops! Record not found.'], 404);
            }
            $board_list_card->title = $request->get('title');
            $board_list_card->position = $request->get('position');
            $board_list_card->description = $request->get('description');
            if ($request->remove_cover_image == 1) {
                $board_list_card->cover_image = null;
            }
            $board_list_card->cover_background_color = $request->get('cover_background_color');
            $board_list_card->cover_image_size = $request->get('cover_image_size');
            $board_list_card->priority = $request->get('priority');
            $board_list_card->is_check_start_date = $request->get('is_check_start_date', 0);
            $board_list_card->start_date = $request->get('start_date');
            $board_list_card->is_check_due_date = $request->get('is_check_due_date', 0);
            $board_list_card->due_date = $request->get('due_date');
            $board_list_card->task_completed = $request->get('task_completed', 0);
            $board_list_card->status = $request->get('status') == 'on' ? 1 : 0;
            if ($request->hasFile('cover_image')) {
                $cover_image = $request->file('cover_image');
                $board_list_card = $this->create_cover_image_trait($request, $board_list_card, $cover_image, $cover_image->getClientOriginalName());
            }
            if ($board_list_card->save()) {
                return response()->json(['success' => 'Board List Card updated successfully']);
            }
            return response()->json(['error' => 'Failed to update board list record.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
