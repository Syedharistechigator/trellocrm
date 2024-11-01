<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadNotification extends Notification implements ShouldQueue
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->greeting('Hello Admin!')
                    ->line('Received New Lead:')
                    ->line('Lead Title: '.$this->lead['title'])
                    ->line('Contact Name: '.$this->lead['name'])
                    ->line('Email: '.$this->lead['email'])
                    ->line('Phone: '.$this->lead['phone'])
                    ->line('Details: '.$this->lead['details'])
                    ->line('Lead Source: '.$this->lead['source'])
                    ->line('Lead Value: $'.$this->lead['value'].'/-')
                    ->line('Brand Name: '.$this->lead['brand_name'])
                    ->line('Team Name: '.$this->lead['team_name'])
                    ->action('View Lead', url('/admin/lead/'.$this->lead['lead_id']))
                    ->line('Thank you for using our application!');

    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
