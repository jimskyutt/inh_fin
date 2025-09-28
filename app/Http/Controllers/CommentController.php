<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = new Comment();
        $comment->content = $validated['content'];
        $comment->user_id = Auth::id();
        $comment->post_id = $post->id;
        $comment->save();

        // Load the user relationship for the response
        $comment->load('user');

        // Trigger notification if a service provider comments on a homeowner's post
        if (Auth::user()->role === 'service_provider' && $post->user->role === 'homeowner') {
            NotificationService::createCommentNotification(
                Auth::user(), 
                $post,
                $validated['content']
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully!',
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'created_at' => $comment->created_at->diffForHumans(),
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                    'face_img' => $comment->user->face_img ? asset('storage/' . $comment->user->face_img) : null,
                ]
            ]
        ], 201);
    }

    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // Force update the timestamp
        $comment->content = $validated['content'];
        $comment->updated_at = now();
        $comment->save();

        // Format the timestamp with custom units
        $diff = now()->diffInMinutes($comment->updated_at);
        $formattedTime = '';
        
        if ($diff < 60) {
            $formattedTime = "$diff " . ($diff === 1 ? 'min' : 'mins') . ' ago';
        } elseif ($diff < 1440) {
            $hours = floor($diff / 60);
            $formattedTime = "$hours " . ($hours === 1 ? 'hr' : 'hrs') . ' ago';
        } else {
            $days = floor($diff / 1440);
            $formattedTime = "$days " . ($days === 1 ? 'day' : 'days') . ' ago';
        }

        return response()->json([
            'success' => true,
            'message' => 'Comment updated successfully!',
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'updated_at' => $formattedTime,
                'is_updated' => $comment->created_at->ne($comment->updated_at)
            ]
        ]);
    }

    /**
     * Remove the specified comment from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $comment = Comment::withTrashed()->findOrFail($id);
            $this->authorize('delete', $comment);
            
            $postId = $comment->post_id;
            
            // If not already deleted, delete it
            if (!$comment->trashed()) {
                $comment->delete();
            }

            // Get the updated comment count
            $commentCount = Comment::where('post_id', $postId)->count();

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully!',
                'post_id' => $postId,
                'comment_count' => $commentCount
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // If we can't find the comment, try to get the post_id from the URL
            $postId = request()->input('post_id');
            
            return response()->json([
                'success' => true,
                'message' => 'Comment already deleted.',
                'post_id' => $postId
            ]);
        }
    }
}
