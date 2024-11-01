<?php

namespace App\Http\View\Composers;

use App\Models\BoardListCard;
use App\Models\BoardListCardAttachment;
use App\Models\BoardListCardComment;
use App\Models\User;
use App\Notifications\BoardListNotification;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view)
    {
        $webuser = Auth::guard('web')->user();
        $admin = Auth::guard('admin')->user();
        $user = $webuser ?? $admin;
        if (!$user) {
            $view->with(['unreadNotifications' => collect(), 'unreadBoardNotifications' => collect()]);
            return;
        }
        $unreadNotifications = ($user->unreadNotifications ?? collect())->filter(function ($notification) use ($user) {
            $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true, 512, JSON_THROW_ON_ERROR);
            return !empty($data['user_id']);
        })->map(function ($notification) {
            $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true, 512, JSON_THROW_ON_ERROR);
            if ($notification->type === BoardListNotification::class) {
                if (isset($data['board_list_card_id'])) {
                    $board_list_card = BoardListCard::find($data['board_list_card_id'])?->toArray();
                    $notification->board_list_card = $board_list_card ?? null;
                }
                if (isset($notification->data['id'])) {
                    $board_list_card = BoardListCard::find($data['board_list_card_id'])?->toArray();
                    $notification->board_list_card = $board_list_card ?? null;
                    if($notification->data['activity_type'] == 0){
                        $board_list_card_comment = BoardListCardComment::where('activity_id',$data['id'])->first()?->toArray();
                        $notification->board_list_card_comment = $board_list_card_comment ?? null;
                    }
//                    elseif($notification->data['activity_type'] == 1){
//                        $board_list_card_attachment = BoardListCardAttachment::where('activity_id',$data['id'])->first()?->toArray();
//                        $notification->board_list_card_attachment = $board_list_card_attachment ?? null;
//                    }
                }
            }
            if (!empty($data['user_id'])) {
                $user = User::find($data['user_id']);
                $notification->user = $user ? $user->only(['id', 'name', 'email', 'type', 'image']) : null;
            }
            return $notification;
        }) ?? collect();
        $unreadBoardNotifications = $unreadNotifications->where('type', BoardListNotification::class);
        $view->with(['unreadNotifications' => $unreadNotifications, 'unreadBoardNotifications' => $unreadBoardNotifications]);
    }
}
