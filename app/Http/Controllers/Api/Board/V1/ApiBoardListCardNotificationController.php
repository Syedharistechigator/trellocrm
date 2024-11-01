<?php

namespace App\Http\Controllers\Api\Board\V1;

use App\Http\Controllers\Controller;
use App\Models\BoardListCard;
use App\Models\BoardListCardComment;
use App\Models\User;
use App\Notifications\BoardListNotification;
use App\Traits\BoardListCardCoverImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiBoardListCardNotificationController extends Controller
{
    use BoardListCardCoverImageTrait;

    public function index(Request $request)
    {
        try {
            $per_page = $request->input('per_page', 15);
            $offset = $request->input('offset', 0);
            $query = auth()->user()->notifications()->where('type', BoardListNotification::class);
            if ($per_page) {
                if (!is_null($offset)) {
                    $notifications = $query->offset($offset)->limit($per_page)->get();
                    $total_notifications = $query->count();

                    $pagination_info = [
                        'total' => $total_notifications,
                        'current' => count($notifications),
                        'per_page' => (int)$per_page,
                        'offset' => (int)$offset,
                        'next_offset' => ($offset + $per_page < $total_notifications) ? $offset + $per_page : null,
                        'prev_offset' => ($offset - $per_page >= 0) ? $offset - $per_page : null,
                    ];
                } else {
                    $notifications = $query->paginate($per_page);

                    $pagination_info = [
                        'total' => $notifications->total(),
                        'current' => $notifications->count(),
                        'per_page' => $notifications->perPage(),
                        'current_page' => $notifications->currentPage(),
                        'last_page' => $notifications->lastPage(),
                        'next_page_url' => $notifications->nextPageUrl(),
                        'prev_page_url' => $notifications->previousPageUrl(),
                    ];
                }
            } else {
                $notifications = $query->get();
                $pagination_info = null;
            }

            $notifications_data = $notifications->map(function ($notification) {
                $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true, 512, JSON_THROW_ON_ERROR);
                $notification_data = [
                    'id' => $notification->id,
                    'code' => null,
                    'card' => null,
                    'activity_user' => null,
                    'activity' => $data['activity'] ?? null,
                    'message' => $data['message'] ?? null,
                    'read_at' => $notification->read_at ? $notification->read_at->diffForHumans() : null,
                    'created_at' => $notification->created_at ? $notification->created_at->diffForHumans() : null,
                ];
                if (isset($data['board_list_card_id'])) {
                    $board_list_card = BoardListCard::find($data['board_list_card_id']);
                    if ($board_list_card) {
                        $notification_data['card'] = [
                            'id' => $board_list_card->id,
                            'code' => $this->encrypt_2($board_list_card->id),
                            'title' => $board_list_card->title,
                            'description' => $board_list_card->description,
                            'cover_image_thumbnail' =>  "150x150/{$board_list_card->cover_image}",
//                            'cover_image_thumbnail' => $this->cover_image_url_trait($board_list_card, 'thumbnail'),
                            'board_list' => $board_list_card->getBoardList ? [
                                'id' => $board_list_card->getBoardList->id,
                                'title' => $board_list_card->getBoardList->title
                            ] : null
                        ];
                    }
                }
                if (!empty($data['user_id'])) {
                    $user = User::find($data['user_id']);
                    $notification_data['activity_user'] = $user ? $user->only(['id', 'name', 'email', 'type', 'image']) : null;
                }
                return $notification_data;
            });
            $user_notifications = auth()->user()->notifications()->whereType(BoardListNotification::class);
            $total_notifications_count = $user_notifications->count();
            $unread_notifications_count = (clone $user_notifications)->whereNull('read_at')->count();
            $read_notifications_count = (clone $user_notifications)->whereNotNull('read_at')->count();

            return response()->json([
                'success' => 'Board card notifications fetched successfully.',
                'notifications' => $notifications_data,
                'pagination' => $pagination_info,
                'total_notifications_count' => $total_notifications_count,
                'unread_notifications_count' => $unread_notifications_count,
                'read_notifications_count' => $read_notifications_count
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch notifications', 'message' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function mark_as_read(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $rules = [
                'ids' => 'required|array',
                'ids.*' => 'exists:notifications,id',
            ];
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $notificationIds = $request->input('ids');
            $notifications = auth()->user()->unreadNotifications->where('type', BoardListNotification::class)->whereIn('id', $notificationIds);
            if ($notifications->isEmpty()) {
                return response()->json(['error' => 'No notifications found or all notifications are already read.'], 404);
            }
            foreach ($notifications as $notification) {
                $notification->markAsRead();
            }
            return response()->json(['success' => 'Notifications marked as read.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to mark notifications as read', 'message' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function mark_all_as_read(): ?\Illuminate\Http\JsonResponse
    {
        try {
            $notifications = auth()->user()->unreadNotifications->where('type', BoardListNotification::class);
            if ($notifications->isEmpty()) {
                return response()->json(['error' => 'No notifications found or all notifications are already read.'], 404);
            }
            $notifications->markAsRead();
            return response()->json(['success' => 'All Notifications marked as read.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to mark notifications as read', 'message' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }
}
