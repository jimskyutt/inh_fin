@extends('layouts.app')

@section('title', 'Messages' . ' | ' . config('app.name'))
@section('content')
<div class="flex bg-white" style="height: calc(100vh - 3.3rem);">
    <!-- Left Sidebar - Conversation List -->
    <div class="w-full md:w-1/3 lg:w-1/4 border-r border-gray-200 flex flex-col">
        <!-- Header -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-xl font-bold text-gray-800">Messages</h1>
                <button onclick="document.getElementById('new-message-modal').classList.remove('hidden')" class="text-blue-500 hover:text-blue-600 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </button>
            </div>
            
            <!-- Search Bar -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" 
                       placeholder="Search messages" 
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-full bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>
        </div>
        
        <!-- Conversation List -->
        <div class="flex-1 overflow-y-auto conversation-list">
            @if($conversations->isEmpty())
                <!-- No Conversations State -->
                <div class="flex flex-col items-center justify-center h-full px-4 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">No messages yet</h3>
                    <p class="text-gray-500 text-sm mb-4">Your messages will appear here</p>
                    <button onclick="document.getElementById('new-message-modal').classList.remove('hidden')" 
                            class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Start a conversation
                    </button>
                </div>
            @else
                <!-- Conversations List -->
                <div class="divide-y divide-gray-200">
                    @foreach($conversations as $conversation)
                        @php
                            $otherUser = $conversation->participants->first();
                            $latestMessage = $conversation->latestMessage;
                        @endphp
                        <div class="p-4 hover:bg-gray-50 cursor-pointer border-l-2 border-transparent hover:border-blue-500 conversation-item"
                             data-user-id="{{ $otherUser->id }}"
                             data-conversation-id="{{ $conversation->id }}"
                             onclick="selectUser({{ $otherUser->id }}, '{{ addslashes($otherUser->name) }}', '{{ $otherUser->face_img }}', '{{ $otherUser->avatar }}', {{ $latestMessage ? 'false' : 'true' }})">
                            <div class="flex items-center">
                                <img src="{{ $otherUser->face_img ? asset('storage/' . $otherUser->face_img) : ($otherUser->avatar ?: 'https://ui-avatars.com/api/?name=' . urlencode($otherUser->name)) }}" 
                                     class="h-10 w-10 rounded-full object-cover" 
                                     alt="{{ $otherUser->name }}">
                                <div class="ml-3 flex-1">
                                    <div class="flex justify-between items-center">
                                        <h3 class="text-sm font-medium text-gray-900">{{ $otherUser->name }}</h3>
                                        @if($conversation->unread_count > 0)
                                            <span class="bg-blue-500 text-white text-xs font-medium px-2 py-0.5 rounded-full unread-badge">
                                                {{ $conversation->unread_count }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($latestMessage)
                                        @if($latestMessage->trashed())
                                            <p class="text-sm text-gray-400 italic truncate">unsent a message</p>
                                        @else
                                            <div class="flex justify-between items-center w-full">
                                                <p class="text-sm {{ $conversation->unread_count > 0 ? 'font-semibold text-gray-700' : 'text-gray-500' }} truncate message-preview">
                                                    {{ $latestMessage->sender_id === auth()->id() ? 'You: ' : '' }}
                                                    {{ Str::limit($latestMessage->message, 20) }}
                                                </p>
                                                <div class="flex items-center">
                                                    <span class="text-xs text-gray-400 whitespace-nowrap ml-2" title="{{ $latestMessage->created_at->setTimezone(config('app.timezone'))->format('M j, Y g:i A') }}">
                                                        @php
                                                            $now = \Carbon\Carbon::now();
                                                            $messageTime = $latestMessage->created_at->setTimezone(config('app.timezone'));
                                                            $diffInMinutes = $now->diffInMinutes($messageTime);
                                                            
                                                            if ($diffInMinutes < 1) {
                                                                echo 'Just now';
                                                            } elseif ($diffInMinutes < 60) {
                                                                echo $diffInMinutes . ' min' . ($diffInMinutes > 1 ? 's' : '') . ' ago';
                                                            } else {
                                                                echo $messageTime->diffForHumans();
                                                            }
                                                        @endphp
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Right Side - Chat Area -->
    <div class="hidden md:flex flex-1 flex-col bg-gray-50">
        <!-- Empty Chat State (Initially shown) -->
        <div id="empty-chat-state" class="h-full flex flex-col items-center justify-center p-6 text-center">
            <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mb-4 shadow-sm">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">Select a conversation</h3>
            <p class="text-gray-500 text-sm">Choose an existing conversation or start a new one</p>
        </div>

        <!-- Active Chat (Initially hidden) -->
        <div id="active-chat" class="h-full flex flex-col hidden">
            <!-- Chat Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-white">
                <div class="flex items-center w-full">
                    <button id="back-to-conversations" class="md:hidden mr-2 text-gray-600 hover:text-gray-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <div class="flex items-center w-full">
                        <div id="chat-user-avatar" class="w-10 h-10 rounded-full bg-gray-200 flex-shrink-0 flex items-center justify-center overflow-hidden mr-3">
                            <img src="" alt="" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0 flex items-center">
                            <div>
                                <div class="flex items-center">
                                    <h2 id="chat-user-name" class="font-medium text-gray-900 truncate">Select a conversation</h2>
                                </div>
                                <p id="chat-user-status" class="text-xs text-gray-500 truncate">
                                    <span class="flex items-center">
                                        <span class="h-2 w-2 rounded-full bg-gray-400 mr-1"></span>
                                        Offline
                                    </span>
                                </p>
                            </div>
                            <!-- Remove button for new conversations -->
                            <div id="remove-chat-button" class="hidden">
                                <button class="p-0 ml-2 -mr-1 text-gray-500 hover:text-red-600 focus:outline-none" id="remove-new-chat">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Call and video call buttons removed -->
                <div class="w-10"></div>
            </div>

            <!-- Messages Area -->
            <div id="messages-container" class="flex-1 p-4 overflow-y-auto" style="scrollbar-width: none; -ms-overflow-style: none;">
                <!-- Messages will be loaded here -->
                <div class="flex justify-center items-center h-full">
                    <p class="text-gray-500 text-sm">No messages yet. Send a message to start the conversation.</p>
                </div>
            </div>
            <style>
                /* Hide scrollbar for all browsers */
                #messages-container::-webkit-scrollbar {
                    display: none;
                    width: 0;
                    height: 0;
                }
                #messages-container {
                    -ms-overflow-style: none;
                    scrollbar-width: none;
                }

            </style>

            <!-- Delete Confirmation Modal -->
            <div id="delete-confirmation-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="mt-3 text-lg font-medium text-gray-900">Delete conversation</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Are you sure you want to delete this conversation? This action cannot be undone.</p>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 flex justify-end space-x-3">
                        <button type="button" id="confirm-delete" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Delete
                        </button>    
                        <button type="button" id="cancel-delete" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>

            <!-- Message Input -->
            <div class="p-4 border-t border-gray-200 bg-white">
                <div class="flex items-center">
                    <div class="relative flex-1 mx-2">
                        <div id="edit-message-indicator" class="hidden text-xs text-gray-500 mb-1 px-2">
                            Editing message
                            <button id="cancel-edit" class="ml-2 text-blue-500 hover:text-blue-700">
                                Cancel
                            </button>
                        </div>
                        <input type="text" 
                               id="message-input"
                               placeholder="Write a message..." 
                               class="w-full py-2 px-4 border border-gray-300 rounded-full focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <button class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                        </button>
                    </div>
                    <div class="flex items-center space-x-1">
                        <button id="save-edit" class="p-2 rounded-full text-green-500 hover:bg-green-50 hidden">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                        <button id="send-message" class="p-2 rounded-full bg-blue-500 text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-5 h-5 transform rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Context Menu -->
