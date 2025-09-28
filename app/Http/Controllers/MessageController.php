<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Display the messages index page
     */
    public function index(Request $request)
    {
        // Get all users except admins and the current user
        $users = \App\Models\User::where('role', '!=', 'Admin')
            ->where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get();
            
        // Get user's conversations with their latest message and other participant
        $conversations = auth()->user()->conversations()
            ->with(['participants' => function($query) {
                $query->where('user_id', '!=', auth()->id());
            }, 'latestMessage.sender'])
            ->withCount(['messages as unread_count' => function($query) {
                $query->where('receiver_id', auth()->id())
                      ->whereNull('read_at');
            }])
            ->orderBy('updated_at', 'desc')
            ->get();
            
        // Check if we need to auto-select a conversation
        $selectedUserId = $request->query('user');
        $shouldSelect = $request->query('select') === 'true';
        $selectedConversation = null;
        
        if ($selectedUserId) {
            // Find the conversation with the selected user
            $selectedConversation = $conversations->first(function($conversation) use ($selectedUserId) {
                return $conversation->participants->contains('id', $selectedUserId);
            });
            
            // If we should select but no conversation exists, we'll create a new one
            if ($shouldSelect && !$selectedConversation) {
                $selectedConversation = $this->findOrCreateConversation($selectedUserId);
                if ($selectedConversation) {
                    // Refresh the conversations list to include the new one
                    $conversations = auth()->user()->conversations()
                        ->with(['participants' => function($query) {
                            $query->where('user_id', '!=', auth()->id());
                        }, 'latestMessage.sender'])
                        ->withCount(['messages as unread_count' => function($query) {
                            $query->where('receiver_id', auth()->id())
                                  ->whereNull('read_at');
                        }])
                        ->orderBy('updated_at', 'desc')
                        ->get();
                    
                    $selectedConversation = $conversations->first(function($conv) use ($selectedUserId) {
                        return $conv->participants->contains('id', $selectedUserId);
                    });
                }
            }
        }
            
        return view('messages.index', [
            'users' => $users,
            'conversations' => $conversations,
            'selectedConversation' => $selectedConversation,
            'selectedUserId' => $selectedUserId
        ]);
    }

    /**
     * Get the count of unread messages for the authenticated user
     */
    /**
     * Find or create a conversation between two users
     * 
     * @param int $user1 First user ID
     * @return Conversation|null
     */
    protected function findOrCreateConversation($receiverId, $senderId = null)
    {
        try {
            $senderId = $senderId ?? auth()->id();
            
            // Ensure consistent ordering of user IDs
            $user1 = min($senderId, $receiverId);
            $user2 = max($senderId, $receiverId);
            
            // Find existing conversation between these two users
            $conversation = Conversation::whereHas('participants', function($q) use ($user1, $user2) {
                $q->whereIn('user_id', [$user1, $user2]);
                $q->groupBy('conversation_id');
                $q->havingRaw('COUNT(DISTINCT user_id) = 2');
            })->first();

            if ($conversation) {
                return $conversation;
            }

            // If no conversation exists, create a new one with created_by set
            $conversation = new Conversation([
                'created_by' => $senderId
            ]);
            
            if (!$conversation->save()) {
                throw new \Exception('Failed to save conversation');
            }
            
            // Attach both users to the conversation
            $conversation->participants()->attach([$user1, $user2]);
            
            return $conversation;
            
        } catch (\Exception $e) {
            Log::error('Failed to create conversation: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }
    
    /**
     * Get the count of unread messages for the authenticated user
     */
    public function getUnreadCount(Request $request)
    {
        $count = auth()->user()->conversations()
            ->withCount(['messages as unread_count' => function($query) {
                $query->where('receiver_id', auth()->id())
                      ->whereNull('read_at');
            }])
            ->get()
            ->sum('unread_count');
            
        return response()->json(['count' => $count]);
    }
    
    /**
     * Mark messages as read for a conversation
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request, $userId)
    {
        try {
            $user = auth()->user();
            
            // Find the conversation between the current user and the specified user
            $conversation = $user->conversations()
                ->whereHas('participants', function($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->first();
            
            if ($conversation) {
                // Mark all unread messages in this conversation as read
                $updated = \App\Models\Message::where('conversation_id', $conversation->id)
                    ->where('receiver_id', $user->id)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
                
                return response()->json([
                    'success' => true,
                    'updated_count' => $updated
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Conversation not found'
            ], 404);
            
        } catch (\Exception $e) {
            \Log::error('Error marking messages as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while marking messages as read'
            ], 500);
        }
    }

    /**
     * Soft delete a message
     */
    public function destroy(Message $message)
    {
        // Check if the authenticated user is the sender or receiver of the message
        if (auth()->id() !== $message->sender_id && auth()->id() !== $message->receiver_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        try {
            // Use transaction to ensure data consistency
            DB::beginTransaction();
            
            // Soft delete the message
            $message->delete();
            
            // Reload the message with trashed to get the deleted_at timestamp
            $deletedMessage = Message::withTrashed()->find($message->id);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Message deleted successfully.',
                'deleted_message' => $deletedMessage
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting message: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete message. Please try again.'
            ], 500);
        }
    }

    /**
     * Send a new message
     */
    public function sendMessage(Request $request)
    {
        Log::info('Sending message', ['request' => $request->all()]);
        
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $receiverId = $request->receiver_id;
        $senderId = auth()->id();

        Log::info('Finding or creating conversation', ['receiver_id' => $receiverId]);
        $conversation = $this->findOrCreateConversation($receiverId);
        Log::info('Conversation found/created', ['conversation_id' => $conversation ? $conversation->id : 'null']);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message' => $request->message,
        ]);
        Log::info('Message created', ['message_id' => $message->id]);

        $conversation->touch();
        Log::info('Conversation updated_at timestamp updated');

        $response = [
            'status' => 'success',
            'message' => $message->load('sender')
        ];
        Log::info('Sending response', $response);

        return response()->json($response);
    }

    /**
     * Get conversation history between the authenticated user and another user
     */
    public function getConversation($userId)
    {
        $authUserId = auth()->id();
        
        // Find the conversation between these two users
        $conversation = Conversation::whereHas('participants', function($q) use ($authUserId) {
            $q->where('user_id', $authUserId);
        })->whereHas('participants', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->withCount('participants')
        ->having('participants_count', '=', 2)
        ->first();

        if (!$conversation) {
            return response()->json([
                'messages' => [],
                'is_new' => true
            ]);
        }

        // Mark messages as read
        Message::where('conversation_id', $conversation->id)
            ->where('receiver_id', $authUserId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // First, get all message IDs to check for deletions in a single query
        $allMessageIds = $conversation->messages()->withTrashed()->pluck('id');
        
        // Get all soft-deleted message IDs in a single query
        $deletedMessageIds = Message::onlyTrashed()
            ->whereIn('id', $allMessageIds)
            ->pluck('id')
            ->toArray();
            
        // Get all non-deleted messages with their senders
        $messages = $conversation->messages()
            ->with(['sender'])
            ->whereNotIn('id', $deletedMessageIds) // Explicitly exclude deleted messages
            ->select('messages.*')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($message) {
                return [
                    'id' => $message->id,
                    'conversation_id' => $message->conversation_id,
                    'sender_id' => $message->sender_id,
                    'receiver_id' => $message->receiver_id,
                    'message' => $message->message,
                    'created_at' => $message->created_at,
                    'updated_at' => $message->updated_at,
                    'deleted_at' => null,
                    'edited_at' => $message->edited_at,
                    'is_deleted' => false,
                    'sender' => $message->sender ? [
                        'id' => $message->sender->id,
                        'name' => $message->sender->name,
                        'face_img' => $message->sender->face_img,
                        'avatar' => $message->sender->avatar
                    ] : null
                ];
            });
            
        // Add deleted messages with minimal data
        if (!empty($deletedMessageIds)) {
            $deletedMessages = Message::withTrashed()
                ->with(['sender'])
                ->whereIn('id', $deletedMessageIds)
                ->select('messages.*')
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function($message) {
                    return [
                        'id' => $message->id,
                        'conversation_id' => $message->conversation_id,
                        'sender_id' => $message->sender_id,
                        'receiver_id' => $message->receiver_id,
                        'message' => null, // Clear the message content
                        'created_at' => $message->created_at,
                        'updated_at' => $message->updated_at,
                        'deleted_at' => $message->deleted_at,
                        'edited_at' => $message->edited_at,
                        'is_deleted' => true,
                        'sender' => [
                            'id' => $message->sender_id,
                            'name' => $message->sender ? $message->sender->name : 'Unknown',
                            'face_img' => $message->sender ? $message->sender->face_img : null,
                            'avatar' => $message->sender ? $message->sender->avatar : null
                        ]
                    ];
                });
                
            // Merge and sort all messages by creation date
            $messages = $messages->merge($deletedMessages)
                ->sortBy('created_at')
                ->values();
        }

        return response()->json([
            'messages' => $messages,
            'is_new' => false
        ]);
    }

    
    /**
     * Update a message
     */
    public function update(Request $request, Message $message)
    {
        try {
            // Check if the authenticated user is the sender of the message
            if ($message->sender_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this message'
                ], 403);
            }
            
            $request->validate([
                'message' => 'required|string|max:1000',
            ]);
            
            // Update the message
            $message->update([
                'message' => $request->input('message'),
                'edited_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Message updated successfully',
                'data' => $message->fresh()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error updating message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update message',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
