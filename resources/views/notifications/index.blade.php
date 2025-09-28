@extends('layouts.app')

@php
    // Debug: Dump the notifications collection
    // Remove this after debugging
    // dd($notifications);
    
    // Debug: Log the notifications to the console
    echo "<script>console.log('Notifications:', " . json_encode($notifications) . ")</script>";
@endphp

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-semibold text-gray-800">Notifications</h2>
                    @if($notifications->where('is_read', false)->count() > 0)
                        <form action="{{ route('notifications.read-all') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-blue-600 hover:text-blue-800 focus:outline-none">
                                Mark all as read
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="divide-y divide-gray-200">
                @php
                    // Debug: Dump notifications to check what we're working with
                    // Remove this after debugging
                    // dd($notifications);
                @endphp

                @forelse($notifications as $notification)
                    <div class="p-4 hover:bg-gray-50 transition duration-150 ease-in-out {{ !$notification->is_read ? 'bg-blue-50' : '' }}">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-4">
                                @if($notification->relatedUser)
                                    @php
                                        $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($notification->relatedUser->name);
                                        if ($notification->relatedUser->face_img) {
                                            $avatarUrl = asset('storage/' . $notification->relatedUser->face_img);
                                        } elseif (!empty($notification->relatedUser->avatar)) {
                                            $avatarUrl = $notification->relatedUser->avatar;
                                        }
                                    @endphp
                                    <img class="h-10 w-10 rounded-full" 
                                         src="{{ $avatarUrl }}" 
                                         alt="{{ $notification->relatedUser->name }}">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-bell text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between">
                                    <p class="text-sm font-medium text-gray-900">
                                        @if(isset($notification->title) && $notification->title)
                                            {{ $notification->title }}
                                        @else
                                            {{ ucfirst(str_replace('_', ' ', $notification->type)) }}
                                        @endif
                                    </p>
                                    <div class="flex items-center">
                                        <span class="text-xs text-gray-500">
                                            {{ $notification->created_at ? $notification->created_at->diffForHumans() : 'Just now' }}
                                        </span>
                                        @if(!$notification->is_read)
                                            <form action="{{ route('notifications.read', $notification) }}" method="POST" class="ml-2 mark-as-read">
                                                @csrf
                                                <button type="submit" class="text-blue-600 hover:text-blue-800 focus:outline-none" 
                                                        title="Mark as read">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-1 text-sm text-gray-600">
                                    {{ $notification->message }}
                                </div>
                                @if($notification->post_id)
                                    <div class="mt-2">
                                        <a href="{{ route('page.newsfeed') }}#post-{{ $notification->post_id }}" 
                                           class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                            View Post
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <i class="fas fa-bell-slash text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">No notifications yet.</p>
                    </div>
                @endforelse
            </div>

            @if($notifications->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .pagination {
        display: flex;
        justify-content: center;
        list-style: none;
        padding: 0;
    }
    .pagination li {
        margin: 0 4px;
    }
    .pagination .page-link {
        display: inline-block;
        padding: 6px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        color: #4a5568;
        text-decoration: none;
    }
    .pagination .page-item.active .page-link {
        background-color: #4299e1;
        color: white;
        border-color: #4299e1;
    }
    .pagination .page-item.disabled .page-link {
        color: #a0aec0;
        pointer-events: none;
    }
</style>
<script>
    // Handle mark as read via AJAX
    document.querySelectorAll('form.mark-as-read').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the unread indicator
                    const notificationItem = this.closest('div[class*="bg-blue-50"]');
                    if (notificationItem) {
                        notificationItem.classList.remove('bg-blue-50');
                    }
                    // Remove the mark as read button
                    this.remove();
                    
                    // Update the unread count in the navbar if it exists
                    const unreadCount = document.querySelector('.unread-count');
                    if (unreadCount) {
                        const count = parseInt(unreadCount.textContent);
                        if (count > 1) {
                            unreadCount.textContent = count - 1;
                        } else {
                            unreadCount.closest('.relative').remove();
                        }
                    }
                }
            });
        });
    });
</script>
@endsection