<div id="message-context-menu" class="fixed hidden z-50 bg-white rounded-lg shadow-lg border border-gray-200 w-40">
    <button id="edit-message-btn" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-lg">
        <i class="fas fa-edit mr-2"></i> Edit
    </button>
    <div class="border-t border-gray-200"></div>
    <button id="delete-message-btn" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-b-lg">
        <i class="fas fa-trash-alt mr-2"></i> Delete
    </button>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-message-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-80">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="mt-3 text-lg font-medium text-gray-900">Unsent Message</h3>
            <div class="mt-2">
                <p class="text-sm text-gray-500">Are you sure you want to unsent this message? This action cannot be undone.</p>
            </div>
        </div>
        <div class="mt-5 sm:mt-6 grid grid-cols-2 gap-3">
            <button type="button" id="confirm-delete-message" class="w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Unsent
            </button>    
            <button type="button" id="cancel-delete-message" class="w-full px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Custom scrollbar for conversation list */
    .conversation-list::-webkit-scrollbar {
        width: 6px;
    }
    .conversation-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    .conversation-list::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }
    .conversation-list::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    /* Hide scrollbar for messages container but keep it scrollable */
    #messages-container {
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE and Edge */
    }
    #messages-container::-webkit-scrollbar {
        display: none; /* Chrome, Safari and Opera */
        width: 0 !important;
    }
</style>
@endpush

