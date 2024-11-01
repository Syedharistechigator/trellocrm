<?php

namespace App\Http\Controllers\Api\Board\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BoardListCardActivityResource;
use App\Http\Resources\BoardListCardAttachmentResource;
use App\Http\Resources\BoardListCardResource;
use App\Models\BoardListCard;
use App\Models\BoardListCardActivity;
use App\Models\BoardListCardAttachment;
use App\Notifications\BoardListNotification;
use App\Rules\NotSoftDeleted;
use App\Rules\UserMatch;
use App\Traits\BoardListCardCoverImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use File;
use Intervention\Image\Facades\Image as InterventionImage;

class ApiBoardListCardCoverImageController extends Controller
{
    use BoardListCardCoverImageTrait;

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer|exists:board_list_cards,id',
                'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:20480', //20mb
                'cover_background_color' => 'nullable|string|max:20',
            ], [
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
                'cover_image.required' => 'The cover image is required.',
                'cover_image.image' => 'The file must be an image.',
                'cover_image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
                'cover_image.max' => 'The image may not be greater than 2048 kilobytes.',
                'cover_background_color.string' => 'The cover background color must be a string.',
                'cover_background_color.max' => 'The cover background color may not be greater than :max characters.',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::where('id', $request->get('task_id'))->first();
            $cover_image = $request->file('cover_image');
            $board_list_card = $this->create_cover_image_trait($request, $board_list_card, $cover_image, $cover_image->getClientOriginalName());
            if ($board_list_card->save()) {
                $board_list_card_resource = new BoardListCardResource($board_list_card);
                return response()->json(['success' => 'Cover image added successfully', 'board_list_card' => $board_list_card_resource]);
            }
            return response()->json(['error' => 'Failed to add board card cover image.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    function convert_filesize($bytes, $decimals = 2)
    {
        $size = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer|exists:board_list_cards,id',
                'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:20480', //20 mb
                'cover_background_color' => 'nullable|string|max:20',
            ], [
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
                'cover_image.required' => 'The cover image is required.',
                'cover_image.image' => 'The file must be an image.',
                'cover_image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
                'cover_image.max' => 'The image may not be greater than 20480 kilobytes.',
                'cover_background_color.string' => 'The cover background color must be a string.',
                'cover_background_color.max' => 'The cover background color may not be greater than :max characters.',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::where('id', $request->get('task_id'))->first();
            $image = $request->file('cover_image');
//            $board_list_card = $this->create_cover_image_trait($request, $board_list_card, $image);
            $auth_id = auth()->user()->id;
            $board_list_card_activity = new BoardListCardActivity();
            $board_list_card_activity->board_list_card_id = $board_list_card->id;
            $board_list_card_activity->user_id = $auth_id;
            $board_list_card_activity->activity = "updated cover image";
            $board_list_card_activity->activity_type = 1;
            /** 0 = comment , 1 = attachment , 2 = activity*/
            $board_list_card_activity->save();
            $board_list_card_attachment = new BoardListCardAttachment();
            $board_list_card_attachment->board_list_card_id = $board_list_card->id;
            $board_list_card_attachment->user_id = $auth_id;
            $board_list_card_attachment->activity_id = $board_list_card_activity->id;
            $board_list_card_attachment->original_name = $image->getClientOriginalName();
            $attachment_mime_type = $image->getMimeType();
            $board_list_card_attachment->mime_type = $attachment_mime_type;
            $board_list_card_attachment->file_size = $this->convert_filesize($image->getSize());
            $board_list_card_attachment->extension = $image->getClientOriginalExtension();
            $file_directory = str_contains($attachment_mime_type, 'image') ? 'images' : 'images';
            $department_id = optional($board_list_card->getBoardList->getDepartment)->id ?? null;
            $board_list_card_id = optional($board_list_card)->id ?? null;
            $file_directory_path = public_path("assets/{$file_directory}/board-list-card/original/");
            $additional_path = "{$department_id}/{$board_list_card_id}/";
            $file_name = mt_rand() . mt_rand() . mt_rand() . time() . '00' . $auth_id . rand(111, 999) . '_' . $image->getClientOriginalName();
            try {
                $board_list_card = $this->create_cover_image_trait($request, $board_list_card, $image, $image->getClientOriginalName(), $additional_path, $file_name, $file_directory_path);
                $board_list_card->cover_image = $additional_path . $file_name;
                $board_list_card->save();
            } catch (\RuntimeException $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            $board_list_card_attachment->file_name = $additional_path . $file_name;
            $board_list_card_attachment->file_path = $file_directory_path . $additional_path . $file_name;
            $board_list_card_attachment->save();
            $notify_message = " updated cover image ({$image->getClientOriginalName()}) to " . optional($board_list_card)->title;
            $notify_users = $board_list_card->getBoardListCardUsers;
            Notification::send($notify_users, new BoardListNotification($board_list_card_activity, $notify_message));
            $board_list_card_resource = new BoardListCardResource($board_list_card);
            $activity_resource = new BoardListCardActivityResource($board_list_card_activity, 'Activity');
            $attachment_resource = new BoardListCardAttachmentResource($board_list_card_attachment, 'Attachment');
            return response()->json(['success' => 'Cover image updated successfully', 'board_list_card' => $board_list_card_resource, 'activity' => $activity_resource, 'attachment' => $attachment_resource]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function update_cover_background_color(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer|exists:board_list_cards,id',
                'cover_background_color' => 'required|string|max:20',
            ], [
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
                'cover_background_color.string' => 'The cover background color must be a string.',
                'cover_background_color.max' => 'The cover background color may not be greater than :max characters.',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::where('id', $request->get('task_id'))->first();
            $board_list_card->cover_background_color = $request->get('cover_background_color');
            if ($board_list_card->save()) {
                $board_list_card_resource = new BoardListCardResource($board_list_card);
                return response()->json(['success' => 'Cover image background color updated successfully', 'board_list_card' => $board_list_card_resource]);
            }
            return response()->json(['error' => 'Failed to update board card cover image background color.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function set_attachment_as_cover_image(Request $request)
    {
        $auth_id = optional(auth()->user())->id;
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer|exists:board_list_cards,id',
                'attachment_id' => 'required|integer|exists:board_list_card_attachments,id',
            ], [
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
                'attachment_id.required' => 'The attachment id field is required.',
                'attachment_id.exists' => 'The selected attachment id is invalid.',
                'attachment_id.integer' => 'The attachment id must be an integer.',
            ]);
            $validator->after(function ($validator) use ($request) {
                if ($validator->errors()->isEmpty()) {
                    $attachment_id = $request->input('attachment_id');
                    $notSoftDeleted = new NotSoftDeleted(BoardListCardAttachment::class);
                    if (!$notSoftDeleted->passes('attachment_id', $attachment_id)) {
                        $validator->errors()->add('attachment_id', $notSoftDeleted->message());
                    }
                }
            });
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::where('id', $request->get('task_id'))->first();
            $attachment = BoardListCardAttachment::find($request->attachment_id);
            $original_image_name = $attachment->file_name;
            if (!$board_list_card || !$attachment || !$original_image_name) {
                return response()->json(['error' => 'The provided task or attachment ID is invalid. Please verify the ID and try again.'], 400);
            }
            $image_path_1 = public_path("assets/images/board-list-card/original/");
            if (!str_contains($attachment->mime_type, 'image')) {
                return response()->json(['error' => 'The provided attachment is not a valid image file. Please upload a supported image format.'], 400);
            }
            if (!file_exists("{$image_path_1}{$original_image_name}")) {
                return response()->json(['error' => 'The requested attachment file could not be found. Please ensure the file exists and try again.'], 404);
            }
            $image_path = $image_path_1;
            $segments = explode('/', $original_image_name);
            $image_name = end($segments);
            $department_id = $segments[count($segments) - 3];
            $board_list_card_id = $segments[count($segments) - 2];
            $additional_path = "{$department_id}/{$board_list_card_id}/";
            $board_list_card = $this->create_cover_image_trait($request, $board_list_card, $image_path . $original_image_name, $attachment->original_name, $additional_path, $image_name, $image_path, true);
            if ($board_list_card->save()) {
                return response()->json([
                    'success' => 'Attachment successfully added as cover image.',
                    'board_list_card' => new BoardListCardResource($board_list_card)
                ]);
            }
            return response()->json(['error' => 'Failed to update board card cover image.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function remove(Request $request): ?\Illuminate\Http\JsonResponse
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
            $board_list_card->cover_image = null;
            if ($board_list_card->save()) {
                $board_list_card_resource = new BoardListCardResource($board_list_card);
                return response()->json(['success' => 'Cover image removed successfully', 'board_list_card' => $board_list_card_resource]);
            }
            return response()->json(['error' => 'Failed to remove board card cover image.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }
}
