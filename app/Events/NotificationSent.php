<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Notification  $notification
     * @return void
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
        
        // Don't include the entire user relationship to prevent over-fetching
        $this->notification->loadMissing('user:id,name,email');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.User.' . $this->notification->user_id);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'notification.sent';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->notification->id,
            'title' => $this->notification->title,
            'message' => $this->notification->message,
            'type' => $this->notification->type,
            'is_read' => $this->notification->is_read,
            'created_at' => $this->notification->created_at->toDateTimeString(),
            'notifiable_type' => $this->notification->notifiable_type,
            'notifiable_id' => $this->notification->notifiable_id,
            'user' => [
                'id' => $this->notification->user->id,
                'name' => $this->notification->user->name,
                'email' => $this->notification->user->email,
            ],
        ];
    }
}
