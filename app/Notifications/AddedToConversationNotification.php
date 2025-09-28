<?php

namespace App\Notifications;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AddedToConversationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $conversation;
    public $adder;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Conversation  $conversation
     * @param  \App\Models\User  $adder
     * @return void
     */
    public function __construct(Conversation $conversation, User $adder)
    {
        $this->conversation = $conversation;
        $this->adder = $adder;
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
                    ->subject('You\'ve been added to a conversation by ' . $this->adder->name)
                    ->line($this->adder->name . ' added you to a conversation.')
                    ->action('View Conversation', route('messages.show', $this->conversation->id))
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
            'conversation_id' => $this->conversation->id,
            'conversation_title' => $this->conversation->title,
            'adder_id' => $this->adder->id,
            'adder_name' => $this->adder->name,
            'added_at' => now(),
        ];
    }
}
