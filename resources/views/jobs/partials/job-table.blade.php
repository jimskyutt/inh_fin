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
                    <template x-for="job in jobs" :key="job.id">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <a :href="`/jobs/${job.id}`" class="text-indigo-600 hover:text-indigo-900" x-text="job.title"></a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="job.service?.service_name || 'N/A'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="job.service_provider?.name || 'Not assigned'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="job.location"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="new Date(job.scheduled_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2 justify-center">
                                    <a :href="`/jobs/${job.id}`" class="text-indigo-600 hover:text-indigo-900" title="View">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @if(auth()->user()->role !== 'ServiceProvider')
                                    <button type="button" 
                                            class="mark-complete-btn text-green-600 hover:text-green-900" 
                                            title="Mark as Completed"
                                            :data-job-id="job.id">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                    @endif
                                    <button type="button" 
                                            class="cancel-job-btn text-red-600 hover:text-red-900" 
                                            title="Cancel Job"
                                            :data-job-id="job.id"
                                            @click="showCancelModal('{{ route('jobs.update', '') }}/' + job.id)">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                    <!-- Hidden form for job cancellation -->
                                    <form :id="'cancelForm' + job.id" :action="'{{ route('jobs.update', '') }}/' + job.id" method="POST" class="hidden">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="cancelled">
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </template>
                </template>
                <tr x-show="!jobs || jobs.length === 0">
                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                        No jobs found
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Complete Confirmation Modal -->
    <div id="completeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: none;">
        <div class="relative top-20 mx-auto p-5 border shadow-lg rounded-md bg-white" style="width:600px;">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-3">Mark as Completed</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Are you sure you want to mark this job as completed?</p>
                </div>
                <div class="flex flex-col items-center px-4 py-3 gap-3">
                    <div class="flex justify-center gap-3">
                        <form id="completeJobForm" action="" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="py-2 text-white text-base font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500" style="background-color: green; width: 200px;">
                                Yes, Mark as Completed
                            </button>
                        </form>
                        <button id="closeCompleteModal" class="py-2 bg-white text-gray-700 text-base font-medium rounded-md border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500" style="width: 120px;">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: none;">
        <div class="relative top-20 mx-auto p-5 border shadow-lg rounded-md bg-white" style="width:600px;">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-3">Cancel Job</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Are you sure you want to cancel this job? This action cannot be undone.</p>
                </div>
                <div class="flex flex-col items-center px-4 py-3 gap-3">
                    <div class="flex justify-center gap-3">
                        <form id="cancelJobForm" action="" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="py-2 text-white text-base font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500" style="background-color: #dc2626; width: 200px;">
                                Yes, Cancel Job
                            </button>
                        </form>
                        <button id="closeCancelModal" class="py-2 bg-white text-gray-700 text-base font-medium rounded-md border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500" style="width: 120px;">
                            No, Go Back
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
    // Handle complete button click
    $(document).on('click', '.mark-complete-btn', function() {
        const jobId = $(this).data('job-id');
        console.log('Opening complete modal for job:', jobId);
        // Update the form action with the correct job ID
        $('#completeJobForm').attr('action', `/jobs/${jobId}/complete`);
        $('#completeModal').show();
    });

    // Show cancel modal function for Alpine.js
    window.showCancelModal = function(url) {
        if (url && !url.endsWith('undefined')) {
            document.getElementById('cancelJobForm').action = url;
            document.getElementById('cancelModal').style.display = 'block';
        } else {
            console.error('Invalid job ID');
        }
    };

    // Handle cancel button click for jQuery
    $(document).on('click', '.cancel-job-btn', function(e) {
        const jobId = $(this).data('job-id');
        if (jobId) {
            const url = '{{ route('jobs.update', '') }}/' + jobId;
            $('#cancelJobForm').attr('action', url);
            $('#cancelModal').show();
        } else {
            console.error('No job ID found');
        }
        e.preventDefault();
    });

    // Close modals when clicking cancel buttons
    $('#closeCompleteModal, #closeCancelModal').click(function() {
        $('#completeModal, #cancelModal').hide();
    });

    // Close modals when clicking outside
    $(window).click(function(event) {
        if ($(event.target).is('#completeModal, #cancelModal')) {
            $(event.target).hide();
        }
    });

    // Handle tab switching if needed
    $('[data-tab]').click(function() {
        const tab = $(this).data('tab');
        // Add your tab switching logic here
    });
});
</script>