<?php

namespace App\Http\Controllers\Api\Board\V1;

use App\Http\Controllers\Controller;
use App\Models\BoardListCard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiBoardListCardDateController extends Controller
{
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer|exists:board_list_cards,id',
                'is_check_start_date' => 'required|boolean',
                'is_check_due_date' => 'required|boolean',
                'start_date' => ['nullable', 'regex:/^(0?[1-9]|1[0-2])\/(0?[1-9]|[12][0-9]|3[01])\/[0-9]{4}$/'],
                'due_date' => ['nullable', 'regex:/^(0?[1-9]|1[0-2])\/(0?[1-9]|[12][0-9]|3[01])\/[0-9]{4}$/'],
            ], [
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
                'start_date.regex' => 'The start date must be in the format MM/DD/YYYY, e.g., 01/31/2024.',
                'due_date.regex' => 'The due date must be in the format MM/DD/YYYY, e.g., 01/31/2024.'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()]);
            }

            $board_list_card = BoardListCard::where('id', $request->task_id)->first();

            if (!$board_list_card) {
                return response()->json(['error' => 'Error! Task not found.'], 404);
            }

            // Validate and handle date conditions
            if ($request->get('is_check_start_date') == 1 && $request->filled('start_date') && $request->filled('due_date') && Carbon::createFromFormat('m/d/Y', $request->input('start_date')) > Carbon::createFromFormat('m/d/Y', $request->input('due_date'))) {
                return response()->json(['error' => 'Start date cannot be greater than due date.'], 400);
            }

            if ($request->get('is_check_start_date') == 1 && $request->input('start_date')) {
                $board_list_card->is_check_start_date = $request->input('is_check_start_date');
                $board_list_card->start_date = Carbon::createFromFormat('m/d/Y', $request->input('start_date'))->format('Y-m-d H:i:s');
            }

            if ($request->get('is_check_due_date') == 1 && $request->input('due_date')) {
                $board_list_card->is_check_due_date = $request->input('is_check_due_date');
                $due_date = Carbon::createFromFormat('m/d/Y', $request->input('due_date'))->format('Y-m-d');

                if ($request->filled('due_time')) {
                    // Merge date and time if provided
                    $due_date = Carbon::createFromFormat('m/d/Y h:i A', $request->input('due_date') . ' ' . $request->input('due_time'))->format('Y-m-d H:i:s');
                } else {
                    // Default time to the start of the day
                    $due_date .= ' 00:00:00';
                }

                $board_list_card->due_date = $due_date;
            }

            // Save changes to the model
            if ($board_list_card->save()) {
                $data = [
                    'success' => 'Dates updated successfully',
                    'is_check_start_date' => $board_list_card->is_check_start_date,
                    'is_check_due_date' => $board_list_card->is_check_due_date,
                    'start_date' => $board_list_card->start_date ? Carbon::parse($board_list_card->start_date)->format('m/d/Y') : null,
                    'due_date' => $board_list_card->due_date ? Carbon::parse($board_list_card->due_date)->format('m/d/Y h:i A') : null,
                    'due_time' => $board_list_card->due_date ? Carbon::parse($board_list_card->due_date)->format('h:i A') : null,
                    'formatted_date' => $board_list_card->start_date && $board_list_card->is_check_start_date == 1 ? Carbon::parse($board_list_card->start_date)->format('M j') . ' - ' . Carbon::parse($board_list_card->due_date)->format('M j') : ($board_list_card->due_date ? Carbon::parse($board_list_card->due_date)->format('M j') : null),
                ];

                return response()->json($data);
            }

            return response()->json(['error', 'Error! Failed to update date.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function remove(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer|exists:board_list_cards,id',
                'is_check_start_date' => 'nullable|boolean',
                'is_check_due_date' => 'nullable|boolean',
                'start_date' => ['nullable', 'regex:/^(0?[1-9]|1[0-2])\/(0?[1-9]|[12][0-9]|3[01])\/[0-9]{4}$/'],
                'due_date' => ['nullable', 'regex:/^(0?[1-9]|1[0-2])\/(0?[1-9]|[12][0-9]|3[01])\/[0-9]{4}$/'],
                // 'due_time' => ['nullable', 'regex:/^(0?[1-9]|1[0-2]):[0-5][0-9] (AM|PM)$/'],
            ], [
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
                'start_date' => 'The start date must be in the format MM/DD/YYYY, e.g., 01/31/2024.',
                'due_date' => 'The due date must be in the format MM/DD/YYYY, e.g., 01/31/2024.'

            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()]);
            }
            $board_list_card = BoardListCard::where('id', $request->task_id)->first();

            if (!$board_list_card) {
                return response()->json(['error' => 'Error! Task not found.'], 404);
            }
            if ($request->filled('is_check_start_date') && $request->input('start_date') === null) {
                $board_list_card->is_check_start_date = 0;
                $board_list_card->start_date = null;
            }

            if ($request->filled('is_check_due_date') && $request->input('due_date') === null) {
                $board_list_card->is_check_due_date = 0;
                $board_list_card->due_date = null;
            }

            if ($board_list_card->save()) {
                $data = null;

                return response()->json(['success' => 'Dates remove successfully', 'status' => 1, 'data' => $data]);
            }

            throw new \RuntimeException('Failed to update date.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function mergeDateAndTime($date, $time)
    {
        if ($date && $time) {
            return Carbon::parse($date . ' ' . $time)->toDateTimeString();
        }
        return null;
    }

}
