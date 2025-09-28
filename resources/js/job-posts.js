
function toggleMenu(postId) {
    $(`#menu-${postId}`).toggleClass('hidden');
}

$(document).on('click', function(event) {
    if (!$(event.target).closest('.hidden').length) {
        $('.hidden').addClass('hidden');
    }
});

function openCommentModal(postId) {
    $(`#commentModal-${postId}`).removeClass('hidden');
    $('body').css('overflow', 'hidden');
}

function closeCommentModal(postId) {
    $(`#commentModal-${postId}`).addClass('hidden');
    $('body').css('overflow', 'auto');
}

$(document).on('click', function(event) {
    if ($(event.target).hasClass('bg-opacity-50')) {
        $('[id^="commentModal-"]').addClass('hidden');
        $('body').css('overflow', 'auto');
    }
});

$(document).on('click', '[id^="commentModal-"] .bg-white', function(event) {
    event.stopPropagation();
});

$(document).ready(function() {
    $(document).on('click', '.like-button', function() {
        const $button = $(this);
        const postId = $button.data('post-id');
        const $likeText = $button.find('.like-text');
        const isLiked = $likeText.text().trim() === 'Liked';
        const url = `/posts/${postId}/likes`;
        const method = isLiked ? 'DELETE' : 'POST';
        
        $likeText.text(isLiked ? 'Unliking...' : 'Liking...');
        
        $.ajax({
            url: url,
            method: method,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(data) {
                if (data.success) {
                    $button.toggleClass('text-red-500 text-gray-600 hover:text-blue-600');
                    
                    const $svg = $button.find('svg');
                    $svg.attr('fill', !isLiked ? 'currentColor' : 'none');
                    $likeText.text(!isLiked ? 'Liked' : 'Like');
                    
                    const $countersContainer = $(`#counters-${postId}`);
                    const newLikesCount = data.likesCount;
                    
                    $countersContainer.css('display', 'flex');
                    
                    let $likesCounter = $countersContainer.find('.post-likes-counter');
                    
                    if (newLikesCount > 0) {
                        if ($likesCounter.length === 0) {
                            const likesHtml = `
                                <div class="flex items-center post-likes-counter">
                                    <span class="post-likes-count">${newLikesCount}</span>
                                    <span class="ml-1">${newLikesCount === 1 ? ' like' : ' likes'}</span>
                                </div>
                            `;
                            
                            const $commentsCounter = $countersContainer.find('.post-comments-counter');
                            if ($commentsCounter.length) {
                                $commentsCounter.before(likesHtml);
                            } else {
                                $countersContainer.prepend(likesHtml);
                            }
                        } else {
                            const $countElement = $likesCounter.find('.post-likes-count');
                            const $textElement = $likesCounter.find('span:last-child');
                            
                            if ($countElement.length) $countElement.text(newLikesCount);
                            if ($textElement.length) {
                                $textElement.text(newLikesCount === 1 ? ' like' : ' likes');
                            }
                        }
                    } else if ($likesCounter.length) {
                        $likesCounter.remove();
                        
                        if ($countersContainer.children().length === 0) {
                            $countersContainer.hide();
                        }
                    }
                    
                    const $modalLikesCounter = $(`#modal-post-${postId} .post-likes-counter`);
                    if ($modalLikesCounter.length) {
                        const $modalCount = $modalLikesCounter.find('.post-likes-count');
                        const $modalText = $modalLikesCounter.find('span:last-child');
                        
                        if (newLikesCount > 0) {
                            if ($modalCount.length) $modalCount.text(newLikesCount);
                            if ($modalText.length) {
                                $modalText.text(newLikesCount === 1 ? ' like' : ' likes');
                            }
                            $modalLikesCounter.show();
                        } else {
                            $modalLikesCounter.hide();
                        }
                    }
                }
            },
            error: function(error) {
                console.error('Error:', error);
                $likeText.text(isLiked ? 'Liked' : 'Like');
            }
        });
    });
});
