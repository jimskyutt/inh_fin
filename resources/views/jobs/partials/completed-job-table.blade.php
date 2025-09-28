<div class="bg-white shadow overflow-hidden sm:rounded-lg" id="jobTable">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr class="text-center">
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Service Provider</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled Date</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-center">
                <template x-if="jobs && jobs.length > 0">
                    <template x-for="job in jobs.filter(job => job.status === 'completed')" :key="job.id">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <a :href="`/jobs/${job.id}`" class="text-indigo-600 hover:text-indigo-900" x-text="job.title"></a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="job.service?.service_name || 'N/A'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="job.service_provider?.name || 'Not assigned'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="job.location"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="new Date(job.scheduled_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex space-x-2 justify-center">
                                    <a :href="`/jobs/${job.id}`" class="text-indigo-600 hover:text-indigo-900" title="View">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <button type="button" class="delete-job-btn text-red-600 hover:text-red-900" title="Delete"
                                            :data-job-id="job.id">
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
                <tr x-show="!jobs || jobs.length === 0 || jobs.every(job => job.deleted_by_owner)">
                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                        No jobs found
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: none;">
        <div class="relative top-20 mx-auto p-5 border shadow-lg rounded-md bg-white" style="width:600px;">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-3">Delete Job</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Are you sure you want to delete this job? This action cannot be undone.</p>
                </div>
                <div class="flex flex-col items-center px-4 py-3 gap-3">
                    <div class="flex justify-center gap-3">
                        <form id="deleteJobForm" action="" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="py-2 text-white text-base font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500" style="background-color: #dc2626; width: 200px;">
                                Yes, Delete Job
                            </button>
                        </form>
                        <button id="closeDeleteModal" class="py-2 bg-white text-gray-700 text-base font-medium rounded-md border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500" style="width: 120px;">
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
    // Handle delete button click
    $(document).on('click', '.delete-job-btn', function() {
        const jobId = $(this).data('job-id');
        console.log('Opening delete modal for job:', jobId);
        // Update the form action with the correct job ID
        $('#deleteJobForm').attr('action', `/jobs/${jobId}`);
        $('#deleteModal').show();
    });

    // Close modals when clicking cancel buttons
    $('#closeDeleteModal').click(function() {
        $('#deleteModal').hide();
    });

    // Close modals when clicking outside
    $(window).click(function(event) {
        if ($(event.target).is('#deleteModal')) {
            $(event.target).hide();
        }
    });
});
</script>
<script>
$(document).ready(function() {
    // Handle delete button click
    $(document).on('click', '.delete-cancel-job-btn', function() {
        const jobId = $(this).data('job-id');
        console.log('Opening delete modal for job:', jobId);
        // Update the form action with the correct job ID
        $('#deleteCancelJobForm').attr('action', `/jobs/${jobId}`);
        $('#deleteCancelModal').show();
    });

    // Close modals when clicking cancel buttons
    $('#closeDeleteCancelModal').click(function() {
        $('#deleteCancelModal').hide();
    });

    // Close modals when clicking outside
    $(window).click(function(event) {
        if ($(event.target).is('#deleteCancelModal')) {
            $(event.target).hide();
        }
    });
});
</script>