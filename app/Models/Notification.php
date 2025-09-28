<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'related_user_id',
        'post_id',
        'type',
        'title',
        'message',
        'is_read',
        'read_at',
    ];
    
    protected $with = ['relatedUser'];
    // Removed post.user from eager loading to prevent issues when post_id is null
    
    protected $appends = ['created_ago'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related user of the notification.
     */
    public function relatedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'related_user_id');
    }

    /**
     * Get the created_ago attribute.
     */
    public function getCreatedAgoAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : null;
    }
    
    /**
     * Get the post that the notification belongs to.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class)->withDefault();
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include read notifications.
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update(['is_read' => true]);
        }
    }

    /**
     * Mark the notification as unread.
     */
    public function markAsUnread()
    {
        if ($this->is_read) {
            $this->update(['is_read' => false]);
        }
    }
}
