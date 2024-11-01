<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadPushNotification extends Notification
{
    use Queueable;
    public $lead;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($lead)
    {
        $this->lead = $lead;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

   
    public function toArray($notifiable)
    {
        return [
            'team_key' => $this->lead['team_key'],
            'brand_key' => $this->lead['brand_key'],
            'title' => $this->lead['title'],
            'name' => $this->lead['name'],
            'email' => $this->lead['email'],
            'phone' => $this->lead['phone'],
            'details' => $this->lead['details'],
            'source' => $this->lead['source'],
            'value' => $this->lead['value']
        ];
    }
}
