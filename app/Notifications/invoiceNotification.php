<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class invoiceNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public $invoiceData;
   
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($invoiceData)
    {
       $this->invoiceData = $invoiceData;     
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
                    ->greeting('Hi '.$this->invoiceData['name'].',')
                    ->line('Please find attached your invoice.')
                    ->line('Invoice ID: '.$this->invoiceData['invoice_num'])
                    ->line('Amount: $'.$this->invoiceData['final_amount'])
                    ->line('Due Date: '.$this->invoiceData['due_date'])
                    ->line('You can view your invoice and make any payments using the link below.')
                    ->line('Your invoice is attached.')
                    ->action('Pay Now', url('/payment/'.$this->invoiceData['invoice_key']));
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
