<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Models\Post;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send a notification to a user
     *
     * @param User $user The user to notify
     * @param string $title The notification title
     * @param string $message The notification message
     * @param string $type The notification type (info, success, warning, error)
     * @param mixed $notifiable The related model (optional)
     * @return Notification|null
     */
    public function send(User $user, string $title, string $message, string $type = 'info', $notifiable = null): ?Notification
    {
        try {
            $notification = $user->notifications()->create([
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'notifiable_type' => $notifiable ? get_class($notifiable) : null,
                'notifiable_id' => $notifiable ? $notifiable->id : null,
            ]);

            // Dispatch event for real-time notification
            event(new \App\Events\NotificationSent($notification));

            return $notification;
        } catch (\Exception $e) {
            Log::error('Failed to send notification: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Send a notification to multiple users
     *
     * @param \Illuminate\Support\Collection|array $users
     * @param string $title
     * @param string $message
     * @param string $type
     * @param mixed $notifiable
     * @return int Number of notifications sent
     */
    public function sendToMany($users, string $title, string $message, string $type = 'info', $notifiable = null): int
    {
        $count = 0;
        
        foreach ($users as $user) {
            if ($this->send($user, $title, $message, $type, $notifiable)) {
                $count++;
            }
        }
        
        return $count;
    }

    /**
     * Mark a notification as read
     *
     * @param Notification $notification
     * @return bool
     */
    public function markAsRead(Notification $notification): bool
    {
        if ($notification->is_read) {
            return true;
        }

        try {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark all notifications as read for a user
     *
     * @param User $user
     * @return int Number of notifications marked as read
     */
    public function markAllAsRead(User $user): int
    {
        return $user->unreadNotifications()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Delete a notification
     *
     * @param Notification $notification
     * @return bool
     */
    public function delete(Notification $notification): bool
    {
        try {
            return (bool) $notification->delete();
        } catch (\Exception $e) {
            Log::error('Failed to delete notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear all notifications for a user
     *
     * @param User $user
     * @return int Number of notifications deleted
     */
    public function clearAll(User $user): int
    {
        return $user->notifications()->delete();
    }

    /**
     * Create a notification for a new post
     *
     * @param Post $post
     */
    public static function createPostNotification(Post $post)
    {
        // Only create notification for homeowner posts
        if ($post->user->role === 'homeowner') {
            $message = 'A new post has been created: ' . $post->title;
            
            // Get all service providers who should be notified
            $serviceProviders = User::where('role', 'service_provider')
                ->where('id', '!=', $post->user_id)
                ->get();
            
            // Create notifications for each service provider
            foreach ($serviceProviders as $serviceProvider) {
                Notification::create([
                    'user_id' => $serviceProvider->id,
                    'related_user_id' => $post->user_id,
                    'post_id' => $post->id,
                    'type' => 'post_created',
                    'message' => $message,
                ]);
            }
        }
    }

    /**
     * Create a notification for a post like
     *
     * @param User $liker
     * @param Post $post
     */
    public static function createLikeNotification(User $liker, Post $post)
    {
        // Only create notification if a service provider likes a homeowner's post
        if ($liker->role === 'service_provider' && $post->user->role === 'homeowner') {
            $message = $liker->name . ' liked your post: ' . $post->title;
            
            Notification::create([
                'user_id' => $post->user_id, // Notify the post owner
                'related_user_id' => $liker->id,
                'post_id' => $post->id,
                'type' => 'post_liked',
                'message' => $message,
            ]);
        }
    }

    /**
     * Create a notification for a post comment
     *
     * @param User $commenter
     * @param Post $post
     * @param string $commentText
     */
    public static function createCommentNotification(User $commenter, Post $post, string $commentText)
    {
        // Only create notification if a service provider comments on a homeowner's post
        if ($commenter->role === 'service_provider' && $post->user->role === 'homeowner') {
            $message = $commenter->name . ' commented on your post: ' . 
                      (strlen($commentText) > 50 ? substr($commentText, 0, 50) . '...' : $commentText);
            
            Notification::create([
                'user_id' => $post->user_id, // Notify the post owner
                'related_user_id' => $commenter->id,
                'post_id' => $post->id,
                'type' => 'post_commented',
                'message' => $message,
            ]);
        }
    }

    /**
     * Notify users about a new post
     *
     * @param Post $post
     * @return int Number of notifications sent
     */
    public static function notifyAboutNewPost(Post $post): int
    {
        // Only notify if the post is from a homeowner
        if ($post->user->role !== 'Homeowner') {
            return 0;
        }

        // Get all homeowners except the one who created the post
        $homeowners = User::where('role', 'Homeowner')
            ->where('id', '!=', $post->user_id)
            ->get();

        $count = 0;
        $now = now();
        
        foreach ($homeowners as $homeowner) {
            $notification = new \App\Notifications\NewPostCreatedNotification($post, $post->user->name);
            
            // Create the notification record directly
            $notification = new \App\Models\Notification([
                'user_id' => $homeowner->id,
                'related_user_id' => $post->user_id,
                'post_id' => $post->id,
                'type' => 'post_created',
                'title' => 'New Post',
                'message' => "{$post->user->name} added a new post",
                'is_read' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            
            $notification->save();
            
            // Dispatch event for real-time notification
            event(new \App\Events\NotificationSent($notification));
            
            $count++;
        }

        return $count;
    }
}
