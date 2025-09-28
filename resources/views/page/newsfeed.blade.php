@extends('layouts.app')

@section('title', 'Homeowner Dashboard | INeedHand')

@php
    $containerWidth = 'w-[100%]';
    $containerHeight = 'h-[screen]';
    $marginTop = 'mt-[80px]';
    $marginBottom = 'mb-[20px]';
    $containerBg = 'bg-none';
@endphp
@section('content')
<style>
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>
<div class="container">
    <x-success />
    <x-delpost />
    <div class="flex justify-center md:flex-row">
        <div class="cont w-full md:w-2/3">
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                @if(auth()->user()->role !== 'ServiceProvider' || auth()->user()->role === 'Admin')
                <div class="bg-gray-100 p-4 rounded-lg mb-6">
                    <div class="flex items-center space-x-4 mb-4">
                        <img src="{{ Auth::user()->face_img ? asset('storage/' . Auth::user()->face_img) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}" 
                             alt="{{ Auth::user()->name }}" 
                             class="w-10 h-10 rounded-full">
                        <span class="font-medium">{{ Auth::user()->name }}</span>
                    </div>
                    <form action="{{ route('posts.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <textarea name="content" rows="3" 
                                    class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="What's on your mind?" required></textarea>
                            </div>
                            @if(auth()->user()->role !== 'Admin' && auth()->user()->role !== 'ServiceProvider')
                            <div class="flex gap-4">
                                <div class="flex-1">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-map-marker-alt text-red-500"></i>
                                        </div>
                                        <select name="barangay_id" 
                                                class="w-full pl-10 p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                required>
                                            <option value="" disabled selected>Select Barangay</option>
                                            @foreach($barangays as $barangay)
                                            <option value="{{ $barangay->brgy_id }}" 
                                                    {{ old('barangay_id') == $barangay->brgy_id ? 'selected' : '' }}>
                                                {{ $barangay->brgy_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-tools text-blue-500"></i>
                                        </div>
                                        <select name="service_id" 
                                                class="w-full pl-10 p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="" disabled selected>Select Service</option>
                                            @foreach($services as $service)
                                            <option value="{{ $service->service_id }}" 
                                                    {{ old('service_id') == $service->service_id ? 'selected' : '' }}>
                                                {{ $service->service_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="w-32">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-green-500 font-medium">₱</span>
                                        </div>
                                        <input type="number" 
                                               name="budget" 
                                               class="w-full pl-8 p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               min="0"
                                               placeholder=""
                                               step="0.01"
                                               value="{{ old('budget') }}">
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <div>
                                <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-paper-plane me-2"></i> Post
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                @endif

                <div class="space-y-6">
                    @foreach($posts as $post)
                    <div class="bg-gray-50 rounded-lg p-4 mb-4 post" data-post-id="{{ $post->id }}">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-4 mb-2">
                                    <img src="{{ $post->user->face_img ? asset('storage/' . $post->user->face_img) : 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name) }}" 
                                         alt="{{ $post->user->name }}" 
                                         class="w-10 h-10 rounded-full">
                                    <div>
                                        <h3 class="font-medium">{{ $post->user->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    
                                    @if(!$post->user || $post->user->role !== 'Admin')
                                    <div class="flex gap-4">
                                        @if($post->barangay_name)
                                        <div class="flex items-center space-x-2">
                                            <span class="font-medium"><i class="fas fa-map-marker-alt text-red-500"></i> {{ $post->barangay_name }}</span>
                                        </div>
                                        @endif
                                        @if($post->service_name)
                                            <div class="flex items-center space-x-2">
                                                <span class="font-medium"><i class="fas fa-tools text-blue-500"></i> {{ $post->service_name }}</span>
                                            </div>
                                        @endif
                                        @if($post->budget)
                                            <div class="flex items-center space-x-2">
                                                <span class="font-medium">
                                                    <span class="text-green-500 font-medium">₱</span>
                                                    {{ number_format($post->budget, $post->budget == floor($post->budget) ? 0 : 2) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    @endif
                                    @if(auth()->id() === $post->user_id)
                                    <div class="relative">
                                        <button 
                                            type="button"
                                            class="text-gray-400 hover:text-gray-600 focus:outline-none"
                                            onclick="toggleMenu({{ $post->id }})">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 512" class="w-5 h-5">
                                                <path d="M64 360a56 56 0 1 0 0 112 56 56 0 1 0 0-112zm0-160a56 56 0 1 0 0 112 56 56 0 1 0 0-112zM120 96A56 56 0 1 0 8 96a56 56 0 1 0 112 0z"/>
                                            </svg>
                                        </button>
                                        <div 
                                            id="menu-{{ $post->id }}" 
                                            class="hidden absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-100">
                                            <a href="{{ route('posts.edit', $post->id) }}" 
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                <i class="fas fa-edit mr-2 text-blue-500"></i> Edit
                                            </a>
                                            <button type="button" 
                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                                                    data-delete-post="{{ $post->id }}">
                                                <i class="fas fa-trash-alt mr-2"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                        <div class="mb-4">
                            <p class="text-gray-700">{{ $post->content }}</p>
                        </div>

                        <!-- Like and Comment Counter -->
                        <div class="flex items-center text-sm text-gray-500 space-x-4 mb-1" id="counters-{{ $post->id }}" style="display: {{ ($post->likes->count() > 0 || $post->comments->count() > 0) ? 'flex' : 'none' }};">
                            @if($post->likes->count() > 0)
                            <div class="flex items-center post-likes-counter">
                                <svg class="w-4 h-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                </svg>
                                <span class="post-likes-count">{{ $post->likes->count() }}</span>
                                <span class="ml-1">{{ $post->likes->count() === 1 ? 'like' : 'likes' }}</span>
                            </div>
                            @endif
                            @if($post->comments->count() > 0)
                            <div class="flex items-center post-comments-counter">
                                <svg class="w-4 h-4 text-blue-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span class="post-comments-count">{{ $post->comments->count() }}</span>
                                <span class="comment-text ml-1">{{ $post->comments->count() === 1 ? 'comment' : 'comments' }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-between border-t pt-2">
                            <div class="flex items-center space-x-4">
                                @php
                                    $userLiked = $post->isLikedByCurrentUser();
                                @endphp
                                <button type="button" 
                                        data-post-id="{{ $post->id }}"
                                        class="like-button flex items-center {{ $userLiked ? 'text-red-500' : 'text-gray-600 hover:text-blue-600' }}">
                                    <svg class="w-5 h-5" fill="{{ $userLiked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                    <span class="like-text ml-1">{{ $userLiked ? 'Liked' : 'Like' }}</span>
                                    <span class="like-count ml-2 hidden">{{ $post->likes->count() }}</span>
                                </button>

                                <button type="button" 
                                        class="flex items-center text-gray-600 hover:text-blue-600 comment-button"
                                        onclick="openCommentModal('{{ $post->id }}')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    <span class="ml-1">Comment</span>
                                    @if($post->comments->count() > 0)
                                    <span class="ml-1 text-gray-700 post-comments-count hidden">{{ $post->comments->count() }}</span>
                                    @endif
                                </button>
                            </div>
                        </div>

                        @include('partials.comment-modal', ['post' => $post])

                    </div>
                    @endforeach

                    {{ $posts->links() }}
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function toggleMenu(postId) {
        const menu = document.getElementById('menu-' + postId);
        menu.classList.toggle('hidden');
    }

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const menus = document.querySelectorAll('.hidden');
        menus.forEach(menu => {
            if (!menu.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    });

    // Handle delete post confirmation
    document.addEventListener('alpine:init', () => {
        Alpine.data('deleteModal', () => ({
            show: false,
            postId: null,
            init() {
                this.$watch('show', value => {
                    if (value) {
                        document.body.style.overflow = 'hidden';
                    } else {
                        document.body.style.overflow = 'auto';
                    }
                });
            },
            openModal(postId) {
                this.postId = postId;
                this.show = true;
            },
            closeModal() {
                this.show = false;
                this.postId = null;
            }
        }));
    });
</script>
<script>
// Comment Modal Functions
function openCommentModal(postId) {
    document.getElementById(`commentModal-${postId}`).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeCommentModal(postId) {
    document.getElementById(`commentModal-${postId}`).classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('bg-opacity-50')) {
        const modals = document.querySelectorAll('[id^="commentModal-"]');
        modals.forEach(modal => {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        });
    }
});

// Like button functionality
document.addEventListener('DOMContentLoaded', function() {
    // Handle like button clicks in the comment modal
    document.addEventListener('click', function(e) {
        const modalLikeButton = e.target.closest('.modal-like-button');
        if (modalLikeButton && !modalLikeButton.disabled) {
            e.preventDefault();
            const postId = modalLikeButton.dataset.postId;
            const mainLikeButton = document.querySelector(`.like-button[data-post-id="${postId}"]`);
            const modalLikeText = modalLikeButton.querySelector('.modal-like-text');
            const isLiked = modalLikeButton.classList.contains('text-red-500');
            
            // Show loading state
            const originalText = modalLikeText.textContent;
            modalLikeButton.disabled = true;
            modalLikeText.textContent = isLiked ? 'Unliking...' : 'Liking...';
            
            if (mainLikeButton) {
                mainLikeButton.click();
            }
            
            // Re-enable button after a short delay in case of error
            setTimeout(() => {
                if (modalLikeButton) {
                    modalLikeButton.disabled = false;
                    modalLikeText.textContent = originalText;
                }
            }, 3000);
        }
    });
    document.querySelectorAll('.like-button').forEach(button => {
        button.addEventListener('click', async function(e) {
            // Prevent default form submission and stop event propagation
            e.preventDefault();
            e.stopPropagation();
            
            // Disable the button to prevent multiple clicks
            this.disabled = true;
            
            const postId = this.getAttribute('data-post-id');
            const likeButton = this;
            const likeText = this.querySelector('.like-text');
            const likeCount = this.querySelector('.like-count');
            const isLiked = this.classList.contains('text-red-500');
            
            // Store original state for potential rollback
            const originalState = {
                isLiked: isLiked,
                text: likeText.textContent,
                count: likeCount ? parseInt(likeCount.textContent) : 0
            };
            
            // Show loading state in main button
            likeText.textContent = isLiked ? 'Unliking...' : 'Liking...';
            
            // Show loading state in modal button if open
            const modalLikeButton = document.querySelector(`#commentModal-${postId} .modal-like-button`);
            const modalLikeText = modalLikeButton ? modalLikeButton.querySelector('.modal-like-text') : null;
            if (modalLikeButton && modalLikeText) {
                modalLikeButton.disabled = true;
                modalLikeText.textContent = isLiked ? 'Unliking...' : 'Liking...';
            }
            
            // Use different endpoints for like and unlike to ensure proper routing
            const url = `/posts/${postId}/likes`;
            const method = isLiked ? 'DELETE' : 'POST';
            
            // Add a timestamp to prevent caching
            const timestamp = new Date().getTime();
            const urlWithTimestamp = `${url}?_=${timestamp}`;
            
            console.log('Sending request:', { url, method, isLiked });
            
            try {
                const response = await fetch(urlWithTimestamp, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                
                const data = await response.json();
                
                console.log('Server response:', {
                    success: data.success,
                    isLiked: data.isLiked,
                    likesCount: data.likesCount,
                    rawData: data
                });
                
                if (data.success !== undefined) {
                    const isNowLiked = data.isLiked;
                    const newCount = data.likesCount || 0;
                    
                    // Update main button appearance based on server response
                    likeButton.classList.toggle('text-red-500', isNowLiked);
                    likeButton.classList.toggle('text-gray-600', !isNowLiked);
                    likeButton.querySelector('svg').setAttribute('fill', isNowLiked ? 'currentColor' : 'none');
                    likeText.textContent = isNowLiked ? 'Liked' : 'Like';
                    
                    // Update modal button if open
                    const modalLikeButton = document.querySelector(`#commentModal-${postId} .modal-like-button`);
                    const modalLikeIcon = modalLikeButton ? modalLikeButton.querySelector('.modal-like-icon') : null;
                    const modalLikeText = modalLikeButton ? modalLikeButton.querySelector('.modal-like-text') : null;
                    
                    if (modalLikeButton && modalLikeIcon && modalLikeText) {
                        modalLikeButton.disabled = false;
                        modalLikeButton.classList.toggle('text-red-500', isNowLiked);
                        modalLikeButton.classList.toggle('text-gray-600', !isNowLiked);
                        modalLikeButton.classList.toggle('hover:text-blue-600', !isNowLiked);
                        modalLikeIcon.style.fill = isNowLiked ? 'currentColor' : 'none';
                        modalLikeIcon.style.stroke = 'currentColor';
                        modalLikeText.textContent = isNowLiked ? 'Liked' : 'Like';
                    }
                    
                    // Update like count in the button
                    if (likeCount) {
                        likeCount.textContent = newCount;
                    }
                    
                    // Update the counters section in the post
                    const countersContainer = document.getElementById(`counters-${postId}`);
                    if (countersContainer) {
                        let postLikesCounter = countersContainer.querySelector('.post-likes-count');
                        let postLikesText = countersContainer.querySelector('.post-likes-counter span:last-child');
                        
                        if (newCount > 0) {
                            if (!postLikesCounter) {
                                // Create the likes counter if it doesn't exist
                                const likesCounterHtml = `
                                    <div class="flex items-center post-likes-counter">
                                        <svg class="w-4 h-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="post-likes-count">${newCount}</span>
                                        <span class="ml-1">${newCount === 1 ? 'like' : 'likes'}</span>
                                    </div>
                                `;
                                countersContainer.insertAdjacentHTML('afterbegin', likesCounterHtml);
                            } else {
                                // Update existing counter
                                postLikesCounter.textContent = newCount;
                                if (postLikesText) {
                                    postLikesText.textContent = newCount === 1 ? 'like' : 'likes';
                                }
                            }
                        } else if (postLikesCounter) {
                            // Remove likes counter if count is 0
                            postLikesCounter.closest('.post-likes-counter').remove();
                        }
                        
                        // Update visibility of counters container
                        const hasLikes = newCount > 0;
                        const hasComments = countersContainer.querySelector('.post-comments-counter');
                        countersContainer.style.display = (hasLikes || hasComments) ? 'flex' : 'none';
                    }
                    
                    // Dispatch event to update like counter in comment modal
                    const likeEvent = new CustomEvent('likeUpdated', {
                        detail: {
                            postId: postId,
                            count: newCount,
                            isLiked: isNowLiked
                        }
                    });
                    document.dispatchEvent(likeEvent);
                    
                    console.log('UI updated:', { isNowLiked, newCount });
                } else {
                    console.error('Invalid response format:', data);
                }
                
                // Re-enable the button
                likeButton.disabled = false;
            } catch (error) {
                console.error('Error:', error);
                
                // Revert to original state
                likeButton.classList.toggle('text-red-500', originalState.isLiked);
                likeButton.classList.toggle('text-gray-600', !originalState.isLiked);
                likeButton.classList.toggle('hover:text-blue-600', !originalState.isLiked);
                likeButton.querySelector('svg').setAttribute('fill', originalState.isLiked ? 'currentColor' : 'none');
                likeText.textContent = originalState.text;
                
                // Update like count in the button
                if (likeCount) {
                    likeCount.textContent = originalState.count;
                }
                
                // Update the counters section in the post
                const countersContainer = document.getElementById(`counters-${postId}`);
                if (countersContainer) {
                    let postLikesCounter = countersContainer.querySelector('.post-likes-count');
                    let postLikesText = countersContainer.querySelector('.post-likes-counter span:last-child');
                    
                    if (originalState.count > 0) {
                        if (!postLikesCounter) {
                            // Create the likes counter if it doesn't exist
                            const likesCounterHtml = `
                                <div class="flex items-center post-likes-counter">
                                    <svg class="w-4 h-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="post-likes-count">${originalState.count}</span>
                                    <span class="ml-1">${originalState.count === 1 ? 'like' : 'likes'}</span>
                                </div>
                            `;
                            countersContainer.insertAdjacentHTML('afterbegin', likesCounterHtml);
                        } else {
                            // Update existing counter
                            postLikesCounter.textContent = originalState.count;
                            if (postLikesText) {
                                postLikesText.textContent = originalState.count === 1 ? 'like' : 'likes';
                            }
                        }
                    } else if (postLikesCounter) {
                        // Remove likes counter if count is 0
                        postLikesCounter.closest('.post-likes-counter').remove();
                    }
                    
                    // Update visibility of counters container
                    const hasLikes = originalState.count > 0;
                    const hasComments = countersContainer.querySelector('.post-comments-counter');
                    countersContainer.style.display = (hasLikes || hasComments) ? 'flex' : 'none';
                }
                
                // Dispatch event to revert like counter in comment modal
                const likeEvent = new CustomEvent('likeUpdated', {
                    detail: {
                        postId: postId,
                        count: originalState.count,
                        isLiked: originalState.isLiked
                    }
                });
                document.dispatchEvent(likeEvent);
                
                alert('Failed to update like. Please try again.');
            } finally {
                // Re-enable the button
                likeButton.disabled = false;
            }
        });
    });
});
</script>
@endsection
