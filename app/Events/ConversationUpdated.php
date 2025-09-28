<?php

namespace App\Events;

use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversation;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Conversation  $conversation
     * @return void
     */
    public function __construct(Conversation $conversation)
    {
        $this->conversation = $conversation->load(['participants', 'latestMessage']);
        
        // Don't send sensitive information
        $this->conversation->makeHidden(['created_at', 'updated_at', 'deleted_at']);
        
        if ($this->conversation->latestMessage) {
            $this->conversation->latestMessage->makeHidden(['user_id', 'conversation_id', 'updated_at', 'deleted_at']);
        }
        
        $this->conversation->participants->each(function($participant) {
            $participant->makeHidden(['email', 'email_verified_at', 'created_at', 'updated_at', 'deleted_at']);
        });
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $channels = [];
        
        // Send to each participant's private channel
        foreach ($this->conversation->participants as $participant) {
            $channels[] = new PrivateChannel('user.' . $participant->id);
        }
        
        return $channels;
    }
    
    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'conversation.updated';
    }
    
    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'conversation' => $this->conversation,
            'unread_count' => $this->conversation->messages()
                ->where('user_id', '!=', auth()->id())
                ->whereNull('read_at')
                ->count(),
        ];
    }
}
