<?php

namespace App\Http\Controllers\Api\Board\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BoardListCardActivityResource;
use App\Http\Resources\BoardListCardAttachmentResource;
use App\Models\BoardListCard;
use App\Models\BoardListCardActivity;
use App\Models\BoardListCardAttachment;
use App\Notifications\BoardListNotification;
use App\Rules\NotSoftDeleted;
use App\Traits\BoardListCardCoverImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use App\Rules\UserMatch;
use Intervention\Image\Facades\Image as InterventionImage;

ini_set('display_errors', 1);
error_reporting(E_ALL);

class ApiBoardListCardAttachmentController extends Controller
{
    use BoardListCardCoverImageTrait;

    function convert_filesize($bytes, $decimals = 2)
    {
        $size = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
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
            if (isset($board_list_card->getAttachments)) {
                return response()->json([
                    'attachments' => BoardListCardAttachmentResource::collection($board_list_card->getAttachments),
                    'attachments_count' => optional($board_list_card->getAttachments)->count() ?? 0,
                ], 200);
            }
            return response()->json(['error', 'Error! Failed to fetch task attachments'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    private function convertToBytes($value)
    {
        $unit = strtolower(substr($value, -1));
        $value = (int)$value;
        switch ($unit) {
            case 'g':
                return $value * 1024 * 1024 * 1024;
            case 'm':
                return $value * 1024 * 1024;
            case 'k':
                return $value * 1024;
            default:
                return $value;
        }
    }

    public function add_attachments(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer|exists:board_list_cards,id',
                'attachments' => 'required',
//                'attachments.*' => 'required|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar,eml,mp4|max:10240', // 10MB
            ], [
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
                'attachments.required' => 'The attachments field is required.',
                'attachments.*.required' => 'Each attachment is required.',
                'attachments.*.file' => 'Each attachment must be a file.',
                'attachments.*.mimes' => 'Each attachment must be a file of type: jpg, jpeg, png, gif, pdf, doc, docx, xls, xlsx, ppt, pptx, txt, zip, rar, eml , mp4 , webp , ai.',
                'attachments.*.max' => 'Each attachment may not be greater than 10MB.',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::find($request->get('task_id'));
            $res_attachments = is_array($request->file('attachments')) ? $request->file('attachments') : [$request->file('attachments')];
            $errors = [];
            $valid_attachments = [];
            foreach ($res_attachments as $key => $attachment) {
                $attachmentValidator = Validator::make(
                    ['attachments' => $request->file('attachments')],
                    ['attachments.' . $key => 'file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar,eml,mp4,webp,ai|max:51200'],
                    [
                        'attachments.*.file' => "Attachment {$attachment->getClientOriginalName()} must be a valid file.",
                        'attachments.*.mimes' => "Attachment {$attachment->getClientOriginalName()} must be of valid type.",
                        'attachments.*.max' => "Attachment {$attachment->getClientOriginalName()} may not be greater than 20MB.",
                    ]
                );
                if ($attachmentValidator->fails()) {
                    $errors['attachments.' . $key] = $attachmentValidator->errors()->all();
                } else {
                    $valid_attachments[] = $attachment;
                }
                if (str_contains($attachment->getMimeType(), 'image')) {
                    $imageInfo = getimagesize($attachment->getRealPath());
                    $width = isset($imageInfo[0]) ? $imageInfo[0] : 0;
                    $height = isset($imageInfo[1]) ? $imageInfo[1] : 0;
                    $channels = 4;
                    $bits = 8;
                    $estimatedMemory = (($width * $height * $channels * $bits) / 8) * 2;
                    $memoryLimit = ini_get('memory_limit');
                    $memoryLimitBytes = $this->convertToBytes($memoryLimit);
                    $currentMemoryUsage = memory_get_usage(true);
                    if (($estimatedMemory + $currentMemoryUsage) > $memoryLimitBytes) {
                        $errors['attachments.' . $key][] = "Memory limit exceeded for processing Attachment {$attachment->getClientOriginalName()}.";
                    }
                }
            }
            DB::beginTransaction();
            $auth_id = optional(auth()->user())->id;
            $activities = [];
            $notify_activity = [];
            $attachments = [];
            $attachmentNames = [];
            foreach ($valid_attachments as $key => $attachment) {
                $board_list_card_activity = new BoardListCardActivity();
                $board_list_card_activity->board_list_card_id = $board_list_card->id;
                $board_list_card_activity->user_id = $auth_id;
                $board_list_card_activity->activity = "attached";
                $board_list_card_activity->activity_type = 1;
                /** 0 = comment , 1 = attachment , 2 = activity*/
                $board_list_card_activity->save();
                $notify_activity[] = $board_list_card_activity;
                $board_list_card_attachment = new BoardListCardAttachment();
                $board_list_card_attachment->board_list_card_id = $board_list_card->id;
                $board_list_card_attachment->user_id = $auth_id;
                $board_list_card_attachment->activity_id = $board_list_card_activity->id;
                $attachmentNames[] = $board_list_card_attachment->original_name = $attachment->getClientOriginalName();
                $attachment_mime_type = $attachment->getMimeType();
                $board_list_card_attachment->mime_type = $attachment_mime_type;
                $board_list_card_attachment->file_size = $this->convert_filesize($attachment->getSize());
                $board_list_card_attachment->extension = $attachment->getClientOriginalExtension();
                $file_directory = str_contains($attachment_mime_type, 'image') ? 'images' : 'images';
                $department_id = optional($board_list_card->getBoardList->getDepartment)->id ?? null;
                $board_list_card_id = optional($board_list_card)->id ?? null;
                $file_directory_path = public_path("assets/{$file_directory}/board-list-card/original/");
                $additional_path = "{$department_id}/{$board_list_card_id}/";
                $file_name = mt_rand() . mt_rand() . mt_rand() . time() . '00' . $auth_id . rand(111, 999) . '_' . $attachment->getClientOriginalName();
//                if (str_contains($attachment_mime_type, 'image') && ((!$board_list_card->cover_image) || ($board_list_card->cover_image && !file_exists($file_directory_path . $board_list_card->cover_image)))) {
                if (str_contains($attachment_mime_type, 'image')) {
                    $board_list_card = $this->create_cover_image_trait($request, $board_list_card, $attachment, $attachment->getClientOriginalName(), $additional_path, $file_name, $file_directory_path);
                    $board_list_card->save();
                } else {
                    $attachment->move($file_directory_path . $additional_path, $file_name);
                }
                $board_list_card_attachment->file_name = $additional_path . $file_name;
                $board_list_card_attachment->file_path = $file_directory_path . $additional_path . $file_name;
                $board_list_card_attachment->save();
                $board_list_card_activity->load('getAttachmentWithTrashed:id,user_id,board_list_card_id,activity_id,original_name,mime_type,file_name,file_path', 'getUser:id,name,email');
                $activities[] = new BoardListCardActivityResource($board_list_card_activity, 'Activity');
                $attachments[] = new BoardListCardAttachmentResource($board_list_card_attachment, 'Attachment');
                DB::commit();
            }
            if (empty($valid_attachments)) {
                return response()->json(['error' => count($res_attachments) > 1 ? "None of attachment saved successfully." : "Failed to save attachment."], 422);
            }
            if (isset($notify_activity[0])) {
                $notify_message = " added " . (count($res_attachments) > 1 ? "attachments" : "an attachment") . " (" . implode(', ', $attachmentNames) . ") to " . optional($board_list_card)->title;
                $notify_users = $board_list_card->getBoardListCardUsers;
                Notification::send($notify_users, new BoardListNotification($notify_activity[0], $notify_message));
            }
            $response = [
                'success' => (count($valid_attachments) > 1 ? "Attachments" : "Attachment") . " added successfully.",
                'activities' => $activities,
                'attachments' => $attachments,
                'attachments_count' => optional($board_list_card->getAttachments)->count() ?? 0,
            ];
            $statusCode = 201;
            if (!empty($errors)) {
                $response['errors'] = $errors;
                $statusCode = 207;
            }
            return response()->json($response)->setStatusCode($statusCode);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $auth_id = optional(auth()->user())->id;
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
            $validator->after(function ($validator) use ($request, $auth_id) {
                if ($validator->errors()->isEmpty()) {
                    $attachment_id = $request->input('attachment_id');
                    $notSoftDeleted = new NotSoftDeleted(BoardListCardAttachment::class);
                    $userMatch = new UserMatch(BoardListCardAttachment::class, $auth_id);
                    if (!$notSoftDeleted->passes('attachment_id', $attachment_id)) {
                        $validator->errors()->add('attachment_id', $notSoftDeleted->message());
                    }
//                    elseif (!$userMatch->passes('attachment_id', $attachment_id)) {
//                        $validator->errors()->add('attachment_id', $userMatch->message());
//                    }
                }
            });
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::find($request->get('task_id'));
            $attachment = BoardListCardAttachment::where('board_list_card_id', $board_list_card->id)->where('id', $request->get('attachment_id'))
//                ->where('user_id', $auth_id)
                ->first();
            if (!$attachment) {
                return response()->json(['error' => 'Error! Attachment not found.'], 404);
            }
//            if (isset($attachment->getActivity)) {
            /** 0 = comment , 1 = attachment , 2 = activity*/
            $new_activity = new BoardListCardActivity();
            $new_activity->user_id = $auth_id;
            $new_activity->board_list_card_id = $board_list_card->id;
            $new_activity->activity = " deleted the " . $attachment->original_name . " attachment from this card";
            $new_activity->activity_type = 2;
            if ($new_activity->save()) {
                $previous_activity_id = optional($attachment->getActivity)->id;
                $attachment->getActivity->delete();
                $attachment->delete();
                return response()->json([
                    'success' => 'Attachment deleted successfully.',
                    'previous_activity_id' => $previous_activity_id,
                    'activity' => new BoardListCardActivityResource($new_activity),
                    'attachments' => BoardListCardAttachmentResource::collection($board_list_card->getAttachments),
                    'attachments_count' => optional($board_list_card->getAttachments)->count() ?? 0,
                ]);
            }
//            }
            return response()->json(['error' => 'Failed to delete attachment.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }
}

//
//namespace App\Http\Controllers\Api\Board\V1;
//
//use App\Http\Controllers\Controller;
//use App\Http\Resources\BoardListCardActivityResource;
//use App\Http\Resources\BoardListCardAttachmentResource;
//use App\Models\BoardListCard;
//use App\Models\BoardListCardActivity;
//use App\Models\BoardListCardAttachment;
//use App\Notifications\BoardListNotification;
//use App\Rules\NotSoftDeleted;
//use App\Traits\BoardListCardCoverImageTrait;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Notification;
//use Illuminate\Support\Facades\Validator;
//use App\Rules\UserMatch;
//use Intervention\Image\Facades\Image as InterventionImage;
//
//class ApiBoardListCardAttachmentController extends Controller
//{
//    use BoardListCardCoverImageTrait;
//
//    function convert_filesize($bytes, $decimals = 2)
//    {
//        $size = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
//        $factor = floor((strlen($bytes) - 1) / 3);
//        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
//    }
//
//    public function show(Request $request, $id = null)
//    {
//        try {
//            if ($request->has('code') && is_null($id)) {
//                $id = $this->decryptV1($request->get('code'));
//            }
//            if (is_null($id)) {
//                return response()->json(['error' => 'ID or code is required.'], 400);
//            }
//            $board_list_card = BoardListCard::where(function ($query) use ($id, $request) {
//                if ($id) {
//                    $query->where('id', $id);
//                } elseif ($request->has('code') && !empty($request->get('code'))) {
//                    $query->where('code', $request->get('code'));
//                }
//            })->first();
//            if (!$board_list_card) {
//                return response()->json(['error' => 'Oops! Task not found.'], 404);
//            }
//            if (isset($board_list_card->getAttachments)) {
//                return response()->json([
//                    'attachments' => BoardListCardAttachmentResource::collection($board_list_card->getAttachments),
//                    'attachments_count' => optional($board_list_card->getAttachments)->count() ?? 0,
//                ], 200);
//            }
//            return response()->json(['error', 'Error! Failed to fetch task attachments'], 400);
//        } catch (\Exception $e) {
//            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
//        }
//    }
//
//    public function add_attachments(Request $request)
//    {
//        try {
//            $validator = Validator::make($request->all(), [
//                'task_id' => 'required|integer|exists:board_list_cards,id',
//                'attachments' => 'required',
//                'attachments.*' => 'required|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar,eml,mp4|max:10240', // 10MB
//            ], [
//                'task_id.required' => 'The task id field is required.',
//                'task_id.exists' => 'The selected task id is invalid.',
//                'task_id.integer' => 'The task id must be an integer.',
//                'attachments.required' => 'The attachments field is required.',
//                'attachments.*.required' => 'Each attachment is required.',
//                'attachments.*.file' => 'Each attachment must be a file.',
//                'attachments.*.mimes' => 'Each attachment must be a file of type: jpg, jpeg, png, gif, pdf, doc, docx, xls, xlsx, ppt, pptx, txt, zip, rar, eml , mp4.',
//                'attachments.*.max' => 'Each attachment may not be greater than 10MB.',
//            ]);
//
//            if ($validator->fails()) {
//                return response()->json(['errors' => $validator->errors()], 422);
//            }
//            $board_list_card = BoardListCard::find($request->get('task_id'));
//
//            $res_attachments = is_array($request->file('attachments')) ? $request->file('attachments') : [$request->file('attachments')];
//
//            foreach ($res_attachments as $attachment) {
//                if (!$attachment->isValid()) {
//                    throw new \RuntimeException('Invalid attachment file.');
//                }
//            }
//            DB::beginTransaction();
//            $auth_id = optional(auth()->user())->id;
//            $activities = [];
//            $attachments = [];
//            $attachmentNames = [];
//
//            foreach ($res_attachments as $key => $attachment) {
//                $board_list_card_activity = new BoardListCardActivity();
//                $board_list_card_activity->board_list_card_id = $board_list_card->id;
//                $board_list_card_activity->user_id = $auth_id;
//                $board_list_card_activity->activity = "attached";
//                $board_list_card_activity->activity_type = 1;
//                /** 0 = comment , 1 = attachment , 2 = activity*/
//                $board_list_card_activity->save();
//
//                $board_list_card_attachment = new BoardListCardAttachment();
//                $board_list_card_attachment->board_list_card_id = $board_list_card->id;
//                $board_list_card_attachment->user_id = $auth_id;
//                $board_list_card_attachment->activity_id = $board_list_card_activity->id;
//
//                $attachmentNames[] = $board_list_card_attachment->original_name = $attachment->getClientOriginalName();
//                $attachment_mime_type = $attachment->getMimeType();
//                $board_list_card_attachment->mime_type = $attachment_mime_type;
//                $board_list_card_attachment->file_size = $this->convert_filesize($attachment->getSize());
//                $board_list_card_attachment->extension = $attachment->getClientOriginalExtension();
//                $file_directory = str_contains($attachment_mime_type, 'image') ? 'images' : 'images';
//
//                $department_id = optional($board_list_card->getBoardList->getDepartment)->id ?? null;
//                $board_list_card_id = optional($board_list_card)->id ?? null;
//
//                $file_directory_path = public_path("assets/{$file_directory}/board-list-card/original/");
//                $additional_path = "{$department_id}/{$board_list_card_id}/";
//                  $file_name = mt_rand() . mt_rand() . mt_rand() . time() . '00' . $auth_id . rand(111, 999) . '_'. $attachment->getClientOriginalName() . '.' . $board_list_card_attachment->extension;
//
////                if (str_contains($attachment_mime_type, 'image') && ((!$board_list_card->cover_image) || ($board_list_card->cover_image && !file_exists($file_directory_path . $board_list_card->cover_image)))) {
//                if (str_contains($attachment_mime_type, 'image')) {
//                    $board_list_card = $this->create_cover_image_trait($request, $board_list_card, $attachment, $additional_path, $file_name, $file_directory_path);
//                    $board_list_card->save();
//                } else {
//                    $attachment->move($file_directory_path . $additional_path, $file_name);
//                }
//                $board_list_card_attachment->file_name = $additional_path . $file_name;
//                $board_list_card_attachment->file_path = $file_directory_path . $additional_path . $file_name;
//                $board_list_card_attachment->save();
//
//                $board_list_card_activity->load('getAttachmentWithTrashed:id,user_id,board_list_card_id,activity_id,original_name,mime_type,file_name,file_path', 'getUser:id,name,email');
//                $activities[] = new BoardListCardActivityResource($board_list_card_activity, 'Activity');
//                $attachments[] = new BoardListCardAttachmentResource($board_list_card_attachment, 'Attachment');
//            }
//
//            $notify_message = " added " . (count($res_attachments) > 1 ? "attachments" : "an attachment") . " (" . implode(', ', $attachmentNames) . ") to " . optional($board_list_card)->title;
//            $notify_users = $board_list_card->getBoardListCardUsers;
//            Notification::send($notify_users, new BoardListNotification($board_list_card_activity, $notify_message));
//
//            DB::commit();
//            return response()->json(['success' => 'Attachment added successfully.', 'activities' => $activities, 'attachments' => $attachments, 'attachments_count' => optional($board_list_card->getAttachments)->count() ?? 0,]);
//
//        } catch (\Exception $e) {
//            DB::rollBack();
//            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
//        }
//    }
//
//    public function delete(Request $request)
//    {
//        try {
//            $auth_id = optional(auth()->user())->id;
//
//            $validator = Validator::make($request->all(), [
//                'task_id' => 'required|integer|exists:board_list_cards,id',
//                'attachment_id' => 'required|integer|exists:board_list_card_attachments,id',
//
//            ], [
//                'task_id.required' => 'The task id field is required.',
//                'task_id.exists' => 'The selected task id is invalid.',
//                'task_id.integer' => 'The task id must be an integer.',
//                'attachment_id.required' => 'The attachment id field is required.',
//                'attachment_id.exists' => 'The selected attachment id is invalid.',
//                'attachment_id.integer' => 'The attachment id must be an integer.',
//            ]);
//            $validator->after(function ($validator) use ($request, $auth_id) {
//                if ($validator->errors()->isEmpty()) {
//                    $attachment_id = $request->input('attachment_id');
//                    $notSoftDeleted = new NotSoftDeleted(BoardListCardAttachment::class);
//                    $userMatch = new UserMatch(BoardListCardAttachment::class, $auth_id);
//
//                    if (!$notSoftDeleted->passes('attachment_id', $attachment_id)) {
//                        $validator->errors()->add('attachment_id', $notSoftDeleted->message());
//                    }
////                    elseif (!$userMatch->passes('attachment_id', $attachment_id)) {
////                        $validator->errors()->add('attachment_id', $userMatch->message());
////                    }
//                }
//            });
//            if ($validator->fails()) {
//                return response()->json(['errors' => $validator->errors()], 422);
//            }
//            $board_list_card = BoardListCard::find($request->get('task_id'));
//            $attachment = BoardListCardAttachment::where('board_list_card_id', $board_list_card->id)->where('id', $request->get('attachment_id'))
////                ->where('user_id', $auth_id)
//                ->first();
//            if (!$attachment) {
//                return response()->json(['error' => 'Error! Attachment not found.'], 404);
//            }
////            if (isset($attachment->getActivity)) {
//            /** 0 = comment , 1 = attachment , 2 = activity*/
//            $new_activity = new BoardListCardActivity();
//            $new_activity->user_id = $auth_id;
//            $new_activity->board_list_card_id = $board_list_card->id;
//            $new_activity->activity = " deleted the " . $attachment->original_name . " attachment from this card";
//            $new_activity->activity_type = 2;
//            if ($new_activity->save()) {
//                $previous_activity_id = optional($attachment->getActivity)->id;
//                $attachment->getActivity->delete();
//                $attachment->delete();
//                return response()->json([
//                    'success' => 'Attachment deleted successfully.',
//                    'previous_activity_id' => $previous_activity_id,
//                    'activity' => new BoardListCardActivityResource($new_activity),
//                    'attachments' => BoardListCardAttachmentResource::collection($board_list_card->getAttachments),
//                    'attachments_count' => optional($board_list_card->getAttachments)->count() ?? 0,
//                ]);
//            }
////            }
//            return response()->json(['error' => 'Failed to delete attachment.'], 400);
//        } catch (\Exception $e) {
//            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
//        }
//    }
//
//}
