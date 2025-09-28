<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\Post;

class NewPostCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $post;
    protected $posterName;

    /**
     * Create a new notification instance.
     *
     * @param Post $post
     * @param string $posterName
     */
    public function __construct(Post $post, string $posterName)
    {
        $this->post = $post;
        $this->posterName = $posterName;
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
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'New Post',
            'message' => "{$this->posterName} added a post",
            'post_id' => $this->post->id,
            'related_user_id' => $this->post->user_id,
            'link' => route('page.newsfeed').'#post-'.$this->post->id,
        ];
    }
}
