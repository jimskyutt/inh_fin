<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine whether the user can update the post.
     */
    public function update(User $user, Post $post): bool
    {
        // Allow admin to update any post
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        // Allow service providers and homeowners to update their own posts
        if (in_array($user->role, [User::ROLE_SERVICE_PROVIDER, User::ROLE_HOMEOWNER]) && 
            $post->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the post.
     */
    public function delete(User $user, Post $post): bool
    {
        // Use the same logic as update for now
        return $this->update($user, $post);
    }
}
