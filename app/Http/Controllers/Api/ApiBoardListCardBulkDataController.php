<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BoardListCardActivityResource;
use App\Http\Resources\BoardListCardAttachmentResource;
use App\Http\Resources\BoardListCardCommentResource;
use App\Http\Resources\BoardListCardResource;
use App\Http\Resources\UserResource;
use App\Models\AssignBoardCard;
use App\Models\BoardList;
use App\Models\BoardListCard;
use App\Models\BoardListCardActivity;
use App\Models\BoardListCardAttachment;
use App\Models\User;
use App\Notifications\BoardListNotification;
use App\Traits\BoardListCardCoverImageTrait;
use Carbon\Carbon;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;

class ApiBoardListCardBulkDataController extends Controller
{
    use BoardListCardCoverImageTrait;

    public function convert_filesize($bytes, $decimals = 2): string
    {
        $size = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / (1024 ** $factor)) . @$size[$factor];
    }

//    public function make_user($value, $type = 1)
//    {
//        if (!preg_match('/^(.+?)\s*\((\w+)\)$/', $value, $matches)) {
//            die('Invalid ' . ($type == 1 ? 'logged in user' : ($type == 2 ? 'activity creator' : ($type == 3 ? 'activity member' : ($type == 4 ? 'activity member' : 'assigned member')))) . " format. The provided format is $value");
//        }
//        [$name, $slug] = array_slice($matches, 1);
//
//        return User::firstOrCreate(['email' => "$slug@gmail.com"], ['name' => ucwords($name), 'password' => Hash::make(random_int(111111111, 999999999)), 'type' => 'trello-user']);
//    }
    public function make_user($value, $type = 1, $trello_id = null, $full_name = null)
    {
        // Ensure $value is a string
        $value = $value ?? '';
        if ($trello_id) {
            $user = User::where('trello_id', $trello_id)->first();
            if ($user) {
                return $user;
            }
        } elseif (!$value) {
            throw new Exception('User details not received');
        }
        // Initialize $part1 and $part2 with default empty strings
        $part1 = $part2 = '';
        if (preg_match('/^(.+)\s+\((.+)\)$/', $value, $matches)) {
            $part1 = trim($matches[1]); // Extracting part before the parentheses
            $part2 = trim($matches[2]); // Extracting part inside the parentheses
        } else {
            $part1 = $value;
            $part2 = $value;
        }
        // Define default values
        $part1name = $part1;
        $part2name = $part2;
        $part1email = '';
        $part2email = '';
        // Check if part1 is an email
        if (filter_var($part1, FILTER_VALIDATE_EMAIL)) {
            $part1name = explode('@', $part1)[0];
            $part1email = strtolower($part1);
        } else {
            // Construct default email for part1
            $part1email = strtolower(str_replace(' ', '', "$part1@trello.com"));
        }
        // Check if part2 is an email
        if (filter_var($part2, FILTER_VALIDATE_EMAIL)) {
            $part2name = explode('@', $part2)[0];
            $part2email = strtolower($part2);
        } else {
            // Construct default email for part2
            $part2email = strtolower(str_replace(' ', '', "$part2@trello.com"));
        }
        // Array of emails to check for existing users
        $emails = [
            $part1email,
            $part2email,
            strtolower(explode('@', $part1email)[0] . "@techigator.com"),
            strtolower(explode('@', $part2email)[0] . "@techigator.com"),
            strtolower(str_replace(' ', '.', $part1 ?? '') . (filter_var($part1, FILTER_VALIDATE_EMAIL) ? "" : "@gmail.com")),
            strtolower(str_replace(' ', '.', $part1 ?? '') . (filter_var($part1, FILTER_VALIDATE_EMAIL) ? "" : "@techigator.com")),
            strtolower(str_replace(' ', '.', $part2 ?? '') . (filter_var($part2, FILTER_VALIDATE_EMAIL) ? "" : "@gmail.com")),
            strtolower(str_replace(' ', '.', $part2 ?? '') . (filter_var($part2, FILTER_VALIDATE_EMAIL) ? "" : "@techigator.com")),
            strtolower(str_replace([' ', '.'], '', $part1 ?? '') . (filter_var($part1, FILTER_VALIDATE_EMAIL) ? "" : "@gmail.com")),
            strtolower(str_replace([' ', '.'], '', $part1 ?? '') . (filter_var($part1, FILTER_VALIDATE_EMAIL) ? "" : "@techigator.com")),
            strtolower(str_replace([' ', '.'], '', $part2 ?? '') . (filter_var($part2, FILTER_VALIDATE_EMAIL) ? "" : "@gmail.com")),
            strtolower(str_replace([' ', '.'], '', $part2 ?? '') . (filter_var($part2, FILTER_VALIDATE_EMAIL) ? "" : "@techigator.com")),
        ];
        $user = User::whereIn('email', $emails)->first();
        if (!$user) {
            $user = User::create([
                'name' => $full_name ?? ucwords(str_replace(['.', '_'], ' ', $part1name)),
                'email' => $part1email,
                'password' => Hash::make(random_int(111111111, 999999999)),
                'type' => 'trello-user',
            ]);
        }
        if ($trello_id) {
            $user->update([
                'trello_id' => $trello_id,
            ]);
        }
        return $user;
    }

    private function mergeDateAndTime($date, $time)
    {
        if ($date && $time) {
            return Carbon::parse($date . ' ' . $time)->toDateTimeString();
        }
        return null;
    }

    public function store(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $rules = [
                'board_title' => 'required',
                'card_title' => 'required',
                'team_key' => 'required|integer|exists:teams,team_key',
                'card_url_id' => 'required|integer|exists:urls,id',
                'logged_in_user' => 'required',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:15360',
                'cover_background_color' => ['nullable', 'regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/i'],
                'description' => 'nullable',
                'activities' => 'nullable|array',
                'activities.*.activity_type' => 'required|integer|in:0,1,2',
                'activities.*.creator' => 'sometimes|required|string',
                'activities.*.creator_full_name' => 'nullable|string',
                'activities.*.member' => 'required_if:activities.*.activity_type,0&activities.*.member,exists|nullable|string',
                'activities.*.member_full_name' => 'nullable|string',
                'activities.*.activity_time' => 'required|date_format:Y-m-d H:i:s',
                'activities.*.activity' => 'required_if:activities.*.activity_type,1,2&activities.*.activity,exists|nullable|string',
                'activities.*.comment' => 'required_if:activities.*.activity_type,0&activities.*.comment,exists|nullable|string',
                'assigned_members' => 'sometimes|array',
                'assigned_members.*' => 'required|string|distinct',
                'start_date' => 'nullable|date_format:n/d/Y',
                'due_date' => 'nullable|date_format:n/d/Y',
                'due_time' => ['nullable', 'regex:/^(0?[1-9]|1[0-2]):[0-5][0-9] (AM|PM)$/'],
            ];
            $messages = [
                'board_title.required' => 'The Board title field is required.',
                'card_title.required' => 'The Card title field is required.',
                'card_url_id.required' => 'The Card url id field is required.',
                'card_url_id.integer' => 'The Card url id must be type of integer.',
                'card_url_id.exists' => 'The Card url is invalid.',
                'team_key.required' => 'The Team key field is required.',
                'team_key.integer' => 'The Team Key must be type of integer.',
                "team_key.exists" => "The selected Team is invalid.",
                'cover_image.image' => 'The file must be an image.',
                'cover_image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
                'cover_image.max' => 'The image may not be greater than 15360 kilobytes.',
                'cover_background_color.regex' => 'The cover background color must be a valid hex color code.',
                'description.required' => 'The description field is required.',
                'activities.array' => 'The activities must be an array.',
                'activities.*.activity_type.required' => 'The activity type is required.',
                'activities.*.activity_type.integer' => 'The activity type must be an integer.',
                'activities.*.activity_type.in' => 'The activity type must be 0, 1, or 2.',
                'activities.*.creator.required' => 'The creator field is required for comments.',
                'activities.*.creator.string' => 'The creator must be a string.',
                'activities.*.creator_full_name.string' => 'The creator full name must be a string.',
                'activities.*.member.required_if' => 'The member field is required for comments.',
                'activities.*.member.string' => 'The member must be a string.',
                'activities.*.member_full_name.string' => 'The member full name must be a string.',
                'activities.*.activity_time.required' => 'The date and time field is required for activities.',
                'activities.*.activity_time.date_format' => 'The date and time must be in the format Y-m-d H:i:s.',
                'activities.*.activity.required_if' => 'The activity description field is required.',
                'activities.*.activity.string' => 'The activity description must be a string.',
                'activities.*.comment.required_if' => 'The activity comment field is required.',
                'assigned_members.required' => 'Each assigned member is required.',
                'assigned_members.*.string' => 'Each assigned member must be a string.',
                'assigned_members.*.distinct' => 'Assigned members must be unique.',
                'start_date.date_format' => 'The start date must be in the format 1/31/2024.',
                'due_date.date_format' => 'The due date must be in the format 1/31/2024.',
                'due_time.regex' => 'The due time must be in the format hh:mm AM/PM.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $index = null;
            set_error_handler(function ($errno, $errstr, $errfile, $errline) {
                throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
            });
            $this->make_user($request->get('logged_in_user'), 1, $request->get('logged_in_user_id'));
            /** Board List */
            $board_list = BoardList::where('title', $request->get('board_title'))->first();
            if (!$board_list) {
                return response()->json(['error' => 'Oops! Board not found.'], 404);
            }
            /** Board List Card */
            $board_list_card = new BoardListCard();
            $board_list_card->board_list_id = $board_list->id;
            $board_list_card->team_key = $request->get('team_key', 844163);
            $board_list_card->title = $request->get('card_title');
            $board_list_card->description = $request->get('description');
            $board_list_card->client_id = null;
            $due_date = null;
            if ($request->filled('start_date') && $request->filled('due_date') && Carbon::createFromFormat('m/d/Y', $request->input('start_date'))->greaterThan(Carbon::createFromFormat('m/d/Y', $request->input('due_date')))) {
                return response()->json(['error' => 'Start date cannot be greater than due date.']);
            }
            if ($request->filled('due_date')) {
                $due_date = $this->mergeDateAndTime($request->input('due_date'), $request->input('due_time'));
            }
            if ($request->filled('start_date') && $due_date && Carbon::parse($due_date)->lessThan(Carbon::createFromFormat('m/d/Y', $request->input('start_date')))) {
                return response()->json(['error' => 'Due date cannot be less than start date.']);
            }
            if ($request->input('start_date')) {
                $start_date = Carbon::createFromFormat('m/d/Y', $request->input('start_date'))->format('Y-m-d H:i:s');
                $board_list_card->is_check_start_date = 1;
                $board_list_card->start_date = $start_date;
            }
            if ($due_date) {
                $board_list_card->is_check_due_date = 1;
                $board_list_card->due_date = Carbon::parse($due_date)->format('Y-m-d H:i:s');
            }
            $cover_image = $request->file('cover_image');
            $board_list_card = $this->create_cover_image_trait($request, $board_list_card, $cover_image, $cover_image->getClientOriginalName());
            $board_list_card->save();
            $labelData = [
                ['color_id' => '6', 'label_text' => 'Need More Amazing Mock Design'],
                ['color_id' => '7', 'label_text' => 'Urgent'],
                ['color_id' => '9', 'label_text' => 'Most High Priority'],
                ['color_id' => '22', 'label_text' => 'High Priority Client'],
                ['color_id' => '28', 'label_text' => 'Need Amazing Mockup'],
            ];
            $board_list_card->setLabels()->createMany($labelData);
            /** Assigned Members */
            $assigned_members = $request->has('assigned_members') ? array_unique(array_map(fn($member) => $this->make_user($member, 4)->id, $request->get('assigned_members'))) : [];
            $user_ids = AssignBoardCard::where('board_list_card_id', $board_list_card->id)->pluck('user_id')->toArray();
            $updated_user_ids = array_unique(array_merge(array_diff($user_ids, $assigned_members), $assigned_members));
            $board_list_card->setUsers()->sync($updated_user_ids);
            $assigned_users = User::whereIn('id', $updated_user_ids)->get()->pluck('name');
            /** Activities */
            if ($request->has('activities')) {
                foreach ($request->get('activities') as $key => $activity) {
                    $index = $key;
                    /** 0 = comment , 1 = attachment , 2 = activity*/
                    $activity_creator = $creator_name = $activity_member = $member_name = null;
                    if (isset($activity['creator'])) {
                        $activity_creator = $this->make_user($activity['creator'], 2, $activity['creator_id'] ?? null, $activity['creator_full_name'] ?? null);
                        $creator_name = optional($activity_creator)->name;
                    }
                    if (isset($activity['member'])) {
                        $activity_member = $this->make_user($activity['member'], 3, $activity['member_id'] ?? null, $activity['member_full_name'] ?? null);
                        $member_name = optional($activity_member)->name;
                    }
                    if (isset($activity['activity_type'])) {
                        $commonAttributes = ['board_list_card_id' => $board_list_card->id, 'user_id' => optional($activity_creator)->id];
                        if (isset($activity['activity_time'])) {
                            $activity_time = Carbon::parse($activity['activity_time'], 'Asia/Karachi')->setTimezone('Pacific/Honolulu');
                            $commonAttributes['created_at'] = $activity_time;
                            $commonAttributes['updated_at'] = $activity_time;
                        }
                        if ($activity['activity_type'] == 0) {
                            $board_list_card_activity = $board_list_card->getActivities()->create(array_merge($commonAttributes, [
                                'activity' => "commented",
                                'activity_type' => 0,
                            ]));
                            $board_list_card->getComments()->create(array_merge($commonAttributes, [
                                'activity_id' => $board_list_card_activity->id,
                                'comment' => $activity['comment'],
                            ]));
                        } elseif ($activity['activity_type'] == 1) {
                            if ($request->hasFile('activities.' . $key . '.attachment')) {
                                $attachment = $request->file('activities.' . $key . '.attachment');
                                $validator = Validator::make(['attachment' => $attachment], [
                                    'attachment' => 'required|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar,eml,rtf,woff2,mp4',
                                ]);
                                if ($validator->fails()) {
                                    Log::error('Attachment validation failed: ' . $validator->errors()->first());
                                    continue;
                                }
                                $board_list_card_activity = $board_list_card->getActivities()->create(array_merge($commonAttributes, [
                                    'activity' => " attached ",
                                    'activity_type' => 1,
                                ]));
                                $this->handleAttachment($board_list_card, $board_list_card_activity, $attachment);
                            }
                        } elseif ($activity['activity_type'] == 2) {
                            $board_list_card->getActivities()->create(array_merge($commonAttributes, [
                                'activity' => $activity['activity'],
                                'activity_type' => 2,
                            ]));
                        }
                    }
                }
            }
            $board_list_card->loadMissing('getActivities.getUser');
            $board_list_card_resource = new BoardListCardResource($board_list_card);
            $board_list_card_activity_resource = BoardListCardActivityResource::collection($board_list_card->getActivities);
            $board_list_card_attachment_resource = BoardListCardAttachmentResource::collection($board_list_card->getAttachments);
            $board_list_card_comment_resource = BoardListCardCommentResource::collection($board_list_card->getComments);
            $updated = DB::table('urls')
                ->where('status', 0)
                ->where('id', $request->get('card_url_id'))
                ->update(['status' => 1]);
            $urlData = $updated ? DB::table('urls')
                ->where('id', $request->get('card_url_id'))
                ->first(['id', 'url', 'status']) : null;
            return response()->json([
                'success' => 'Board card created successfully .',
                'board_list_card' => $board_list_card_resource,
                'activities' => $board_list_card_activity_resource,
                'attachments' => $board_list_card_attachment_resource,
                'comments' => $board_list_card_comment_resource,
                'assigned_users' => $assigned_users,
                'url' => $urlData,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine(), 'activity_key' => $index], 500);
        }
    }

    private function handleAttachment($board_list_card, $activity, $attachment)
    {
        try {
            $department_id = optional($board_list_card->getBoardList->getDepartment)->id ?? null;
            $board_list_card_id = optional($board_list_card)->id ?? null;
            $additional_path = "{$department_id}/{$board_list_card_id}/";
            $board_list_card_attachment = new BoardListCardAttachment();
            $board_list_card_attachment->board_list_card_id = $board_list_card->id;
            $board_list_card_attachment->user_id = $activity->user_id;
            $board_list_card_attachment->activity_id = $activity->id;
            $board_list_card_attachment->original_name = $attachment->getClientOriginalName();
            $board_list_card_attachment->mime_type = $attachment->getMimeType();
            $board_list_card_attachment->file_size = $this->convert_filesize($attachment->getSize());
            $board_list_card_attachment->extension = $attachment->getClientOriginalExtension();
            $file_directory = str_contains($board_list_card_attachment->mime_type, 'image') ? 'images' : 'images';
            $file_directory_path = public_path("assets/{$file_directory}/board-list-card/original/");
            $file_name = mt_rand() . mt_rand() . mt_rand() . time() . '00' . $activity->user_id . rand(111, 999) . '_' . $attachment->getClientOriginalName();
            $this->create_cover_image_trait(null, $board_list_card, $attachment, $attachment->getClientOriginalName(), $additional_path, $file_name, $file_directory_path);
//            $attachment->move($file_directory_path . $additional_path, $file_name);
            $board_list_card_attachment->file_name = $additional_path . $file_name;
            $board_list_card_attachment->file_path = $file_directory_path . $additional_path . $file_name;
            $board_list_card_attachment->created_at = $activity->created_at;
            $board_list_card_attachment->updated_at = $activity->created_at;
            $board_list_card_attachment->save();
            return $board_list_card_attachment;
        } catch (\Exception $e) {
            Log::error('Error handling attachment: ' . $e->getMessage());
            return null; // or handle the error in your application
        }
    }

    public function save_php(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $colors = ['green_light' => 1, 'yellow_light' => 2, 'orange_light' => 3, 'red_light' => 4, 'purple_light' => 5, 'green' => 6, 'yellow' => 7, 'orange' => 8, 'red' => 9, 'purple' => 10, 'green_dark' => 11, 'yellow_dark' => 12, 'orange_dark' => 13, 'red_dark' => 14, 'purple_dark' => 15, 'blue_light' => 16, 'sky_light' => 17, 'lime_light' => 18, 'pink_light' => 19, 'black_light' => 20, 'null' => 21, 'blue' => 22, 'sky' => 23, 'lime' => 24, 'pink' => 25, 'black' => 26, 'blue_dark' => 27, 'sky_dark' => 28, 'lime_dark' => 29, 'pink_dark' => 30, 'black_dark' => 31];
            $rules = [
                'board_list_id' => 'required|integer|exists:board_lists,id',
                'trello_card_id' => 'required',
                'card_title' => 'required',
                'team_key' => 'required|integer|exists:teams,team_key',
                'card_url_id' => 'required|integer|exists:urls,id',
//                'logged_in_user' => 'required',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:15360',
                'cover_background_color' => ['nullable', 'regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/i'],
                'description' => 'nullable',
                'labels' => 'nullable|array',
                'labels.*.color' => 'required|string|in:' . implode(',', array_keys($colors)), // Add validation for colors
                'activities' => 'nullable|array',
                'activities.*.activity_type' => 'required|integer|in:0,1,2',
                'activities.*.creator' => 'sometimes|required|string',
                'activities.*.creator_full_name' => 'nullable|string',
                'activities.*.member' => 'required_if:activities.*.activity_type,0&activities.*.member,exists|nullable|string',
                'activities.*.member_full_name' => 'nullable|string',
                'activities.*.activity_time' => 'required|date_format:Y-m-d H:i:s',
                'activities.*.activity' => 'required_if:activities.*.activity_type,1,2&activities.*.activity,exists|nullable|string',
                'activities.*.comment' => 'required_if:activities.*.activity_type,0&activities.*.comment,exists|nullable|string',
                'assigned_members' => 'sometimes|array',
                'assigned_members.*' => 'required|string|distinct',
                // 'start_date' => 'nullable|date_format:m/d/Y',
                // 'due_date' => 'nullable|date_format:m/d/Y',
                'start_date' => ['nullable', 'regex:/^(0?[1-9]|1[0-2])\/(0?[1-9]|[12][0-9]|3[01])\/[0-9]{4}$/'],
                'due_date' => ['nullable', 'regex:/^(0?[1-9]|1[0-2])\/(0?[1-9]|[12][0-9]|3[01])\/[0-9]{4}$/'],
                'due_time' => ['nullable', 'regex:/^(0?[1-9]|1[0-2]):[0-5][0-9] (AM|PM)$/'],
                'task_completed' => "nullable|boolean",
            ];
            $messages = [
                'board_list_id.required' => 'The Board list id field is required.',
                'board_list_id.integer' => 'The Board list id must be type of integer.',
                'board_list_id.exists' => 'The Board list is invalid.',
                'trello_card_id.required' => 'The Trello Card id field is required.',
                'card_title.required' => 'The Card title field is required.',
                'card_url_id.required' => 'The Card url id field is required.',
                'card_url_id.integer' => 'The Card url id must be type of integer.',
                'card_url_id.exists' => 'The Card url is invalid.',
                'team_key.required' => 'The Team key field is required.',
                'team_key.integer' => 'The Team Key must be type of integer.',
                "team_key.exists" => "The selected Team is invalid.",
                'cover_image.image' => 'The file must be an image.',
                'cover_image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
                'cover_image.max' => 'The image may not be greater than 15360 kilobytes.',
                'cover_background_color.regex' => 'The cover background color must be a valid hex color code.',
                'description.required' => 'The description field is required.',
                'labels.array' => 'The activities must be an array.',
                'labels.*.color.required' => 'The color field is required for each label.',
                'labels.*.color.in' => 'The color must be one of the predefined values.',
                'activities.array' => 'The activities must be an array.',
                'activities.*.activity_type.required' => 'The activity type is required.',
                'activities.*.activity_type.integer' => 'The activity type must be an integer.',
                'activities.*.activity_type.in' => 'The activity type must be 0, 1, or 2.',
                'activities.*.creator.required' => 'The creator field is required for comments.',
                'activities.*.creator.string' => 'The creator must be a string.',
                'activities.*.creator_full_name.string' => 'The creator full name must be a string.',
                'activities.*.member.required_if' => 'The member field is required for comments.',
                'activities.*.member_full_name.string' => 'The member full name must be a string.',
                'activities.*.member.string' => 'The member must be a string.',
                'activities.*.activity_time.required' => 'The date and time field is required for activities.',
                'activities.*.activity_time.date_format' => 'The date and time must be in the format Y-m-d H:i:s.',
                'activities.*.activity.required_if' => 'The activity description field is required.',
                'activities.*.activity.string' => 'The activity description must be a string.',
                'activities.*.comment.required_if' => 'The activity comment field is required.',
                'assigned_members.required' => 'Each assigned member is required.',
                'assigned_members.*.string' => 'Each assigned member must be a string.',
                'assigned_members.*.distinct' => 'Assigned members must be unique.',
                'start_date' => 'The start date must be in the format MM/DD/YYYY, e.g., 01/31/2024.',
                'due_date' => 'The due date must be in the format MM/DD/YYYY, e.g., 01/31/2024.',
                'due_time.regex' => 'The due time must be in the format hh:mm AM/PM.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $index = null;
            set_error_handler(function ($errno, $errstr, $errfile, $errline) {
                throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
            });
//            $this->make_user($request->get('logged_in_user'), 1, $request->get('logged_in_user_id'));
            if (!BoardList::where('id', $request->get('board_list_id'))->first()) {
                return response()->json(['error' => 'Oops! Board not found.'], 404);
            }
            /** Board List Card */
//            $board_list_card = BoardListCard::where('trello_id', $request->get('trello_card_id'))->first();
//            if (!$board_list_card) {
            $board_list_card = new BoardListCard();
            $board_list_card->trello_id = $request->get('trello_card_id');
//            }
            $board_list_card->board_list_id = $request->get('board_list_id');
            $board_list_card->team_key = $request->get('team_key', 844163);
            $board_list_card->title = $request->get('card_title');
            $board_list_card->description = $request->get('description');
            $board_list_card->trello_url = $request->get('trello_url');
            $columns = ['is_activity', 'is_attachments'];
            $tableName = $board_list_card->getTable();
            foreach ($columns as $column) {
                if (Schema::hasColumn($tableName, $column)) {
                    $board_list_card->{$column} = $column === 'is_activity' ? 0 : ($column === 'is_attachments' ? 0 : null);
                }
            }
            // $board_list_card->description = $request->get('description');
            $board_list_card->cover_background_color = $request->get('cover_background_color');
            $board_list_card->client_id = null;
            $due_date = null;
            /** Temporary Off*/
//            if ($request->filled('start_date') && $request->filled('due_date') && Carbon::createFromFormat('m/d/Y', $request->input('start_date'))->greaterThan(Carbon::createFromFormat('m/d/Y', $request->input('due_date')))) {
//                return response()->json(['error' => 'Start date cannot be greater than due date.']);
//            }
            if ($request->filled('due_date')) {
                $due_date = $this->mergeDateAndTime($request->input('due_date'), $request->input('due_time'));
            }
//            if ($request->filled('start_date') && $due_date && Carbon::parse($due_date)->lessThan(Carbon::createFromFormat('m/d/Y', $request->input('start_date')))) {
//                return response()->json(['error' => 'Due date cannot be less than start date.']);
//            }
            if ($request->input('start_date')) {
                $start_date = Carbon::createFromFormat('m/d/Y', $request->input('start_date'))->format('Y-m-d H:i:s');
                $board_list_card->is_check_start_date = 1;
                $board_list_card->start_date = $start_date;
            }
            if ($due_date) {
                $board_list_card->is_check_due_date = 1;
                $board_list_card->due_date = Carbon::parse($due_date)->format('Y-m-d H:i:s');
            }
            if ($request->has('task_completed')) {
                $board_list_card->task_completed = $request->get('task_completed', 0);
            }
            $board_list_card->save();
            if ($request->hasFile('cover_image')) {
                $cover_image = $request->file('cover_image');
                $board_list_card = $this->create_cover_image_trait($request, $board_list_card, $cover_image, $cover_image->getClientOriginalName());
                $board_list_card->save();
            }
            $assigned_labels = $labelData = $colorIds = [];
            foreach ($request->input('labels', []) as $label) {
                $labelColor = $label['color'];
                $labelId = $colors[$labelColor] ?? null;
                if ($labelId) {
                    $assigned_labels[] = ['color_id' => $labelId, 'label_text' => $label['name'] ?? null];
                    $colorIds[$labelId][] = $label['name'];
                }
            }
            $requiredLabels = [
                '6' => 'Need More Amazing Mock Design',
                '7' => 'Urgent',
                '9' => 'Most High Priority',
                '22' => 'High Priority Client',
                '28' => 'Need Amazing Mockup'
            ];
            foreach ($requiredLabels as $colorId => $labelText) {
                if (!isset($colorIds[$colorId])) {
                    $labelData[] = ['color_id' => $colorId, 'label_text' => $labelText];
                } else {
                    $exists = false;
                    foreach ($colorIds[$colorId] as $text) {
                        if ($text == $labelText) {
                            $exists = true;
                            break;
                        }
                    }
                    if (!$exists) {
                        $labelData[] = ['color_id' => $colorId, 'label_text' => $labelText];
                    }
                }
            }
            if (!empty($assigned_labels)) {
                $board_list_card->setLabels()->createMany($assigned_labels);
            }
            if (isset($board_list_card->getLabels)) {
                $board_list_card->assignLabels()->sync($board_list_card->getLabels);
            }
            if (!empty($labelData)) {
                $board_list_card->setLabels()->createMany($labelData);
            }
            /** Assigned Members */
            if ($request->has('assigned_members_id')) {
                // $assigned_members = array_unique(array_map(fn($member) => $this->make_user($member, 4, $request->get('assigned_members_id'))->id, $request->get('assigned_members_id')));
                $temp = $assigned_members = array();
                foreach ($request->get('assigned_members_id') as $key => $value) {
                    $temp[] = $value;
                    $data = $this->make_user(null, 4, $value)->getAttributes();
                    $assigned_members[] = $data['id'];
                }
                // dd($assigned_members);
            } elseif ($request->has('assigned_members')) {
                $assigned_members = array_unique(array_map(fn($member) => $this->make_user($member, 4)->id, $request->get('assigned_members')));
            } else {
                $assigned_members = [];
            }
            $user_ids = AssignBoardCard::where('board_list_card_id', $board_list_card->id)->pluck('user_id')->toArray();
            $updated_user_ids = array_unique(array_merge(array_diff($user_ids, $assigned_members), $assigned_members));
            $board_list_card->setUsers()->sync($updated_user_ids);
            $assigned_users = User::whereIn('id', $updated_user_ids)->get()->pluck('name');
            /** Activities */
            if ($request->has('activities')) {
                foreach ($request->get('activities') as $key => $activity) {
                    $index = $key;
                    /** 0 = comment , 1 = attachment , 2 = activity*/
                    $activity_creator = $creator_name = $activity_member = $member_name = null;
                    if (isset($activity['creator'])) {
                        $activity_creator = $this->make_user($activity['creator'], 2, $activity['creator_id'] ?? null, $activity['creator_full_name'] ?? null);
                        $creator_name = optional($activity_creator)->name;
                    }
                    if (isset($activity['member'])) {
                        $activity_member = $this->make_user($activity['member'], 3, $activity['member_id'] ?? null, $activity['member_full_name'] ?? null);
                        $member_name = optional($activity_member)->name;
                    }
                    if (isset($activity['activity_type'])) {
                        $commonAttributes = ['board_list_card_id' => $board_list_card->id, 'user_id' => optional($activity_creator)->id];
                        if (isset($activity['activity_time'])) {
                            $activity_time = Carbon::parse($activity['activity_time'], 'Asia/Karachi')->setTimezone('Pacific/Honolulu');
                            $commonAttributes['created_at'] = $activity_time;
                            $commonAttributes['updated_at'] = $activity_time;
                        }
                        if ($activity['activity_type'] == 0) {
                            $board_list_card_activity = $board_list_card->getActivities()->create(array_merge($commonAttributes, [
                                'activity' => "commented",
                                'activity_type' => 0,
                            ]));
                            $board_list_card->getComments()->create(array_merge($commonAttributes, [
                                'activity_id' => $board_list_card_activity->id,
                                'comment' => $activity['comment'],
                                'is_modified' => 0,
                            ]));
                        } elseif ($activity['activity_type'] == 1) {
                            if ($request->hasFile('activities.' . $key . '.attachment')) {
                                $attachment = $request->file('activities.' . $key . '.attachment');
                                $validator = Validator::make(['attachment' => $attachment], [
                                    'attachment' => 'required|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar,eml,rtf,woff2,mp4',
                                ]);
                                if ($validator->fails()) {
                                    Log::error('Attachment validation failed: ' . $validator->errors()->first());
                                    continue;
                                }
                                $board_list_card_activity = $board_list_card->getActivities()->create(array_merge($commonAttributes, [
                                    'activity' => " attached ",
                                    'activity_type' => 1,
                                ]));
                                $this->handleAttachment($board_list_card, $board_list_card_activity, $attachment);
                            }
                        } elseif ($activity['activity_type'] == 2) {
                            $board_list_card->getActivities()->create(array_merge($commonAttributes, [
                                'activity' => $activity['activity'],
                                'activity_type' => 2,
                            ]));
                        }
                    }
                }
            }
            $board_list_card->load('getLabels.color');
            $board_list_card->loadMissing('getActivities.getUser', 'getBoardList.getDepartment');
            $board_list_card_resource = new BoardListCardResource($board_list_card);
            $board_list_card_activity_resource = BoardListCardActivityResource::collection($board_list_card->getActivities);
            $board_list_card_attachment_resource = BoardListCardAttachmentResource::collection($board_list_card->getAttachments);
            $board_list_card_comment_resource = BoardListCardCommentResource::collection($board_list_card->getComments);
            $updated = DB::table('urls')
                ->where('status', 0)
                ->where('id', $request->get('card_url_id'))
                ->update(['status' => 0]);
            $urlData = $updated ? DB::table('urls')
                ->where('id', $request->get('card_url_id'))
                ->first(['id', 'url', 'status']) : null;
            return response()->json([
                'success' => 'Board card created successfully .',
                'board_list_card' => $board_list_card_resource,
                'activities' => $board_list_card_activity_resource,
                'attachments' => $board_list_card_attachment_resource,
                'comments' => $board_list_card_comment_resource,
                'assigned_users' => $assigned_users,
                'url' => $urlData,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine(), 'activity_key' => $index ?? null], 500);
        }
    }

    public function add_attachment_php(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer|exists:board_list_cards,id',
                'idMember' => 'required|exists:users,trello_id',
                'bytes' => 'required',
                'date' => 'required|date',
                'mimeType' => 'required|string',
                'file_name' => 'required|string',
            ], [
                'task_id.required' => 'The task ID field is required.',
                'task_id.integer' => 'The task ID must be an integer.',
                'task_id.exists' => 'The selected task ID does not exist.',
                'idMember.required' => 'The member ID field is required.',
                'idMember.integer' => 'The member ID must be an integer.',
                'idMember.exists' => 'The selected member ID does not exist.',
                'bytes.required' => 'The bytes field is required.',
                'date.required' => 'The date field is required.',
                'date.date' => 'The date field must be a valid date.',
                'mimeType.required' => 'The MIME type field is required.',
                'mimeType.string' => 'The MIME type must be a string.',
                'file_name.required' => 'The file name field is required.',
                'file_name.string' => 'The file name must be a string.',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::where('id', $request->get('task_id'))->withTrashed()->first();
            if (!$board_list_card) {
                return response()->json(['error' => 'Error! Board not found.'], 404);
            }
            $clientPath = optional($board_list_card)->client_id ?? "random-client";
            $user = $this->make_user(null, 1, $request->get('idMember'));
            if (!$user) {
                return response()->json(['error' => 'Error! User not found.'], 404);
            }
            DB::beginTransaction();
            $auth_id = $user->id;
            $Date = Carbon::parse($request->get('date'), 'Asia/Karachi')->setTimezone('Pacific/Honolulu');
            $board_list_card_activity = new BoardListCardActivity();
            $board_list_card_activity->board_list_card_id = $board_list_card->id;
            $board_list_card_activity->user_id = $auth_id;
            $board_list_card_activity->activity = "attached";
            $board_list_card_activity->activity_type = 1;
            /** 0 = comment , 1 = attachment , 2 = activity*/
            $board_list_card_activity->created_at = $Date;
            $board_list_card_activity->updated_at = $Date;
            $board_list_card_activity->save();
            $board_list_card_attachment = new BoardListCardAttachment();
            $board_list_card_attachment->board_list_card_id = $board_list_card->id;
            $board_list_card_attachment->user_id = $auth_id;
            $board_list_card_attachment->activity_id = $board_list_card_activity->id;
            $board_list_card_attachment->original_name = $request->get('file_name');
            $board_list_card_attachment->mime_type = $request->get('mimeType');
            $board_list_card_attachment->file_size = $this->convert_filesize($request->get('bytes'));
            $board_list_card_attachment->extension = pathinfo($request->get('file_name'), PATHINFO_EXTENSION);
            $file_directory = str_contains($board_list_card_attachment->mime_type, 'image') ? 'images' : 'images';
            $file_directory_path = public_path("assets/{$file_directory}/board-list-card/original/");
            $file_name = $request->get('random_name');
            $board_list_card_attachment->file_name = $file_name;
            $board_list_card_attachment->file_path = $file_directory_path . $file_name;
            $board_list_card_attachment->created_at = $Date;
            $board_list_card_attachment->updated_at = $Date;
            $board_list_card_attachment->save();
            $board_list_card_activity->load('getAttachmentWithTrashed:id,activity_id,original_name,mime_type,file_name,file_path', 'getUser:id,name,email');
            $activity = new BoardListCardActivityResource($board_list_card_activity, 'Activity');
            $attachment = new BoardListCardAttachmentResource($board_list_card_attachment, 'Attachment');
            $notify_message = " added an attachment" . " (" . $request->get('file_name') . ") to " . optional($board_list_card)->title;
            $notify_users = $board_list_card->getBoardListCardUsers;
            Notification::send($notify_users, new BoardListNotification($board_list_card_activity, $notify_message));
            DB::commit();
            return response()->json(['success' => 'Attachment added successfully.', 'activity' => $activity, 'attachment' => $attachment]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function update_cover_image_php(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer|exists:board_list_cards,id',
                'cover_image' => 'required|string',
                'cover_background_color' => 'nullable|string|max:20',
            ], [
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
                'cover_image.required' => 'The cover image is required.',
                'cover_image.string' => 'The cover image must be a string.',
                'cover_background_color.string' => 'The cover background color must be a string.',
                'cover_background_color.max' => 'The cover background color may not be greater than :max characters.',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::where('id', $request->get('task_id'))->first();
            if (!$board_list_card) {
                return response()->json(['error' => 'Error! Board not found.'], 404);
            }
            $department_id = optional($board_list_card->getBoardList->getDepartment)->id ?? null;
            $board_list_card_id = optional($board_list_card)->id ?? null;
            $additional_path = "{$department_id}/{$board_list_card_id}/";
            $board_list_card->cover_image = $request->get('cover_image');
            if ($request->has('cover_background_color')) {
                $board_list_card->cover_background_color = $request->cover_background_color;
            }
            if ($board_list_card->save()) {
                $board_list_card_resource = new BoardListCardResource($board_list_card);
                return response()->json(['success' => 'Cover image updated successfully', 'board_list_card' => $board_list_card_resource]);
            }
            return response()->json(['error' => 'Failed to update board card cover image.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function save_trello_users(Request $request, $id)
    {
        try {
            if (!$id) {
                return response()->json(['error' => 'Error! Board id field is required.'], 422);
            }
            $response = Http::get("https://api.trello.com/1/boards/{$id}/members", [
                'key' => $request->get('key', 'e93872d6ab2bcf409a7a0a992715ab81'),
                'token' => $request->get('token', 'ATTA7d07f82b48f5765e1e0d8e3d8b54dce8a1826b89f270245143c8c907fb7aeee5FE81E477'),
            ]);
            $members = $response->json() ?? [];
            $results = [];
            foreach ($members as $member) {
                $user = $this->make_user($member['username'], 1, $member['id'] ?? null, $member['fullName'] ?? null);
                $userResource = new UserResource($user);
                $userResource->additional([
                    'created_at_readable' => $user->created_at->diffForHumans(),
                    'updated_at_readable' => $user->updated_at->diffForHumans(),
                ]);
                // if ($user->wasRecentlyCreated || ($user->created_at->eq($user->updated_at))) {
                if ($user->wasRecentlyCreated) {
                    $results['new_users'][] = $userResource;
                } else {
                    $results['existing_users'][] = $userResource;
                }
            }
            return response()->json([
                'status' => 'success',
                'board_id' => $id,
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()]);
        }
    }

    public function create_trello_users(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'full_name' => 'nullable|string',
                'username' => 'required|string',
                'trello_id' => 'nullable|string',
            ], [
                'username.required' => 'The user name field is required.',
                'username.string' => 'The user name must be a string.',
                'trello_id.string' => 'The trello id must be a string.',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $user = $this->make_user($request->get('username'), 1, $request->get('trello_id'), $request->get('full_name'));
            $userResource = new UserResource($user);
            $userResource->additional([
                'created_at_readable' => $user->created_at->diffForHumans(),
                'updated_at_readable' => $user->updated_at->diffForHumans(),
                'existing_user' => !$user->wasRecentlyCreated,
            ]);
            return response()->json([
                'status' => 'success',
                'results' => $userResource,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()]);
        }
    }
}
