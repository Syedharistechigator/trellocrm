<?php

namespace App\Http\Controllers\Api\Board\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BoardListCardActivityResource;
use App\Http\Resources\BoardListCardCommentResource;
use App\Models\BoardListCard;
use App\Models\BoardListCardActivity;
use App\Models\BoardListCardComment;
use App\Models\BoardListCardCommentPreviousLog;
use App\Notifications\BoardListNotification;
use App\Rules\UserMatch;
use App\Rules\NotSoftDeleted;
use App\Traits\BoardListCardCoverImageTrait;
use Carbon\Carbon;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image as InterventionImage;

class ApiBoardListCardCommentController extends Controller
{
    use BoardListCardCoverImageTrait;

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
            if (isset($board_list_card->getComments)) {
                return response()->json([
                    'comments' => BoardListCardCommentResource::collection($board_list_card->getComments),
                    'comments_count' => optional($board_list_card->getComments)->count() ?? 0,
                ], 200);
            }
            return response()->json(['error', 'Error! Failed to fetch task comments'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer|exists:board_list_cards,id',
                'text' => 'required',
            ], [
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
                'text.required' => 'The comment field is required.',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::find($request->get('task_id'));
            DB::beginTransaction();
            $auth_id = optional(auth()->user())->id;
            $text_content = $request->input('text');
            if (preg_match_all('/data:([^;]+);base64,([a-zA-Z0-9\/+=]+)/', $text_content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $key => $match) {
                    $extension = explode('/', $match[1])[1]; // Extract the extension from the MIME type
                    $image_data = base64_decode($match[2]);
                    $filename = '_blcc' . $key . mt_rand() . '.' . $extension;
                    $file_name = mt_rand() . mt_rand() . mt_rand() . time() . "00" . $auth_id . rand(111, 999) . $filename;
                    $original_path = "assets/images/board-list-card/original/";
                    $department_id = optional($board_list_card->getBoardList->getDepartment)->id ?? null;
                    $board_list_card_id = optional($board_list_card)->id ?? null;
                    $additional_path = "{$department_id}/{$board_list_card_id}/";
                    $full_image_path = $original_path . $additional_path . $file_name;
                    if (!File::exists(public_path($original_path . $additional_path))) {
                        File::makeDirectory(public_path($original_path . $additional_path), 0755, true);
                    }
                    file_put_contents(public_path($full_image_path), $image_data);
                    $url = asset("$original_path{$additional_path}$file_name");
                    $pos = strpos($text_content, $match[0]);
                    if ($pos !== false) {
                        $text_content = substr_replace($text_content, $url, $pos, strlen($match[0]));
                    }
                    if (@getimagesize(public_path($full_image_path))) {
                        foreach ($this->imageSizes as [$width, $height]) {
                            $resizedImagePath = public_path("assets/images/board-list-card/{$width}x{$height}/");
                            if (!File::exists($resizedImagePath . $additional_path)) {
                                File::makeDirectory($resizedImagePath . $additional_path, 0755, true);
                            }
                            $resizedImage = InterventionImage::make(public_path($full_image_path));
                            $originalWidth = $resizedImage->width();
                            $originalHeight = $resizedImage->height();
                            $maxSize = $height;
                            if ($originalWidth >= $originalHeight) {
                                $newWidth = $maxSize;
                                $newHeight = (int)($originalHeight * ($maxSize / $originalWidth));
                            } else {
                                $newHeight = $maxSize;
                                $newWidth = (int)($originalWidth * ($maxSize / $originalHeight));
                            }
                            $resizedImage = $resizedImage->resize($newWidth, $newHeight, function ($c) {
                                $c->aspectRatio();
                                $c->upsize();
                            })->resizeCanvas($newWidth, $newHeight);
                            $resizedImage->save($resizedImagePath . $additional_path . $file_name);
                        }
                    }
                }
            }
            $board_list_card_activity = new BoardListCardActivity();
            $board_list_card_activity->board_list_card_id = $board_list_card->id;
            $board_list_card_activity->user_id = $auth_id;
            $board_list_card_activity->activity = "commented";
            /** 0 = comment , 1 = attachment , 2 = activity*/
            $board_list_card_activity->activity_type = 0;
            $board_list_card_activity->save();
            $board_list_card_comment = new BoardListCardComment();
            $board_list_card_comment->board_list_card_id = $board_list_card->id;
            $board_list_card_comment->user_id = $auth_id;
            $board_list_card_comment->activity_id = $board_list_card_activity->id;
            $board_list_card_comment->comment = $text_content;
            $board_list_card_comment->save();
            $board_list_card_activity->load('getCommentWithTrashed:id,activity_id,comment', 'getUser:id,name,email');
            $notify_message = "write a comment {$text_content}";
            $notify_users = $board_list_card->getBoardListCardUsers;
            Notification::send($notify_users, new BoardListNotification($board_list_card_activity, $notify_message));
            DB::commit();
            return response()->json(['success' => 'Comment added successfully.',
                'activity' => new BoardListCardActivityResource($board_list_card_activity, 'Activity'),
                'comment' => new BoardListCardCommentResource($board_list_card_comment, 'Comment'),
                'comments_count' => optional($board_list_card->getComments)->count() ?? 0,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $auth_id = optional(auth()->user())->id;
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer|exists:board_list_cards,id',
                'comment_id' => 'required|integer|exists:board_list_card_comments,id',
                'text' => 'required',
            ], [
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
                'comment_id.required' => 'The comment id field is required.',
                'comment_id.exists' => 'The selected comment id is invalid.',
                'comment_id.integer' => 'The comment id must be an integer.',
                'text.required' => 'The comment field is required.',
            ]);
            $validator->after(function ($validator) use ($request, $auth_id) {
                if ($validator->errors()->isEmpty()) {
                    $comment_id = $request->input('comment_id');
                    $notSoftDeleted = new NotSoftDeleted(BoardListCardComment::class);
                    $userMatch = new UserMatch(BoardListCardComment::class, $auth_id);
                    if (!$notSoftDeleted->passes('comment_id', $comment_id)) {
                        $validator->errors()->add('comment_id', $notSoftDeleted->message());
                    } elseif (!$userMatch->passes('comment_id', $comment_id)) {
                        $validator->errors()->add('comment_id', $userMatch->message());
                    }
                }
            });
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::find($request->get('task_id'));
            $board_list_card_comment = BoardListCardComment::where('board_list_card_id', $board_list_card->id)->where('id', $request->get('comment_id'))->first();
            if (!$board_list_card_comment) {
                return response()->json(['error' => 'Error! Comment not found.'], 404);
            }
            $text_content = $request->input('text');
            $text_content = $request->input('text');
            if (preg_match_all('/data:([^;]+);base64,([a-zA-Z0-9\/+=]+)/', $text_content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $key => $match) {
                    $extension = explode('/', $match[1])[1]; // Extract the extension from the MIME type
                    $image_data = base64_decode($match[2]);
                    $filename = '_blcc' . $key . mt_rand() . '.' . $extension;
                    $file_name = mt_rand() . mt_rand() . mt_rand() . time() . "00" . $auth_id . rand(111, 999) . $filename;
                    $original_path = "assets/images/board-list-card/original/";
                    $department_id = optional($board_list_card->getBoardList->getDepartment)->id ?? null;
                    $board_list_card_id = optional($board_list_card)->id ?? null;
                    $additional_path = "{$department_id}/{$board_list_card_id}/";
                    $full_image_path = $original_path . $additional_path . $file_name;
                    if (!File::exists(public_path($original_path . $additional_path))) {
                        File::makeDirectory(public_path($original_path . $additional_path), 0755, true);
                    }
                    file_put_contents(public_path($full_image_path), $image_data);
                    $url = asset("$original_path{$additional_path}$file_name");
                    $pos = strpos($text_content, $match[0]);
                    if ($pos !== false) {
                        $text_content = substr_replace($text_content, $url, $pos, strlen($match[0]));
                    }
                    if (@getimagesize(public_path($full_image_path))) {
                        foreach ($this->imageSizes as [$width, $height]) {
                            $resizedImagePath = public_path("assets/images/board-list-card/{$width}x{$height}/");
                            if (!File::exists($resizedImagePath . $additional_path)) {
                                File::makeDirectory($resizedImagePath . $additional_path, 0755, true);
                            }
                            $resizedImage = InterventionImage::make(public_path($full_image_path));
                            $originalWidth = $resizedImage->width();
                            $originalHeight = $resizedImage->height();
                            $maxSize = $height;
                            if ($originalWidth >= $originalHeight) {
                                $newWidth = $maxSize;
                                $newHeight = (int)($originalHeight * ($maxSize / $originalWidth));
                            } else {
                                $newHeight = $maxSize;
                                $newWidth = (int)($originalWidth * ($maxSize / $originalHeight));
                            }
                            $resizedImage = $resizedImage->resize($newWidth, $newHeight, function ($c) {
                                $c->aspectRatio();
                                $c->upsize();
                            })->resizeCanvas($newWidth, $newHeight);
                            $resizedImage->save($resizedImagePath . $additional_path . $file_name);
                        }
                    }
                }
            }
//            if (isset($board_list_card_comment->getActivity)) {
            $previous_log = new BoardListCardCommentPreviousLog();
            $previous_log->user_id = auth()->user()->id;
            $previous_log->previous_comment = $board_list_card_comment->comment;
            $previous_log->comment_id = $board_list_card_comment->id;
            $previous_log->save();
            $notify_message = "updated card ( {$board_list_card->title} ) comment from {$board_list_card_comment->comment} to {$text_content}";
            $board_list_card_comment->comment = $text_content;
            $board_list_card_comment->save();
            $board_list_card_activity = new BoardListCardActivity();
            $board_list_card_activity->board_list_card_id = $board_list_card->id;
            $board_list_card_activity->user_id = auth()->user()->id;
            $board_list_card_activity->activity = "updated comment on this card.";
            $board_list_card_activity->activity_type = 2;
            $board_list_card_activity->save();
            $board_list_card_activity->load('getUser');
            $notify_users = $board_list_card->getBoardListCardUsers;
            Notification::send($notify_users, new BoardListNotification($board_list_card_activity, $notify_message));
            return response()->json(['success' => 'Comment updated successfully.', 'activity' => new BoardListCardActivityResource($board_list_card_activity, 'Comment'),
                'comment' => new BoardListCardCommentResource($board_list_card_comment, 'Comment'),
                'comments_count' => optional($board_list_card->getComments)->count() ?? 0,
            ]);
//            }
//            return response()->json(['error' => 'Failed to update Comment.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $auth_id = optional(auth()->user())->id;
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer|exists:board_list_cards,id',
                'comment_id' => 'required|integer|exists:board_list_card_comments,id',
            ], [
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
                'comment_id.required' => 'The comment id field is required.',
                'comment_id.exists' => 'The selected comment id is invalid.',
                'comment_id.integer' => 'The comment id must be an integer.',
            ]);
            $validator->after(function ($validator) use ($request, $auth_id) {
                if ($validator->errors()->isEmpty()) {
                    $comment_id = $request->input('comment_id');
                    $notSoftDeleted = new NotSoftDeleted(BoardListCardComment::class);
                    $userMatch = new UserMatch(BoardListCardComment::class, $auth_id);
                    if (!$notSoftDeleted->passes('comment_id', $comment_id)) {
                        $validator->errors()->add('comment_id', $notSoftDeleted->message());
                    } elseif (!$userMatch->passes('comment_id', $comment_id)) {
                        $validator->errors()->add('comment_id', $userMatch->message());
                    }
                }
            });
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::find($request->get('task_id'));
            $comment = BoardListCardComment::where('board_list_card_id', $board_list_card->id)->where('id', $request->get('comment_id'))->where('user_id', $auth_id)->first();
            if (!$comment) {
                return response()->json(['error' => 'Error! Comment not found.'], 404);
            }
            if (isset($comment->getActivity)) {
                /** 0 = comment , 1 = attachment , 2 = activity*/
                $new_activity = new BoardListCardActivity();
                $new_activity->user_id = $auth_id;
                $new_activity->board_list_card_id = $board_list_card->id;
                $new_activity->activity = " deleted comment from this card";
                $new_activity->activity_type = 2;
                if ($new_activity->save()) {
                    $previous_activity_id = optional($comment->getActivity)->id;
                    $comment->getActivity->delete();
                    $comment->delete();
                    return response()->json([
                        'success' => 'Comment deleted successfully.',
                        'previous_activity_id' => $previous_activity_id,
                        'activity' => new BoardListCardActivityResource($new_activity),
                        'comments' => BoardListCardCommentResource::collection($board_list_card->getComments),
                        'comments_count' => optional($board_list_card->getComments)->count() ?? 0,
                    ]);
                }
            }
            return response()->json(['error' => 'Failed to delete comment.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
