<div id="deleteModal" class="fixed inset-0 z-50 hidden">
    <!-- Background overlay -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

    <!-- Modal panel -->
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
             role="dialog" 
             aria-modal="true" 
             aria-labelledby="modal-headline">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                        Delete Post
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Are you sure you want to delete this post? This action cannot be undone.
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <form id="deletePostForm" method="POST" class="inline-flex sm:ml-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Delete
                    </button>
                </form>
                <button type="button" 
                        id="cancelDeleteBtn"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Show modal
        $(document).on('click', '[data-delete-post]', function(e) {
            e.preventDefault();
            const postId = $(this).data('delete-post');
            const deleteUrl = '/posts/' + postId;
            
            // Update form action
            $('#deletePostForm').attr('action', deleteUrl);
            
            // Show modal with animation
            $('#deleteModal').removeClass('hidden').addClass('block');
            $('body').css('overflow', 'hidden');
        });
        
        // Close modal when clicking cancel button
        $('#cancelDeleteBtn').on('click', function() {
            closeDeleteModal();
        });
        
        // Close modal when clicking outside the modal content
        $(document).on('click', function(e) {
            if ($(e.target).hasClass('bg-opacity-75')) {
                closeDeleteModal();
            }
        });
        
        // Close modal on Escape key
        $(document).keyup(function(e) {
            if (e.key === 'Escape' && $('#deleteModal').hasClass('block')) {
                closeDeleteModal();
            }
        });
        
        // Handle form submission
        $('#deletePostForm').on('submit', function(e) {
            // Optional: Add loading state here if needed
            // e.preventDefault();
            // const form = $(this);
            // Submit form programmatically if you need to do something before submission
            // form.off('submit').submit();
        });
    });
    
    function closeDeleteModal() {
        $('#deleteModal').addClass('hidden').removeClass('block');
        $('body').css('overflow', 'auto');
    }
</script>
@endpush
