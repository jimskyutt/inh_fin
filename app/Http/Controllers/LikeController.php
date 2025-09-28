<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Store a newly created like in storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function store(Post $post)
    {
        \Log::info('LikeController@store', [
            'post_id' => $post->id,
            'user_id' => Auth::id()
        ]);
        // Check if user already liked the post
        $existingLike = Like::where('user_id', Auth::id())
            ->where('post_id', $post->id)
            ->first();

        if ($existingLike) {
            // If already liked, return the current state
            $post->loadCount('likes');
            return response()->json([
                'success' => true,
                'likesCount' => $post->likes_count,
                'isLiked' => true
            ]);
        }

        // If not liked, create a new like
        $like = new Like([
            'user_id' => Auth::id(),
        ]);

        $post->likes()->save($like);
        $post->loadCount('likes');

        return response()->json([
            'success' => true,
            'likesCount' => $post->likes_count,
            'isLiked' => true
        ]);
    }

    /**
     * Remove the specified like from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        try {
            \Log::info('LikeController@destroy - Starting', [
                'post_id' => $post->id,
                'user_id' => Auth::id()
            ]);

            // Find and delete the like
            $deleted = Like::where('user_id', Auth::id())
                ->where('post_id', $post->id)
                ->delete();

            if ($deleted) {
                // Get the updated like count
                $likesCount = Like::where('post_id', $post->id)->count();
                
                \Log::info('LikeController@destroy - Success', [
                    'post_id' => $post->id,
                    'user_id' => Auth::id(),
                    'likes_count' => $likesCount
                ]);
                
                return response()->json([
                    'success' => true,
                    'likesCount' => $likesCount,
                    'isLiked' => false
                ]);
            }

            \Log::warning('LikeController@destroy - Like not found', [
                'post_id' => $post->id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Like not found',
                'isLiked' => false
            ], 404);

        } catch (\Exception $e) {
            \Log::error('LikeController@destroy - Error', [
                'post_id' => $post->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while unliking the post',
                'isLiked' => true
            ], 500);
        }
    }
}