<!-- New Message Modal -->
<div id="new-message-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">New Message</h3>
            <div class="mt-4">
                <div class="relative mb-4">
                    <input type="text" 
                           id="user-search"
                           placeholder="Search for a person" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                           onkeyup="filterUsers()">
                </div>
                <div id="user-list-container" class="h-64 overflow-y-auto border-t border-gray-200 pt-2 hidden">
                    @if($users->count() > 0)
                        <ul id="user-list" class="divide-y divide-gray-200">
                            @foreach($users as $user)
                                <li class="py-3 px-2 hover:bg-gray-50 rounded cursor-pointer user-item"
                                    onclick="selectUser({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->face_img }}', '{{ $user->avatar }}', true)">
                                    <div class="flex items-center">
                                        <img class="h-10 w-10 rounded-full object-cover flex-shrink-0" 
                                             src="{{ $user->face_img ? asset('storage/' . $user->face_img) : ($user->avatar ?: 'https://ui-avatars.com/api/?name=' . urlencode($user->name)) }}" 
                                             alt="{{ $user->name }}">
                                        <div class="ml-3 flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                                                <span class="text-xs text-gray-500 ml-2 whitespace-nowrap">{{ $user->role }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500 text-center py-4">No users found</p>
                    @endif
                </div>
                <div id="no-search" class="h-64 flex items-center justify-center">
                    <p class="text-sm text-gray-500">Search for a user to start a conversation</p>
                </div>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="document.getElementById('new-message-modal').classList.add('hidden')" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Check for selected conversation from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const selectedUserId = urlParams.get('user');
    const shouldSelect = urlParams.get('select') === 'true';
    
    // Get selected conversation data from the server-side variable if available
    const selectedConversation = @json($selectedConversation ?? null);
    
    // Modal elements
    const deleteModal = document.getElementById('delete-confirmation-modal');
    const cancelDeleteBtn = document.getElementById('cancel-delete');
    const confirmDeleteBtn = document.getElementById('confirm-delete');
    
    // Auto-select conversation on page load if needed
    document.addEventListener('DOMContentLoaded', function() {
        // If we have a selected conversation from the server, select it
        if (selectedConversation && selectedConversation.id) {
            // Find the other participant (not the current user)
            const otherParticipant = selectedConversation.participants.find(
                p => p.id != {{ auth()->id() }}
            );
            
            if (otherParticipant) {
                // Use the selectUser function to load the conversation
                selectUser(
                    otherParticipant.id,
                    otherParticipant.name,
                    otherParticipant.face_img || null,
                    otherParticipant.avatar || null,
                    false
                );
                
                // Scroll the selected conversation into view
                const selectedElement = document.querySelector(`[data-conversation-id="${selectedConversation.id}"]`);
                if (selectedElement) {
                    selectedElement.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            }
        } 
        // If we have a user ID but no conversation yet, show empty chat
        else if (selectedUserId && !shouldSelect) {
            // Try to get user data from session storage
            const selectedUser = JSON.parse(sessionStorage.getItem('selectedUser') || 'null');
            if (selectedUser) {
                // Show empty chat with this user
                selectUser(
                    selectedUser.id,
                    selectedUser.name,
                    selectedUser.faceImg,
                    null,
                    true
                );
                
                // Clear the stored user data
                sessionStorage.removeItem('selectedUser');
            }
        }
    });
    let currentDeleteConversationId = null;
    
    // Show delete confirmation modal
    function showDeleteModal(conversationId) {
        currentDeleteConversationId = conversationId;
        deleteModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    // Hide delete confirmation modal
    function hideDeleteModal() {
        deleteModal.classList.add('hidden');
        document.body.style.overflow = '';
        currentDeleteConversationId = null;
    }

    // Global functions that need to be accessible from HTML onclick attributes
    window.selectUser = function(userId, userName, userFaceImg, userAvatar, isNewConversation = false) {
        console.log('Selecting user:', {userId, userName, userFaceImg, userAvatar, isNewConversation});
        
        // Hide the new message modal if it exists
        const modal = document.getElementById('new-message-modal');
        if (modal) modal.classList.add('hidden');
        
        // Update UI based on whether it's a new or existing conversation
        const removeChatBtn = document.getElementById('remove-chat-button');
        
        if (removeChatBtn) {
            if (isNewConversation) {
                removeChatBtn.classList.remove('hidden');
            } else {
                removeChatBtn.classList.add('hidden');
            }
        }
        
        // Store user info in global variables for later use
        window.currentReceiverId = userId;
        window.currentReceiverName = userName;
        window.currentReceiverFaceImg = userFaceImg;
        window.currentReceiverAvatar = userAvatar;
        
        // Update the UI to show the selected user
        const chatUserName = document.getElementById('chat-user-name');
        if (chatUserName) chatUserName.textContent = userName || '';
        
        // Update the avatar
        updateChatUserAvatar(userFaceImg, userAvatar, userName);
        
        // Show the active chat and hide empty state
        const emptyChatState = document.getElementById('empty-chat-state');
        const activeChat = document.getElementById('active-chat');
        if (emptyChatState) emptyChatState.classList.add('hidden');
        if (activeChat) activeChat.classList.remove('hidden');
        
        // On mobile, hide the conversation list and show the chat area
        const conversationList = document.querySelector('.w-full.md\\:w-1\\/3');
        if (window.innerWidth < 768 && conversationList) {
            conversationList.classList.add('hidden');
        }
        
        // Mark messages as read for this conversation
        if (!isNewConversation) {
            fetch(`/messages/${userId}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ user_id: userId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Find the conversation item in the list
                    const conversationItem = document.querySelector(`[data-user-id="${userId}"]`);
                    if (conversationItem) {
                        // Remove the unread badge
                        const unreadBadge = conversationItem.querySelector('.unread-badge');
                        if (unreadBadge) {
                            unreadBadge.remove();
                        }
                        
                        // Remove the 'font-semibold' class from the message preview
                        const messagePreview = conversationItem.querySelector('.message-preview');
                        if (messagePreview) {
                            messagePreview.classList.remove('font-semibold');
                        }
                        
                        // Move the conversation to the top of the list
                        const conversationList = document.querySelector('.conversation-list .divide-y');
                        if (conversationList && conversationItem.parentNode === conversationList) {
                            conversationList.insertBefore(conversationItem, conversationList.firstChild);
                        }
                    }
                    
                    // Update the unread count in the UI
                    updateUnreadCount();
                    
                    // Update the unread count in the navigation
                    const unreadCountElement = document.getElementById('unread-messages-count');
                    if (unreadCountElement) {
                        const currentCount = parseInt(unreadCountElement.textContent) || 0;
                        const newCount = Math.max(0, currentCount - data.updated_count);
                        unreadCountElement.textContent = newCount > 0 ? newCount : '';
                        unreadCountElement.style.display = newCount > 0 ? 'flex' : 'none';
                    }
                }
            })
            .catch(error => console.error('Error marking messages as read:', error));
        }
        
        // Set the current receiver ID in the global scope
        window.currentReceiverId = userId;
        console.log('Current receiver set to:', window.currentReceiverId);
        
        // Check if user is online and update status
        fetch(`/api/users/${userId}/status`)
            .then(response => response.json())
            .then(data => {
                const statusElement = document.getElementById('chat-user-status');
                if (statusElement) {
                    const isOnline = data.online || false;
                    statusElement.innerHTML = `
                        <span class="flex items-center">
                            <span class="h-2 w-2 rounded-full ${isOnline ? 'bg-green-500' : 'bg-gray-400'} mr-1"></span>
                            ${isOnline ? 'Online' : 'Offline'}
                        </span>
                    `;
                }
            })
            .catch(error => {
                console.error('Error fetching user status:', error);
            });
        
        // Clear any existing messages
        const messagesContainer = document.getElementById('messages-container');
        if (messagesContainer) {
            messagesContainer.innerHTML = `
                <div class="flex justify-center items-center h-full">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                </div>`;
        }
        
        // Get the message input and focus it
        const messageInput = document.getElementById('message-input');
        if (messageInput) {
            messageInput.focus();
            messageInput.placeholder = 'Type a message...';
        }
        
        // Fetch the conversation history
        fetch(`/messages/conversation/${userId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (messagesContainer) {
                // Create a temporary container for the new messages
                const tempContainer = document.createElement('div');
                tempContainer.style.display = 'none'; // Hide while building
                
                if (data.messages && data.messages.length > 0) {
                    // Sort messages by created_at (oldest first)
                    const sortedMessages = [...data.messages].sort((a, b) => 
                        new Date(a.created_at) - new Date(b.created_at)
                    );
                    
                    // Create all message elements in the temporary container
                    sortedMessages.forEach(message => {
                        const isCurrentUser = message.sender_id === {{ auth()->id() }};
                        const messageElement = createMessageElement({
                            id: message.id,
                            message: message.message,
                            created_at: message.created_at,
                            deleted_at: message.deleted_at,
                            is_deleted: message.is_deleted,
                            edited_at: message.edited_at,
                            sender_id: message.sender_id,
                            receiver_id: message.receiver_id,
                            conversation_id: message.conversation_id,
                            sender: {
                                id: message.sender_id,
                                name: isCurrentUser ? 'You' : (message.sender?.name || 'Unknown'),
                                face_img: message.sender?.face_img,
                                avatar: message.sender?.avatar
                            }
                        }, isCurrentUser);
                        tempContainer.appendChild(messageElement);
                    });
                    
                    // Replace the entire content in one operation
                    messagesContainer.innerHTML = '';
                    messagesContainer.appendChild(tempContainer);
                    tempContainer.style.display = ''; // Show the new content
                    
                    // Scroll to bottom after loading messages
                    requestAnimationFrame(() => {
                        scrollToBottom(messagesContainer);
                    });
                } else {
                    messagesContainer.innerHTML = `
                        <div class="flex flex-col items-center justify-center h-full text-gray-500">
                            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <p class="text-sm">No messages yet. Say hello!</p>
                        </div>`;
                }
            }
            
            // Update the header based on whether it's a new conversation
            const actionButtons = document.querySelector('.action-buttons');
            const removeButton = document.getElementById('remove-chat');
            
            if (actionButtons) actionButtons.classList.toggle('hidden', data.is_new);
            if (removeButton) removeButton.classList.toggle('hidden', !data.is_new);
            
            // Scroll to bottom after messages are loaded
            setTimeout(scrollToBottom, 100);
        })
        .catch(error => {
            console.error('Error:', error);
            if (messagesContainer) {
                messagesContainer.innerHTML = `
                    <div class="flex justify-center items-center h-full">
                        <p class="text-red-500 text-sm">Error loading messages. Please try again.</p>
                    </div>`;
            }
        });
    };

    // Function to update chat user avatar
    function updateChatUserAvatar(userFaceImg, userAvatar, userName) {
        const chatUserAvatarContainer = document.getElementById('chat-user-avatar');
        if (!chatUserAvatarContainer) return;
        
        const chatUserAvatarImg = chatUserAvatarContainer.querySelector('img');
        if (!chatUserAvatarImg) return;
        
        let avatarUrl = '';
        
        if (userFaceImg) {
            // Check if it's already a full URL or a data URL
            if (userFaceImg.startsWith('http') || userFaceImg.startsWith('data:image')) {
                avatarUrl = userFaceImg;
            } else {
                // Remove any leading slashes or storage/ prefix that might be duplicated
                const cleanPath = userFaceImg.replace(/^[\/\\]|^storage[\/\\]/, '');
                avatarUrl = '{{ asset('storage') }}/' + cleanPath;
            }
        } else if (userAvatar) {
            avatarUrl = userAvatar;
        } else {
            // Fallback to generated avatar
            avatarUrl = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(userName || 'U') + '&background=random';
        }
        
        // Only update if the URL has changed to prevent flickering
        if (chatUserAvatarImg.src !== avatarUrl) {
            chatUserAvatarImg.src = avatarUrl;
        }
        
        chatUserAvatarImg.alt = userName || 'User';
        chatUserAvatarImg.onerror = function() {
            // If image fails to load, fall back to generated avatar
            this.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(userName || 'U') + '&background=random';
        };
    }

    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('new-message-modal');
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        });
    }); // Close DOMContentLoaded
    
    // Filter users based on search input
    window.filterUsers = function() {
        const input = document.getElementById('user-search');
        const userListContainer = document.getElementById('user-list-container');
        const noSearchDiv = document.getElementById('no-search');
        
        if (input && userListContainer && noSearchDiv) {
            if (input.value.length > 0) {
                userListContainer.classList.remove('hidden');
                noSearchDiv.classList.add('hidden');
                
                const filter = input.value.toLowerCase();
                const userItems = document.getElementsByClassName('user-item');
                let hasVisibleItems = false;
                
                for (let i = 0; i < userItems.length; i++) {
                    const nameElement = userItems[i].querySelector('.text-gray-900');
                    if (nameElement) {
                        const name = nameElement.textContent.toLowerCase();
                        if (name.includes(filter)) {
                            userItems[i].style.display = 'block';
                            hasVisibleItems = true;
                        } else {
                            userItems[i].style.display = 'none';
                        }
                    }
                }
                
                // Show no results message if no users match the search
                const noResults = document.getElementById('no-results');
                if (!noResults && !hasVisibleItems && userItems.length > 0) {
                    const noResultsMsg = document.createElement('p');
                    noResultsMsg.id = 'no-results';
                    noResultsMsg.className = 'text-sm text-gray-500 text-center py-4';
                    noResultsMsg.textContent = 'No users found';
                    userListContainer.appendChild(noResultsMsg);
                } else if (noResults && hasVisibleItems) {
                    noResults.remove();
                }
            } else {
                userListContainer.classList.add('hidden');
                noSearchDiv.classList.remove('hidden');
                
                // Remove any existing no-results message
                const noResults = document.getElementById('no-results');
                if (noResults) {
                    noResults.remove();
                }
            }
        }
    };

    // This duplicate code has been removed as it was causing syntax errors

    // Add event listener for the remove new chat button
    const removeNewChatBtn = document.getElementById('remove-new-chat');
    if (removeNewChatBtn) {
        removeNewChatBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Show empty chat state and hide active chat
            const emptyChatState = document.getElementById('empty-chat-state');
            const activeChat = document.getElementById('active-chat');
            
            if (emptyChatState) emptyChatState.classList.remove('hidden');
            if (activeChat) activeChat.classList.add('hidden');
            
            // Reset the URL
            history.pushState(null, '', '/messages');
        });
    }
    
    // Add event listener for the remove chat button (old one, keeping for backward compatibility)
    const removeChatButton = document.getElementById('remove-chat');
    if (removeChatButton) {
        removeChatButton.addEventListener('click', function() {
            // Show empty chat state and hide active chat
            const emptyChatState = document.getElementById('empty-chat-state');
            const activeChat = document.getElementById('active-chat');
            
            if (emptyChatState) emptyChatState.classList.remove('hidden');
            if (activeChat) activeChat.classList.add('hidden');
        });
    }
    
    // Back to conversations (mobile view)
    const backButton = document.getElementById('back-to-conversations');
    if (backButton) {
        backButton.addEventListener('click', function() {
            const conversationList = document.querySelector('.w-full.md\\:w-1\\/3');
            const activeChat = document.getElementById('active-chat');
            if (conversationList) conversationList.classList.remove('hidden');
            if (activeChat) activeChat.classList.add('hidden');
        });
    }
    
    // Store the current selected user ID
    let currentReceiverId = null;
    
    // Handle send message
    const sendButton = document.getElementById('send-message');
    if (sendButton) {
        sendButton.addEventListener('click', sendMessage);
    }
    
    // Function to send a message
    // Track temporary messages to prevent duplicates
    const temporaryMessages = new Map();
    
    function sendMessage() {
        console.log('sendMessage called');
        const messageInput = document.getElementById('message-input');
        
        if (!messageInput) {
            console.error('Message input not found');
            return;
        }
        
        const messageText = messageInput.value.trim();
        if (!messageText) return;
        
        // Get the receiver ID
        const receiverId = window.currentReceiverId;
        if (!receiverId) {
            console.error('No receiver selected');
            return;
        }
        
        // Create temporary message ID for optimistic update
        const tempId = 'temp-' + Date.now();
        
        // Create avatar URL for the current user
        const avatarUrl = '{{ auth()->user()->face_img ? asset('storage/' . str_replace("'", "\\'", auth()->user()->face_img)) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=random' }}';
        
        // Create a temporary message element
        const tempMessage = {
            id: tempId,
            message: messageText,
            sender_id: {{ auth()->id() }},
            created_at: new Date().toISOString(),
            is_temp: true, // Mark as temporary message
            sender: {
                id: {{ auth()->id() }},
                name: 'You',
                face_img: '{{ auth()->user()->face_img }}',
                avatar: '{{ auth()->user()->avatar }}'
            }
        };
        
        // Store the temporary message
        temporaryMessages.set(tempId, tempMessage);
        
        // Add the temporary message to the UI and store the element reference
        const tempMessageElement = appendMessage(tempMessage, true);
        // Ensure the temporary message is clickable immediately
        if (tempMessageElement) {
            const messageBubble = tempMessageElement.querySelector('.message-content');
            const timestamp = tempMessageElement.querySelector('.message-timestamp');
            
            if (messageBubble && timestamp) {
                messageBubble.addEventListener('click', function(e) {
                    if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON') {
                        return;
                    }
                    const isVisible = timestamp.style.display === 'block';
                    timestamp.style.display = isVisible ? 'none' : 'block';
                    timestamp.style.opacity = isVisible ? '0' : '0.7';
                });
            }
        }
        
        // Clear input
        messageInput.value = '';
        
        // Get conversation list container
        const conversationList = document.querySelector('.conversation-list .divide-y') || 
                               (() => {
                                   const list = document.querySelector('.conversation-list');
                                   if (list && list.querySelector('.flex.flex-col.items-center.justify-center')) {
                                       // If no conversations exist yet, clear the 'no conversations' message
                                       list.innerHTML = '<div class="divide-y divide-gray-200"></div>';
                                       return list.querySelector('.divide-y');
                                   }
                                   return list;
                               })();
        
        if (!conversationList) return;
        
        // Check if conversation already exists in the list
        const conversationItems = document.querySelectorAll('.conversation-list .divide-y > div');
        let existingConversation = null;
        
        // Find the conversation by checking the onclick handler
        for (const item of conversationItems) {
            const onclickAttr = item.getAttribute('onclick') || '';
            if (onclickAttr.includes(`selectUser(${receiverId},`)) {
                existingConversation = item;
                break;
            }
        }
        
        if (existingConversation) {
            // Update existing conversation
            const messagePreview = existingConversation.querySelector('p.text-sm.text-gray-500');
            const timestamp = existingConversation.querySelector('span.text-xs.text-gray-500');
            
            if (messagePreview) {
                const shortMessage = messageText.length > 20 ? messageText.substring(0, 20) + '...' : messageText;
                const currentContent = messagePreview.textContent.trim();
                const prefix = currentContent.startsWith('You: ') ? 'You: ' : '';
                messagePreview.textContent = `${prefix}${shortMessage}`;
            }
            
            // Get or create the timestamp element
            let timestampElement = existingConversation.querySelector('.text-xs.text-gray-400.whitespace-nowrap');
            if (!timestampElement) {
                timestampElement = document.createElement('span');
                timestampElement.className = 'text-xs text-gray-400 ml-2 whitespace-nowrap';
                const messageContainer = existingConversation.querySelector('.flex.justify-between.items-center');
                if (messageContainer) {
                    messageContainer.appendChild(timestampElement);
                }
            }
            timestampElement.textContent = 'Just now';
            
            // Move to top of the list if not already
            if (conversationList.firstChild !== existingConversation) {
                conversationList.insertBefore(existingConversation, conversationList.firstChild);
            }
        } else {
            // Create new conversation item
            const newConversation = document.createElement('div');
            newConversation.className = 'p-4 hover:bg-gray-50 cursor-pointer border-l-2 border-blue-500';
            
            // Get user info from the current chat or use defaults
            const userName = window.currentReceiverName || 'User';
            let userImg = '';
            
            // Get the correct image source, handling both face_img and avatar
            const faceImg = window.currentReceiverFaceImg || '';
            const avatar = window.currentReceiverAvatar || '';
            
            // Try to get the avatar from the chat header first
            const chatAvatarImg = document.querySelector('#chat-user-avatar img');
            if (chatAvatarImg && chatAvatarImg.src) {
                userImg = chatAvatarImg.src;
            } 
            // If not available, use face_img or avatar
            else if (faceImg) {
                if (faceImg.startsWith('http') || faceImg.startsWith('data:image')) {
                    userImg = faceImg;
                } else {
                    // Clean up the path and add storage prefix if needed
                    const cleanPath = faceImg.replace(/^[\/\\]|^storage[\/\\]/, '');
                    userImg = '{{ asset('storage') }}/' + cleanPath;
                }
            } else if (avatar) {
                userImg = avatar;
            } else {
                // Fallback to default avatar with user's name
                userImg = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(userName) + '&background=random';
            }
            
            newConversation.innerHTML = `
                <div class="flex items-center">
                    <img src="${userImg}" 
                         class="h-10 w-10 rounded-full object-cover" 
                         alt="${userName}">
                    <div class="ml-3 flex-1 min-w-0">
                        <div class="flex justify-between items-center">
                            <h3 class="text-sm font-medium text-gray-900 truncate">${userName}</h3>
                            <span class="text-xs text-gray-500 whitespace-nowrap ml-2">Just now</span>
                        </div>
                        <p class="text-sm text-gray-500 truncate">${messageText.length > 25 ? messageText.substring(0, 25) + '...' : messageText}</p>
                    </div>
                </div>`;
                
            // Add click handler
            newConversation.setAttribute('onclick', `selectUser(${receiverId}, '${userName.replace(/'/g, "\\'")}', '${faceImg.replace(/'/g, "\\'")}', '${avatar.replace(/'/g, "\\'")}', false)`);
            
            // Add to the top of the conversation list
            if (conversationList) {
                conversationList.insertBefore(newConversation, conversationList.firstChild);
            }
        }
        
        // Hide 'No messages yet' UI if it's visible and add the new message
        const messagesContainer = document.getElementById('messages-container');
        if (messagesContainer) {
            const noMessagesElement = messagesContainer.querySelector('.flex.flex-col.items-center.justify-center');
            if (noMessagesElement) {
                messagesContainer.innerHTML = ''; // Clear the 'No messages' content
            }
            
            // Add message to UI immediately without timestamp
            appendMessage(tempMessage, true, false);
            
            // Scroll to show the new message
            scrollToBottom(messagesContainer);
        } else {
            // Fallback in case messagesContainer is not found
            appendMessage(tempMessage, true, false);
        }
        
        // Send the message to the server
        fetch('{{ route('messages.send') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                receiver_id: receiverId,
                message: messageText
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Failed to send message');
                });
            }
            
            // After successful message send, switch to showing vertical dots menu
            const removeChatBtn = document.getElementById('remove-chat-button');
            const chatMenuContainer = document.getElementById('chat-menu-container');
            
            if (removeChatBtn && chatMenuContainer) {
                removeChatBtn.classList.add('hidden');
                chatMenuContainer.classList.remove('hidden');
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Server response:', data);
            if (data.status === 'success') {
                console.log('Message sent successfully');
                // Remove the temporary message if it exists
                const tempMessage = document.querySelector(`[data-temp-id="${tempId}"]`);
                if (tempMessage) {
                    tempMessage.remove();
                }
                // Add the confirmed message from the server
                appendMessage(data.message, true);
            } else {
                throw new Error(data.message || 'Failed to send message');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Show error message
            const errorElement = document.createElement('div');
            errorElement.className = 'text-red-500 text-xs text-center py-2';
            errorElement.textContent = 'Failed to send message. Please try again.';
            document.getElementById('messages-container').appendChild(errorElement);
        });
    }
    
    // Helper function to check if scrolled to bottom
    function isScrolledToBottom(element, threshold = 100) {
        if (!element) return true;
        return element.scrollHeight - element.scrollTop - element.clientHeight < threshold;
    }
    
    // Function to append a message to the chat
    function appendMessage(message, isCurrentUser, showTimestamp = false) {
        const messagesContainer = document.getElementById('messages-container');
        if (!messagesContainer) return;
        
        // Check if this is a temporary message that was already processed
        if (message.is_temp && temporaryMessages.has(message.id)) {
            // This is a temporary message we already handled
            temporaryMessages.delete(message.id);
        } else if (message.id && message.id.toString().startsWith('temp-')) {
            // This is a temporary message from WebSocket, but we already handled it
            return;
        }
        
        // Create message element
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${isCurrentUser ? 'justify-end' : 'justify-start'} mb-4`;
        
        if (message.id) {
            if (String(message.id).startsWith('temp-')) {
                messageDiv.setAttribute('data-temp-id', message.id);
            } else {
                messageDiv.setAttribute('data-message-id', message.id);
            }
        }
        
        // Get avatar URL
        let avatarUrl = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(message.sender?.name || 'U');
        if (message.sender?.face_img) {
            // Check if the URL already contains 'storage/' to avoid duplicating it
            if (message.sender.face_img.includes('storage/')) {
                avatarUrl = '{{ asset('') }}' + message.sender.face_img;
            } else {
                avatarUrl = '{{ asset('storage') }}/' + message.sender.face_img;
            }
        } else if (message.sender?.avatar) {
            avatarUrl = message.sender.avatar;
        }
        
        const messageTime = message.created_at ? formatMessageTime(message.created_at) : 'Sending...';
        
        // Create message elements directly for better event handling
        const messageContainer = document.createElement('div');
        messageContainer.className = 'flex flex-col max-w-xs lg:max-w-md';
        
        const messageRow = document.createElement('div');
        messageRow.className = 'flex';
        
        // Add avatar if not current user
        if (!isCurrentUser) {
            const avatarImg = document.createElement('img');
            avatarImg.src = avatarUrl;
            avatarImg.className = 'w-8 h-8 rounded-full mr-2 object-cover';
            avatarImg.alt = message.sender?.name || 'User';
            messageRow.appendChild(avatarImg);
        }
        
        // Create message bubble container
        const messageBubbleContainer = document.createElement('div');
        messageBubbleContainer.className = 'flex flex-col';
        
        // Create message bubble
        const messageBubble = document.createElement('div');
        messageBubble.className = `${isCurrentUser ? 'bg-blue-500 text-white' : 'bg-white text-gray-800'} rounded-lg py-2 px-4 message-content`;
        messageBubble.style.cursor = 'pointer';
        
        const messageText = document.createElement('p');
        messageText.className = 'text-sm';
        messageText.textContent = message.message;
        messageBubble.appendChild(messageText);
        
        // Create timestamp
        const timestamp = document.createElement('span');
        timestamp.className = `message-timestamp text-xs text-gray-500 mt-1 ${isCurrentUser ? 'text-right' : 'text-left'}`;
        timestamp.textContent = messageTime;
        timestamp.style.display = 'none';
        timestamp.style.opacity = '0';
        timestamp.style.transition = 'opacity 0.2s ease-in-out';
        
        // Assemble message bubble with timestamp
        messageBubbleContainer.appendChild(messageBubble);
        messageBubbleContainer.appendChild(timestamp);
        messageRow.appendChild(messageBubbleContainer);
        
        // Add avatar if current user
        if (isCurrentUser) {
            const userAvatarImg = document.createElement('img');
            userAvatarImg.src = avatarUrl;
            userAvatarImg.className = 'w-8 h-8 rounded-full ml-2 object-cover';
            userAvatarImg.alt = message.sender?.name || 'You';
            messageRow.appendChild(userAvatarImg);
        } else {
            // For received messages, add some left margin to the timestamp
            timestamp.style.marginLeft = '10px';
        }
        
        // Assemble the message
        messageContainer.appendChild(messageRow);
        messageDiv.appendChild(messageContainer);
        messagesContainer.appendChild(messageDiv);
        
        // Add click handler to the message bubble
        messageBubble.addEventListener('click', function(e) {
            // Don't toggle if clicking on a link or button inside the message
            if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON') {
                return;
            }
            
            const isVisible = timestamp.style.display === 'block';
            timestamp.style.display = isVisible ? 'none' : 'block';
            timestamp.style.opacity = isVisible ? '0' : '0.7';
        });
        
        // Scroll to bottom if needed
        if (isScrolledToBottom(messagesContainer)) {
            scrollToBottom(messagesContainer);
        }
        
        return messageDiv;
    }
    
    // Helper function to scroll to bottom
    function scrollToBottom(element) {
        if (element) {
            element.scrollTop = element.scrollHeight;
        }
    }
    
    // Function to create a message element
    function createMessageElement(message, isCurrentUser) {
        // Create elements directly instead of using innerHTML for better performance
        const messageDiv = document.createElement('div');
        const messageWrapper = document.createElement('div');
        messageWrapper.className = 'flex max-w-xs lg:max-md mb-1';
        
        // Set message ID attributes
        if (message.id) {
            messageDiv.setAttribute(String(message.id).startsWith('temp-') ? 'data-temp-id' : 'data-message-id', 
                                  message.id);
        }
        
        // Determine if message is deleted
        const isDeleted = message.is_deleted || message.deleted_at;
        
        // Set base classes
        messageDiv.className = `flex mb-4 ${isCurrentUser ? 'justify-end' : 'justify-start'}`;
        if (isDeleted) {
            messageDiv.classList.add('deleted-message');
        }
        
        // Create avatar if not current user
        if (!isCurrentUser) {
            const avatarDiv = document.createElement('div');
            avatarDiv.className = 'flex-shrink-0';
            
            const avatarImg = document.createElement('img');
            avatarImg.className = 'w-8 h-8 rounded-full object-cover';
            avatarImg.alt = message.sender?.name || 'User';
            
            // Set avatar source with fallback
            const avatarName = message.sender?.name || 'U';
            let avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(avatarName)}`;
            if (message.sender?.face_img) {
                avatarUrl = `{{ asset('storage') }}/${message.sender.face_img}`;
            } else if (message.sender?.avatar) {
                avatarUrl = message.sender.avatar;
            }
            
            avatarImg.src = avatarUrl;
            avatarImg.onerror = function() {
                this.onerror = null;
                this.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(avatarName)}`;
            };
            
            avatarDiv.appendChild(avatarImg);
            messageWrapper.appendChild(avatarDiv);
        }
        
        // Create message content
        const messageContentDiv = document.createElement('div');
        messageContentDiv.className = 'relative';
        
        const messageBubble = document.createElement('div');
        messageBubble.className = `message-content rounded-lg py-2 px-4 cursor-pointer ${
            isDeleted ? 'bg-gray-100 text-gray-500 italic' : 
            (isCurrentUser ? 'bg-blue-500 text-white' : 'bg-white text-gray-800')
        }`;
        messageBubble.setAttribute('data-deleted', isDeleted);
        messageBubble.setAttribute('data-show-time', 'false');
        
        // Add click handler to toggle timestamp
        messageBubble.addEventListener('click', function(e) {
            // Don't toggle if clicking on a link or button inside the message
            if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON') {
                return;
            }
            
            const showTime = this.getAttribute('data-show-time') === 'true';
            this.setAttribute('data-show-time', !showTime);
            const timestamp = this.nextElementSibling;
            if (timestamp) {
                timestamp.style.display = showTime ? 'none' : 'flex';
                // Add a small animation for better UX
                timestamp.style.transition = 'opacity 0.2s ease-in-out';
                timestamp.style.opacity = showTime ? '0' : '0.7';
            }
        });
        
        const messageText = document.createElement('p');
        messageText.className = 'text-sm';
        messageText.textContent = isDeleted ? 'Unsent a message' : (message.message || '');
        
        messageBubble.appendChild(messageText);
        messageContentDiv.appendChild(messageBubble);
        
        // Create timestamp container (initially hidden)
        const timestampContainer = document.createElement('div');
        timestampContainer.className = `hidden ${isCurrentUser ? 'justify-end' : 'justify-start'} mt-1`;
        timestampContainer.style.display = 'none'; // Start hidden
        timestampContainer.style.opacity = '0.7'; // Make timestamp slightly transparent
        
        // Add message timestamp
        const timestampText = document.createElement('span');
        timestampText.className = 'text-xs text-gray-500';
        timestampText.textContent = message.created_at ? formatMessageTime(message.created_at) : 'Sending...';
        timestampContainer.appendChild(timestampText);
        
        // Add edited indicator if applicable
        if (message.edited_at) {
            const editedText = document.createElement('span');
            editedText.className = 'text-xs text-gray-500 ml-2';
            editedText.textContent = '• edited';
            timestampContainer.appendChild(editedText);
        }
        
        messageContentDiv.appendChild(timestampContainer);
        
        messageWrapper.appendChild(messageContentDiv);
        
        // Add user's own avatar on the right if it's their message
        if (isCurrentUser) {
            const userAvatarDiv = document.createElement('div');
            userAvatarDiv.className = 'flex-shrink-0';
            
            const userAvatarImg = document.createElement('img');
            userAvatarImg.className = 'w-8 h-8 rounded-full object-cover';
            userAvatarImg.alt = 'You';
            
            // Set user avatar with fallback
            const userName = 'You';
            let userAvatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(userName)}`;
            if (message.sender?.face_img) {
                userAvatarUrl = `{{ asset('storage') }}/${message.sender.face_img}`;
            } else if (message.sender?.avatar) {
                userAvatarUrl = message.sender.avatar;
            }
            
            userAvatarImg.src = userAvatarUrl;
            userAvatarImg.onerror = function() {
                this.onerror = null;
                this.src = `https://ui-avatars.com/api/?name=U`;
            };
            
            userAvatarDiv.appendChild(userAvatarImg);
            messageWrapper.appendChild(userAvatarDiv);
        }
        
        messageDiv.appendChild(messageWrapper);
        return messageDiv;
    }
    
    // Function to format message time
    function formatMessageTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
    
    // Function to scroll to the bottom of the messages container
    function scrollToBottom() {
        const container = document.getElementById('messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }
    
    // Send message on Enter key (but allow Shift+Enter for new line)
    const messageInput = document.getElementById('message-input');
    if (messageInput) {
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
        
        // Auto-resize textarea as user types
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }

    // Handle delete conversation
    const deleteConversationBtn = document.getElementById('delete-conversation');
    if (deleteConversationBtn) {
        deleteConversationBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const receiverId = window.currentReceiverId;
            showDeleteModal(receiverId);
        });
    }

    // Cancel delete button click handler
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', hideDeleteModal);
    }

    // Confirm delete button click handler
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if (!currentDeleteConversationId) return;
            
            fetch(`/messages/conversation/${currentDeleteConversationId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                hideDeleteModal();
                
                if (data.success) {
                    // Remove the conversation from the list
                    const conversationItem = document.querySelector(`.conversation-item[data-conversation-id="${currentDeleteConversationId}"]`);
                    if (conversationItem) {
                        conversationItem.remove();
                    }
                    
                    // Clear the chat area
                    const messagesContainer = document.getElementById('messages-container');
                    messagesContainer.innerHTML = `
                        <div class="flex justify-center items-center h-full">
                            <p class="text-gray-500 text-sm">Select a conversation to start messaging</p>
                        </div>
                    `;
                    
                    // Hide the chat header
                    const chatHeader = document.getElementById('active-chat');
                    const emptyChat = document.getElementById('empty-chat-state');
                    if (chatHeader && emptyChat) {
                        chatHeader.classList.add('hidden');
                        emptyChat.classList.remove('hidden');
                    }
                } else {
                    alert('Failed to delete conversation. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                hideDeleteModal();
                alert('An error occurred while deleting the conversation.');
            });
        });
    }

    // Close modal when clicking outside
    deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) {
            hideDeleteModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !deleteModal.classList.contains('hidden')) {
            hideDeleteModal();
        }
    });
    
    // Function to update unread message count
    function updateUnreadCount() {
        fetch('{{ route("messages.ajax.unread-count") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
                const unreadCount = data.unread_count || 0;
                const counterElement = document.getElementById('unread-messages-count');
                
                if (counterElement) {
                    if (unreadCount > 0) {
                        counterElement.textContent = unreadCount;
                        counterElement.classList.remove('hidden');
                    } else {
                        counterElement.classList.add('hidden');
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching unread count:', error);
            });
    }

    // Update unread count every 30 seconds
    setInterval(updateUnreadCount, 30000);
    
    // Show toast notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-md shadow-lg text-white ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } transition-all duration-300 transform translate-y-2 opacity-0`;
        
        toast.textContent = message;
        document.body.appendChild(toast);
        
        // Trigger reflow
        void toast.offsetWidth;
        
        // Show toast
        toast.classList.remove('opacity-0', 'translate-y-2');
        toast.classList.add('opacity-100');
        
        // Auto-remove after 3 seconds
        setTimeout(() => {
            toast.classList.remove('opacity-100');
            toast.classList.add('opacity-0', 'translate-y-2');
            
            // Remove from DOM after animation
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }
    
    // Initial update
    updateUnreadCount();

    // Message Context Menu Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const contextMenu = document.getElementById('message-context-menu');
        const deleteModal = document.getElementById('delete-message-modal');
        let longPressTimer;
        let currentMessageElement = null;
        let isContextMenuOpen = false;
        const longPressDuration = 500; // milliseconds

        // Function to show context menu
        function showContextMenu(x, y, messageElement) {
            // Hide any existing context menu first
            if (isContextMenuOpen) {
                hideContextMenu();
            }
            
            currentMessageElement = messageElement;
            
            // Position the context menu
            const menuWidth = 160; // Width of the context menu
            const menuHeight = 80; // Height of the context menu
            
            // Adjust position to ensure it stays within viewport
            const viewportWidth = window.innerWidth;
            const viewportHeight = window.innerHeight;
            
            // Adjust X position if too close to the right edge
            let posX = x;
            if (x + menuWidth > viewportWidth) {
                posX = viewportWidth - menuWidth - 10;
            }
            
            // Adjust Y position if too close to the bottom
            let posY = y;
            if (y + menuHeight > viewportHeight) {
                posY = viewportHeight - menuHeight - 10;
            }
            
            contextMenu.style.left = `${posX}px`;
            contextMenu.style.top = `${posY}px`;
            contextMenu.classList.remove('hidden');
            isContextMenuOpen = true;
            
            // No highlight on the message
            // Prevent any default actions that might interfere
            return false;
        }

        // Function to hide context menu
        function hideContextMenu() {
            if (!isContextMenuOpen) return;
            
            // Add a small delay to prevent immediate hiding on touch devices
            setTimeout(() => {
                if (isContextMenuOpen) {
                    contextMenu.classList.add('hidden');
                    isContextMenuOpen = false;
                    
                    // Clean up
                    if (currentMessageElement) {
                        currentMessageElement = null;
                    }
                }
            }, 100);
        }

        // Handle long press on messages
        function handleLongPress(e) {
            // Only handle left mouse button (0) or touch events
            if (e.type === 'mousedown' && e.button !== 0) return;
            
            // Only handle message content elements (the actual message bubble)
            const messageBubble = e.target.closest('.bg-blue-500, .bg-white');
            if (!messageBubble) return;
            
            // Get the parent message element
            const messageElement = messageBubble.closest('.flex.justify-end, .flex.justify-start');
            if (!messageElement) return;
            
            // Prevent default to avoid text selection and context menu
            e.preventDefault();
            
            // Clear any existing timer
            clearTimeout(longPressTimer);
            
            // Set timer for long press
            longPressTimer = setTimeout(() => {
                // Get position for context menu
                const x = e.clientX || (e.touches && e.touches[0] ? e.touches[0].clientX : 0);
                const y = e.clientY || (e.touches && e.touches[0] ? e.touches[0].clientY : 0);
                
                // Only show if we have valid coordinates
                if (x && y) {
                    showContextMenu(x, y, messageElement);
                }
            }, longPressDuration);
            
            // Prevent context menu on long press
            return false;
        }

        // Cancel long press on mouse up/touch end
        function cancelLongPress() {
            clearTimeout(longPressTimer);
        }

        // Add event listeners for long press
        document.addEventListener('mousedown', handleLongPress);
        document.addEventListener('mouseup', (e) => {
            if (!isContextMenuOpen) {
                cancelLongPress();
            }
            // Don't hide the context menu on mouseup
            return false;
        });
        document.addEventListener('mousemove', (e) => {
            if (!isContextMenuOpen) {
                cancelLongPress();
            }
        });
        
        // Touch events with non-passive listeners to prevent default behavior
        let touchStartTime = 0;
        const touchOptions = { passive: false };
        
        function handleTouchStart(e) {
            touchStartTime = Date.now();
            handleLongPress(e);
        }
        
        function handleTouchEnd(e) {
            // Only cancel if the touch was too short to be a long press
            if (Date.now() - touchStartTime < longPressDuration) {
                cancelLongPress();
            }
            // Don't hide the context menu if it's already shown
        }
        
        function handleTouchMove(e) {
            // Only cancel if we haven't shown the menu yet
            if (!isContextMenuOpen) {
                cancelLongPress();
            }
        }
        
        // Add touch event listeners with non-passive option
        document.addEventListener('touchstart', handleTouchStart, touchOptions);
        document.addEventListener('touchend', handleTouchEnd, touchOptions);
        document.addEventListener('touchmove', handleTouchMove, touchOptions);
        
        // Cleanup function to remove event listeners
        function cleanupTouchListeners() {
            document.removeEventListener('touchstart', handleTouchStart, touchOptions);
            document.removeEventListener('touchend', handleTouchEnd, touchOptions);
            document.removeEventListener('touchmove', handleTouchMove, touchOptions);
        }

        // Close context menu when clicking outside
        const handleClickOutside = (e) => {
            if (isContextMenuOpen && !contextMenu.contains(e.target) && !e.target.closest('#message-context-menu')) {
                hideContextMenu();
            }
        };
        
        // Use mousedown instead of click to prevent race conditions
        document.addEventListener('mousedown', handleClickOutside);
        
        // Clean up event listener when context menu is hidden
        const originalHideContextMenu = hideContextMenu;
        hideContextMenu = function() {
            if (!isContextMenuOpen) return;
            document.removeEventListener('mousedown', handleClickOutside);
            originalHideContextMenu.apply(this, arguments);
            document.addEventListener('mousedown', handleClickOutside);
        };
        
        // Prevent default context menu on long press
        document.addEventListener('contextmenu', (e) => {
            if (isContextMenuOpen) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });

        // Variables to track edit state
        let isEditing = false;
        let currentEditMessageId = null;
        let currentEditMessageElement = null;
        let originalMessageContent = '';
        
        // Get UI elements
        const messageInput = document.getElementById('message-input');
        const sendButton = document.getElementById('send-message');
        const saveEditButton = document.getElementById('save-edit');
        const cancelEditButton = document.getElementById('cancel-edit');
        const editIndicator = document.getElementById('edit-message-indicator');
        
        // Function to reset edit state
        function resetEditState() {
            isEditing = false;
            currentEditMessageId = null;
            currentEditMessageElement = null;
            originalMessageContent = '';
            messageInput.value = '';
            messageInput.placeholder = 'Write a message...';
            saveEditButton.classList.add('hidden');
            editIndicator.classList.add('hidden');
            sendButton.classList.remove('hidden');
        }
        
        // Handle edit button click
        document.getElementById('edit-message-btn')?.addEventListener('click', () => {
            if (!currentMessageElement) return;
            
            const messageContent = currentMessageElement.querySelector('.message-content p');
            if (!messageContent) return;
            
            // Set edit state
            isEditing = true;
            currentEditMessageId = currentMessageElement.getAttribute('data-message-id') || 
                                 currentMessageElement.getAttribute('data-temp-id');
            currentEditMessageElement = currentMessageElement;
            originalMessageContent = messageContent.textContent;
            
            // Update UI for editing
            messageInput.value = originalMessageContent;
            messageInput.focus();
            saveEditButton.classList.remove('hidden');
            editIndicator.classList.remove('hidden');
            sendButton.classList.add('hidden');
            
            // Hide context menu
            const contextMenu = document.getElementById('message-context-menu');
            if (contextMenu) {
                contextMenu.classList.add('hidden');
            }
        });
        
        // Handle save edit
        saveEditButton?.addEventListener('click', () => {
            if (!isEditing || !currentEditMessageId || !currentEditMessageElement) return;
            
            const newText = messageInput.value.trim();
            if (!newText || newText === originalMessageContent) {
                resetEditState();
                return;
            }
            
            // Send update request to server
            fetch(`/messages/${currentEditMessageId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-HTTP-Method-Override': 'PUT'
                },
                body: JSON.stringify({
                    message: newText,
                    _method: 'PUT'
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to update message');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && currentEditMessageElement) {
                    // Update the message in the UI
                    const messageContent = currentEditMessageElement.querySelector('.message-content p');
                    if (messageContent) {
                        messageContent.textContent = newText;
                    }
                    
                    // Update in messages array if it exists
                    if (window.messages) {
                        const messageIndex = window.messages.findIndex(m => m.id == currentEditMessageId);
                        if (messageIndex !== -1) {
                            window.messages[messageIndex].message = newText;
                        }
                    }
                    
                    showToast('Message updated successfully', 'success');
                }
            })
            .catch(error => {
                console.error('Error updating message:', error);
                showToast('Failed to update message', 'error');
            })
            .finally(() => {
                resetEditState();
            });
        });
        
        // Handle cancel edit
        cancelEditButton?.addEventListener('click', resetEditState);
        
        // Handle Enter key in message input
        messageInput?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (isEditing) {
                    saveEditButton.click();
                } else {
                    sendMessage();
                }
            } else if (e.key === 'Escape' && isEditing) {
                resetEditState();
            }
        });
        
        // Update send button click handler to check for edit mode
        const originalSendMessage = window.sendMessage;
        window.sendMessage = function() {
            if (isEditing) {
                saveEditButton.click();
                return;
            }
            if (originalSendMessage) {
                originalSendMessage();
            }
        };
        
        // This is now handled by the mousedown listener above

        // Handle delete button click
        document.getElementById('delete-message-btn')?.addEventListener('click', () => {
            if (!currentMessageElement) return;
            
            // Show delete confirmation modal
            deleteModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Store the message ID for deletion
            const messageId = currentMessageElement.getAttribute('data-message-id') || 
                             currentMessageElement.getAttribute('data-temp-id');
            
            // Store the message ID in a variable to avoid scope issues
            const messageIdToDelete = currentMessageElement.getAttribute('data-message-id') || 
                                   currentMessageElement.getAttribute('data-temp-id');
            
            if (!messageIdToDelete) {
                console.error('No message ID found');
                return;
            }
            
            // Store a reference to the message element
            const messageElementToDelete = currentMessageElement;
            
            // Handle delete confirmation
            document.getElementById('confirm-delete-message').onclick = () => {
                
                // Show loading state
                const deleteBtn = document.getElementById('confirm-delete-message');
                const originalText = deleteBtn.innerHTML;
                deleteBtn.disabled = true;
                deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                
                // Send delete request to the server
                fetch(`/messages/${messageIdToDelete}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to delete message');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.deleted_message) {
                        // Update the message to show 'Unsent a message' in gray italic
                        if (messageElementToDelete) {
                            const messageContent = messageElementToDelete.querySelector('.message-content');
                            if (messageContent) {
                                // Update the message content
                                messageContent.innerHTML = '<p class="text-sm text-gray-500 italic">Unsent a message</p>';
                                messageContent.className = 'message-content bg-gray-100 text-gray-500 italic rounded-lg py-2 px-4';
                                
                                // Update the message data attribute to mark it as deleted
                                messageElementToDelete.setAttribute('data-deleted', 'true');
                                if (data.deleted_message && data.deleted_message.deleted_at) {
                                    messageElementToDelete.setAttribute('data-deleted-at', data.deleted_message.deleted_at);
                                }
                                
                                // Disable context menu for deleted messages
                                messageElementToDelete.style.pointerEvents = 'none';
                                
                                // Remove any existing context menu
                                const existingContextMenu = document.getElementById('message-context-menu');
                                if (existingContextMenu) {
                                    existingContextMenu.classList.add('hidden');
                                }
                                
                                // Update the message in the messages array if it exists
                                if (window.messages) {
                                    const messageIndex = window.messages.findIndex(m => m.id == messageIdToDelete);
                                    if (messageIndex !== -1 && data.deleted_message) {
                                        window.messages[messageIndex] = data.deleted_message;
                                    }
                                }
                            }
                        }
                        
                        // Show success message
                        showToast('Message deleted successfully', 'success');
                    } else {
                        throw new Error(data.message || 'Failed to delete message');
                    }
                })
                .catch(error => {
                    console.error('Error deleting message:', error);
                    showToast('Failed to delete message: ' + (error.message || 'Unknown error'), 'error');
                })
                .finally(() => {
                    // Hide the modal and reset button state
                    deleteModal.classList.add('hidden');
                    document.body.style.overflow = '';
                    if (deleteBtn) {
                        deleteBtn.disabled = false;
                        deleteBtn.innerHTML = originalText;
                    }
                    hideContextMenu();
                });
            };
            
            // Handle cancel button
            document.getElementById('cancel-delete-message').onclick = () => {
                deleteModal.classList.add('hidden');
                document.body.style.overflow = '';
            };
        });
        
        // Close modal when clicking outside
        deleteModal.addEventListener('click', (e) => {
            if (e.target === deleteModal) {
                deleteModal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });
    });
</script>
@endpush
@endsection
