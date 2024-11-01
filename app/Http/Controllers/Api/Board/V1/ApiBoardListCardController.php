<?php

namespace App\Http\Controllers\Api\Board\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BoardListCardActivityResource;
use App\Http\Resources\BoardListCardResource;
use App\Http\Resources\BoardListResource;
use App\Http\Resources\DepartmentResource;
use App\Jobs\SendBoardListNotification;
use App\Models\BoardList;
use App\Models\BoardListCard;
use App\Models\BoardListCardActivity;
use App\Models\BoardListCardAttachment;
use App\Models\BoardListCardComment;
use App\Models\Client;
use App\Models\Department;
use App\Models\Team;
use App\Models\User;
use App\Notifications\BoardListNotification;
use App\Traits\BoardListCardCoverImageTrait;
use App\Traits\BoardListDateFormatTrait;
use App\Traits\DepartmentTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ApiBoardListCardController extends Controller
{
    use DepartmentTrait, BoardListCardCoverImageTrait , BoardListDateFormatTrait;

    public function store(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $rules = [
                'board_list_id' => 'required|integer|exists:board_lists,id',
                'title' => 'required|string|max:255',
                'team_key' => 'required|integer|exists:teams,team_key',
            ];

            $messages = [
                'board_list_id.required' => 'The board list id field is required.',
                'board_list_id.exists' => 'The selected board list is invalid.',
                'board_list_id.integer' => 'The board list id must be an integer.',
                'title.required' => 'The title field is required.',
                'title.max' => 'The title may not be greater than 255 characters.',
                'client_id.required' => 'The client id field is required.',
                'client_id.exists' => 'The selected client is invalid.',
                'client_id.integer' => 'The client id must be an integer.',
                'team_key.required' => 'The team key field is required.',
                'team_key.exists' => 'The team key is invalid.',
                'team_key.integer' => 'The team key be an integer.',
            ];
            if ($request->has('client_id')) {
                $rules['client_id'] = 'required|integer|exists:clients,id';
            }

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $board_list_card = new BoardListCard();
            $board_list_card->board_list_id = $request->get('board_list_id');
            $board_list_card->title = $request->get('title');
            $board_list_card->team_key = $request->get('team_key');
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
                    'user_id' => $request->get('user_id'),
                    'board_list_card_id' => $board_list_card->id,
                    'activity' => 'added this card to ' . $board_list_card->getBoardList->title,
                    'activity_type' => 2,
                ]) && $board_list_card->getLabels->count() > 0) {
                $board_list_card->load('getBoardList', 'getClient', 'getTeam', 'getLabels', 'getLabels.color', 'getActivities.getUser', 'getActivities.getAttachmentWithTrashed', 'getActivities.getCommentWithTrashed', 'getAttachments', 'getComments', 'getBoardListCardUsers');

                return response()->json(['success' => 'Board card created successfully.', 'board_list_card' => new BoardListCardResource($board_list_card)], 201);
            }
            return response()->json(['error' => 'Failed to create board card.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function show(Request $request, $id = null): ?\Illuminate\Http\JsonResponse
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
            })->withTrashed()->first();

            if (!$board_list_card) {
                return response()->json(['error' => 'Oops! Task not found.'], 404);
            }
            $board_list_card->load('getBoardList', 'getClient', 'getTeam', 'getLabels', 'getLabels.color', 'getActivities.getUser', 'getActivities.getAttachmentWithTrashed', 'getActivities.getCommentWithTrashed', 'getAttachments', 'getComments', 'getBoardListCardUsers');    
            
            $board_list_card_resource = new BoardListCardResource($board_list_card, 'Board List Card');

            $departmentData = $this->department_trait();

            return response()->json([
                'success' => 'Board card fetched successfully.',
                'departments' => $departmentData['departments_resource'],
                'all_departments' => $departmentData['all_departments_resource'],
                'board_list_card' => $board_list_card_resource,
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(),
                'line' => $e->getLine(),], 500);
        }
    }
    
    private function userImageUrl($image)
    {
        if (!$image) {
            return null;
        }
        if (filter_var($image, FILTER_VALIDATE_URL)) {
            return $image;
        }
        if (file_exists(public_path('assets/images/profile_images/') . $image) && in_array(strtolower(pathinfo($image, PATHINFO_EXTENSION)), ['jpeg', 'png', 'jpg', 'gif'])) {
            return asset("assets/images/profile_images/{$image}");
        }
        return null;
    }
    
        private function imageUrl($attachment_file_name,$attachment_mime_type): string
    {
        $file_directory = str_contains($attachment_mime_type, 'image') ? 'images' : 'images';

        $path = "assets/{$file_directory}/board-list-card/original";
        $directories = [
            "{$path}/{$attachment_file_name}",
        ];
        foreach ($directories as $directory) {
            $fullPath = public_path($directory);
            if (file_exists($fullPath)) {
                return asset($directory);
            }
        }
        return asset("assets/images/no-results-found.png");
    }

    public function show_raw(Request $request, $id = null): ?\Illuminate\Http\JsonResponse
    {
        try {
            if ($request->has('code') && is_null($id)) {
                $id = $this->decryptV1($request->get('code'));
            }
            if (is_null($id)) {
                return response()->json(['error' => 'ID or code is required.'], 400);
            }
            
            $users = DB::select(DB::raw("SELECT id, name, email, image, type, trello_id, team_key FROM users WHERE type != 'client' AND status = 1 AND deleted_at IS NULL;"));

            $card_query = "SELECT 
                bl.id AS bl_id,
                bl.title AS bl_title,        
                d.id AS department_id,
                d.name AS department_name,
                d.background_image AS department_background_image,
                blc.id AS card_id,
                t.id as team_id,
                t.team_key as team_key,
                t.name as team_name,
                blc.client_id,
                blc.title AS card_title,
                blc.description AS card_description,
                blc.cover_image,
                blc.cover_image_updated_at,
                blc.cover_background_color,
                blc.is_check_start_date,
                blc.is_check_due_date,
                blc.priority, blc.start_date,
                blc.due_date, blc.task_completed,
                blc.position,
                blc.trello_url,
                adb.department_id ,
                c.id as client_id ,
                c.name as client_name, 
                c.email as client_email
            FROM 
                board_list_cards AS blc 
            LEFT JOIN 
                clients AS c 
                ON blc.client_id = c.id 
            LEFT JOIN 
                board_lists AS bl 
                ON blc.board_list_id = bl.id 
            LEFT JOIN 
                assign_department_board_lists AS adb 
                ON blc.board_list_id = adb.board_list_id
            LEFT JOIN
                teams as t
                ON blc.team_key = t.team_key
            LEFT JOIN
                departments AS d
                ON adb.department_id = d.id 
                AND d.deleted_at IS NULL 
                AND d.status = 1 
            WHERE 
                blc.id = :id 
                AND blc.status = 1 
                AND blc.deleted_at IS NULL";
            $card = DB::selectOne(DB::raw($card_query), ['id' => $id]);
            $card_id = $card->card_id;
            $assigned_user_results = DB::select(DB::raw("SELECT u.id, u.name, u.email, u.image, u.type, u.trello_id, u.team_key FROM users AS u LEFT JOIN assign_board_cards AS abc ON u.id = abc.user_id WHERE abc.board_list_card_id = :card_id AND u.type != 'client' AND u.status = 1 AND u.deleted_at IS NULL;"), ['card_id' => $card_id]);
                    $assigned_users = [];
                    $assigned_user_ids = [];
                    foreach ($assigned_user_results as $assigned_user) {
                        $assigned_user_ids[] = $assigned_user->id;
                        $assigned_users[] = ['id' => $assigned_user->id, 'name' => $assigned_user->name, 'email' => $assigned_user->email, 'image' => $this->userImageUrl($assigned_user->image), 'type' => $assigned_user->type, 'trello_id' => $assigned_user->trello_id, 'team_key' => $assigned_user->team_key];
                    }
                    $unassigned_user_results = array_filter($users, function ($user) use ($assigned_user_ids) {
                        return !in_array($user->id, $assigned_user_ids);
                    });
                    $unassigned_users = [];
                    foreach ($unassigned_user_results as $unassigned_user) {
                        $unassigned_users[] = ['id' => $unassigned_user->id, 'name' => $unassigned_user->name, 'email' => $unassigned_user->email, 'image' => $this->userImageUrl($unassigned_user->image), 'type' => $unassigned_user->type, 'trello_id' => $unassigned_user->trello_id, 'team_key' => $unassigned_user->team_key];
                    }

            $labelsData = DB::select(DB::raw("Select l.id , l.user_id , l.board_list_card_id , l.label_text , l.color_id , c.color_name , c.color_value , c.color_position , u.name as user_name , u.email as user_email  FROM labels as l LEFT JOIN colors as c ON l.color_id = c.id LEFT JOIN users as u ON l.user_id = u.id WHERE board_list_card_id = :id "), ['id' => $id]);
            $AssignedlabelsData = DB::select(DB::raw("Select abl.label_id FROM assign_board_labels as abl WHERE board_list_card_id = :id "), ['id' => $id]);
            $assignedLabelIds = array_column($AssignedlabelsData, 'label_id');
            $labels = [];
            foreach($labelsData as $label){
                $labels[] = [
                    "id"=>$label->id,
                    "user"=> $label->user_id ? ['id'=>$label->user_id , 'name'=>$label->user_name, 'email'=>$label->user_email]:null,
                    "board_list_card_id"=> $label->board_list_card_id,
                    "label"=> $label->label_text,
                    "assigned" => in_array($label->id, $assignedLabelIds),
                    "color"=> [
                        "id"=> $label->color_id,
                        "color"=> $label->color_name,
                        "value"=> $label->color_value,
                        "position"=> $label->color_position
                    ],
                ];
            }
            
            $activitiesData = DB::select(DB::raw("Select 
            blca.id , blca.activity , blca.activity_type , blca.user_id , blca.created_at as activity_created_at, 
            u.name as user_name , u.email as user_email, u.image as user_image, u.type as user_type , u.trello_id as user_trello_id , u.team_key as user_team_key , 
            blcc.id as comment_id  , blcc.activity_id as comment_activity_id , blcc.user_id as comment_user_id , blcc.comment , blcc.created_at as comment_created_at , blcc.deleted_at as comment_deleted_at,
            blcatt.id as attachment_id , blcatt.activity_id as attachment_activity_id , blcatt.user_id as attachment_user_id , blcatt.original_name as attachment_original_name , blcatt.file_name as attachment_file_name , blcatt.mime_type as attachment_mime_type , blcatt.file_size as attachment_file_size , blcatt.created_at as attachment_created_at , blcatt.deleted_at as attachment_deleted_at,
            blccpl.id as board_list_card_comment_previous_log_id
            FROM board_list_card_activities as blca LEFT JOIN users as u ON blca.user_id = u.id  
            LEFT JOIN board_list_card_comments as blcc ON blca.id = blcc.activity_id 
            LEFT JOIN board_list_card_comment_previous_logs as blccpl ON blcc.id = blccpl.comment_id 
            LEFT JOIN board_list_card_attachments as blcatt ON blca.id = blcatt.activity_id
            WHERE blca.board_list_card_id = :id ORDER BY blca.created_at DESC "), ['id' => $id]);
            $activities = [];
            foreach($activitiesData as $activity){
                $isDeleted = false;
                $activityData = [
                    "id"=>$activity->id,
                    "activity"=> $activity->activity,
                    "activity_type"=> $activity->activity_type,
                    "activity_user"=> $activity->user_id ? ['id'=>$activity->user_id ,'name'=>$activity->user_name ,'email'=>$activity->user_email ,'email'=>$activity->user_email ,'image'=>$activity->user_image ,'type'=>$activity->user_type ,'trello_id'=>$activity->user_trello_id ,'team_key'=>$activity->user_team_key] : null,
                    "created_at"=> $this->formatTimestamp($activity->activity_created_at),
                ];
                $activityData2 = [];
                if($activity->activity_type == 0){
                    $activityData2['comment'] = null;
                    if($activity->comment_activity_id){
                        $activityData2['comment'] = [
                                'id' => $activity->comment_id,
                                'user_id' => $activity->comment_user_id,
                                'activity_id' => $activity->comment_activity_id,
                                'comment' => $activity->comment,
                                'created_at' => $activity->comment_created_at,
                            ];
                        
                        if (!$activity->comment_deleted_at) {
                            $activityData2['comment']['created_at'] = $this->formatTimestamp($activity->comment_created_at);
                        }
                        if ($activity->comment_deleted_at) {
                            $isDeleted = true;
                            $activityData2['comment']['deleted_at'] = $this->formatTimestamp($activity->comment_deleted_at);
                        }
                    }
                    $activityData2['comment_edited'] = isset($activity->board_list_card_comment_previous_log_id);
                }
                if($activity->activity_type == 1){
                    $activityData2['attachment'] = null;
                    if($activity->attachment_activity_id){
                        $activityData2['attachment'] = [
                                'id' => $activity->attachment_id,
                                'user_id' => $activity->attachment_user_id,
                                'activity_id' => $activity->attachment_activity_id,
                                'original_name' => $activity->attachment_original_name,
                                'file_name' => $activity->attachment_file_name,
                                'mime_type' => $activity->attachment_mime_type,
                                'file_size' => $activity->attachment_file_size,
                            ];
                            
                            if (!$activity->attachment_deleted_at) {
                                $activityData2['attachment']['file_path'] = $this->imageUrl($activity->attachment_file_name,$activity->attachment_mime_type);
                                $activityData2['attachment']['created_at'] = $this->formatTimestamp($activity->attachment_created_at);
                            }
                            if ($activity->attachment_deleted_at) {
                                $isDeleted = true;
                                $activityData2['attachment']['deleted_at'] = $this->formatTimestamp($activity->attachment_deleted_at);
                            }
                    }
                }
                if ($activity->activity_type != 2) {
                    $activityData2['activity_2'] = $isDeleted ? 'from this card' : 'to this card';
                }
                $activityMergedata = array_merge($activityData, $activityData2);

                $activities [] = $activityMergedata;
            }
            
            $blc_attachments = DB::select(DB::raw("Select 
            blcatt.id as attachment_id , blcatt.activity_id as attachment_activity_id , blcatt.user_id as attachment_user_id , blcatt.original_name as attachment_original_name , blcatt.file_name as attachment_file_name , blcatt.mime_type as attachment_mime_type , blcatt.file_size as attachment_file_size , blcatt.created_at as attachment_created_at , blcatt.deleted_at as attachment_deleted_at
            FROM board_list_card_attachments as blcatt 
            
            WHERE blcatt.board_list_card_id = :id ORDER BY blcatt.created_at DESC "), ['id' => $id]);
            $attachments = [];
            foreach($blc_attachments as $attachment){
                $attachmentData = [
                    'id' => $attachment->attachment_id,
                    'user_id' => $attachment->attachment_user_id,
                    'activity_id' => $attachment->attachment_activity_id,
                    'original_name' => $attachment->attachment_original_name,
                    'file_name' => $attachment->attachment_file_name,
                    'mime_type' => $attachment->attachment_mime_type,
                    'file_size' => $attachment->attachment_file_size,
                ];
                
                if (!$attachment->attachment_deleted_at) {
                    $attachmentData['file_path'] = $this->imageUrl($attachment->attachment_file_name,$attachment->attachment_mime_type);
                    $attachmentData['created_at'] = $this->formatTimestamp($attachment->attachment_created_at);
                }
                if ($attachment->attachment_deleted_at) {
                    $isDeleted = true;
                    $attachmentData['deleted_at'] = $this->formatTimestamp($attachment->attachment_deleted_at);
                }
                $attachments[] = $attachmentData;
            }
            
            $blc_comments = DB::select(DB::raw("Select 
            blcc.id as comment_id  , blcc.activity_id as comment_activity_id , blcc.user_id as comment_user_id , blcc.comment , blcc.created_at as comment_created_at , blcc.deleted_at as comment_deleted_at,
            blccpl.id as board_list_card_comment_previous_log_id
            FROM board_list_card_comments as blcc 
            LEFT JOIN board_list_card_comment_previous_logs as blccpl ON blcc.id = blccpl.comment_id 
            WHERE blcc.board_list_card_id = :id ORDER BY blcc.created_at DESC "), ['id' => $id]);
            $comments = [];
            foreach($blc_comments as $comment){
                $commentData = 
                [
                    'id' => $comment->comment_id,
                    'user_id' => $comment->comment_user_id,
                    'activity_id' => $comment->comment_activity_id,
                    'comment' => $comment->comment,
                    'created_at' => $comment->comment_created_at,
                ];
            
                if (!$comment->comment_deleted_at) {
                    $commentData['created_at'] = $this->formatTimestamp($comment->comment_created_at);
                }
                if ($comment->comment_deleted_at) {
                    $isDeleted = true;
                    $commentData['deleted_at'] = $this->formatTimestamp($comment->comment_deleted_at);
                }
                $commentData['comment_edited'] = isset($comment->board_list_card_comment_previous_log_id);
                $comments[] = $commentData;
            }

            $activityTimezone = 'Asia/Karachi';
            $now_karachi = Carbon::now($activityTimezone);
            $cardData = 
            [
                'id' => $card_id,
                'team_key' => $card->team_key,
                'department_id' => $card->department_id,
                'team' => [
                    'id'=> $card->team_id,
                    'team_key'=>$card->team_key,
                    'name'=>$card->team_name
                    ],
                'board_list' => [
                    'id'=> $card->bl_id,
                    'title'=>$card->bl_title,
                    'department'=>[
                        'id'=> $card->department_id,
                        'name'=>$card->department_name,
                        'background_image'=>$card->department_background_image,
                        ]
                    ],
                'client' => $card->client_id ? [
                    'id'=> $card->client_id,
                    'name'=>$card->client_name,
                    'email'=>$card->client_email,
                    ] : null,
                'title' => $card->card_title,
                'description' => $card->card_description,
                'cover_image' => $card->cover_image ? "original/{$card->cover_image}" : null,
                'cover_image_updated_at' => $card->cover_image_updated_at,
                'cover_time_difference' => $card->cover_image_updated_at ? $now_karachi->diffInMinutes(Carbon::parse($card->cover_image_updated_at)->timezone($activityTimezone)->subHours(15)) : null,
                'cover_image_thumbnail' => $card->cover_image ? "150x150/{$card->cover_image}" : null,
                'cover_background_color' => $card->cover_background_color,
                'priority' => $card->priority,
                'is_check_start_date' => $card->is_check_start_date,
                'start_date' => $card->start_date,
                'is_check_due_date' => $card->is_check_due_date,
                'due_date' => $card->due_date,
                'task_completed' => $card->task_completed,
                'labels' => $labels,
                'labels_count' => count($labels),
                'activities' => $activities,
                'activities_count' => count($activities),
                'attachments' => $attachments,
                'attachments_count' => count($attachments),
                'comments' => $comments,
                'comments_count' => count($comments),
                'assigned_users' => $assigned_users,
                'assigned_users_count' => count($assigned_user_results),
                'position' => $card->position,
                'unassigned_users' => $unassigned_users,
                'unassigned_users_count' => count($unassigned_user_results),
                'code' => $this->encrypt_2($card_id),
                'trello_url' => $card->trello_url,
            ];



            // $board_list_card_resource = new BoardListCardResource($board_list_card, 'Board List Card');

            $departmentData = $this->department_trait();

            return response()->json([
                'success' => 'Board card fetched successfully.',
                'departments' => $departmentData['departments_resource'],
                'all_departments' => $departmentData['all_departments_resource'],
                'board_list_card' => $cardData,
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(),
                'line' => $e->getLine(),], 500);
        }
    }


    public function show_debug(Request $request, $id = null): ?\Illuminate\Http\JsonResponse
    {
        $timings = [];
        try {
            if ($request->has('code') && is_null($id)) {
                $startTime = microtime(true);
                $id = $this->decryptV1($request->get('code'));
                $endTime = microtime(true);
                $timings["id_conversion"] = $endTime - $startTime;
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
            })->withTrashed()->first();

            if (!$board_list_card) {
                return response()->json(['error' => 'Oops! Task not found.'], 404);
            }
            
            $activityIds = $board_list_card->getActivities->pluck('id')->toArray();
           

            $relations = [
                'getBoardList',
                'getClient',
                'getTeam',
                'getLabels.color',
                'getActivities.getUser',
                'getActivities.getAttachmentWithTrashed',
                // 'getActivities.getCommentWithTrashed',
                'getAttachments',
                // 'getComments',
                'getBoardListCardUsers',
            ];


            foreach ($relations as $relation) {
                $startTime = microtime(true);
                
                // if ($relation === 'getActivities.getCommentWithTrashed') {
                //     $board_list_card->load([$relation => function ($query) {
                //         $query->where('id', '!=', 15067);
                //     }]);
                // } elseif ($relation === 'getComments') {
                //     $board_list_card->load([$relation => function ($query) {
                //         $query->where('id', '!=', 15067);
                //     }]);
                // } else {
                   
                // }
                 $board_list_card->load($relation);
                $endTime = microtime(true);
                $timings[$relation] = $endTime - $startTime;
            }
            
            // dd($timings);
            // $board_list_card->load('getBoardList', 'getClient', 'getTeam', 'getLabels', 'getLabels.color', 'getActivities.getUser', 'getActivities.getAttachmentWithTrashed', 'getActivities.getCommentWithTrashed', 'getAttachments', 'getBoardListCardUsers');    
            
            // $query = "Select * FROM board_list_card_comments where board_list_card_id = :id";
            // $comment_parameters = ['id' => $id];

            // $comments = DB::select(DB::raw($query),$comment_parameters);

            //  dd($comments);
                
            $board_list_card_resource = new BoardListCardResource($board_list_card, 'Board List Card');
        
            $departmentData = $this->department_trait();

            return response()->json([
                'timings' => $timings,
                'success' => 'Board card fetched successfully.',
                'departments' => $departmentData['departments_resource'],
                'all_departments' => $departmentData['all_departments_resource'],
                'board_list_card' => $board_list_card_resource,
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(),
                'line' => $e->getLine(),], 500);
        }
    }

    /** Move card api start */
    public function move_card(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'board_list_id' => 'required|integer|exists:board_lists,id',
            'task_id' => 'required|integer|exists:board_list_cards,id',
            'position' => 'required|integer',
            'user_id' => 'required|integer|exists:users,id',
        ], [
            'board_list_id.required' => 'The board list id field is required.',
            'board_list_id.exists' => 'The selected board list id is invalid.',
            'board_list_id.integer' => 'The board list id must be an integer.',
            'task_id.required' => 'The task id field is required.',
            'task_id.exists' => 'The selected task id is invalid.',
            'task_id.integer' => 'The task id must be an integer.',
            'position.required' => 'The position is required.',
            'position.integer' => 'The position must be an integer.',
            'user_id.required' => 'The user id field is required.',
            'user_id.exists' => 'The user id is invalid.',
            'user_id.integer' => 'The user id must be an integer.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $board_list_card = BoardListCard::findOrFail($request->task_id);
            $new_board_list_id = $request->board_list_id;
            $new_position = $request->position;

            $current_position = $board_list_card->position;
            $old_board_list_id = $board_list_card->board_list_id;

            if ($new_board_list_id != $old_board_list_id) {
                $resp = $this->moveToAnotherBoardList($board_list_card, $old_board_list_id, $new_board_list_id, $current_position, $new_position);
            } else {
                $resp = $this->repositionWithinSameBoardList($board_list_card, $old_board_list_id, $current_position, $new_position);
            }
            $board_list_card->getActivities()->create([
                'user_id' => $request->get('user_id'),
                'board_list_card_id' => $board_list_card->id,
                'activity' => $resp['message'],
                'activity_type' => 2,
            ]);
            DB::commit();

            return response()->json(['success' => 'Card moved successfully.', 'message' => $resp['message']], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    private function moveToAnotherBoardList($board_list_card, $old_board_list_id, $new_board_list_id, $current_position, $new_position)
    {
        $target_board_list_card_count = BoardListCard::where('board_list_id', $new_board_list_id)->count();

        if ($new_position > $target_board_list_card_count + 1) {
            $new_position = $target_board_list_card_count + 1;
        }

        $affectedCards['old_board_list_cards'] = BoardListCard::where('board_list_id', $old_board_list_id)
            ->where('position', '>', $current_position)
            ->get()->toArray();
        $affectedCards['new_board_list_cards'] = BoardListCard::where('board_list_id', $new_board_list_id)
            ->where('position', '>=', $new_position)
            ->get()->toArray();
        BoardListCard::where('board_list_id', $old_board_list_id)
            ->where('position', '>', $current_position)
            ->decrement('position');
        BoardListCard::where('board_list_id', $new_board_list_id)
            ->where('position', '>=', $new_position)
            ->increment('position');

        $message = "Moved this card from " . optional($board_list_card->getBoardList)->title . " to " . BoardList::where('id', $new_board_list_id)->value('title');

        $board_list_card->board_list_id = $new_board_list_id;
        $board_list_card->position = $new_position;
        $board_list_card->save();

        return ['message' => $message, 'new_position' => $new_position, 'current_position' => $current_position, 'affectedCards' => $affectedCards];
    }

    private function repositionWithinSameBoardList($board_list_card, $old_board_list_id, $current_position, $new_position)
    {
        $max_position = BoardListCard::where('board_list_id', $old_board_list_id)->max('position');

        if ($new_position < 1) {
            $new_position = 1;
        } elseif ($new_position > $max_position) {
            $new_position = $max_position;
        }
        $affectedCards = [];
        if ($new_position != $current_position) {
            if ($new_position > $current_position) {
                $affectedCards['old_board_list_cards'] = BoardListCard::where('board_list_id', $old_board_list_id)
                    ->whereBetween('position', [$current_position + 1, $new_position])
                    ->get()->toArray();
                BoardListCard::where('board_list_id', $old_board_list_id)
                    ->whereBetween('position', [$current_position + 1, $new_position])
                    ->decrement('position');
            } else {
                $affectedCards['old_board_list_cards'] = BoardListCard::where('board_list_id', $old_board_list_id)
                    ->whereBetween('position', [$new_position, $current_position - 1])
                    ->get()->toArray();
                BoardListCard::where('board_list_id', $old_board_list_id)
                    ->whereBetween('position', [$new_position, $current_position - 1])
                    ->increment('position');
            }
            $board_list_card->position = $new_position;
            $board_list_card->save();
            $message = "Repositioned this card within " . optional($board_list_card->getBoardList)->title;
        } else {
            $message = "No change in position.";
        }
        return ['message' => $message, 'new_position' => $new_position, 'current_position' => $current_position, 'affectedCards' => $affectedCards];
    }

    /** Move card api end */

    /** Move card api 2 start */
    public function move_card_2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'board_list_id' => 'required|integer|exists:board_lists,id',
            'task_id' => 'required|integer|exists:board_list_cards,id',
            'user_id' => 'required|integer|exists:users,id',
            'sort' => 'required|array',
            'sort.*.id' => 'required|integer',
            'sort.*.position' => 'required|integer',
        ], [
            'board_list_id.required' => 'The board list id is required.',
            'board_list_id.exists' => 'The selected board list card id is invalid.',
            'board_list_id.integer' => 'The board list id must be an integer.',
            'task_id.required' => 'The task id field is required.',
            'task_id.exists' => 'The selected task id is invalid.',
            'task_id.integer' => 'The task id must be an integer.',
            'user_id.required' => 'The user id field is required.',
            'user_id.exists' => 'The user id is invalid.',
            'user_id.integer' => 'The user id must be an integer.',
            'sort.required' => 'The sort array is required.',
            'sort.*.id.required' => 'Each sort item must have an id.',
            'sort.*.id.integer' => 'Each sort item id must be an integer.',
            'sort.*.position.required' => 'Each sort item must have a position.',
            'sort.*.position.integer' => 'Each sort item position must be an integer.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $board_list_card_id = $request->input('task_id');
            $new_board_list_id = $request->input('board_list_id');
            $user_id = $request->input('user_id');

            $board_list_card = BoardListCard::findOrFail($board_list_card_id);

            if ($new_board_list_id !== $board_list_card->board_list_id) {
                $board_list_card_activity = new BoardListCardActivity();
                $board_list_card_activity->board_list_card_id = $board_list_card->id;
                $board_list_card_activity->user_id = $user_id;
                $board_list_card_activity->activity = $successMessage = "moved this card from " . optional($board_list_card->getBoardList)->title . " to " . BoardList::where('id', $new_board_list_id)->value('title');
                $board_list_card_activity->activity_type = 2;
                $board_list_card_activity->save();

                $board_list_card->board_list_id = $new_board_list_id;
                $board_list_card->save();
            } else {
                $board_list_card_activity = new BoardListCardActivity();
                $board_list_card_activity->board_list_card_id = $board_list_card->id;
                $board_list_card_activity->user_id = $user_id;
                $board_list_card_activity->activity = $successMessage = "repositioned this card within " . optional($board_list_card->getBoardList)->title;
                $board_list_card_activity->activity_type = 2;
                $board_list_card_activity->save();
            }

            $positions = $request->input('sort');
            $updateData = [];
            foreach ($positions as $positionData) {
                $updateData[$positionData['id']] = ['position' => $positionData['position']];
            }
            BoardListCard::whereIn('id', array_keys($updateData))->update($updateData);

            DB::commit();

            return response()->json(['sort' => $request->get('board_list_id'), 'success' => $successMessage]);
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /** Move card api 2 end */

//    /** Move card api 3 MoVe CARD start */
//    public function move_card_3(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'board_list_id' => 'required|integer|exists:board_lists,id',
//            'task_id' => 'required|integer|exists:board_list_cards,id',
//            'task_list_ids' => 'required|string',
//        ], [
//            'board_list_id.required' => 'The board list id is required.',
//            'board_list_id.exists' => 'The selected board list id is invalid.',
//            'board_list_id.integer' => 'The board list id must be an integer.',
//            'task_id.required' => 'The task id field is required.',
//            'task_id.exists' => 'The selected task id is invalid.',
//            'task_id.integer' => 'The task id must be an integer.',
//            'task_list_ids.required' => 'The task list ids field is required.',
//            'task_list_ids.string' => 'The task list ids field must be a string.',
//        ]);
//
//        if ($validator->fails()) {
//            return response()->json(['errors' => $validator->errors()], 422);
//        }
//
//        $task_list_ids = explode(',', $request->input('task_list_ids'));
//
//        $validator = Validator::make(['task_list_ids' => $task_list_ids], [
//            'task_list_ids.*' => 'exists:board_list_cards,id'
//        ], [
//            'task_list_ids.*.exists' => 'One or more selected task ids are invalid.',
//        ]);
//
//        if ($validator->fails()) {
//            return response()->json(['errors' => $validator->errors()], 422);
//        }
//
//        try {
//            DB::beginTransaction();
//
//            $board_list_card_id = $request->input('task_id');
//            $new_board_list_id = $request->input('board_list_id');
//
//            $board_list = BoardList::find($new_board_list_id);
//            if (!$board_list) {
//                return response()->json(['error' => 'The selected board list id is invalid.'], 404);
//            }
//
//            $board_list_card = BoardListCard::find($board_list_card_id);
//            if (!$board_list_card) {
//                return response()->json(['error' => 'The selected task id is invalid.'], 404);
//            }
//            $current_task_list_ids = $board_list->getBoardListCards ? $board_list->getBoardListCards->sortBy('position')->pluck('id')->toArray() : [];
//            if ($current_task_list_ids == $task_list_ids) {
//                return response()->json(['success' => 'The task list is already updated.'], 200);
//            }
//            $notify_message = null;
//            if ($new_board_list_id != $board_list_card->board_list_id) {
//                $successMessage = "moved this card from " . optional($board_list_card->getBoardList)->title . " to " . $board_list->title;
//                $notify_message = "moved {$board_list_card->title} from " . optional($board_list_card->getBoardList)->title . " to " . $board_list->title;
//                $board_list_card->board_list_id = $new_board_list_id;
//                $board_list_card->save();
//            } else {
//                $successMessage = "repositioned this card within " . optional($board_list_card->getBoardList)->title;
////                $notify_message = "repositioned {$board_list_card->title} within " . optional($board_list_card->getBoardList)->title;
//            }
//
//            $board_list_card_activity = new BoardListCardActivity();
//            $board_list_card_activity->board_list_card_id = $board_list_card_id;
//            $board_list_card_activity->user_id = auth()->user()->id;
//            $board_list_card_activity->activity = $successMessage;
//            $board_list_card_activity->activity_type = 2;
//            $board_list_card_activity->save();
//
//            foreach ($task_list_ids as $key => $task_list_id) {
//                BoardListCard::where('id', $task_list_id)->update(['position' => $key + 1]);
//            }
//            DB::commit();
//
//            if ($notify_message) {
//                $notify_users = $board_list_card->getBoardListCardUsers;
//                Notification::send($notify_users, new BoardListNotification($board_list_card_activity, $notify_message));
//            }
//            return response()->json(['success' => $successMessage]);
//        } catch (\Exception $e) {
//            DB::rollBack();
//            return response()->json(['error' => $e->getMessage()], 500);
//        }
//    }
//
//    /** Move card api 3 end */
    /** Move card api 3 MoVe CARD start */
    public function move_card_3(Request $request): ?\Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'department_id' => 'nullable|digits_between:1,18|integer|exists:departments,id',
            'board_list_id' => 'required|digits_between:1,18|integer|exists:board_lists,id',
            'task_id' => 'required|digits_between:1,18|integer|exists:board_list_cards,id',
            'task_list_ids' => 'required_without:position|string',
            'position' => 'required_without:task_list_ids|digits_between:1,18|integer',
        ], [
            'department_id.exists' => 'The selected department id is invalid.',
            'department_id.integer' => 'The department id must be an integer.',
            'department_id.digits_between' => 'The department id must be between 1 and 18 digits long.',
            'board_list_id.required' => 'The board list id is required.',
            'board_list_id.exists' => 'The selected board list id is invalid.',
            'board_list_id.integer' => 'The board list id must be an integer.',
            'board_list_id.digits_between' => 'The board list id must be between 1 and 18 digits long.',
            'task_id.required' => 'The task id field is required.',
            'task_id.exists' => 'The selected task id is invalid.',
            'task_id.integer' => 'The task id must be an integer.',
            'task_id.digits_between' => 'The task id must be between 1 and 18 digits long.',
            'task_list_ids.required_without' => 'The task list ids field is required when position is not provided.',
            'task_list_ids.required' => 'The task list ids field is required.',
            'task_list_ids.string' => 'The task list ids field must be a string.',
            'position.required_without' => 'The position field is required when task list ids are not provided.',
            'position.integer' => 'The task list position field must be an integer.',
            'position.digits_between' => 'The task list position must be between 1 and 18 digits long.',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $simplifiedErrors = [];
            foreach ($errors->messages() as $field => $messages) {
                $simplifiedErrors[$field] = $errors->has("{$field}.digits_between") ? $errors->first("{$field}.digits_between") : reset($messages);
            }
            return response()->json(['errors' => $simplifiedErrors], 422);
        }

        $task_list_ids = explode(',', $request->input('task_list_ids', ''));

        $validator = Validator::make(['task_list_ids' => $task_list_ids], [
            'task_list_ids.*' => 'exists:board_list_cards,id'
        ], [
            'task_list_ids.*.exists' => 'One or more selected task ids are invalid.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $board_list_card_id = $request->input('task_id');
            $new_board_list_id = $request->input('board_list_id');
            $department_id = $request->input('department_id');
            $position = $request->input('position');

            $department = null;
            if ($department_id) {
                $department = Department::find($department_id);
                if (!$department || !$department->getBoardLists->contains($new_board_list_id)) {
                    return response()->json(['error' => 'The selected board list id does not belong to the specified department.'], 404);
                }
            }
            $board_list = BoardList::find($new_board_list_id);
            if (!$board_list) {
                return response()->json(['error' => 'The selected board list id is invalid.'], 404);
            }
            $board_list_card = BoardListCard::find($board_list_card_id);
            if (!$board_list_card) {
                return response()->json(['error' => 'The selected task id is invalid.'], 404);
            }
            if (!$position && !$department_id && $task_list_ids) {
                $board_list_cards_array = BoardListCard::where('board_list_id',$board_list->id)->whereIn('id',$task_list_ids)->orderBy('position')->pluck('id')->toArray();
                $current_task_list_ids = $board_list_cards_array && count($board_list_cards_array) > 0 ? $board_list_cards_array : [];
                if ($current_task_list_ids == $task_list_ids) {
                    return response()->json(['success' => 'The task list is already updated.'], 200);
                }
                if ($new_board_list_id != $board_list_card->board_list_id) {
                    if (count(array_diff($current_task_list_ids, $task_list_ids)) > 0) {
                        return response()->json(['error' => 'The task list ids must include all tasks from the new board list and the moving task.'], 422);
                    }
                } else if (count(array_diff($current_task_list_ids, $task_list_ids)) > 0) {
                    return response()->json(['error' => 'The task list ids must include all tasks from the current board list.'], 422);
                }
            } elseif ($position && $department_id) {
                if ($new_board_list_id == $board_list_card->board_list_id && $board_list_card->position == $position) {
                    return response()->json(['success' => 'The task list is already updated.'], 200);
                }
            } else {
                return response()->json(['error' => 'Please provide required fields.'], 200);
            }

            $notify_message = null;
            if ($new_board_list_id != $board_list_card->board_list_id) {
                $successMessage = "moved this card from " . ($department ? optional($board_list->getDepartment)->name . " - " : "") . optional($board_list_card->getBoardList)->title . " to " . ($department ? "{$department->name} - " : "") . $board_list->title;
                $notify_message = "moved {$board_list_card->title} from " . ($department ? optional($board_list->getDepartment)->name . " - " : "") . optional($board_list_card->getBoardList)->title . " to " . ($department ? "{$department->name} - " : "") . $board_list->title;
                $board_list_card->board_list_id = $new_board_list_id;
                $board_list_card->save();
            } else {
                $successMessage = "repositioned this card within " . optional($board_list_card->getBoardList)->title;
//                $notify_message = "repositioned {$board_list_card->title} within " . optional($board_list_card->getBoardList)->title;
            }

            if ($position) {
                BoardListCard::where('board_list_id', $new_board_list_id)
                    ->where('position', '>=', $position)
                    ->increment('position');
                $board_list_card->position = $position;
                $board_list_card->save();
            } else {
                foreach ($task_list_ids as $key => $task_list_id) {
                    BoardListCard::where('id', $task_list_id)->update(['position' => $key + 1]);
                }
            }

            $board_list_card_activity = new BoardListCardActivity();
            $board_list_card_activity->board_list_card_id = $board_list_card_id;
            $board_list_card_activity->user_id = auth()->user()->id;
            $board_list_card_activity->activity = $successMessage;
            $board_list_card_activity->activity_type = 2;
            $board_list_card_activity->save();

            DB::commit();

            if ($notify_message) {
                $notify_users = $board_list_card->getBoardListCardUsers;
                Notification::send($notify_users, new BoardListNotification($board_list_card_activity, $notify_message));
            }
            return response()->json(['success' => $successMessage]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function move_card_on_dropdown(Request $request): ?\Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'board_list_id' => 'required|digits_between:1,18|integer|exists:board_lists,id',
            'task_id' => 'required|digits_between:1,18|integer|exists:board_list_cards,id',
        ], [
            'board_list_id.required' => 'The board list id is required.',
            'board_list_id.exists' => 'The selected board list id is invalid.',
            'board_list_id.integer' => 'The board list id must be an integer.',
            'board_list_id.digits_between' => 'The board list id must be between 1 and 18 digits long.',
            'task_id.required' => 'The task id field is required.',
            'task_id.exists' => 'The selected task id is invalid.',
            'task_id.integer' => 'The task id must be an integer.',
            'task_id.digits_between' => 'The task id must be between 1 and 18 digits long.',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $simplifiedErrors = [];
            foreach ($errors->messages() as $field => $messages) {
                $simplifiedErrors[$field] = $errors->has("{$field}.digits_between") ? $errors->first("{$field}.digits_between") : reset($messages);
            }
            return response()->json(['errors' => $simplifiedErrors], 422);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $board_list_card_id = $request->input('task_id');
            $new_board_list_id = $request->input('board_list_id');

            $board_list = BoardList::find($new_board_list_id);
            if (!$board_list) {
                return response()->json(['error' => 'The selected board list id is invalid.'], 404);
            }
            $board_list_card = BoardListCard::find($board_list_card_id);
            if (!$board_list_card) {
                return response()->json(['error' => 'The selected task id is invalid.'], 404);
            }
            if ($new_board_list_id == $board_list_card->board_list_id && $board_list_card->board_list_card == 0) {
                return response()->json(['message' => 'No changes made, card remains in the same position.'], 200);
            }

            $notify_message = null;
            if ($new_board_list_id != $board_list_card->board_list_id) {
                $successMessage = "moved this card from " . (isset($board_list->getDepartment) ? $board_list->getDepartment->name . " - " : "") . optional($board_list_card->getBoardList)->title . " to " . (isset($board_list->getDepartment) ? "{$board_list->getDepartment->name} - " : "") . $board_list->title;
                $notify_message = "moved {$board_list_card->title} from " . (isset($board_list->getDepartment) ? $board_list->getDepartment->name . " - " : "") . optional($board_list_card->getBoardList)->title . " to " . (isset($board_list->getDepartment) ? "{$board_list->getDepartment->name} - " : "") . $board_list->title;
                $board_list_card->board_list_id = $new_board_list_id;
                $board_list_card->save();
            } else {
                $successMessage = "repositioned this card within " . optional($board_list_card->getBoardList)->title;
//                $notify_message = "repositioned {$board_list_card->title} within " . optional($board_list_card->getBoardList)->title;
            }

            BoardListCard::where('board_list_id', $new_board_list_id)
                ->where('position', '>=', 1)
                ->increment('position');
            $board_list_card->position = 0;
            $board_list_card->save();

            $board_list_card_activity = new BoardListCardActivity();
            $board_list_card_activity->board_list_card_id = $board_list_card_id;
            $board_list_card_activity->user_id = auth()->user()->id;
            $board_list_card_activity->activity = $successMessage;
            $board_list_card_activity->activity_type = 2;
            $board_list_card_activity->save();

            DB::commit();

            if ($notify_message) {
                $notify_users = $board_list_card->getBoardListCardUsers;
                Notification::send($notify_users, new BoardListNotification($board_list_card_activity, $notify_message));
            }
            return response()->json(['success' => $successMessage]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /** Move card api 3 end */
// TODO SCENARIO : Department -> Board List -> Board List Card (card or task)
//    *. New Board List Id =  Provided Task (Card) Board List (which we will fetch through board list card relation )
//          => Then we will check board list card position provided or not.
//              => If yes then we will check if the previous position is same.
//                  => If previous position is same then we will return task is already updated.
//                  => If previous position is not same then we will updated that card or task position and add increment to other cards in that board list.
//              => If not then we will use provided task ids.
//                  => Then we will check whether new board list all task list ids provided or not through current task ids and provided task list ids.
//                      => If yes then we will proceed from task list ids updating all new board list task list position according to the provided task list indexing.
//                      => If not then we will through error accordingly.
//    *. New Board List Id != Provided Task (Card) Board List (which we will fetch through board list card relation )
//          => Then we will check board list card position provided or not.
//              => If yes then we will add that card to that position and add increment to other cards >= provided position in new board list
//              => If not then we will use provided task ids.
//                  => Then we will check whether new board list all task list ids provided or not through current task ids and provided task list ids.
//                      => If yes then we will proceed from task list ids updating all new board list task list position according to the provided task list indexing.
//                      => If not then we will through error accordingly.


    public function change_client(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer|exists:board_list_cards,id',
                'client_id' => 'required|integer|exists:clients,id',
            ], [
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
                'client_id.required' => 'The client id field is required.',
                'client_id.exists' => 'The selected client is invalid.',
                'client_id.integer' => 'The client id must be an integer.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::where('id', $request->get('task_id'))->first();

            if (!$board_list_card) {
                return response()->json(['error' => 'Error! Task not found.'], 404);
            }

            $client = Client::where('id', $request->get('client_id'))->first();
            $notify_message = "updated card ( {$board_list_card->title} ) client " . ($board_list_card->getClient->name ?? "") . " to " .($client->name ?? "");


            $board_list_card->client_id = $request->get('client_id');

            if ($board_list_card->save()) {

                $board_list_card_activity = new BoardListCardActivity();
                $board_list_card_activity->board_list_card_id = $board_list_card->id;
                $board_list_card_activity->user_id = auth()->user()->id;
                $board_list_card_activity->activity = $notify_message;
                $board_list_card_activity->activity_type = 2;
                $board_list_card_activity->save();

                $notify_users = $board_list_card->getBoardListCardUsers;
                Notification::send($notify_users, new BoardListNotification($board_list_card_activity, $notify_message));

                $board_list_card->load('getBoardList', 'getClient', 'getTeam', 'getLabels', 'getLabels.color', 'getActivities.getUser', 'getActivities.getAttachmentWithTrashed', 'getActivities.getCommentWithTrashed', 'getAttachments', 'getComments', 'getBoardListCardUsers');
                return response()->json(['success' => 'Board list card client updated successfully.', 'board_list_card' => new BoardListCardResource($board_list_card), 'activity' => new BoardListCardActivityResource($board_list_card_activity)]);
            }
            return response()->json(['error' => 'Failed to update client.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }


    public function change_team(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer|exists:board_list_cards,id',
                'team_key' => 'required|integer|exists:teams,team_key',
            ], [
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
                'team_key.required' => 'The team key field is required.',
                'team_key.exists' => 'The selected team is invalid.',
                'team_key.integer' => 'The team key must be an integer.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::where('id', $request->get('task_id'))->first();

            if (!$board_list_card) {
                return response()->json(['error' => 'Error! Task not found.'], 404);
            }

            $team = Team::where('team_key', $request->get('team_key'))->first();
            $notify_message = "updated card ( {$board_list_card->title} ) team " . ($board_list_card->getTeam->name ?? "") . " to " . ($team->name ?? "");

            $board_list_card->team_key = $request->get('team_key');

            if ($board_list_card->save()) {

                $board_list_card_activity = new BoardListCardActivity();
                $board_list_card_activity->board_list_card_id = $board_list_card->id;
                $board_list_card_activity->user_id = auth()->user()->id;
                $board_list_card_activity->activity = $notify_message;
                $board_list_card_activity->activity_type = 2;
                $board_list_card_activity->save();

                $notify_users = $board_list_card->getBoardListCardUsers;
                Notification::send($notify_users, new BoardListNotification($board_list_card_activity, $notify_message));

                $board_list_card->load('getBoardList', 'getClient', 'getTeam', 'getLabels', 'getLabels.color', 'getActivities.getUser', 'getActivities.getAttachmentWithTrashed', 'getActivities.getCommentWithTrashed', 'getAttachments', 'getComments', 'getBoardListCardUsers');
                return response()->json(['success' => 'Task team updated successfully.', 'board_list_card' => new BoardListCardResource($board_list_card), 'activity' => new BoardListCardActivityResource($board_list_card_activity)]);
            }
            return response()->json(['error' => 'Failed to update team.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function task_completed(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|integer|exists:board_list_cards,id',
                'task_completed' => 'required|boolean',
            ], [
                'task_id.required' => 'The task id field is required.',
                'task_id.exists' => 'The selected task id is invalid.',
                'task_id.integer' => 'The task id must be an integer.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $board_list_card = BoardListCard::where('id', $request->get('task_id'))->first();

            if (!$board_list_card) {
                return response()->json(['error' => 'Error! Task not found.'], 404);
            }

            if ($board_list_card->task_completed == $request->get('task_completed')) {
                return response()->json(['error' => 'Error! Task completed status already updated.'], 404);
            }

            $board_list_card->task_completed = $request->get('task_completed');

            if ($board_list_card->save()) {

                $notify_message = "updated card ( {$board_list_card->title} )" . ($board_list_card->task_completed == 1 ? " to completed" : "to incomplete");

                $board_list_card_activity = new BoardListCardActivity();
                $board_list_card_activity->board_list_card_id = $board_list_card->id;
                $board_list_card_activity->user_id = auth()->user()->id;
                $board_list_card_activity->activity = "updated this card to " . ($board_list_card->task_completed == 1 ? "completed" : "incomplete");
                $board_list_card_activity->activity_type = 2;
                $board_list_card_activity->save();

                $notify_users = $board_list_card->getBoardListCardUsers;
                Notification::send($notify_users, new BoardListNotification($board_list_card_activity, $notify_message));

                $board_list_card->load('getBoardList', 'getClient', 'getTeam', 'getLabels', 'getLabels.color', 'getActivities.getUser', 'getActivities.getAttachmentWithTrashed', 'getActivities.getCommentWithTrashed', 'getAttachments', 'getComments', 'getBoardListCardUsers');

                return response()->json(['success' => 'Task completed status updated successfully.', 'board_list_card' => new BoardListCardResource($board_list_card), 'activity' => new BoardListCardActivityResource($board_list_card_activity)]);
            }
            return response()->json(['error' => 'Failed to updated Task completed status.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function search(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $rules = [
                'query' => 'required|string|max:255',
            ];

            $messages = [
                'query.required' => 'The search query is required.',
                'query.string' => 'The search query must be a string.',
                'query.max' => 'The search query may not be greater than 255 characters.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }


            $departmentsQuery = Department::where('status', 1)->whereNull('deleted_at');
            if (auth()->user()->type === 'executive') {
                $departmentsQuery->whereHas('getUsers', function ($query) {
                    $query->where('user_id', auth()->user()->id);
                });
            }

            $user_department_ids = $departmentsQuery->get(['id', 'name'])->pluck('id')->toArray();

            $selected_department_id = (int)$request->get('department_id');
            $default_department_id = $selected_department_id && in_array($selected_department_id, $user_department_ids)
                ? $selected_department_id
                : (!empty($user_department_ids) ? $user_department_ids->pluck('id')->first() : 2);

            $board_lists = BoardList::where('status', 1)->whereNull('deleted_at')->whereHas('getDepartment', function ($query) use ($default_department_id) {
                $query->where('department_id', $default_department_id);
            })
                ->orderBy('position')
                ->get(['id', 'title']);
            $board_list_ids = $board_lists->pluck('id')->toArray();

            $query = $request->get('query');
            $team_key = $request->get('team_key');
            $board_list_cards = BoardListCard::where('title', 'like', "%{$query}%")->where(function ($q) use ($team_key) {
                if ($team_key) {
                    $q->where('team_key', $team_key);
                }
            })->where(function ($query) use ($board_list_ids) {
                $query->whereIn('board_list_id', $board_list_ids);
            })->whereNotNull('board_list_id')->with('getBoardList.getDepartment:departments.id,departments.name')->get(['id', 'title', 'description', 'cover_image', 'board_list_id']);

            $board_list_cards = $board_list_cards->map(function ($card) {
                $boardList = $card->getBoardList;
                $department = optional($boardList)->getDepartment;

                return [
                    'id' => $card->id,
                    'title' => $card->title,
                    'description' => $card->description,
                    'cover_image_thumbnail' => "150x150/{$card->cover_image}",
//                    'cover_image_thumbnail' => $this->cover_image_url_trait($card, 'thumbnail'),
                    'board_list' => [
                        'id' => optional($boardList)->id,
                        'title' => optional($boardList)->title,
                        'department' => [
                            'id' => optional($department)->id,
                            'name' => optional($department)->name
                        ],
                    ],
                ];
            });
            return response()->json(['success' => 'Board card fetched successfully.', 'cards' => $board_list_cards, 'cards_count' => count($board_list_cards)], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function delete(Request $request): ?\Illuminate\Http\JsonResponse
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
            $board_list_card_id = $request->get('task_id');
            $board_list_card = BoardListCard::where('id', $board_list_card_id)->first();
            if (!$board_list_card) {
                return response()->json(['error' => 'Error! Task not found.'], 404);
            }
            DB::beginTransaction();
            $board_list_card->delete();
            $notify_message = "deleted card ( {$board_list_card->title} )";
            $board_list_card_activity = new BoardListCardActivity();
            $board_list_card_activity->board_list_card_id = $board_list_card_id;
            $board_list_card_activity->user_id = auth()->user()->id;
            $board_list_card_activity->activity = "deleted this card.";
            $board_list_card_activity->activity_type = 2;
            $board_list_card_activity->save();
            $notify_users = $board_list_card->getBoardListCardUsers;
            Notification::send($notify_users, new BoardListNotification($board_list_card_activity, $notify_message));
            DB::commit();
            return response()->json(['success' => 'Task deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function restore(Request $request): ?\Illuminate\Http\JsonResponse
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
            $board_list_card_id = $request->get('task_id');
            BoardListCard::onlyTrashed()->whereId($board_list_card_id)->restore();
            $board_list_card = BoardListCard::whereId($board_list_card_id)->first();
            if (!$board_list_card) {
                return response()->json(['error' => 'Error! Task not found.'], 404);
            }
            DB::beginTransaction();
            $notify_message = "restore card ( {$board_list_card->title} )";
            $board_list_card_activity = new BoardListCardActivity();
            $board_list_card_activity->board_list_card_id = $board_list_card_id;
            $board_list_card_activity->user_id = auth()->user()->id;
            $board_list_card_activity->activity = "restore this card.";
            $board_list_card_activity->activity_type = 2;
            $board_list_card_activity->save();
            $notify_users = $board_list_card->getBoardListCardUsers;
            Notification::send($notify_users, new BoardListNotification($board_list_card_activity, $notify_message));
            DB::commit();
            return response()->json(['success' => 'Task restored successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

}
