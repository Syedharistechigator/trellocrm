<?php

namespace App\Http\Controllers\Usercontroller;

use App\Http\Controllers\Controller;
use App\Models\AssignBoardCard;
use App\Models\AssignBoardLabel;
use App\Models\BoardList;
use App\Models\BoardListCard;
use App\Models\BoardListCardActivity;
use App\Models\BoardListCardAttachment;
use App\Models\BoardListCardComment;
use App\Models\BoardListCardCommentPreviousLog;
use App\Models\Color;
use App\Models\Label;
use App\Models\User;
use App\Traits\BoardListCardCoverImageTrait;
use Carbon\Carbon;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BoardListCardController extends Controller
{
    use BoardListCardCoverImageTrait;

    public function index(Request $request)
    {
        $board_list_card = BoardListCard::where('id', $request->board_list_card_id)->first();
        return response()->json([
            'board_list_card' => $board_list_card
        ]);
    }

    private function file_path($file_name, $type = "images", $clientPath = null)
    {
        $path = "assets/{$type}";
        $directories = [
            "{$path}/{$file_name}",
            "{$path}/board-list-card/{$file_name}",
            "{$path}/board-list-card/original/{$file_name}",
//            "{$path}/board-list-card/original/{$clientPath}/{$file_name}",
//            "{$path}/board-list-card/original/random-client/{$file_name}",
        ];
        foreach ($directories as $directory) {
            $fullPath = public_path($directory);
            if (file_exists($fullPath)) {
                return [
                    "exists" => true,
                    "path" => asset($directory)
                ];
            }
        }
        return [
            "exists" => false,
            "path" => asset("assets/images/no-results-found.png")
        ];
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'board_list_id' => 'required',
                'title' => 'required',
                'client_id' => 'required',
            ];
            $messages = [
                'board_list_id.required' => 'The board card field is required.',
                'title.required' => 'The title field is required.',
                'client_id.required' => 'The Client field is required.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = new BoardListCard();
            $board_list_card->board_list_id = $request->get('board_list_id');
            $board_list_card->title = $request->get('title');
            $board_list_card->client_id = $request->get('client_id');
            $board_list_card->save();
            $labelData = [
                ['color_id' => '6', 'label_text' => 'Need More Amazing Mock Design'],
                ['color_id' => '7', 'label_text' => 'Urgent'],
                ['color_id' => '9', 'label_text' => 'Most High Priority'],
                ['color_id' => '22', 'label_text' => 'High Priority Client'],
                ['color_id' => '28', 'label_text' => 'Need Amazing Mockup'],
            ];
            $board_list_card->setLabels()->createMany($labelData);
            if ($board_list_card->getActivities()->create([
                    'user_id' => auth()->id(),
                    'board_list_card_id' => $board_list_card->id,
                    'activity' => 'added this card to ' . $board_list_card->getBoardList->title,
                    'activity_type' => 2,
                ]) && $board_list_card->getLabels->count() > 0) {
                return response()->json(['success' => 'created successfully']);
            }
            return response()->json(['error' => 'Failed to create board card record.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update_profile_image(Request $request)
    {
        try {
            if ($request->hasFile('image')) {
                // Upload and process the new profile image
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('assets/images/profile_images'), $imageName);
                auth()->user()->update(['image' => $imageName]);
                return response()->json([
                    'success' => true,
                ]);
            }
            throw new \RuntimeException('Failed to create board card record.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function title_update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'board_list_card_id' => 'required',
                'title' => 'required',
            ], [
                'board_list_card_id.required' => 'The title field is required.',
                'title.required' => 'The title field is required.',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::find($request->get('board_list_card_id'));
            $board_list_card->title = $request->get('title');
            if ($board_list_card->save()) {
                return response()->json(['success' => 'Card title updated successfully', 'title' => $board_list_card->title]);
            }
            throw new \RuntimeException('Failed to update board card title.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function image_update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'cover_image.required' => 'The cover image is required.',
                'cover_image.image' => 'The file must be an image.',
                'cover_image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
                'cover_image.max' => 'The image may not be greater than 2048 kilobytes.',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::find($id);
            $cover_image = $request->file('cover_image');
            $board_list_card = $this->create_cover_image_trait($request, $board_list_card, $cover_image, $cover_image->getClientOriginalName());
            if ($board_list_card->save()) {
                return response()->json(['success' => 'Cover image updated successfully']);
            }
            return response()->json(['error' => 'Failed to update board card cover image.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function att_as_cover_img(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'board_list_card_id' => 'required',
                'attachment_id' => 'required',
            ], [
                'board_list_card_id.required' => 'The Board list card id is required.',
                'attachment_id.required' => 'The Attachment id is required.',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::find($request->board_list_card_id);
//            $clientPath = optional($board_list_card)->client_id ?? "random-client";
            $attachment = BoardListCardAttachment::find($request->attachment_id);
            $file_path = $this->file_path($attachment->file_name, "images");
            if ($board_list_card && $attachment->file_name && $file_path['exists']) {
                $board_list_card->cover_image = $attachment->file_name;
                if ($board_list_card->save()) {
                    return response()->json(['success' => 'Cover image updated successfully', 'background_image' => $file_path['path']
                    ]);
                }
            }
            return response()->json(['error' => 'Failed to update board card cover image.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update_cover_background_color(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cover_background_color' => 'required|regex:/^#[a-fA-F0-9]{6}$/',
            ], [
                'cover_background_color.required' => 'The cover image color is required.',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::find($id);
            $board_list_card->cover_background_color = $request->cover_background_color;
            if ($board_list_card->save()) {
                return response()->json(['success' => 'Cover image background color updated successfully', 'cover_background_color' => $board_list_card->cover_background_color, 'cover_image_size' => $board_list_card->cover_image_size]);
            }
            throw new \RuntimeException('Failed to board card cover image background color update.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update_image_size(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'board_list_card_id' => 'required',
                'cover_image_size' => 'required',
            ], [
                'board_list_card_id.required' => 'The board list card id is required.',
                'cover_image_size.required' => 'The image size field is required.',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::find($request->get('board_list_card_id'));
            $board_list_card->cover_image_size = $request->get('cover_image_size') == 1 ? 1 : 0;
            if ($board_list_card->save()) {
                return response()->json(['success' => 'Image size updated successfully', 'cover_image_size' => $board_list_card->cover_image_size, 'cover_background_color' => $board_list_card->cover_background_color]);
            }
            throw new \RuntimeException('Failed to board card image size update.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function add_comment(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'comment' => 'required',
            ], [
                'comment.required' => 'The comment field is required.',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::find($id);
            if (!$board_list_card) {
                throw new \RuntimeException('Board list card not found.');
            }
            $board_list_card_activity = new BoardListCardActivity();
            $board_list_card_activity->board_list_card_id = $board_list_card->id;
            $board_list_card_activity->user_id = auth()->user()->id;
            $board_list_card_activity->activity = auth()->user()->name . " commented " . now();
            $board_list_card_activity->activity_type = 0;
            /** 0 = comment , 1 = attachment , 2 = activity*/
            $board_list_card_activity->save();
            if ($board_list_card_activity->save()) {
                $board_list_card_comment = new BoardListCardComment();
                $board_list_card_comment->board_list_card_id = $board_list_card->id;
                $board_list_card_comment->user_id = auth()->user()->id;
                $board_list_card_comment->activity_id = $board_list_card_activity->id;
                $board_list_card_comment->comment = $request->comment;
                if ($board_list_card_comment->save()) {
                    $timestamp = Carbon::parse($board_list_card_comment->created_at);
                    $activity = [
                        'activity_id' => $board_list_card_activity->id,
                        'activity' => $board_list_card_activity->activity,
                        'activity_type' => $board_list_card_activity->activity_type,
                        'user_name' => optional($board_list_card_activity->getUser)->name ?? null,
                        'user_id' => $board_list_card_activity->user_id,
                        'comment_id' => $board_list_card_comment->id,
                        'comment' => html_entity_decode($board_list_card_comment->comment),
                        'comment_user_id' => $board_list_card_comment->user_id,
                        'comment_user_name' => optional($board_list_card_comment->getUser)->name ?? null,
                        'comment_created_at' => $timestamp->isToday() ? $timestamp->format('h:i A') : ($timestamp->isYesterday() ? 'yesterday at ' . $timestamp->format('h:i A') : $timestamp->format('F jS \a\t h:i A')),
                    ];
                    return response()->json(['success' => 'Comment added successfully', 'status' => 1, 'activity' => $activity, 'board_list_card_id' => $board_list_card->id, 'comment_count' => optional($board_list_card->getComments)->count() ?? 0, 'user_name' => auth()->user()->name]);
                }
            }
            return response()->json(['error' => 'Failed to add activity.', 'board_list_card_id' => $board_list_card->id, 'comment_count' => optional($board_list_card->getComments)->count() ?? 0], 500);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'error' => $e->getMessage(), 'board_list_card_id' => $board_list_card->id, 'comment_count' => optional($board_list_card->getComments)->count() ?? 0], 500);
        }
    }

    public function update_comment(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'comment_id' => 'required',
                'comment' => 'required',
            ], [
                'comment_id.required' => 'The comment id field is required.',
                'comment.required' => 'The comment field is required.',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card_comment = BoardListCardComment::where('user_id', auth()->user()->id)->where('id', $request->comment_id)->first();
            if ($board_list_card_comment) {
                $previous_log = new BoardListCardCommentPreviousLog();
                $previous_log->user_id = auth()->user()->id;
                $previous_log->previous_comment = $board_list_card_comment->comment;
                $previous_log->comment_id = $board_list_card_comment->id;
                if ($previous_log->save()) {
                    $board_list_card_comment->comment = $request->comment;
                    if ($board_list_card_comment->save()) {
                        $timestamp = Carbon::parse($board_list_card_comment->created_at);
//                        return response()->json(['comment' => html_entity_decode($request->comment), 'comment_created_at' => $timestamp->isToday() ? $timestamp->format('h:i A') : ($timestamp->isYesterday() ? 'yesterday at ' . $timestamp->format('h:i A') : $timestamp->format('F jS \a\t h:i A')), 'status' => 1]);
                        $activity = [
                            'activity_id' => optional($board_list_card_comment->getActivity)->id ?? null,
                            'activity' => optional($board_list_card_comment->getActivity)->activity ?? null,
                            'activity_type' => optional($board_list_card_comment->getActivity)->activity_type ?? null,
                            'user_name' => optional($board_list_card_comment->getActivity->getUser)->name ?? null,
                            'user_id' => optional($board_list_card_comment->getActivity)->user_id ?? null,
                            'comment_id' => $board_list_card_comment->id,
                            'comment' => html_entity_decode($board_list_card_comment->comment),
                            'comment_user_id' => $board_list_card_comment->user_id,
                            'comment_user_name' => optional($board_list_card_comment->getUser)->name ?? null,
                            'comment_created_at' => $timestamp->isToday() ? $timestamp->format('h:i A') : ($timestamp->isYesterday() ? 'yesterday at ' . $timestamp->format('h:i A') : $timestamp->format('F jS \a\t h:i A')),
                        ];
                        return response()->json(['success' => 'Comment updated successfully', 'status' => 1, 'activity' => $activity, 'user_name' => auth()->user()->name]);
                    }
                }
            }
            throw new \RuntimeException('Failed to update comment.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function delete_comment($id)
    {
        try {
            $comment = BoardListCardComment::where('id', $id)->where('user_id', auth()->user()->id)->first();
            if ($comment) {
                if (isset($comment->getActivity)) {
                    $board_list_card_id = $comment->getActivity->board_list_card_id;
                    if ($board_list_card_id) {
                        /** 0 = comment , 1 = attachment , 2 = activity*/
//                    $activity = new BoardListCardActivity();
//                    $activity->user_id = auth()->user()->id;
//                    $activity->board_list_card_id = $comment->getActivity->board_list_card_id;
//                    $activity->activity = auth()->user()->name . " deleted comment " . carbon::now()->format('F jS \a\t h:i A');
//                    $activity->activity_type = 0;
//                    if ($activity->save()) {
                        $activity = [
                            'user_id' => auth()->user()->id,
                            'board_list_card_id' => $comment->getActivity->board_list_card_id,
                            'activity' => auth()->user()->name . " deleted comment " . carbon::now()->format('F jS \a\t h:i A'),
                            'activity_type' => 0,
                            'activity_id' => $comment->getActivity->id,
                            'user_name' => auth()->user()->name,
                            'created_at' => Carbon::parse($comment->getActivity->created_at)->format('F jS \a\t h:i A'),
                        ];
                        $comment->getActivity->delete();
                        $comment->delete();
                        return response()->json([
                            'success' => 'Comment deleted successfully',
                            'status' => 1,
                            'comment_count' => optional($comment->getBoardListCard->getComments)->count() ?? 0,
                            'board_list_card_id' => $board_list_card_id,
                            'activity' => $activity,
                            'user_name' => auth()->user()->name
                        ]);
                    }
                }
            }
            return response()->json([
                'error' => 'Failed to delete comment.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    function convert_filesize($bytes, $decimals = 2)
    {
        $size = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }

    public function add_attachment(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'attachments.*' => 'required|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar,eml,rtf,woff2,mp4', // 10MB
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::find($id);
            if (!$board_list_card) {
                throw new \RuntimeException('Board list card not found.');
            }
            if (!$request->hasFile('attachments')) {
                throw new \RuntimeException('Attachments not found.');
            }
            foreach ($request->file('attachments') as $attachment) {
                if (!$attachment->isValid()) {
                    throw new \RuntimeException('Invalid attachment file.');
                }
            }
//            $clientPath = optional($board_list_card)->client_id ?? "random-client";
            $department_id = optional($board_list_card->getBoardList->getDepartment)->id ?? null;
            $board_list_card_id = optional($board_list_card)->id ?? null;
            $additional_path = "{$department_id}/{$board_list_card_id}/";
            foreach ($request->file('attachments') as $attachment) {
                $board_list_card_activity = new BoardListCardActivity();
                $board_list_card_activity->board_list_card_id = $id;
                $board_list_card_activity->user_id = auth()->user()->id;
                $board_list_card_activity->activity = " attached ";
                $board_list_card_activity->activity_type = 1;
                /** 0 = comment , 1 = attachment , 2 = activity*/
                if ($board_list_card_activity->save()) {
                    $activity_timestamp = Carbon::parse($board_list_card_activity->created_at);
                    $board_list_card_attachment = new BoardListCardAttachment();
                    $board_list_card_attachment->board_list_card_id = $id;
                    $board_list_card_attachment->user_id = auth()->user()->id;
                    $board_list_card_attachment->activity_id = $board_list_card_activity->id;
                    $board_list_card_attachment->original_name = $attachment->getClientOriginalName();
                    $board_list_card_attachment->mime_type = $attachment->getMimeType();
                    $board_list_card_attachment->file_size = $this->convert_filesize($attachment->getSize());
                    $board_list_card_attachment->extension = $attachment->getClientOriginalExtension();
                    $file_directory = str_contains($board_list_card_attachment->mime_type, 'image') ? 'images' : 'images';
                    $file_directory_path = public_path("assets/{$file_directory}/board-list-card/original/");
                    $file_name = time() . '-' . auth()->user()->id . rand(11, 20) . '.' . $board_list_card_attachment->extension;
                    $attachment->move($file_directory_path, $file_name);
                    $board_list_card_attachment->file_name = $file_name;
                    $board_list_card_attachment->file_path = $file_directory_path . $file_name;
                    if ($board_list_card_attachment->save()) {
                        $timestamp = Carbon::parse($board_list_card_attachment->created_at);
                        $activity = [
                            'activity_id' => $board_list_card_activity->id,
                            'created_at' => $activity_timestamp->isToday() ? $activity_timestamp->format('h:i A') : ($activity_timestamp->isYesterday() ? 'yesterday at ' . $activity_timestamp->format('h:i A') : $activity_timestamp->format('F jS \a\t h:i A')),
                            'activity' => $board_list_card_activity->activity,
                            'activity_type' => $board_list_card_activity->activity_type,
                            'user_id' => $board_list_card_activity->user_id,
                            'user_name' => optional($board_list_card_activity->getUser)->name ?? null,
                            'attachment_id' => $board_list_card_attachment->id,
                            'attachment_activity_id' => $board_list_card_attachment->activity_id,
                            'attachment_name' => $board_list_card_attachment->file_name,
                            'attachment_original_name' => $board_list_card_attachment->original_name,
                            'attachment_mime_type' => $board_list_card_attachment->mime_type,
                            'attachment_extension' => $board_list_card_attachment->extension,
                            'attachment_file_path' => asset("assets/{$file_directory}/board-list-card/original/{$additional_path}/{$file_name}"),
                            'attachment_user_id' => $board_list_card_attachment->user_id,
                            'attachment_user_name' => optional($board_list_card_attachment->getUser)->name ?? null,
                            'attachment_created_at' => $timestamp->isToday() ? $timestamp->format('h:i A') : ($timestamp->isYesterday() ? 'yesterday at ' . $timestamp->format('h:i A') : $timestamp->format('F jS \a\t h:i A')),
                        ];
                    } else {
                        throw new \RuntimeException('Failed to add attachment. Activity added');
                    }
                } else {
                    throw new \RuntimeException('Failed to add activity.');
                }
                $activities[] = $activity;
            }
            return response()->json(['success' => 'Attachment added successfully.', 'status' => 1, 'board_list_card_id' => $board_list_card->id, 'attachment_count' => optional($board_list_card->getAttachments)->count() ?? 0, 'activities' => $activities, 'activity' => $activity, 'user_name' => auth()->user()->name]);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
        }
    }

    public function delete_attachment($id)
    {
        try {
            if ($attachment = BoardListCardAttachment::where('id', $id)->where('user_id', auth()->user()->id)->first()) {
                /** File will not be deleted
                 *$file_directory = str_contains($attachment->mime_type, 'image') ? 'images' : 'files';
                 * if ($attachment->file_name) {
                 * $fileExists = public_path("assets/{$file_directory}/board-list-card/original/") . $attachment->file_name;
                 * if (File::exists($fileExists)) {
                 * File::delete($fileExists);
                 * }
                 * }
                 */
                $board_list_card = BoardListCard::find($attachment->board_list_card_id);
                if (isset($attachment->getActivity)) {
                    $board_list_card_id = $attachment->getActivity->board_list_card_id;
                    if ($board_list_card_id) {
                        /** 0 = comment , 1 = attachment , 2 = activity*/
                        $activityModel = new BoardListCardActivity();
                        $activityModel->user_id = auth()->user()->id;
                        $activityModel->board_list_card_id = $board_list_card_id;
                        $activityModel->activity = " deleted " . $attachment->original_name . " from this card";
                        $activityModel->activity_type = 2;
                        if ($activityModel->save()) {
                            $activity = [
                                'user_id' => $activityModel->user_id,
                                'board_list_card_id' => $activityModel->board_list_card_id,
                                'activity' => $activityModel->activity,
                                'activity_type' => $activityModel->activity_type,
                                'activity_id' => $activityModel->id,
                                'attachment_id' => $attachment->id,
                                'attachment_original_name' => $attachment->original_name,
                                'user_name' => auth()->user()->name,
                                'created_at' => Carbon::parse($activityModel->created_at)->format('F jS \a\t h:i A'),
                            ];
                            $attachment->getActivity->delete();
                            $attachment->delete();
                            return response()->json([
                                'success' => 'Attachment deleted successfully',
                                'status' => 1,
                                'board_list_card_id' => $board_list_card_id,
                                'attachment_count' => optional($board_list_card->getAttachments)->count() ?? 0,
                                'activity' => $activity,
                                'user_name' => auth()->user()->name
                            ]);
                        }
                    }
                }
            }
            return response()->json(['error' => 'Failed to delete attachment.', 'board_list_card_id' => $board_list_card_id, 'attachment_count' => optional($board_list_card->getAttachments)->count() ?? 0,], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function get_description_by_id(Request $request, $id)
    {
        try {
            if ($card = BoardListCard::where('id', $id)->first('description')) {
                return response()->json(['success' => 'Updated Successfully successfully', 'status' => 1, 'description' => $card->description]);
            }
            throw new \RuntimeException('Failed to load description.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function card_update_description(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'description' => 'required',
            ], [
                'description.required' => 'The description field is required.',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::where('id', $id)->first();
            $board_list_card->description = $request->description;
            if ($board_list_card->save()) {
                return response()->json(['success' => 'Description updated successfully', 'status' => 1, 'description' => html_entity_decode($request->description)]);
            }
            throw new \RuntimeException('Failed to update description.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function get_comment_by_id(Request $request, $id)
    {
        try {
            if ($comment = BoardListCardComment::where('id', $id)->where('user_id', auth()->user()->id)->first()) {
                return response()->json(['success' => 'Comment fetched successfully', 'status' => 1, 'comment' => $comment->comment]);
            }
            throw new \RuntimeException('Failed to load comment.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function get_card_all_comments(Request $request, $id)
    {
        try {
            if ($comments = BoardListCardComment::where('board_list_card_id', $id)->get()) {
                return response()->json($comments);
            }
            throw new \RuntimeException('Failed to load comments.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function assign_unassign_member(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'value' => 'required|integer',
            ], [
                'value.required' => 'The member id field is required.',
                'value.integer' => "The member id must be an integer. The provided value is {$request->value}.",
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $auth_user_id = auth()->user()->id;
            $board_list_card = BoardListCard::find($id);
            if ($board_list_card) {
                $assign_user_ids = AssignBoardCard::where('board_list_card_id', $board_list_card->id)->get()->pluck('user_id')->toArray();
                $user_id = (((substr($request->value, 0, -13) / 3) / 7) / 7) - 783;
                $member_found = User::where('id', $user_id)->where('type', '!=', 'client')->where('status', 1)->first();
                if (!$member_found) {
                    return response()->json(['error' => 'Failed to update member assignment. Member not found.'], 404);
                }
                if (in_array($user_id, $assign_user_ids)) {
                    $assign_user_ids = array_diff($assign_user_ids, [$user_id]);
                    $action_message = ($auth_user_id == $user_id) ? 'You have left' : 'Member has removed from';
                    $status_code = 200;
                } else {
                    $assign_user_ids = array_unique(array_merge($assign_user_ids, [(int)$user_id]));
                    $action_message = ($auth_user_id == $user_id) ? 'You have joined' : 'Member has added to the';
                    $status_code = 201;
                }
                $user_exists = User::whereIn('id', $assign_user_ids)->where('type', '!=', 'client')->where('status', 1)->get();
                $board_list_card->setUsers()->sync($user_exists);
                $all_users = User::where('type', '!=', 'client')->where('status', 1)->get()->map(function ($user) use ($assign_user_ids) {
                    $user_id_encoded = (((($user->id + 783) * 7) * 7) * 3) . $user->created_at->timestamp . random_int(111, 999);
                    return [
                        'id' => $user_id_encoded,
                        'name' => $user->name,
                        'assigned' => in_array($user->id, $assign_user_ids),
                    ];
                })->toArray();
                return response()->json([
                    'success' => "$action_message board card successfully.",
                    'status' => 1,
                    'board_list_card_id' => $id,
                    'user_names' => optional($board_list_card->getBoardListCardUsers)->pluck('name')->toArray() ?? [],
                    'auth_id' => (((($auth_user_id + 783) * 7) * 7) * 3) . auth()->user()->created_at->timestamp . random_int(111, 999),
                    'user_id' => $request->value,
                    'all_users' => $all_users,
                ], $status_code);
            }
            return response()->json(['error' => 'Failed to update member assignment. Please try again.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred while updating member assignment. Please try again later.'], 500);
        }
    }

    public function assign_own_member($id)
    {
        $auth_user_id = auth()->user()->id . rand(111, 999);
        $auth_id = auth()->user()->id;
        $auth_user = auth()->user();
        $user_id = substr($auth_user_id, 0, -3);
        $board_list_card = BoardListCard::find($id);
        if ($board_list_card) {
            $user = User::where([
                ['id', $auth_id],
                ['type', '!=', 'client'],
                ['status', 1]
            ])->first();
            $exist_assign_board_card = AssignBoardCard::where([
                'board_list_card_id' => $board_list_card->id,
                'user_id' => $auth_id,
            ])->first();
            if (!empty($exist_assign_board_card)) {
                return response()->json(['success' => 'Own Member already assign',
                    'status' => 1,
                    'auth_id' => $auth_id,
                    'user_id' => $exist_assign_board_card->user_id,
                    'user_name' => $exist_assign_board_card->name,
                ]);
            } else {
                $assign_board_card = new AssignBoardCard();
                $assign_board_card->board_list_card_id = $board_list_card->id;
                $assign_board_card->user_id = $user['id'];
                $assign_board_card->save();
                return response()->json(['success' => 'Own Member update successfully',
                    'status' => 1,
                    'auth_id' => $auth_id,
                    'user_id' => $user_id,
                    'user_name' => $auth_user->name,
                ]);
            }
        }
        throw new \RuntimeException('Failed to assign member.');
    }

    public function update_dates(Request $request)
    {
        try {
            $board_list_card = BoardListCard::find($request->input('board_list_card_id'));
            $validator = Validator::make($request->all(), [
                'is_check_start_date' => 'required|boolean',
                'is_check_due_date' => 'required|boolean',
                'start_date' => 'nullable|date_format:m/d/Y',
                'due_date' => 'nullable|date_format:m/d/Y',
                'due_time' => ['nullable', 'regex:/^(0?[1-9]|1[0-2]):[0-5][0-9] (AM|PM)$/'],
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()]);
            }
            $due_date = null;
            if ($request->filled('is_check_start_date')) {
                $board_list_card->is_check_start_date = $request->input('is_check_start_date');
            }
            if ($request->filled('is_check_due_date')) {
                $board_list_card->is_check_due_date = $request->input('is_check_due_date');
            }
            /** Validate and handle date conditions */
            if ($request->filled('start_date') && $request->filled('due_date') && $request->input('start_date') > $request->input('due_date')) {
                return response()->json(['error' => 'Start date cannot be greater than due date.']);
            }
            if ($request->filled('start_date') && !$request->filled('due_date') && $board_list_card->due_date && $request->input('start_date') > $board_list_card->due_date) {
                return response()->json(['error' => 'Start date cannot be greater than due date.']);
            }
            if (!$request->filled('start_date') && $request->filled('due_date') && $board_list_card->start_date && $request->input('due_date') < $board_list_card->start_date) {
                return response()->json(['error' => 'Due date cannot be less than start date.']);
            }
            if ($request->filled('due_date')) {
                $due_date = $request->input('due_date');
                if ($request->filled('due_time')) {
                    $due_date = $this->mergeDateAndTime($request->input('due_date'), $request->input('due_time'));
                }
            }
            if ($request->filled('is_check_start_date') && $request->input('start_date')) {
                $start_date = Carbon::createFromFormat('m/d/Y', $request->input('start_date'))->format('Y-m-d H:i:s');
                $board_list_card->start_date = $start_date;
            }
            if ($request->filled('is_check_due_date') && $due_date) {
                $board_list_card->due_date = $due_date;
            }
            /** Save changes to the model */
            if ($board_list_card->save()) {
                $data = [
                    'is_check_start_date' => $board_list_card->is_check_start_date,
                    'is_check_due_date' => $board_list_card->is_check_due_date,
                    'start_date' => $board_list_card->start_date ? Carbon::parse($board_list_card->start_date)->format('m/d/Y') : Carbon::parse($board_list_card->due_date)->subDay()->format('m/d/Y'),
                    'due_date' => $board_list_card->due_date ? Carbon::parse($board_list_card->due_date)->format('m/d/Y h:i A') : null,
                    'due_time' => $board_list_card->due_date ? Carbon::parse($board_list_card->due_date)->format('h:i A') : null,
                    'formatted_date' => $board_list_card->start_date && $board_list_card->is_check_start_date == 1 ? Carbon::parse($board_list_card->start_date)->format('M j') . ' - ' . Carbon::parse($board_list_card->due_date)->format('M j') : ($board_list_card->due_date ? Carbon::parse($board_list_card->due_date)->format('M j') : null),
                ];
                return response()->json(['success' => 'Dates updated successfully', 'status' => 1, 'data' => $data]);
            }
            throw new \RuntimeException('Failed to update date.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function remove_dates(Request $request)
    {
        try {
            $board_list_card = BoardListCard::find($request->input('board_list_card_id'));
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

    public function edit($id)
    {
        try {
            $auth_id = auth()->user()->id;
            $board_list_card = BoardListCard::with('getBoardListLabels', 'getLabels', 'getLabels.color')->where('id', $id)->first();
            if (!$board_list_card) {
                throw new \RuntimeException('Board list card not found.');
            }
//            $label_ids = optional($board_list_card->getLabels)->pluck('id')->toArray() ?? [];
//            $assign_labels_id = optional($board_list_card->getBoardListLabels)->pluck('id')->toArray() ?? [];
//            $assign_labels = optional($board_list_card->getBoardListLabels)->pluck('label')->toArray() ?? [];
            $assign_color_ids = optional($board_list_card->getBoardListLabels)->pluck('color_id')->toArray() ?? [];
            $board_list_name = optional($board_list_card->getBoardList)->title;
//            $clientPath = optional($board_list_card)->client_id ?? "random-client";
            $activities = $board_list_card->getActivities ? $board_list_card->getActivities->map(function ($activity) use ($board_list_card) {
                $result = [
                    'activity_id' => $activity->id,
                    'activity' => $activity->activity,
                    'activity_type' => $activity->activity_type,
                    'user_id' => $activity->user_id,
                    'user_name' => optional($activity->getUser)->name ?? null,
                    'created_at' => Carbon::parse($activity->created_at)->format('F jS \a\t h:i A'),
                ];
                if ($activity->activity_type == 0) {
                    $comment = $activity->getComment; // Exclude soft-deleted comments
                    if ($comment) {
                        $result['comment_id'] = $comment->id;
                        $result['comment'] = $comment->comment;
                        $result['comment_user_id'] = $comment->user_id;
                        $result['comment_user_name'] = optional($comment->getCommentUser)->name ?? null;
                        $result['comment_created_at'] = $comment->created_at ? (Carbon::parse($comment->created_at)->isToday() ? Carbon::parse($comment->created_at)->format('h:i A') : (Carbon::parse($comment->created_at)->isYesterday() ? 'yesterday at ' . Carbon::parse($comment->created_at)->format('h:i A') : Carbon::parse($comment->created_at)->format('F jS \a\t h:i A'))) : null;
                    }
                }
                if ($activity->activity_type == 1) {
                    $attachment = $activity->getAttachment; // Exclude soft-deleted attachments
                    if ($attachment) {
                        $attachment_timestamp = Carbon::parse($attachment->created_at);
                        $result['attachment_id'] = $attachment->id;
                        $result['attachment_name'] = $attachment->file_name;
                        $result['attachment_original_name'] = $attachment->original_name;
                        $result['attachment_file_name'] = $attachment->file_name;
                        $result['attachment_mime_type'] = $attachment->mime_type;
                        $result['attachment_file_path'] = $this->file_path($attachment->file_name, str_contains($attachment->mime_type, 'image') ? 'images' : 'files')['path'];
                        $result['attachment_user_id'] = $attachment->user_id;
                        $result['attachment_user_name'] = optional($attachment->getAttachmentUser)->name ?? null;
                        $result['attachment_created_at'] = $attachment_timestamp->isToday() ? $attachment_timestamp->format('h:i A') : ($attachment_timestamp->isYesterday() ? 'yesterday at ' . $attachment_timestamp->format('h:i A') : $attachment_timestamp->format('F jS \a\t h:i A'));
                    }
                }
                return $result;
            })->toArray() : [];
            $attachments = $board_list_card->getAttachments ? $board_list_card->getAttachments->map(function ($attachment) use ($board_list_card) {
                $attachment_timestamp = Carbon::parse($attachment->created_at);
                return [
                    'attachment_id' => $attachment->id,
                    'attachment_activity_id' => $attachment->activity_id,
                    'attachment_activity' => optional($attachment->getActivity)->activity ?? null,
                    'attachment_file_name' => $attachment->file_name,
                    'attachment_original_name' => $attachment->original_name,
                    'attachment_mime_type' => $attachment->mime_type,
                    'attachment_extension' => $attachment->extension,
                    'attachment_file_path' => $this->file_path($attachment->file_name, str_contains($attachment->mime_type, 'image') ? 'images' : 'files')['path'],
                    'attachment_user_id' => $attachment->user_id,
                    'attachment_user_name' => optional($attachment->getUser)->name ?? null,
                    'attachment_created_at' => $attachment_timestamp->isToday() ? $attachment_timestamp->format('h:i A') : ($attachment_timestamp->isYesterday() ? 'yesterday at ' . $attachment_timestamp->format('h:i A') : $attachment_timestamp->format('F jS \a\t h:i A')),
                ];
            })->toArray() : [];
            $assign_colors = Color::whereIn('id', $assign_color_ids)->get();
            $assign_label_colors = $assign_colors->pluck('color_value')->toArray();
            $assign_label_colors_position = $assign_colors->pluck('color_position')->toArray();
//            $labels = Label::whereIn('id', $label_ids)->where('board_list_card_id', $board_list_card->id)->get();
//            $label_data = [];
//
//            foreach ($labels as $label) {
//                $label_color = Color::find($label->color_id);
//
//                if ($label_color) {
//                    $color_position[$label_color->color_position] = $label_color->color_value;
//
//                    $label_data[] = [
//                        "board_list_card_id" => $label->board_list_card_id,
//                        "user_id" => $label->user_id,
//                        "label_text" => $label->label_text,
//                        'label_id' => $label->id,
//                        'color_id' => $label_color->id,
//                        'color_value' => $label_color->color_value,
//                        'color_position' => $label_color->color_position,
//                        'positions' => $color_position,
//                    ];
//                }
//            }
            $assign_board_label = AssignBoardLabel::with('label', 'label.color')->where('board_list_card_id', $board_list_card->id)->get()->toArray();
//            $assign_users = [];
//            if ($user_ids) {
//                foreach (User::whereIn('id', $user_ids)->get() as $key => $user) {
//                    $assign_users[$key] = [
//                        'id' => (((($user->id + 783) * 7) * 7) * 3) . $user->created_at->timestamp . random_int(111, 999),
//                        'name' => $user->name,
//                    ];
//                }
//            }
//            $all_users = [];
//            $users = User::where('type', '!=', 'client')->where('status', 1)->get();
//            if (count($users) !== 0){
//                foreach ($users as $key => $user) {
//                    $all_users[$key] = [
//                        'id' => (((($user->id + 783) * 7) * 7) * 3) . $user->created_at->timestamp . random_int(111, 999),
//                        'name' => $user->name,
//                    ];
//                }
//            }
//
//
            $assign_user_ids = optional($board_list_card->getBoardListCardUsers)->pluck('id')->toArray() ?? [];
            $all_users = User::where('type', '!=', 'client')->where('status', 1)->get()->map(function ($user) use ($assign_user_ids) {
                $user_id_encoded = (((($user->id + 783) * 7) * 7) * 3) . $user->created_at->timestamp . random_int(111, 999);
                return [
                    'id' => $user_id_encoded,
                    'name' => $user->name,
                    'assigned' => in_array($user->id, $assign_user_ids),
                ];
            })->toArray();
            return response()->json([
                'status' => 1,
                'board_list_card_id' => $board_list_card->id,
                'board_assigned_labels' => $board_list_card->getLabels->toArray(),
                'board_list_name' => $board_list_name,
                'background_image' => $board_list_card->cover_image,
                'background_image_path' => $this->file_path($board_list_card->cover_image, 'images')['path'],
                'cover_background_color' => $board_list_card->cover_background_color,
                'cover_image_size' => $board_list_card->cover_image_size,
                'project_id' => $board_list_card->project_id,
                'title' => $board_list_card->title,
                'client_id' => $board_list_card->client_id,
                'description' => $board_list_card->description,
                'auth_id' => (((($auth_id + 783) * 7) * 7) * 3) . auth()->user()->created_at->timestamp . random_int(111, 999),
                'assign_labels' => $assign_board_label,
                'label_data' => $board_list_card->getLabels,
                'all_users' => $all_users,
                'activities' => $activities,
                'attachments' => $attachments,
                'attachment_count' => optional($board_list_card->getAttachments)->count() ?? 0,
                'comment_count' => optional($board_list_card->getComments)->count() ?? 0,
                'is_check_start_date' => $board_list_card->is_check_start_date,
                'is_check_due_date' => $board_list_card->is_check_due_date,
                'start_date' => $board_list_card->start_date ? Carbon::parse($board_list_card->start_date)->format('m/d/Y') : Carbon::parse($board_list_card->due_date)->subDay()->format('m/d/Y'),
                'due_date' => $board_list_card->due_date ? Carbon::parse($board_list_card->due_date)->format('m/d/Y h:i A') : null,
                'due_time' => $board_list_card->due_date ? Carbon::parse($board_list_card->due_date)->format('h:i A') : null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        dd($request, $id, 'edit');
        try {
            $rules = [
//                'edit_project_id' => 'required',
//                'edit_title' => 'required',
//                'edit_description' => 'required',
//                'edit_due_date' => 'required',
            ];
            $messages = [
//                'edit_project_id.required' => 'The project field is required.',
//                'edit_title.required' => 'The title field is required.',
//                'edit_description.required' => 'The description field is required.',
//                'edit_due_date.required' => 'The due date field is required.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::find($id);
            if (!$board_list_card) {
                return response()->json(['error' => 'Board List not found'], 404);
            }
            if ($request->hasFile('cover_image')) {
                $image = $request->file('cover_image');
                $imageName = time() . '-' . auth()->user()->id . rand(11, 20) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('assets/images/board-list-card/original/'), $imageName);
                if ($board_list_card->cover_image) {
                    $existingImagePath = public_path('assets/images/board-list-card/original/') . $board_list_card->cover_image;
                    if (File::exists($existingImagePath)) {
                        File::delete($existingImagePath);
                    }
                }
                $board_list_card->cover_image = $imageName;
            }
            $board_list_card->project_id = $request->get('edit_project_id');
            $board_list_card->title = $request->get('edit_title');
            $board_list_card->description = $request->get('edit_description');
            $board_list_card->due_date = $request->get('edit_due_date');
            $board_list_card->save();
            if ($board_list_card->save()) {
                $assign_users = $request->get('edit_assign_to');
                if ($assign_users) {
                    $user_exists = User::whereIn('name', $assign_users)->where('type', '!=', 'client')->get();
                    $board_list_card->setUsers()->sync($user_exists);
                }
            }
            return response()->json([
                'status' => 1,
                'background_image' => $board_list_card->cover_image,
                'project_id' => $board_list_card->project_id,
                'title' => $board_list_card->title,
                'description' => $board_list_card->description,
                'user_names' => optional($board_list_card->getBoardListCardUsers)->pluck('name')->toArray() ?? [],
                'due_date' => $board_list_card->due_date,
                'message' => 'Board list card updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function label_create(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'label_text' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $board_list_card = BoardListCard::find($id);
        if (!$board_list_card) {
            return response()->json(['error' => 'Board list card not found'], 404);
        }
        $label_text = $request->input('label_text');
        $label_color_id = $request->get('label_color_id', 21);
        try {
            $color = Color::find($label_color_id);
            $create_board_label = null;
            $msg = 'created';
            if ($request->has('label_id')) {
                $create_board_label = Label::where('id', $request->get('label_id'))->where('board_list_card_id', $board_list_card->id)->first();
                $msg = 'updated';
            }
            if (!$create_board_label) {
                $create_board_label = new Label();
            }
            $create_board_label->board_list_card_id = $board_list_card->id;
            $create_board_label->color_id = $label_color_id;
            $create_board_label->label_text = $label_text;
            $create_board_label->save();
            $assign_label = AssignBoardLabel::where('board_list_card_id', $board_list_card->id)->where('label_id', $create_board_label->id)->first();
            $data = [
                'success' => "Label {$msg} successfully.",
                'status' => 1,
                'board_list_card_id' => $board_list_card->id,
                'label_id' => $create_board_label->id,
                'label_text' => $label_text,
                'checked' => $assign_label ? 'checked' : '',
                'record' => $msg,
            ];
            if ($color) {
                $data['color_id'] = $color->id;
                $data['color_value'] = $color->color_value;
                $data['color_position'] = $color->color_position;
                $data['positions'] = [(int)$color->color_position => $color->color_value];
            }
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create label in board card record' . $e->getMessage()], 422);
        }
    }

    /** Assign / Unassign Label **/
    public function label_assign_unassign(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'label_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $label_id = $request->get('label_id');
        $is_checked = $request->get('is_checked');
        $board_list_card = BoardListCard::find($id);
        if ($board_list_card) {
            $assign = 0;
            $unchange_check = AssignBoardLabel::where('board_list_card_id', $board_list_card->id)->where('label_id', $label_id)->first();
            if ($is_checked == 'false' && !$unchange_check) {
                $assign = 0;
            } elseif ($is_checked == 'true' && $unchange_check) {
                $assign = 1;
            }
            if (($is_checked == 'false' && !$unchange_check) || ($is_checked == 'true' && $unchange_check)) {
                return response()->json([
                    'success' => 'Label already changed.',
                    'status' => 1,
                    'assign' => $assign,
                    'action' => 'unchanged',
                    'label_id' => $label_id,
                    'board_list_card_id' => $board_list_card->id,
                ]);
            }
            $exist_assign_board_label_ids = AssignBoardLabel::where('board_list_card_id', $board_list_card->id)->get()->pluck('label_id')->toArray();
            if (in_array((int)$label_id, $exist_assign_board_label_ids, true)) {
                $exist_assign_board_label_ids = array_diff($exist_assign_board_label_ids, [$label_id]);
            } else {
                $exist_assign_board_label_ids = array_unique(array_merge($exist_assign_board_label_ids, [$label_id]));
            }
            $exist_assign_board_labels = Label::whereIn('id', $exist_assign_board_label_ids)->get();
            $board_list_card->assignLabels()->sync($exist_assign_board_labels);
            $fetch_assign_label = AssignBoardLabel::where('board_list_card_id', $board_list_card->id)->where('label_id', $label_id)->first();
            if ($fetch_assign_label) {
                $assign = 1;
            }
            $labeling = AssignBoardLabel::with('label.color')->where('board_list_card_id', $board_list_card->id)->where('label_id', $label_id)->first();
            return response()->json([$assign == 1 ? 'success' : 'warning' => $assign == 1 ? 'Assign label successfully!' : "Unassign label successfully!",
                'status' => 1,
                'assign' => $assign,
                'board_list_card_id' => $board_list_card->id,
                'label_data' => $labeling ?? null
            ]);
        }
        return response()->json(['error' => 'Failed to update label in board card record.'], 422);
    }

    public function label_remove(Request $request, $id)
    {
        $user = auth()->user();
        $label_id = $request->get('label_id');
        $assign_board_list_card = AssignBoardLabel::where([
            'id' => $label_id,
            'board_list_card_id' => $id,
        ])->first();
        if ($assign_board_list_card) {
            $assign_board_list_card->delete();
            return response()->json(['success' => 'Label removed successfully']);
        } else {
            return response()->json(['success' => 'Label not found or unauthorized'], 404);
        }
    }
}
