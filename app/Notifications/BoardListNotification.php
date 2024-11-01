<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BoardListNotification extends Notification
{
    use Queueable;

    public $data;
    public $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data,$message)
    {
        $this->data = $data;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'id' => $this->data['id'],
            'user_id' => $this->data['user_id'],
            'board_list_card_id' => $this->data['board_list_card_id'],
            'activity' => $this->data['activity'],
            'message' => $this->message,
            'activity_type' => $this->data['activity_type'],
            'deleted_at' => $this->data['deleted_at'],
            'created_at' => $this->data['created_at'],
            'updated_at' => $this->data['updated_at'],
        ];
    }
}
