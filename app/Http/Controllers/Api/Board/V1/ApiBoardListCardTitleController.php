<?php

namespace App\Http\Controllers\Api\Board\V1;

use App\Http\Controllers\Controller;
use App\Models\BoardListCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiBoardListCardTitleController extends Controller
{
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer|exists:board_list_cards,id',
                'title' => 'required|string',
            ], [
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
                'title.required' => 'The title field is required.',
                'title.string' => 'The title must be a string.',
                'title.regex' => 'The title must only contain letters, numbers, spaces, and the following special characters: .,!@#$%^&*()',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::where('id', $request->get('task_id'))->first();

            $board_list_card->title = $request->get('title');
            if ($board_list_card->save()) {
                return response()->json(['success' => 'Task title added successfully.', 'title' => $board_list_card->title]);
            }
            return response()->json(['error' => 'Failed to add task title.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer|exists:board_list_cards,id',
                'title' => 'required|string',
            ], [
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
                'title.required' => 'The title field is required.',
                'title.string' => 'The title must be a string.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::where('id', $request->get('task_id'))->first();

            $board_list_card->title = $request->get('title');
            if ($board_list_card->save()) {
                return response()->json(['success' => 'Task title updated successfully.', 'title' => $board_list_card->title]);
            }
            return response()->json(['error' => 'Failed to update task title.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer|exists:board_list_cards,id',
            ], [
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::where('id', $request->get('task_id'))->first();

            $board_list_card->title = null;
            if ($board_list_card->save()) {
                return response()->json(['success' => 'Task title removed successfully.']);
            }
            return response()->json(['error' => 'Failed to remove task title.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

}
