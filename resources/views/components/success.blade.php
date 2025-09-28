@props(['message'])

@if (session()->has('success'))
    <div id="successMessage" class="fixed bottom-4 right-4 px-6 py-3 rounded-md bg-green-500 text-white shadow-lg z-50 transform translate-y-2 opacity-0 transition-all duration-300">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            const $successMessage = $('#successMessage');
            
            // Show with animation
            setTimeout(() => {
                $successMessage
                    .removeClass('translate-y-2 opacity-0')
                    .addClass('translate-y-0 opacity-100');
                
                // Hide after 3 seconds
                setTimeout(() => {
                    $successMessage
                        .removeClass('translate-y-0 opacity-100')
                        .addClass('translate-y-2 opacity-0');
                        
                    // Remove from DOM after animation completes
                    setTimeout(() => {
                        $successMessage.remove();
                    }, 300);
                }, 3000);
            }, 50); // Small delay to ensure the element is in the DOM
        });
    </script>
    @endpush
@endif
