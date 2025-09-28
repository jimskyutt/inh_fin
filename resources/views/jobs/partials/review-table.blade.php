<div class="bg-white shadow overflow-hidden sm:rounded-lg" id="reviewTable">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service Provider</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Review Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed On</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-if="filteredJobs && filteredJobs.length > 0">
                    <template x-for="review in filteredJobs" :key="review.id">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="review.job_title || 'N/A'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="review.service_name || 'N/A'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="review.service_provider_name || 'N/A'"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <template x-for="i in 5" :key="i">
                                        <svg :class="{'text-yellow-400': i <= review.rating, 'text-gray-300': i > review.rating}" 
                                             class="w-5 h-5" 
                                             fill="currentColor" 
                                             viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </template>
                                    <span class="ml-2 text-sm text-gray-500" x-text="review.rating > 0 ? review.rating + '.0' : 'Not rated'"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span x-bind:class="{
                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                                    'bg-yellow-100 text-yellow-800': review.status === 'pending',
                                    'bg-green-100 text-green-800': review.status === 'approved',
                                    'bg-red-100 text-red-800': review.status === 'rejected'
                                }" x-text="review.status ? (review.status.charAt(0).toUpperCase() + review.status.slice(1)) : 'N/A'"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" 
                                x-text="review.reviewed_at ? new Date(review.reviewed_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A'">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <!-- View Icon -->
                                    <a :href="`/reviews/${review.id}`" class="text-blue-600 hover:text-blue-900" title="View Review">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span class="sr-only">View</span>
                                    </a>

                                    <!-- Edit Icon (only show if status is not completed) -->
                                    <template x-if="review.status !== 'completed'">
                                        <a :href="`/reviews/${review.id}/edit`" class="text-indigo-600 hover:text-indigo-900" title="Add Review">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            <span class="sr-only">Add</span>
                                        </a>
                                    </template>

                                    <!-- Delete Icon with modal trigger -->
                                    <button type="button" class="delete-review-btn text-red-600 hover:text-red-900" title="Delete Review"
                                            :data-review-id="review.id">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span class="sr-only">Delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </template>
                <tr x-show="!filteredJobs || filteredJobs.length === 0">
                    <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                        <template x-if="searchQuery && searchQuery.length > 0">
                            <span>No reviews match your search criteria.</span>
                        </template>
                        <template x-if="!searchQuery || searchQuery.length === 0">
                            <span>No reviews found. Complete a job to leave a review.</span>
                        </template>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Delete Review Confirmation Modal -->
    <div id="deleteReviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: none;">
        <div class="relative top-20 mx-auto p-5 border shadow-lg rounded-md bg-white" style="width:600px;">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-3">Delete Review</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Are you sure you want to delete this review? This action cannot be undone.</p>
                </div>
                <div class="flex flex-col items-center px-4 py-3 gap-3">
                    <div class="flex justify-center gap-3">
                        <button id="confirmDeleteReview" class="py-2 text-white text-base font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500" style="background-color: #dc2626; width: 200px;">
                            Yes, Delete Review
                        </button>
                        <button id="closeDeleteReviewModal" class="py-2 bg-white text-gray-700 text-base font-medium rounded-md border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500" style="width: 120px;">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let currentReviewId = null;

    // Handle delete button click
    $(document).on('click', '.delete-review-btn', function() {
        currentReviewId = $(this).data('review-id');
        $('#deleteReviewModal').show();
    });

    // Handle confirm delete
    $('#confirmDeleteReview').click(function() {
        if (!currentReviewId) return;

        fetch(`/reviews/${currentReviewId}`, { 
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            // For redirect responses, we need to let the browser handle it
            if (response.redirected) {
                window.location.href = response.url;
                return;
            }
            // For JSON responses
            return response.json().then(data => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else if (data.success) {
                    window.location.reload();
                } else {
                    throw new Error('Failed to delete review');
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
            const event = new CustomEvent('toast', { 
                detail: { 
                    type: 'error', 
                    message: 'Failed to delete review' 
                } 
            });
            window.dispatchEvent(event);
        })
        .finally(() => {
            $('#deleteReviewModal').hide();
            currentReviewId = null;
        });
    });

    // Close modal when clicking cancel
    $('#closeDeleteReviewModal').click(function() {
        $('#deleteReviewModal').hide();
        currentReviewId = null;
    });

    // Close modal when clicking outside
    $(window).click(function(event) {
        if ($(event.target).is('#deleteReviewModal')) {
            $('#deleteReviewModal').hide();
            currentReviewId = null;
        }
    });
});
</script>
