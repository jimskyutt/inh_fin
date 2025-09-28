<div id="commentModal-{{ $post->id }}" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative mx-auto mt-2 p-4 border w-11/12 md:w-2/3 lg:w-1/2 xl:w-2/5 max-h-[90vh] shadow-lg rounded-md bg-white" id="modal-post-{{ $post->id }}">
        <!-- Modal content -->
        <div class="mt-3 text-left">
            <!-- Post header -->
            <div class="flex items-center space-x-4 mb-4">
                <img src="{{ $post->user->face_img ? asset('storage/' . $post->user->face_img) : 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name) }}" 
                     alt="{{ $post->user->name }}" 
                     class="w-10 h-10 rounded-full">
                <div>
                    <h3 class="font-medium">{{ $post->user->name }}</h3>
                    <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                </div>
            </div>
            
            <!-- Post content -->
            <div class="mt-2 px-4 py-2">
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
                    <span class="ml-1 like-plural">{{ $post->likes->count() === 1 ? 'like' : 'likes' }}</span>
                </div>
                @endif
                @if($post->comments->count() > 0)
                <div class="flex items-center post-comments-counter">
                    <svg class="w-4 h-4 text-blue-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span class="post-comments-count">{{ $post->comments->count() }}</span>
                    <span class="ml-1">{{ $post->comments->count() === 1 ? 'comment' : 'comments' }}</span>
                </div>
                @endif                   
            </div>
            <script>
            // Listen for like updates from the main post
            document.addEventListener('likeUpdated', function(e) {
                if (e.detail.postId === '{{ $post->id }}') {
                    // Update the like counter display
                    const modalLikesCounter = document.querySelector(`#commentModal-{{ $post->id }} .post-likes-count`);
                    const modalLikesText = document.querySelector(`#commentModal-{{ $post->id }} .like-plural`);
                    const modalCountersContainer = document.querySelector(`#commentModal-{{ $post->id }} #counters-{{ $post->id }}`);
                    
                    if (e.detail.count > 0) {
                        // Update or create the likes counter
                        if (!modalLikesCounter) {
                            const likesCounterHtml = `
                                <div class="flex items-center post-likes-counter">
                                    <svg class="w-4 h-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="post-likes-count">${e.detail.count}</span>
                                    <span class="ml-1 like-plural">${e.detail.count === 1 ? 'like' : 'likes'}</span>
                                </div>
                            `;
                            modalCountersContainer.insertAdjacentHTML('afterbegin', likesCounterHtml);
                        } else {
                            modalLikesCounter.textContent = e.detail.count;
                            if (modalLikesText) {
                                modalLikesText.textContent = e.detail.count === 1 ? 'like' : 'likes';
                            }
                        }
                        if (modalCountersContainer) {
                            modalCountersContainer.style.display = 'flex';
                        }
                    } else if (modalLikesCounter) {
                        // Remove the likes counter if count is 0
                        const likesCounter = modalLikesCounter.closest('.post-likes-counter');
                        if (likesCounter) {
                            likesCounter.remove();
                            // Hide counters container if no likes or comments
                            if (modalCountersContainer && !modalCountersContainer.querySelector('.post-comments-counter')) {
                                modalCountersContainer.style.display = 'none';
                            }
                        }
                    }
                    
                    // Update the like button state in the modal
                    const modalLikeButton = document.querySelector(`#commentModal-{{ $post->id }} .modal-like-button`);
                    const modalLikeIcon = document.querySelector(`#commentModal-{{ $post->id }} .modal-like-icon`);
                    const modalLikeText = document.querySelector(`#commentModal-{{ $post->id }} .modal-like-text`);
                    const modalLikeCount = document.querySelector(`#commentModal-{{ $post->id }} .modal-like-count`);
                    
                    if (modalLikeButton && modalLikeIcon && modalLikeText && modalLikeCount) {
                        if (e.detail.isLiked) {
                            // When liked
                            modalLikeButton.classList.remove('text-gray-600', 'hover:text-blue-600');
                            modalLikeButton.classList.add('text-red-500');
                            modalLikeIcon.style.fill = 'currentColor';
                            modalLikeIcon.style.stroke = 'currentColor';
                            modalLikeText.textContent = 'Liked';
                        } else {
                            // When unliked
                            modalLikeButton.classList.remove('text-red-500');
                            modalLikeButton.classList.add('text-gray-600', 'hover:text-blue-600');
                            modalLikeIcon.style.fill = 'none';
                            modalLikeIcon.style.stroke = 'currentColor';
                            modalLikeText.textContent = 'Like';
                        }
                        modalLikeCount.textContent = e.detail.count;
                    }
                }
            });
            </script>
            
            <script>
            // Handle comment editing
            document.addEventListener('click', function(e) {
                // Close any open edit forms first
                const existingEditForms = document.querySelectorAll('.comment-edit-form');
                existingEditForms.forEach(form => {
                    const content = form.closest('.relative').querySelector('.comment-content');
                    if (content) {
                        content.style.display = 'block';
                    }
                    form.remove();
                });
                
                // Edit comment
                if (e.target.closest('.edit-comment')) {
                    const button = e.target.closest('.edit-comment');
                    const commentId = button.dataset.commentId;
                    const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
                    const commentContent = commentElement.querySelector('.comment-content');
                    const currentContent = commentContent.textContent.trim();
                    
                    // Hide the edit/delete buttons
                    button.closest('.flex').style.display = 'none';
                    
                    // Create textarea for editing
                    const textarea = document.createElement('textarea');
                    textarea.className = 'w-full p-2 border rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent';
                    textarea.value = currentContent;
                    textarea.rows = 2;
                    
                    // Create save and cancel buttons
                    const buttonContainer = document.createElement('div');
                    buttonContainer.className = 'flex space-x-2 mt-2';
                    
                    const saveButton = document.createElement('button');
                    saveButton.type = 'button';
                    saveButton.className = 'px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600';
                    saveButton.textContent = 'Save';
                    
                    const cancelButton = document.createElement('button');
                    cancelButton.type = 'button';
                    cancelButton.className = 'px-3 py-1 bg-gray-200 text-gray-700 text-sm rounded hover:bg-gray-300';
                    cancelButton.textContent = 'Cancel';
                    
                    // Save edited comment
                    saveButton.addEventListener('click', async function() {
                        const newContent = textarea.value.trim();
                        if (newContent && newContent !== currentContent) {
                            try {
                                saveButton.disabled = true;
                                saveButton.textContent = 'Saving...';
                                
                                const response = await fetch(`/comments/${commentId}`, {
                                    method: 'PUT',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ content: newContent })
                                });
                                
                                if (response.ok) {
                                    return response.json().then(data => {
                                        // Update the comment content and timestamp
                                        commentContent.textContent = data.comment.content;
                                        const timestampElement = commentElement.querySelector('.comment-timestamp');
                                        if (timestampElement) {
                                            timestampElement.textContent = 'Updated ' + data.comment.updated_at;
                                        }
                                        
                                        // Restore UI
                                        commentContent.style.display = 'block';
                                        const editForm = commentElement.querySelector('.comment-edit-form');
                                        if (editForm) editForm.remove();
                                        
                                        // Show the edit/delete buttons
                                        const buttons = commentElement.querySelector('.comment-actions');
                                        if (buttons) buttons.style.display = 'flex';
                                    });
                                } else {
                                    throw new Error('Failed to update comment');
                                }
                            } catch (error) {
                                console.error('Error updating comment:', error);
                                alert('Failed to update comment. Please try again.');
                                saveButton.disabled = false;
                                saveButton.textContent = 'Save';
                            }
                        } else {
                            // If no changes, just close the edit form
                            commentContent.style.display = 'block';
                            const editForm = document.querySelector('.comment-edit-form');
                            if (editForm) editForm.remove();
                            const buttons = commentElement.querySelector('.flex');
                            if (buttons) buttons.style.display = 'flex';
                        }
                    });
                    
                    // Cancel edit
                    cancelButton.addEventListener('click', function() {
                        commentContent.style.display = 'block';
                        const editForm = commentElement.querySelector('.comment-edit-form');
                        if (editForm) editForm.remove();
                        
                        // Show the edit/delete buttons
                        const buttons = commentElement.querySelector('.comment-actions');
                        if (buttons) buttons.style.display = 'flex';
                    });
                    
                    // Create edit form container
                    const editForm = document.createElement('div');
                    editForm.className = 'comment-edit-form mt-2';
                    editForm.appendChild(textarea);
                    editForm.appendChild(buttonContainer);
                    buttonContainer.appendChild(saveButton);
                    buttonContainer.appendChild(cancelButton);
                    
                    // Hide content and show edit form
                    commentContent.style.display = 'none';
                    commentContent.parentNode.insertBefore(editForm, commentContent.nextSibling);
                    textarea.focus();
                }
            });
            </script>
            
            <div class="flex items-center justify-between border-t pt-2">
                <div class="flex items-center space-x-4">
                    @php
                        $userLiked = $post->isLikedByCurrentUser();
                    @endphp
                    <button type="button" 
                        data-post-id="{{ $post->id }}"
                        class="modal-like-button flex items-center {{ $userLiked ? 'text-red-500' : 'text-gray-600 hover:text-blue-600' }}">
                        <svg class="w-5 h-5 modal-like-icon" fill="{{ $userLiked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <span class="modal-like-text ml-1">{{ $userLiked ? 'Liked' : 'Like' }}</span>
                        <span class="modal-like-count ml-2 hidden">{{ $post->likes->count() }}</span>
                    </button>

                    <button type="button" 
                        onclick="openCommentModal('{{ $post->id }}')" 
                        class="flex items-center text-gray-600 hover:text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span class="ml-1">Comment</span>
                        <span class="hidden comment-count">{{ $post->comments->count() }}</span>
                    </button>
                </div>
            </div>

            <!-- Comments section -->
            <div class="mt-2 pt-1 flex flex-col" style="height: 20rem;">
                <h4 class="font-medium mb-3">Comments</h4>
                <div class="flex-1 overflow-y-auto pb-16 scrollbar-hide">
                <style>
                    .scrollbar-hide::-webkit-scrollbar {
                        display: none;
                    }
                    .scrollbar-hide {
                        -ms-overflow-style: none;
                        scrollbar-width: none;
                    }
                </style>
                    <div class="space-y-3 pr-2 pb-4" id="comments-container-{{ $post->id }}">
                        @foreach($post->comments->sortByDesc('created_at') as $comment)
                        <div class="group relative flex items-start space-x-3" data-comment-id="{{ $comment->id }}" data-user-id="{{ $comment->user_id }}">
                            <img src="{{ $comment->user->face_img ? asset('storage/' . $comment->user->face_img) : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) }}" 
                                 alt="{{ $comment->user->name }}" 
                                 class="w-8 h-8 rounded-full mt-1">
                            <div class="bg-gray-100 p-2 rounded-lg flex-1">
                                <div class="flex items-center space-x-2">
                                    <span class="font-medium text-sm">{{ $comment->user->name }}</span>
                                    <span class="text-xs text-gray-500 comment-timestamp">
                                        @if($comment->created_at->ne($comment->updated_at))
                                            Updated {{ $comment->getFormattedTime() }}
                                        @else
                                            {{ $comment->getFormattedTime() }}
                                        @endif
                                    </span>
                                    @if(auth()->id() === $comment->user_id)
                                    <div class="comment-actions ml-auto flex space-x-1">
                                        <button type="button" class="edit-comment text-blue-500 hover:text-blue-700 text-sm" data-comment-id="{{ $comment->id }}">
                                            Edit
                                        </button>
                                        <span class="text-gray-300">•</span>
                                        <button type="button" class="delete-comment text-red-500 hover:text-red-700 text-sm" data-comment-id="{{ $comment->id }}" data-bs-toggle="modal" data-bs-target="#deleteCommentModal" data-comment-id="{{ $comment->id }}">
                                            Delete
                                        </button>
                                    </div>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-800 mt-1 comment-content">{{ $comment->content }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Add comment form - Sticky at the bottom -->
                <div class="sticky bottom-0 bg-white pt-2 pb-4 border-t border-gray-200" style="margin-top: 10px;">
                    <form id="comment-form-{{ $post->id }}" 
                          action="{{ route('comments.store', ['post' => $post->id]) }}" 
                          method="POST" 
                          class="flex space-x-2" 
                          onsubmit="event.preventDefault(); submitComment(event, {{ $post->id }});">
                        @csrf
                        <input type="text" 
                               name="content" 
                               id="comment-input-{{ $post->id }}"
                               placeholder="Write a comment..." 
                               class="flex-1 p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition whitespace-nowrap">
                            Post
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteCommentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Delete Comment</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('deleteCommentModal').classList.add('hidden')">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-2">
                    <p class="text-sm text-gray-600">Are you sure you want to delete this comment? This action cannot be undone.</p>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="document.getElementById('deleteCommentModal').classList.add('hidden')">
                        Cancel
                    </button>
                    <button type="button" id="confirmDeleteComment" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let commentToDelete = null;
    const deleteModal = document.getElementById('deleteCommentModal');
    
    // Handle delete button click
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-comment')) {
            e.preventDefault();
            commentToDelete = e.target.closest('.delete-comment').dataset.commentId;
            deleteModal.classList.remove('hidden');
        }
    });

    // Handle confirm delete
    document.getElementById('confirmDeleteComment').addEventListener('click', function() {
        if (!commentToDelete) return;
        
        const commentElement = document.querySelector(`[data-comment-id="${commentToDelete}"]`);
        
        // Get the post ID from the modal
        const postId = '{{ $post->id }}';
        
        fetch(`/comments/${commentToDelete}?post_id=${postId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(async response => {
            const data = await response.json().catch(() => ({}));
            
            if (!response.ok) {
                console.error('Server response:', data);
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            }
            
            return data;
        })
        .then(data => {
            if (data.success) {
                // Remove the comment from the UI
                if (commentElement) commentElement.remove();
                
                // Get the post ID from the URL or data attribute
                const postId = data.post_id || '{{ $post->id }}';
                const newCount = data.comment_count !== undefined ? data.comment_count : 
                               (parseInt(document.querySelector(`#modal-post-${postId} .post-comments-count`)?.textContent || '0') - 1);
                
                // Update comment count in the modal
                const modalCommentCountElement = document.querySelector(`#modal-post-${postId} .post-comments-count`);
                if (modalCommentCountElement) {
                    modalCommentCountElement.textContent = Math.max(0, newCount);
                    
                    // Update the count text in modal
                    const modalCountText = modalCommentCountElement.nextElementSibling;
                    if (modalCountText) {
                        modalCountText.textContent = newCount === 1 ? ' comment' : ' comments';
                    }
                    
                    // Update the counters container visibility in modal
                    const modalCountersContainer = document.querySelector(`#modal-post-${postId} #counters-${postId}`);
                    if (modalCountersContainer) {
                        const hasLikes = modalCountersContainer.querySelector('.post-likes-counter');
                        const hasComments = newCount > 0;
                        modalCountersContainer.style.display = (hasLikes || hasComments) ? 'flex' : 'none';
                    }
                }
                
                // Update comment count in the newsfeed
                const newsfeedPost = document.querySelector(`.post[data-post-id="${postId}"]`);
                if (newsfeedPost) {
                    // Find all comment counters in the newsfeed post
                    const feedCommentCountElements = newsfeedPost.querySelectorAll('.post-comments-count');
                    const commentCounters = newsfeedPost.querySelectorAll('.post-comments-counter');
                    
                    // Update all comment count elements
                    feedCommentCountElements.forEach(element => {
                        element.textContent = newCount;
                        // Hide the counter if no comments
                        if (element.closest('.post-comments-counter')) {
                            element.closest('.post-comments-counter').style.display = newCount > 0 ? 'flex' : 'none';
                        }
                    });
                    
                    // Update the count text in newsfeed
                    commentCounters.forEach(counter => {
                        const countText = counter.querySelector('.comment-text');
                        if (countText) {
                            countText.textContent = newCount === 1 ? ' comment' : ' comments';
                        }
                    });
                    
                    // Update the counters container visibility in newsfeed
                    const countersContainer = newsfeedPost.querySelector(`#counters-${postId}`);
                    if (countersContainer) {
                        const hasLikes = countersContainer.querySelector('.post-likes-counter');
                        const hasComments = newCount > 0;
                        countersContainer.style.display = (hasLikes || hasComments) ? 'flex' : 'none';
                        
                        // If there's a comment button with a counter, update it too
                        const commentButton = newsfeedPost.querySelector('.comment-button .post-comments-count');
                        if (commentButton) {
                            commentButton.textContent = newCount;
                            // Hide the counter in the comment button if no comments
                            commentButton.style.display = newCount > 0 ? 'inline' : 'none';
                        }
                    }
                }
                
                // Close the modal and clean up
                deleteModal.classList.add('hidden');
                commentToDelete = null;
            }
        })
        .catch(error => {
            console.error('Error deleting comment:', error);
            alert('Failed to delete comment. Please try again.');
        });
    });
});
</script>
@endpush
<script>
function submitComment(event, postId) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const button = form.querySelector('button[type="submit"]');
    const originalButtonText = button.textContent;
    const commentInput = document.getElementById(`comment-input-${postId}`);
    
    // Show loading state
    button.disabled = true;
    button.textContent = 'Posting...';
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear the input
            commentInput.value = '';
            
            // Create and prepend the new comment with edit/delete actions
            const commentsContainer = document.getElementById(`comments-container-${postId}`);
            const newComment = document.createElement('div');
            newComment.className = 'group relative flex items-start space-x-3';
            newComment.setAttribute('data-comment-id', data.comment.id);
            newComment.setAttribute('data-user-id', data.comment.user_id);
            newComment.innerHTML = `
                <img src="${data.comment.user.face_img ? data.comment.user.face_img : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(data.comment.user.name)}" 
                     alt="${data.comment.user.name}" 
                     class="w-8 h-8 rounded-full mt-1">
                <div class="bg-gray-100 p-2 rounded-lg flex-1">
                    <div class="flex items-center space-x-2">
                        <span class="font-medium text-sm">${data.comment.user.name}</span>
                        <span class="text-xs text-gray-500">just now</span>
                        <div class="comment-actions ml-auto flex space-x-1">
                            <button type="button" class="edit-comment text-blue-500 hover:text-blue-700 text-sm" data-comment-id="${data.comment.id}">
                                Edit
                            </button>
                            <span class="text-gray-300">•</span>
                            <button type="button" class="delete-comment text-red-500 hover:text-red-700 text-sm" data-comment-id="${data.comment.id}" data-bs-toggle="modal" data-bs-target="#deleteCommentModal">
                                Delete
                            </button>
                        </div>
                    </div>
                    <p class="comment-content text-sm text-gray-800 mt-1">${data.comment.content}</p>
                </div>`;
            
            // Add event listeners for the new comment's edit and delete buttons
            const editButton = newComment.querySelector('.edit-comment');
            const deleteButton = newComment.querySelector('.delete-comment');
            
            if (editButton) {
                editButton.addEventListener('click', function(e) {
                    const commentId = this.dataset.commentId;
                    const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
                    const commentContent = commentElement.querySelector('.comment-content');
                    const currentContent = commentContent.textContent.trim();
                    
                    // Create edit form
                    const editForm = document.createElement('div');
                    editForm.className = 'comment-edit-form w-full';
                    editForm.innerHTML = `
                        <div class="flex space-x-2">
                            <input type="text" 
                                   class="flex-1 p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   value="${currentContent.replace(/"/g, '&quot;')}">
                            <button type="button" class="save-edit bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-600">
                                Save
                            </button>
                            <button type="button" class="cancel-edit text-gray-500 px-3 py-1 rounded-lg hover:bg-gray-100">
                                Cancel
                            </button>
                        </div>`;
                    
                    // Replace content with edit form
                    commentContent.replaceWith(editForm);
                    const textarea = editForm.querySelector('input');
                    textarea.focus();
                    
                    // Handle save
                    editForm.querySelector('.save-edit')?.addEventListener('click', saveEdit);
                    // Handle cancel
                    editForm.querySelector('.cancel-edit')?.addEventListener('click', () => {
                        editForm.replaceWith(commentContent);
                    });
                });
            }
            
            if (deleteButton) {
                deleteButton.addEventListener('click', function(e) {
                    commentToDelete = this.dataset.commentId;
                    deleteModal.classList.remove('hidden');
                });
            }
            
            commentsContainer.prepend(newComment);
            
            // Update comment count in the modal
            const modalCommentCount = document.querySelector(`#modal-post-${postId} .post-comments-count`);
            const modalCommentText = document.querySelector(`#modal-post-${postId} .post-comments-counter span:last-child`);
            const modalCountersContainer = document.querySelector(`#modal-post-${postId} #counters-${postId}`);
            
            if (modalCommentCount) {
                const newCount = parseInt(modalCommentCount.textContent) + 1;
                modalCommentCount.textContent = newCount;
                
                // Update the text (singular/plural)
                if (modalCommentText) {
                    modalCommentText.textContent = newCount === 1 ? ' comment' : ' comments';
                }
                // Show the counters container if it was hidden
                if (modalCountersContainer) {
                    modalCountersContainer.style.display = 'flex';
                }
            }
            
            // Update comment count in the newsfeed
            const newsfeedPost = document.querySelector(`.post[data-post-id="${postId}"]`);
            if (newsfeedPost) {
                // Update all comment count elements in the newsfeed
                const allCommentCounters = document.querySelectorAll(`.post[data-post-id="${postId}"] .post-comments-count`);
                const newCount = parseInt(modalCommentCount?.textContent || '0');
                
                allCommentCounters.forEach(counter => {
                    counter.textContent = newCount;
                    // Show the counter if it was hidden
                    if (counter.closest('.post-comments-counter')) {
                        counter.closest('.post-comments-counter').style.display = newCount > 0 ? 'flex' : 'none';
                    }
                    // Show/hide the counter in the comment button
                    if (counter.classList.contains('post-comments-count')) {
                        counter.style.display = newCount > 0 ? 'inline' : 'none';
                    }
                });
                
                // Update all comment counter texts (singular/plural)
                const allCounterTexts = document.querySelectorAll(`.post[data-post-id="${postId}"] .comment-text`);
                allCounterTexts.forEach(text => {
                    text.textContent = newCount === 1 ? ' comment' : ' comments';
                });
                
                // Update the counters container visibility
                const countersContainer = newsfeedPost.querySelector(`#counters-${postId}`);
                if (countersContainer) {
                    const hasLikes = countersContainer.querySelector('.post-likes-counter');
                    countersContainer.style.display = (hasLikes || newCount > 0) ? 'flex' : 'none';
                }
                
                // Update the comment button counter if it exists
                const commentButtonCounter = newsfeedPost.querySelector('.comment-button .post-comments-count');
                if (commentButtonCounter) {
                    commentButtonCounter.textContent = newCount;
                    commentButtonCounter.style.display = newCount > 0 ? 'inline' : 'none';
                }
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to post comment. Please try again.');
    })
    .finally(() => {
        // Reset button state
        button.disabled = false;
        button.textContent = originalButtonText;
    });
}

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
</script>
