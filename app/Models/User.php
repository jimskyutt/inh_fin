<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomVerifyEmail;
use App\Models\Job;
use App\Models\Rating;
use App\Models\Service;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Conversation;
use App\Models\Message;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_HOMEOWNER = 'Homeowner';
    public const ROLE_SERVICE_PROVIDER = 'ServiceProvider';
    public const ROLE_ADMIN = 'Admin';

    public const STATUS_PENDING = 'pending';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_REJECTED = 'rejected';
    
    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'status',
        'civil_status',
        'birthday',
        'age',
        'contact_number',
        'sex',
        'address',
        'street',
        'face_img',
        'police_clearance',
        'id_front',
        'id_back',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthday' => 'date',
        'role' => 'string',
        'status' => 'string',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'email_verified_at',
        'birthday',
        'last_seen',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['age'];

    /**
     * Get the user's age.
     *
     * @return int|null
     */
    public function getAgeAttribute()
    {
        return $this->birthday ? now()->diffInYears($this->birthday) : null;
    }

    /**
     * Check if the user is currently online
     *
     * @return bool
     */
    public function isOnline()
    {
        return OnlineUser::where('user_id', $this->id)->exists();
    }


    /**
     * Get the jobs where this user is the service provider.
     */
    /**
     * Get the jobs where this user is the service provider.
     */
    public function jobs()
    {
        return $this->hasMany(Job::class, 'service_provider_id');
    }

    /**
     * Get the jobs where this user is the homeowner.
     */
    public function homeownerJobs()
    {
        return $this->hasMany(Job::class, 'homeowner_id');
    }

    /**
     * Get the reviews received by this user (as a service provider).
     * Uses service_provider_name to match the user's name with the service_provider_name in reviews.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'service_provider_name', 'name');
    }
    
    /**
     * @deprecated Use reviews() instead
     */
    public function ratings()
    {
        return $this->reviews();
    }

    /**
     * Get the services offered by this user (service provider).
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'provider_services', 'provider_id', 'service_id')
            ->withTimestamps();
    }

    /**
     * Get the posts that the user has liked.
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Get the ratings given by the user.
     */
    public function givenRatings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get the jobs posted by the user (as a homeowner).
     */
    public function postedJobs()
    {
        return $this->hasMany(Job::class, 'homeowner_id');
    }

    /**
     * Get all messages sent by the user.
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }
    
    /**
     * Get the conversations that the user is a participant in
     */
    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants')
            ->withTimestamps()
            ->orderBy('conversations.updated_at', 'desc');
    }

    /**
     * Get the count of unread messages for the user.
     *
     * @return int
     */
    public function unreadMessagesCount()
    {
        return $this->conversations()->withCount(['messages as unread_messages' => function($query) {
            $query->where('user_id', '!=', $this->id)
                ->where('created_at', '>', function($q) {
                    $q->select('last_read')
                        ->from('conversation_user')
                        ->whereColumn('conversation_id', 'conversations.id')
                        ->where('user_id', $this->id);
                });
        }])->get()->sum('unread_messages');
    }

    /**
     * Get the conversations with unread messages.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function conversationsWithUnread()
    {
        return $this->conversations()
            ->withCount(['messages as unread_count' => function($query) {
                $query->where('user_id', '!=', $this->id)
                    ->where('created_at', '>', function($q) {
                        $q->select('last_read')
                            ->from('conversation_user')
                            ->whereColumn('conversation_id', 'conversations.id')
                            ->where('user_id', $this->id);
                    });
            }])
            ->get();
    }

    /**
     * Check if the user has unread messages in a specific conversation.
     *
     * @param  int  $conversationId
     * @return bool
     */
    public function hasUnreadMessages($conversationId)
    {
        $conversation = $this->conversations()
            ->where('conversations.id', $conversationId)
            ->withCount(['messages as unread_count' => function($query) {
                $query->where('user_id', '!=', $this->id)
                    ->where('created_at', '>', function($q) {
                        $q->select('last_read')
                            ->from('conversation_user')
                            ->whereColumn('conversation_id', 'conversations.id')
                            ->where('user_id', $this->id);
                    });
            }])
            ->first();

        return $conversation && $conversation->unread_count > 0;
    }

    /**
     * Start a new conversation with one or more participants.
     *
     * @param  array  $participantIds
     * @param  string|null  $title
     * @return \App\Models\Conversation
     */
    public function startConversation(array $participantIds, $title = null)
    {
        // Ensure the current user is included in the conversation
        $participantIds = array_unique(array_merge($participantIds, [$this->id]));
        
        // Check if a conversation with the same participants already exists
        $existingConversation = $this->findExistingConversation($participantIds);
        
        if ($existingConversation) {
            return $existingConversation;
        }
        
        // Create a new conversation
        $conversation = Conversation::create([
            'title' => $title,
            'created_by' => $this->id,
        ]);
        
        // Add participants to the conversation
        $conversation->participants()->attach($participantIds);
        
        return $conversation->load('participants');
    }
    
    /**
     * Find an existing conversation with the same participants.
     *
     * @param  array  $participantIds
     * @return \App\Models\Conversation|null
     */
    protected function findExistingConversation(array $participantIds)
    {
        return Conversation::whereHas('participants', function($query) use ($participantIds) {
            $query->whereIn('user_id', $participantIds);
        }, '=', count($participantIds))
        ->withCount('participants')
        ->get()
        ->filter(function($conversation) use ($participantIds) {
            return $conversation->participants->count() === count($participantIds) &&
                   $conversation->participants->pluck('id')->sort()->values() === collect($participantIds)->sort()->values();
        })
        ->first();
    }

    /**
     * Get the jobs assigned to the user (as a service provider).
     */
    public function assignedJobs()
    {
        return $this->hasMany(Job::class, 'service_provider_id');
    }

    /**
     * Get the notifications for the user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class)->with('relatedUser')->latest();
    }
    
    /**
     * Get the user's unread notifications.
     */
    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)
            ->where('is_read', false)
            ->with('relatedUser')
            ->latest();
    }

    
    
}
