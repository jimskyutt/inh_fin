<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'created_by',
        'participant_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user who created the conversation.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all users in the conversation.
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->withTimestamps();
    }

    /**
     * Get all messages in the conversation, ordered by latest first.
     */
    public function messages()
    {
        return $this->hasMany(Message::class)->withTrashed()->latest();
    }

    /**
     * Get the latest message in the conversation.
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->withTrashed()->latest();
    }

    /**
     * Add a participant to the conversation.
     *
     * @param  int  $userId
     * @return void
     */
    public function addParticipant($userId)
    {
        $this->participants()->syncWithoutDetaching([$userId]);
    }

    /**
     * Remove a participant from the conversation.
     *
     * @param  int  $userId
     * @return void
     */
    public function removeParticipant($userId)
    {
        $this->participants()->detach($userId);
    }

    /**
     * Mark all messages as read for a user.
     *
     * @param  int  $userId
     * @return void
     */
    public function markAsRead($userId)
    {
        $this->messages()
            ->where('user_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        // Update the last_read timestamp for the participant
        $this->participants()->updateExistingPivot($userId, [
            'last_read' => now()
        ]);
        
        return $this;
    }
    
    /**
     * Get the number of unread messages for a user
     *
     * @param int $userId
     * @return int
     */
    public function unreadCount($userId)
    {
        return $this->messages()
            ->where('user_id', '!=', $userId)
            ->where('created_at', '>', function($query) use ($userId) {
                $query->select('last_read')
                    ->from('conversation_user')
                    ->where('user_id', $userId)
                    ->where('conversation_id', $this->id);
            })
            ->count();
    }
    
    /**
     * Notify other participants of a new message
     *
     * @param \App\Models\Message $message
     * @param int $senderId
     * @return void
     */
    public function notifyOtherParticipants(Message $message, $senderId)
    {
        $participants = $this->participants()
            ->where('user_id', '!=', $senderId)
            ->where(function($query) {
                $query->whereNull('conversation_user.muted_at')
                    ->orWhere('conversation_user.muted_at', '>', now());
            })
            ->get();
        
        foreach ($participants as $participant) {
            $participant->notify(new NewMessageNotification($message, $this));
        }
    }
    
    /**
     * Scope a query to only include conversations with unread messages
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithUnreadMessages($query, $userId)
    {
        return $query->whereHas('messages', function($q) use ($userId) {
            $q->where('user_id', '!=', $userId)
                ->where('created_at', '>', function($q2) use ($userId) {
                    $q2->select('last_read')
                        ->from('conversation_user')
                        ->whereColumn('conversation_id', 'conversations.id')
                        ->where('user_id', $userId);
                });
        });
    }

    /**
     * Check if a user is a participant in the conversation.
     *
     * @param  int  $userId
     * @return bool
     */
    public function hasParticipant($userId)
    {
        return $this->participants()->where('user_id', $userId)->exists();
    }

    /**
     * Scope a query to only include conversations for a given user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }
}
