<?php

namespace App\Http\Controllers\Api\Board\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BoardListCardActivityResource;
use App\Http\Resources\BoardListCardResource;
use App\Http\Resources\UserResource;
use App\Models\AssignBoardCard;
use App\Models\BoardListCard;
use App\Models\BoardListCardActivity;
use App\Models\User;
use App\Notifications\BoardListNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class ApiBoardListCardUserController extends Controller
{

//    public function assign_unassign_member(Request $request)
//    {
//        try {
//            $validator = Validator::make($request->all(), [
//                'task_id' => 'required|integer|exists:board_list_cards,id',
//                'member_id' => 'required|integer',
//            ], [
//                'member_id.required' => 'The member id field is required.',
//                'member_id.integer' => "The member id must be an integer. The provided value is {$request->member_id}.",
//                'task_id.required' => 'The task id field is required.',
//                'task_id.exists' => 'The selected task id is invalid.',
//                'task_id.integer' => 'The task id must be an integer.',
//            ]);
//
//            if ($validator->fails()) {
//                return response()->json(['errors' => $validator->errors()], 422);
//            }
//            $auth_id = optional(auth()->user())->id;
//            $board_list_card = BoardListCard::where('id', $request->task_id)->first();
//            if ($board_list_card) {
//                $assign_user_ids = AssignBoardCard::where('board_list_card_id', $board_list_card->id)->get()->pluck('user_id')->toArray();
//
//                $user_id = $request->member_id;
//                $member_found = User::where('id', $user_id)->where('type', '!=', 'client')->where('status', 1)->first();
//                if (!$member_found) {
//                    return response()->json(['error' => 'Failed to update member assignment. Member not found.'], 404);
//                }
//                if (in_array($user_id, $assign_user_ids)) {
//                    $assign_user_ids = array_diff($assign_user_ids, [$user_id]);
//                    $action_message = ($auth_id == $user_id) ? 'You have left' : 'Member has removed from';
//                    $status_code = 200;
//                } else {
//                    $assign_user_ids = array_unique(array_merge($assign_user_ids, [(int)$user_id]));
//                    $action_message = ($auth_id == $user_id) ? 'You have joined' : 'Member has added to the';
//                    $status_code = 201;
//                }
//
//                $user_exists = User::whereIn('id', $assign_user_ids)->where('type', '!=', 'client')->where('status', 1)->get();
//                $board_list_card->setUsers()->sync($user_exists);
//
//                $board_list_card->load('getBoardListCardUsers');
//                $board_list_card_resource = new BoardListCardResource($board_list_card);
//                return response()->json(['success' => "$action_message board card successfully.", 'board_list_card' => $board_list_card_resource], $status_code);
//            }
//            return response()->json(['error' => 'Failed to update member assignment. Please try again.'], 400);
//        } catch (\Exception $e) {
//            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
//        }
//    }
    public function getBoardListCardUnAssignedUsers($board_list_card)
    {
        return User::where('type', '!=', 'client')
            ->where('status', 1)
            ->whereNotIn('id', $board_list_card->getBoardListCardUsers->pluck('id')->all())
            ->get(['id', 'name', 'email']);
    }

    public function assign_unassign_member(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer|exists:board_list_cards,id',
                'member_id' => 'required|integer|exists:users,id',
                'assigned' => 'required|boolean',
            ], [
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
                'member_id.required' => 'The member id field is required.',
                'member_id.exists' => 'The selected member id is invalid.',
                'member_id.integer' => "The member ID must be an integer. The provided value is {$request->member_id}.",
                'assigned.required' => 'The assigned field is required.',
                'assigned.boolean' => 'The assigned field must be 1 or 0.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $auth_id = optional(auth()->user())->id;
            $board_list_card = BoardListCard::where('id', $request->task_id)->first();
            if ($board_list_card) {
                $assign_user_ids = AssignBoardCard::where('board_list_card_id', $board_list_card->id)
                    ->get()->pluck('user_id')->toArray();
                $user_id = $request->member_id;
                $assigned = $request->assigned;

                $member_found = User::where('id', $user_id)
                    ->where('type', '!=', 'client')
                    ->where('status', 1)
                    ->first();

                if (!$member_found) {
                    return response()->json(['error' => 'Failed to update member assignment. Member not found.',
//                        'assigned_users' => UserResource::collection($board_list_card->getBoardListCardUsers),
//                        'assigned_users_count' => UserResource::collection($board_list_card->getBoardListCardUsers)->count(),
//                        'unassigned_users' => UserResource::collection($this->getBoardListCardUnAssignedUsers($board_list_card)),
//                        'unassigned_users_count' => optional($this->getBoardListCardUnAssignedUsers($board_list_card))->count(),
                    ], 404);
                }

                if ($assigned) {
                    if (in_array($user_id, $assign_user_ids)) {
                        return response()->json(['error' => ($auth_id == $user_id) ? 'You have already joined this board card.' : 'Member has already assigned to this board card.',
//                            'assigned_users' => UserResource::collection($board_list_card->getBoardListCardUsers),
//                            'assigned_users_count' => UserResource::collection($board_list_card->getBoardListCardUsers)->count(),
//                            'unassigned_users' => UserResource::collection($this->getBoardListCardUnAssignedUsers($board_list_card)),
//                            'unassigned_users_count' => optional($this->getBoardListCardUnAssignedUsers($board_list_card))->count(),
                        ], 400);
                    }
                    $assign_user_ids = array_unique(array_merge($assign_user_ids, [(int)$user_id]));
                    $action_message = ($auth_id == $user_id) ? 'You have joined' : 'Member has been added to the';
                    $status_code = 201;
                    $activity_message = ($auth_id == $user_id) ? 'joined' : "added {$member_found->name} to";
                } else {
                    if (!in_array($user_id, $assign_user_ids)) {
                        return response()->json(['error' => ($auth_id == $user_id) ? 'You have already left this board card.' : 'Member has already removed from this board card.',
//                            'assigned_users' => UserResource::collection($board_list_card->getBoardListCardUsers),
//                            'assigned_users_count' => UserResource::collection($board_list_card->getBoardListCardUsers)->count(),
//                            'unassigned_users' => UserResource::collection($this->getBoardListCardUnAssignedUsers($board_list_card)),
//                            'unassigned_users_count' => optional($this->getBoardListCardUnAssignedUsers($board_list_card))->count(),
                        ], 400);
                    }
                    $assign_user_ids = array_diff($assign_user_ids, [$user_id]);
                    $action_message = ($auth_id == $user_id) ? 'You have left' : 'Member has been removed from';
                    $status_code = 200;
                    $activity_message = ($auth_id == $user_id) ? 'left' : "removed {$member_found->name} from";
                }

                $user_exists = User::whereIn('id', $assign_user_ids)
                    ->where('type', '!=', 'client')
                    ->where('status', 1)
                    ->get();

                $board_list_card->setUsers()->sync($user_exists);

                $board_list_card->load('getBoardListCardUsers');
                $board_list_card_activity = new BoardListCardActivity();
                $board_list_card_activity->board_list_card_id = $board_list_card->id;
                $board_list_card_activity->user_id = $auth_id;
                $board_list_card_activity->activity = "{$activity_message} this board card.";
                $board_list_card_activity->activity_type = 2;
                $board_list_card_activity->save();

                $board_list_card_activity->load('getUser');
                $notify_users = $user_exists;
                if (!$notify_users->contains('id', $auth_id)) {
                    $notify_users->push(User::find($auth_id));
                }
                Notification::send($notify_users, new BoardListNotification($board_list_card_activity, $activity_message . " {$board_list_card->title}."));
                return response()->json([
                    'success' => "$action_message the board card successfully.",
//                    'board_list_card' => new BoardListCardResource($board_list_card),
                    'assigned_users' => UserResource::collection($user_exists),
                    // 'assigned_users' => UserResource::collection($board_list_card->getBoardListCardUsers),
                    'assigned_users_count' => UserResource::collection($user_exists)->count(),
//                    'assigned_users_count' => UserResource::collection($board_list_card->getBoardListCardUsers)->count(),
                    'unassigned_users' => UserResource::collection($this->getBoardListCardUnAssignedUsers($board_list_card)),
                    'unassigned_users_count' => optional($this->getBoardListCardUnAssignedUsers($board_list_card))->count(),
                    'activity' => new BoardListCardActivityResource($board_list_card_activity)
                ], $status_code);
            }

            return response()->json(['error' => 'Failed to update member assignment. Board Card not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }
}
