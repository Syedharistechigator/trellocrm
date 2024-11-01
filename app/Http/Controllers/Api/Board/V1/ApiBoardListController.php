<?php

namespace App\Http\Controllers\Api\Board\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BoardListResource;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ColorResource;
use App\Http\Resources\UserResource;
use App\Models\AssignBrand;
use App\Models\BoardList;
use App\Models\BoardListCard;
use App\Models\Client;
use App\Models\Color;
use App\Models\User;
use App\Notifications\BoardListNotification;
use App\Traits\BoardListCardCoverImageTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiBoardListController extends Controller
{
    use BoardListCardCoverImageTrait;

//    public function index(Request $request)
//    {
//        try {
//            $board_lists = BoardList::where('status', 1)
//                ->with([
//                    'getBoardListCards' => function ($query) {
//                        $query->select('id', 'board_list_id', 'title', 'description', 'cover_image', 'cover_background_color', 'priority', 'start_date', 'due_date')
//                            ->orderBy('position');
//                    },
//                    'getBoardListCards.getBoardListCardUsers:users.id,assign_board_cards.board_list_card_id,name,email',
//                ])
//                ->get(['id', 'title']);
//            $clients = Client::where('status', 1)->get(['id', 'name', 'email']);
//            $users = User::where('type', '!=', 'client')->where('status', 1)->get(['id', 'name', 'email']);
//            $colors = Color::all();
//
//            $board_lists = BoardListResource::collection($board_lists);
//            $clients = ClientResource::collection($clients);
//            $users = UserResource::collection($users);
//            $colors = ColorResource::collection($colors);
//
//            return response()->json([
//                'board_lists' => $board_lists,
//                'clients' => $clients,
//                'users' => $users,
//                'colors' => $colors,
//            ], 200);
//        } catch (\Exception $e) {
//            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
//        }
//    }
    public function index(Request $request)
    {
        try {
            $users = DB::select(DB::raw("SELECT id, name, email, image, type FROM users WHERE type IN ('lead','staff','executive','pcc' , 'third-party-user') AND status = 1 AND deleted_at IS NULL;"));


            $all_departments_query = "SELECT id, name, background_image FROM departments WHERE status = 1 AND deleted_at IS NULL ";
            $teams = DB::select(DB::raw("SELECT id, team_key, name FROM teams WHERE status = 1 AND deleted_at IS NULL;"));
            $colors = DB::select(DB::raw("SELECT id, color_name AS color_name, color_value AS color_value, color_position AS color_position FROM colors;"));
            $auth_user = auth()->user();
            $user_departments_parameters = [];
            $department_query = "SELECT id, name, background_image FROM departments WHERE status = 1 AND deleted_at IS NULL";
            $client_query = "SELECT id, name, email , phone, CONCAT(name, ' - ',
                  SUBSTRING(email, 1, 3), '****', SUBSTRING(email, -3), ' - ',
                  SUBSTRING(phone, 1, 3), '****', SUBSTRING(phone, -3)
           ) AS combine FROM clients WHERE (status = 1 AND deleted_at IS NULL";
            if (in_array($auth_user->type, ['executive', 'ppc', 'third-party-user'])) {
                $department_query .= " AND EXISTS ( SELECT 1 FROM assign_department_users WHERE department_id = departments.id AND user_id = :user_id )";
                $user_departments_parameters['user_id'] = $auth_user->id;
                $client_query .= " AND id = 1";
            } elseif ($auth_user->type === 'lead' || $auth_user->type === 'staff') {
                $assign_brands = DB::select(DB::raw("SELECT ab.brand_key FROM assign_brands ab INNER JOIN brands b ON ab.brand_key = b.brand_key WHERE ab.team_key = :team_key AND b.status = 1 AND b.deleted_at IS NULL"), ['team_key' => $auth_user->team_key,]);
                $assign_brand_keys = array_column($assign_brands, 'brand_key');
                if (!empty($assign_brand_keys)) {
                    $client_query .= " AND brand_key IN (" . implode(',', array_map(static function ($key) {
                            return "'" . $key . "'";
                        }, $assign_brand_keys)) . ")";
                } else {
                    $client_query .= " AND 0 = 1";
                }

                $department_query .= " AND id NOT IN (3,7,20)";
                $all_departments_query .= " AND id NOT IN (3,7,20)";
            }
            
            $department_query .= " ORDER BY `order` ";
            $all_departments_query .= " ORDER BY `order` ";

            $all_departments = DB::select(DB::raw($all_departments_query));
            $client_query .= " ) OR id = 1";
            $user_departments = DB::select(DB::raw($department_query), $user_departments_parameters);
            $clients = DB::select(DB::raw($client_query));
            $user_department_ids = array_column($user_departments, 'id');
            $selected_department_id = (int)$request->get('department_id');
            $selected_team_key = $request->get('team_key', $auth_user->type === 'staff' ? $auth_user->team_key : null);
            if ($selected_department_id) {
                if (in_array($selected_department_id, $user_department_ids, true)) {
                    $default_department_id = $selected_department_id;
                } else {
                    $default_department_id = !empty($user_department_ids) ? $user_department_ids[0] : 0;
                }
            } else {
                $default_department_id = !empty($user_department_ids) ? $user_department_ids[0] : 0;
            }
            $board_list_parameters = ['department_id' => $default_department_id];
            $board_list_query = "SELECT bl.id AS board_list_id, bl.title AS board_list_title
                     FROM board_lists AS bl
                     WHERE bl.status = 1 AND bl.deleted_at IS NULL
                     AND bl.id IN (
                         SELECT adb.board_list_id
                         FROM assign_department_board_lists AS adb
                         WHERE adb.department_id = :department_id
                     )
                     ORDER BY bl.position;";
            $board_list_results = DB::select(DB::raw($board_list_query), $board_list_parameters);
            $board_list_ids = array_column($board_list_results, 'board_list_id');
            $activityTimezone = 'Asia/Karachi';
            $now_karachi = Carbon::now($activityTimezone);
            $board_lists = [];
            $board_list_cards = [];
            foreach ($board_list_results as $board_list) {
                $board_list_id = $board_list->board_list_id;
                $card_parameters = ['board_list_id' => $board_list_id];
                $card_query = "SELECT blc.id AS card_id,blc.team_key, blc.client_id, blc.title AS card_title, blc.description AS card_description, blc.cover_image,blc.cover_image_updated_at, blc.cover_background_color, blc.priority, blc.start_date, blc.due_date, blc.task_completed, blc.position, adb.department_id FROM board_list_cards AS blc LEFT JOIN assign_department_board_lists AS adb ON blc.board_list_id = adb.board_list_id LEFT JOIN departments AS d ON adb.department_id = d.id AND d.deleted_at IS NULL AND d.status = 1 WHERE blc.board_list_id = :board_list_id AND blc.status = 1 AND blc.deleted_at IS NULL";
                if ($selected_team_key) {
                    $card_query .= " AND blc.team_key = :team_key";
                    $card_parameters['team_key'] = $selected_team_key;
                }
                if ($auth_user->type === 'staff' && !$auth_user->team_key) {
                    $card_query .= " AND 0 = 1";
                }
                $card_query .= " ORDER BY blc.position ";
                $card_query .= " LIMIT 20;";
                $card_results = DB::select(DB::raw($card_query), $card_parameters);
                $cards = [];
                $card_ids_array = [];
                foreach ($card_results as $card) {
                    $card_ids_array[] = $card_id = $card->card_id;
                    $assigned_user_results = DB::select(DB::raw("SELECT u.id, u.name, u.email, u.image, u.type FROM users AS u LEFT JOIN assign_board_cards AS abc ON u.id = abc.user_id WHERE abc.board_list_card_id = :card_id AND u.type != 'client' AND u.status = 1 AND u.deleted_at IS NULL;"), ['card_id' => $card_id]);
                    $assigned_users = [];
                    $assigned_user_ids = [];
                    foreach ($assigned_user_results as $assigned_user) {
                        $assigned_user_ids[] = $assigned_user->id;
                        $assigned_users[] = ['id' => $assigned_user->id, 'name' => $assigned_user->name, 'email' => $assigned_user->email, 'image' => $this->userImageUrl($assigned_user->image), 'type' => $assigned_user->type];
                    }
                    $unassigned_user_results = array_filter($users, function ($user) use ($assigned_user_ids) {
                        return !in_array($user->id, $assigned_user_ids);
                    });
                    $unassigned_users = [];
                    foreach ($unassigned_user_results as $unassigned_user) {
                        $unassigned_users[] = ['id' => $unassigned_user->id, 'name' => $unassigned_user->name, 'email' => $unassigned_user->email, 'image' => $this->userImageUrl($unassigned_user->image), 'type' => $unassigned_user->type];
                    }
                    $attachmentsCount = DB::select(DB::raw("SELECT COUNT(*) AS count FROM board_list_card_attachments WHERE board_list_card_id = :card_id AND deleted_at IS NULL;"), ['card_id' => $card_id]);
                    $commentsCount = DB::select(DB::raw("SELECT COUNT(*) AS count FROM board_list_card_comments WHERE board_list_card_id = :card_id AND deleted_at IS NULL;"), ['card_id' => $card_id]);
                    $cardData = [
                        'id' => $card_id,
                        'team_key' => $card->team_key,
                        'title' => $card->card_title,
                        'description' => $card->card_description,
                        'cover_image' => $card->cover_image ? "original/{$card->cover_image}" : null,
//                        'cover_image' => $this->cover_image_url_trait($card),
                        'cover_image_updated_at' => $card->cover_image_updated_at,
                        'cover_time_difference' => $card->cover_image_updated_at ? $now_karachi->diffInMinutes(Carbon::parse($card->cover_image_updated_at)->timezone($activityTimezone)->subHours(15)) : null,
                        'cover_image_thumbnail' => $card->cover_image ? "150x150/{$card->cover_image}" : null,
//                        'cover_image_thumbnail' => $this->cover_image_url_trait($card, 'thumbnail'),
                        'cover_background_color' => $card->cover_background_color,
                        'priority' => $card->priority,
                        'start_date' => $card->start_date,
                        'due_date' => $card->due_date,
                        'task_completed' => $card->task_completed,
                        'position' => $card->position,
                        'department_id' => $card->department_id,
                        'assigned_users' => $assigned_users,
                        'assigned_users_count' => count($assigned_user_results),
                        'unassigned_users' => $unassigned_users,
                        'unassigned_users_count' => count($unassigned_user_results),
                        'attachments_count' => optional($attachmentsCount[0])->count ?? 0,
                        'comments_count' => optional($commentsCount[0])->count ?? 0,
                        'code' => $this->encrypt_2($card_id),
                    ];
                    $cards[] = $cardData;
                }
                $board_lists[] = ['id' => $board_list_id, 'title' => $board_list->board_list_title, 'cards' => $cards, 'cards_count' => count($cards), 'card_ids' => $card_ids_array];
                /** Search*/
                $board_list_cards_fetch = BoardListCard::where(function ($query) use ($selected_team_key, $auth_user) {
                    if ($selected_team_key) {
                        $query->where('team_key', $selected_team_key);
                    }
                    if ($auth_user->type === 'staff' && !$auth_user->team_key) {
                        $query->whereRaw('0 = 1');
                    }
                })->whereIn('board_list_id', $board_list_ids)->with('getBoardList.getDepartment:departments.id,departments.name')->orderBy('title')->withTrashed()->get(['id', 'code', 'team_key', 'title', 'description', 'cover_image', 'board_list_id', 'trello_url', 'deleted_at']);
                $board_list_cards = $board_list_cards_fetch->map(function ($card) {
                    $boardList = $card->getBoardList;
                    $department = optional($boardList)->getDepartment;
                    $data = [
                        'id' => $card->id,
                        'team_key' => $card->team_key,
                        'title' => $card->title,
                        // 'description' => $card->description,
//                        'cover_image_thumbnail' => $this->cover_image_url_trait($card, 'thumbnail'),
                        'cover_image_thumbnail' => "150x150/{$card->cover_image}",
                        'cover_image_updated_at' => $card->cover_image_updated_at,
                        'trello_url' => $card->trello_url,
                        'board_list' => [
                            'id' => optional($boardList)->id,
                            'title' => optional($boardList)->title,
                            'department' => [
                                'id' => optional($department)->id,
                                'name' => optional($department)->name
                            ],
                        ],
                        'code' => $card->code ?? $this->encrypt_2($card->id),
                    ];
                    if ($card->deleted_at) {
                        $data['deleted_at'] = $card->deleted_at;
                    }
                    return $data;
                });
            }
            return response()->json(['default_department_id' => $default_department_id, 'all_departments' => $all_departments, 'all_departments_count' => count($all_departments), 'departments' => $user_departments, 'departments_count' => count($user_departments), 'teams' => $teams, 'teams_count' => count($teams), 'board_lists' => $board_lists, 'board_lists_count' => count($board_lists), 'clients' => $clients, 'clients_count' => count($clients), 'users' => $users, 'users_count' => count($users), 'colors' => $colors, 'colors_count' => count($colors), 'cards' => $board_list_cards, 'cards_count' => count($board_list_cards), 'unread_notifications_count' => auth()->user()->unreadNotifications->where('type', BoardListNotification::class)->count()], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }


    public function scroll_board_list(Request $request, $id = null): ?\Illuminate\Http\JsonResponse
    {
        try {
            if (!$id) {
                return response()->json(['error' => 'Board list id is required.'], 400);
            }
            $users = DB::select(DB::raw("SELECT id, name, email, image, type FROM users WHERE type IN ('lead','staff','executive','pcc' ,'third-party-user') AND status = 1 AND deleted_at IS NULL;"));
            $auth_user = auth()->user();
            $selected_team_key = $request->get('team_key', $auth_user->type === 'staff' ? $auth_user->team_key : null);
            $offset = $request->input('offset', 0);
            $board_list = DB::select(DB::raw("SELECT id AS board_list_id, title AS board_list_title FROM board_lists WHERE id = :id AND status = 1 AND deleted_at IS NULL;"), ['id' => $id]);
            if (count($board_list) === 0) {
                return response()->json(['error' => 'Oops! Board list not found.'], 404);
            }
            $board_list = $board_list[0];
            $card_parameters = ['board_list_id' => $board_list->board_list_id, 'offset' => $offset];
            $card_query = "SELECT blc.id AS card_id, blc.team_key , blc.client_id, blc.title AS card_title, blc.description AS card_description, blc.cover_image, blc.cover_image_updated_at, blc.cover_background_color, blc.priority, blc.start_date, blc.due_date, blc.task_completed , blc.position , blc.deleted_at , adb.department_id FROM board_list_cards AS blc LEFT JOIN assign_department_board_lists AS adb ON blc.board_list_id = adb.board_list_id LEFT JOIN departments AS d ON adb.department_id = d.id AND d.deleted_at IS NULL AND d.status = 1 WHERE blc.board_list_id = :board_list_id AND blc.status = 1 AND blc.deleted_at IS NULL ";
            if ($selected_team_key) {
                $card_query .= " AND blc.team_key = :team_key";
                $card_parameters['team_key'] = $selected_team_key;
            }
            if ($auth_user->type === 'staff' && !$auth_user->team_key) {
                $card_query .= " AND 0 = 1";
            }
            $card_query .= " ORDER BY blc.position LIMIT 20 OFFSET :offset ;";
            $card_results = DB::select(DB::raw($card_query), $card_parameters);
            $activityTimezone = 'Asia/Karachi';
            $now_karachi = Carbon::now($activityTimezone);
            $cards = [];
            $card_ids_array = [];
            foreach ($card_results as $card) {
                $card_ids_array[] = $card_id = $card->card_id;
                $assigned_user_results = DB::select(DB::raw("SELECT u.id, u.name, u.email, u.image, u.type FROM users AS u LEFT JOIN assign_board_cards AS abc ON u.id = abc.user_id WHERE abc.board_list_card_id = :card_id AND u.type != 'client' AND u.status = 1 AND u.deleted_at IS NULL;"), ['card_id' => $card_id]);
                $assigned_users = [];
                $assigned_user_ids = [];
                foreach ($assigned_user_results as $assigned_user) {
                    $assigned_user_ids[] = $assigned_user->id;
                    $assigned_users[] = ['id' => $assigned_user->id, 'name' => $assigned_user->name, 'email' => $assigned_user->email, 'image' => $this->userImageUrl($assigned_user->image), 'type' => $assigned_user->type];
                }
                $unassigned_user_results = array_filter($users, function ($user) use ($assigned_user_ids) {
                    return !in_array($user->id, $assigned_user_ids);
                });
                $unassigned_users = [];
                foreach ($unassigned_user_results as $unassigned_user) {
                    $unassigned_users[] = ['id' => $unassigned_user->id, 'name' => $unassigned_user->name, 'email' => $unassigned_user->email, 'image' => $this->userImageUrl($unassigned_user->image), 'type' => $unassigned_user->type];
                }
                $attachmentsCount = DB::select(DB::raw("SELECT COUNT(*) AS count FROM board_list_card_attachments WHERE board_list_card_id = :card_id AND deleted_at IS NULL;"), ['card_id' => $card_id]);
                $commentsCount = DB::select(DB::raw("SELECT COUNT(*) AS count FROM board_list_card_comments WHERE board_list_card_id = :card_id AND deleted_at IS NULL;"), ['card_id' => $card_id]);
                $cardData = [
                    'id' => $card_id,
                    'team_key' => $card->team_key,
                    'title' => $card->card_title,
                    'description' => $card->card_description,
//                    'cover_image' => $this->cover_image_url_trait($card),
                    'cover_image' => $card->cover_image ? "original/{$card->cover_image}" : null,
                    'cover_image_updated_at' => $card->cover_image_updated_at,
                    'cover_time_difference' => $card->cover_image_updated_at ? $now_karachi->diffInMinutes(Carbon::parse($card->cover_image_updated_at)->timezone($activityTimezone)->subHours(15)) : null,
//                    'cover_image_thumbnail' => $this->cover_image_url_trait($card, 'thumbnail'),
                    'cover_image_thumbnail' => $card->cover_image ? "150x150/{$card->cover_image}" : null,
                    'cover_background_color' => $card->cover_background_color,
                    'priority' => $card->priority,
                    'start_date' => $card->start_date,
                    'due_date' => $card->due_date,
                    'task_completed' => $card->task_completed,
                    'position' => $card->position,
                    'department_id' => $card->department_id,
                    'assigned_users' => $assigned_users,
                    'assigned_users_count' => count($assigned_user_results),
                    'unassigned_users' => $unassigned_users,
                    'unassigned_users_count' => count($unassigned_user_results),
                    'attachments_count' => optional($attachmentsCount[0])->count ?? 0,
                    'comments_count' => optional($commentsCount[0])->count ?? 0,
                    'code' => $this->encrypt_2($card_id),
                ];
                if ($card->deleted_at !== null) {
                    $cardData['deleted_at'] = $card->deleted_at;
                }
                $cards[] = $cardData;
            }
            return response()->json(['cards' => $cards, 'cards_count' => count($cards), 'card_ids' => $card_ids_array], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

//    public function index(Request $request)
//    {
//        try {
//            $boardListsResult = DB::select(DB::raw("SELECT bl.id AS board_list_id,bl.title AS board_list_title,blc.id AS card_id,blc.client_id AS client_id,blc.title AS card_title,blc.description AS card_description,blc.cover_image,blc.cover_background_color,blc.priority,blc.start_date,blc.due_date,u.id AS user_id,u.name AS user_name,u.email AS user_email,u.image AS user_image,u.type AS user_type, adb.department_id AS department_id FROM board_lists AS bl LEFT JOIN board_list_cards AS blc ON bl.id = blc.board_list_id LEFT JOIN assign_board_cards AS abc ON blc.id = abc.board_list_card_id LEFT JOIN users AS u ON abc.user_id = u.id LEFT JOIN assign_department_board_lists AS adb ON bl.id = adb.board_list_id WHERE bl.status = 1 AND bl.deleted_at IS NULL AND blc.status = 1 AND blc.deleted_at IS NULL AND u.status = 1 AND u.deleted_at IS NULL ORDER BY blc.position;"));
//            $boardLists = [];
//            $cards = [];
//            $card_id = null;
//            foreach ($boardListsResult as $row) {
//                $boardListId = $row->board_list_id;
//                if (!isset($boardLists[$boardListId])) {
//                    $boardLists[$boardListId] = ['id' => $boardListId, 'title' => $row->board_list_title, 'cards' => []];
//                }
//                if ($row->card_id && $card_id != $row->card_id) {
//                    $card_id = $row->card_id;
//                    if (!isset($cards[$card_id])) {
//                        $cards[$card_id] = ['id' => $card_id, 'title' => $row->card_title, 'description' => $row->card_description, 'cover_image' => $this->coverImageUrl($row), 'cover_background_color' => $row->cover_background_color, 'priority' => $row->priority, 'start_date' => $row->start_date, 'due_date' => $row->due_date,'department_id' => $row->department_id, 'assigned_users' => [], 'assigned_users_count' => 0, 'unassigned_users' => [], 'unassigned_users_count' => 0, 'attachments_count' => 0, 'comments_count' => 0, 'code' => $this->encrypt_2($card_id)];
//                    }
//                    if ($row->user_id) {
//                        $cards[$card_id]['assigned_users'][] = ['id' => $row->user_id, 'name' => $row->user_name, 'email' => $row->user_email, 'image' => $this->userImageUrl($row->user_image), 'type' => $row->user_type,];
//                        $cards[$card_id]['assigned_users_count']++;
//                    }
//                    $boardLists[$boardListId]['cards'][] = $cards[$card_id];
//                }
//            }
//            $boardLists = array_values($boardLists);
//            foreach ($boardLists as &$boardList) {
//                foreach ($boardList['cards'] as &$card) {
//                    $assignedUsers = DB::select(DB::raw("SELECT u.id,u.name,u.email,u.image,u.type FROM users u LEFT JOIN assign_board_cards abc ON u.id = abc.user_id AND abc.board_list_card_id = :card_id LEFT JOIN board_list_cards blc ON abc.board_list_card_id = blc.id WHERE u.type != 'client' AND u.status = 1 AND u.deleted_at IS NULL AND abc.user_id IS NOT NULL AND blc.deleted_at IS NULL;"), ['card_id' => $card['id']]);
//                    $card['assigned_users'] = array_map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email, 'image' => $this->userImageUrl($u->image), 'type' => $u->type], $assignedUsers);
//                    $card['assigned_users_count'] = count($assignedUsers);
//                    $unassignedUsers = DB::select(DB::raw("SELECT u.id, u.name, u.email, u.image, u.type FROM users u LEFT JOIN assign_board_cards abc ON u.id = abc.user_id AND abc.board_list_card_id = :card_id LEFT JOIN board_list_cards blc ON abc.board_list_card_id = blc.id WHERE u.type != 'client' AND u.status = 1 AND u.deleted_at IS NULL AND abc.user_id IS NULL AND blc.deleted_at IS NULL;"), ['card_id' => $card['id']]);
//                    $card['unassigned_users'] = array_map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email, 'image' => $this->userImageUrl($u->image), 'type' => $u->type], $unassignedUsers);
//                    $card['unassigned_users_count'] = count($unassignedUsers);
//                    $attachmentsCount = DB::select(DB::raw("SELECT COUNT(*) AS count FROM board_list_card_attachments al LEFT JOIN board_list_cards blc ON al.board_list_card_id = blc.id WHERE board_list_card_id = :card_id AND al.deleted_at IS NULL AND blc.deleted_at IS NULL;"), ['card_id' => $card['id']]);
//                    $card['attachments_count'] = $attachmentsCount[0]->count;
//                    $commentsCount = DB::select(DB::raw("SELECT COUNT(*) AS count FROM board_list_card_comments ac LEFT JOIN board_list_cards blc ON ac.board_list_card_id = blc.id WHERE board_list_card_id = :card_id AND ac.deleted_at IS NULL AND blc.deleted_at IS NULL;"), ['card_id' => $card['id']]);
//                    $card['comments_count'] = $commentsCount[0]->count;
//                }
//            }
//            $clients = DB::select(DB::raw("SELECT id, name, email FROM clients WHERE status = 1 AND deleted_at IS NULL;"));
//            $users = DB::select(DB::raw("SELECT id, name, email FROM users WHERE type != 'client' AND status = 1 AND deleted_at IS NULL;"));
//            $colors = DB::select(DB::raw("SELECT id, color_name AS color_name, color_value AS color_value, color_position AS color_position FROM colors;"));
//            $departments = DB::select(DB::raw("SELECT id, name FROM departments WHERE status = 1 AND deleted_at IS NULL;"));
//            $teams = DB::select(DB::raw("SELECT id,team_key, name FROM teams WHERE status = 1 AND deleted_at IS NULL;"));
//
//            return response()->json(['departments' => $departments, 'teams' => $teams, 'board_lists' => $boardLists, 'clients' => $clients, 'users' => $users, 'colors' => $colors,], 200);
//        } catch (\Exception $e) {
//            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
//        }
//    }
    public function encrypt_2($board_list_card_id)
    {
        return rtrim(base64_encode("DM" . $board_list_card_id . "CARD"), '=');
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
}
