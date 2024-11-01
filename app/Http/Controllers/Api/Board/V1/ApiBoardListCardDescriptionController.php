<?php

namespace App\Http\Controllers\Api\Board\V1;

use App\Http\Controllers\Controller;
use App\Models\BoardListCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiBoardListCardDescriptionController extends Controller
{
    public function add_description(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'description' => 'required',
                'task_id' => 'required|integer|exists:board_list_cards,id',
            ], [
                'description.required' => 'The description field is required.',
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::where('id', $request->task_id)->first();
            $board_list_card->description = $request->description;
            if ($board_list_card->save()) {
                return response()->json(['success' => 'Task description added successfully.', 'description' => html_entity_decode($request->description)]);
            }
            return response()->json(['error' => 'Failed to add task description.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }
    public function update_description(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'description' => 'required',
                'task_id' => 'required|integer|exists:board_list_cards,id',
            ], [
                'description.required' => 'The description field is required.',
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::where('id', $request->task_id)->first();
            $board_list_card->description = $request->description;
            if ($board_list_card->save()) {
                return response()->json(['success' => 'Task description updated successfully.', 'description' => html_entity_decode($request->description)]);
            }
            return response()->json(['error' => 'Failed to update task description.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }
}
