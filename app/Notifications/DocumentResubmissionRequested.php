<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentResubmissionRequested extends Notification
{
    use Queueable;

    protected $data;

    /**
     * Create a new notification instance.
     *
     * @param array $data
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
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

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Document Resubmission Requested')
                    ->line('A resubmission has been requested for your document: ' . $this->data['document_title'])
                    ->line('Message: ' . $this->data['message'])
                    ->action('View Document', url('/student/documentTracker/' . $this->data['document_id']))
                    ->line('Thank you for using E-Skolarian!');
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
            'document_id' => $this->data['document_id'],
            'document_title' => $this->data['document_title'],
            'message' => $this->data['message'],
            'type' => 'resubmission_requested'
        ];
    }
}