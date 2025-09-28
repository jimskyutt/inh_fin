<?php

namespace App\Notifications;

use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $message;
    public $sender;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Message  $message
     * @param  \App\Models\User  $sender
     * @return void
     */
    public function __construct(Message $message, User $sender)
    {
        $this->message = $message;
        $this->sender = $sender;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
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
                    ->subject('New message from ' . $this->sender->name)
                    ->line($this->sender->name . ' sent you a new message.')
                    ->line('"' . str_limit($this->message->body, 100) . '"')
                    ->action('View Conversation', route('messages.show', $this->message->conversation_id))
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
            'message_id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'message_preview' => str_limit($this->message->body, 100),
            'sent_at' => $this->message->created_at,
        ];
    }
}
