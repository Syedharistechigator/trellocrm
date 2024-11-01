<?php

namespace App\Http\Controllers\Api\Board\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BoardListCardColorResource;
use App\Http\Resources\LabelResource;
use App\Http\Resources\UserResource;
use App\Models\AssignBoardLabel;
use App\Models\BoardListCard;
use App\Models\Color;
use App\Models\Label;
use App\Rules\NotSoftDeleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiBoardListCardLabelController extends Controller
{
    public function getLabels()
    {
        return $this->hasMany(Label::class, 'board_list_card_id', 'id')
            ->leftJoin('colors', 'labels.color_id', '=', 'colors.id')
            ->leftJoin('assign_board_labels', function ($join) {
                $join->on('labels.id', '=', 'assign_board_labels.label_id')
                    ->on('assign_board_labels.board_list_card_id', '=', 'board_list_cards.id');
            })
            ->orderByRaw('assign_board_labels.label_id IS NULL, colors.color_position')
            ->select('labels.*');
    }

    public function show(Request $request, $id = null)
    {
        try {
            if ($request->has('code') && is_null($id)) {
                $id = $this->decryptV1($request->get('code'));
            }
            if (is_null($id)) {
                return response()->json(['error' => 'ID or code is required.'], 400);
            }
            $board_list_card = BoardListCard::where(function ($query) use ($id, $request) {
                if ($id) {
                    $query->where('id', $id);
                } elseif ($request->has('code') && !empty($request->get('code'))) {
                    $query->where('code', $request->get('code'));
                }
            })->first();
            if (!$board_list_card) {
                return response()->json(['error' => 'Oops! Task not found.'], 404);
            }

            $labels = Label::where('board_list_card_id', $board_list_card->id)->orwhere('user_id', auth()->user()->id)->get();
            $assigned_label_ids = AssignBoardLabel::where('board_list_card_id', $board_list_card->id)->pluck('label_id')->toArray();
            $labels->load(['color', 'getLabelUser']);
            $data = [];
            foreach ($labels as $key => $label) {
                $data[$key] = [
                    'id' => $label->id,
                    'board_list_card_id' => $board_list_card->id,
                    'user' => isset($label->getLabelUser) && $label->relationLoaded('getLabelUser') ? new UserResource($label->getLabelUser) : null,
                    'label' => $label->label_text,
                    'assigned' => in_array($label->id, $assigned_label_ids, true),
                    'color' => isset($label->color) && $label->relationLoaded('color') ? new BoardListCardColorResource($label->color) : null,
                ];
            }
            return response()->json([
                'label' => $data,
                'labels_count' => count($data),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer|exists:board_list_cards,id',
                'label' => 'required',
                'label_color_id' => 'required|integer|exists:colors,id',
            ], [
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
                'label.required' => 'The label field is required.',
                'label_color_id.required' => 'The color id field is required.',
                'label_color_id.exists' => 'The selected color id is invalid.',
                'label_color_id.integer' => 'The color id must be an integer.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::find($request->get('task_id'));
            $color = Color::find($request->get('label_color_id'));

            DB::beginTransaction();

            $board_list_card_label = new Label();
            $board_list_card_label->board_list_card_id = $board_list_card->id;
            $board_list_card_label->user_id = auth()->user()->id;
            $board_list_card_label->color_id = $color->id;
            $board_list_card_label->label_text = $request->get('label');
            $board_list_card_label->save();

            $assign_label = new AssignBoardLabel();
            $assign_label->board_list_card_id = $board_list_card->id;
            $assign_label->label_id = $board_list_card_label->id;
            $assign_label->save();
            $board_list_card_label->loadMissing('color', 'getLabelUser');
            DB::commit();
            return response()->json(['success' => 'Label added successfully.',
                'label' => new LabelResource($board_list_card_label),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer|exists:board_list_cards,id',
                'label_id' => 'required|integer|exists:labels,id',
                'label' => 'required',
                'label_color_id' => 'required|integer|exists:colors,id',
            ], [
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
                'label_id.required' => 'The label id field is required.',
                'label_id.exists' => 'The selected label id is invalid.',
                'label_id.integer' => 'The label id must be an integer.',
                'label.required' => 'The label field is required.',
                'label_color_id.required' => 'The color id field is required.',
                'label_color_id.exists' => 'The selected color id is invalid.',
                'label_color_id.integer' => 'The color id must be an integer.',
            ]);

            $validator->after(function ($validator) use ($request) {
                if ($validator->errors()->isEmpty()) {
                    $label_id = $request->input('label_id');
                    $notSoftDeleted = new NotSoftDeleted(Label::class);
                    if (!$notSoftDeleted->passes('label_id', $label_id)) {
                        $validator->errors()->add('label_id', $notSoftDeleted->message());
                    }
                }
            });
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::find($request->get('task_id'));
            $board_list_card_label = Label::where('board_list_card_id', $board_list_card->id)->where('id', $request->get('label_id'))->first();
            if (!$board_list_card_label) {
                return response()->json(['error' => 'Error! Label not found.'], 404);
            }
            $board_list_card_label->label_text = $request->get('label');
            $board_list_card_label->save();
            $board_list_card_label->loadMissing('color', 'getLabelUser');
            return response()->json(['success' => 'Label updated successfully.',
                'label' => new LabelResource($board_list_card_label),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    /** Assign / Unassign Label **/
    public function assign_unassign(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer|exists:board_list_cards,id',
                'label_id' => 'required|integer|exists:labels,id',
                'assigned' => 'required|boolean',
            ], [
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
                'label_id.required' => 'The label id field is required.',
                'label_id.exists' => 'The selected label id is invalid.',
                'label_id.integer' => 'The label id must be an integer.',
                'assigned.required' => 'The assigned field is required.',
                'assigned.boolean' => 'The assigned field must be 1 or 0.',
            ]);
            $validator->after(function ($validator) use ($request) {
                if ($validator->errors()->isEmpty()) {
                    $label_id = $request->input('label_id');
                    $notSoftDeleted = new NotSoftDeleted(Label::class);
                    if (!$notSoftDeleted->passes('label_id', $label_id)) {
                        $validator->errors()->add('label_id', $notSoftDeleted->message());
                    }
                }
            });
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $label_id = $request->get('label_id');
            $label = Label::find($label_id);

            $assigned = $request->get('assigned');
            $board_list_card = BoardListCard::find($request->get('task_id'));
            $assign_label = AssignBoardLabel::where('board_list_card_id', $board_list_card->id)
                ->where('label_id', $label->id)
                ->first();
            $board_label_ids = isset($board_list_card->getLabels) ? $board_list_card->getLabels->pluck('id')->toArray() : [];

            if ($assigned && $assign_label && $label->board_list_card_id == $board_list_card->id) {
                return response()->json(['error' => 'Label is already assigned to this task.',], 400);
            }
            if (!in_array($label->id, $board_label_ids, true)) {
                $board_list_card_label = new Label();
                $board_list_card_label->board_list_card_id = $board_list_card->id;
                $board_list_card_label->user_id = auth()->user()->id;
                $board_list_card_label->color_id = $label->color_id;
                $board_list_card_label->label_text = $label->label_text;
                $board_list_card_label->save();
                $label = $board_list_card_label;
                $assign_label = AssignBoardLabel::where('board_list_card_id', $board_list_card->id)
                    ->where('label_id', $board_list_card_label->id)
                    ->first();
            }
            if (!$assign_label) {
                if ($assigned) {
                    $assign_label = new AssignBoardLabel();
                    $assign_label->board_list_card_id = $board_list_card->id;
                    $assign_label->label_id = $label->id;
                    $assign_label->save();
                    return response()->json(['success' => 'Label assigned successfully.',], 201);
                }
            }

            if (!$assigned && $assign_label && $label->board_list_card_id === $board_list_card->id) {
                $assign_label->delete();
                return response()->json(['success' => 'Label unassigned successfully.',], 200);
            }

            return response()->json(['error' => 'Label is not assigned to this task.',], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer|exists:board_list_cards,id',
                'label_id' => 'required|integer|exists:labels,id',
            ], [
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
                'label_id.required' => 'The label id field is required.',
                'label_id.exists' => 'The selected label id is invalid.',
                'label_id.integer' => 'The label id must be an integer.',
            ]);
            $validator->after(function ($validator) use ($request) {
                if ($validator->errors()->isEmpty()) {
                    $comment_id = $request->input('label_id');
                    $notSoftDeleted = new NotSoftDeleted(Label::class);

                    if (!$notSoftDeleted->passes('label_id', $comment_id)) {
                        $validator->errors()->add('label_id', $notSoftDeleted->message());
                    }
                }
            });
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $board_list_card = BoardListCard::find($request->get('task_id'));
            $assign_label = AssignBoardLabel::where('board_list_card_id', $board_list_card->id)
                ->where('label_id', $request->get('label_id'))
                ->first();

            if ($assign_label) {
                $assign_label->delete();
                $label = Label::where('board_list_card_id', $board_list_card->id)
                    ->where('id', $request->get('label_id'))
                    ->first();
                if ($label) {
                    $label->delete();
                }
                return response()->json(['success' => 'Label deleted successfully.',], 200);
            }
            return response()->json(['error' => 'Label not found on the specified task.',], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
