@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Rate & Review</h2>
                
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900">{{ $review->job_title }}</h3>
                    <p class="text-sm text-gray-600">Service: {{ $review->service_name }}</p>
                    <p class="text-sm text-gray-600">Service Provider: {{ $review->service_provider_name }}</p>
                    <p class="text-sm text-gray-600">Scheduled: {{ \Carbon\Carbon::parse($review->scheduled_date)->format('F j, Y') }}</p>
                </div>

                <form action="{{ route('reviews.update', $review) }}" method="POST" enctype="multipart/form-data" id="reviewForm" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Rating -->
                    <div x-data="ratingComponent({{ old('rating', $review->rating) ?? 0 }})" class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Your Rating <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center space-x-4">
                            <div class="flex">
                                <template x-for="i in 5" :key="i">
                                    <button type="button"
                                        @click="setRating(i)"
                                        @mouseover="hoverRating = i"
                                        @mouseleave="hoverRating = 0"
                                        class="relative w-8 h-8 focus:outline-none"
                                        :aria-label="'Rate ' + i + ' stars'"
                                        :title="'Rate ' + i + ' stars'"
                                    >
                                        <!-- Empty Star (Background) -->
                                        <svg class="w-full h-full text-gray-300" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                        </svg>
                                        
                                        <!-- Filled Star (Conditional) -->
                                        <template x-if="isFilledStar(i)">
                                            <svg class="absolute inset-0 w-full h-full text-yellow-400" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                            </svg>
                                        </template>
                                        
                                        <!-- Half Star (Conditional) -->
                                        <template x-if="isHalfStar(i)">
                                            <div class="absolute inset-0 w-full h-full">
                                                <svg class="w-full h-full text-yellow-400" viewBox="0 0 24 24" fill="currentColor" style="clip-path: inset(0 50% 0 0);">
                                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                                </svg>
                                                
                                            </div>
                                        </template>
                                    </button>
                                </template>
                                <input type="hidden" name="rating" x-model="rating">
                            </div>
                            
                            <span class="text-gray-700">
                                <span x-text="rating.toFixed(1)"></span>/5.0
                            </span>
                            
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500">or</span>
                                <input 
                                    type="number" 
                                    x-model.number="rating"
                                    @input="updateFromInput"
                                    class="w-20 pl-2 pr-2 py-1 text-sm border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500" 
                                    step="0.5" 
                                    min="0" 
                                    max="5"
                                    aria-label="Rating"
                                >
                                <span class="text-sm text-gray-500">/ 5.0</span>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.addEventListener('alpine:init', () => {
                            Alpine.data('ratingComponent', (initialRating) => ({
                                rating: parseFloat(initialRating) || 0,
                                hoverRating: 0,

                                init() {
                                    this.rating = this.roundToHalf(this.rating);
                                },

                                setRating(value) {
                                    this.rating = value;
                                },

                                updateFromInput() {
                                    // Ensure rating is between 0 and 5
                                    this.rating = Math.min(5, Math.max(0, parseFloat(this.rating) || 0));
                                },

                                // Get the display rating (hover or actual)
                                get displayRating() {
                                    return this.hoverRating || this.rating;
                                },

                                // Check if a star should be half-filled
                                isHalfStar(starIndex) {
                                    const rating = this.displayRating;
                                    return starIndex === Math.ceil(rating) && (rating % 1) > 0.25;
                                },

                                // Check if a star should be fully filled
                                isFilledStar(starIndex) {
                                    return starIndex <= Math.floor(this.displayRating);
                                }
                            }));
                        });
                    </script>

                    <!-- Review -->
                    <div class="mb-6">
                        <label for="review" class="block text-sm font-medium text-gray-700 mb-2">
                            Your Review <span class="text-red-500">*</span>
                        </label>
                        <textarea name="review" id="review" rows="5" 
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md p-2"
                            placeholder="Share your experience with this service..."
                            required>{{ old('review', $review->review) }}</textarea>
                        @error('review')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image Upload -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Upload Images (Max 5)
                        </label>
                        <div class="flex items-center">
                            <label class="flex flex-col items-center px-4 py-2 bg-white text-blue-600 rounded-lg border border-blue-600 cursor-pointer hover:bg-blue-50 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span class="text-sm">Add Images</span>
                                <input type="file" 
                                       id="fileInput"
                                       name="images[]"
                                       class="hidden" 
                                       multiple 
                                       accept="image/*">
                            </label>
                            <span id="fileCount" class="ml-3 text-sm text-gray-500">0/5 files selected</span>
                        </div>
                        
                        <!-- Image Previews -->
                        <div id="imagePreviews" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                            @if(isset($review->images) && count($review->images) > 0)
                                @foreach($review->images as $image)
                                    <div class="relative group rounded-lg overflow-hidden border border-gray-200">
                                        <img src="{{ Storage::url($image->path) }}" class="h-32 w-full object-cover">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                            <button type="button" 
                                                    onclick="removeImage(this, '{{ $image->id }}')"
                                                    class="p-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-2">
                                            <p class="text-xs text-white truncate">{{ basename($image->path) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <input type="hidden" name="deleted_images" id="deletedImages" value="">
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('jobs.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Submit Review
                        </button>
                    </div>
                </form>

                <script>
                    // Handle file upload and preview
                    const fileInput = document.getElementById('fileInput');
                    const imagePreviews = document.getElementById('imagePreviews');
                    const fileCount = document.getElementById('fileCount');
                    const maxFiles = 5;
                    const deletedImages = new Set();

                    fileInput.addEventListener('change', function(e) {
                        const files = Array.from(e.target.files);
                        const currentFileCount = document.querySelectorAll('#imagePreviews .image-preview').length;
                        const remainingSlots = maxFiles - currentFileCount;

                        if (files.length > remainingSlots) {
                            alert(`You can only upload up to ${maxFiles} files.`);
                            return;
                        }

                        files.forEach(file => {
                            if (!file.type.startsWith('image/')) {
                                alert(`File ${file.name} is not an image.`);
                                return;
                            }

                            if (file.size > 5 * 1024 * 1024) {
                                alert(`File ${file.name} is too large. Maximum size is 5MB.`);
                                return;
                            }

                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const previewDiv = document.createElement('div');
                                previewDiv.className = 'relative group rounded-lg overflow-hidden border border-gray-200 image-preview';
                                previewDiv.innerHTML = `
                                    <img src="${e.target.result}" class="h-32 w-full object-cover">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                        <button type="button" 
                                                onclick="removeImage(this)"
                                                class="p-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-2">
                                        <p class="text-xs text-white truncate">${file.name}</p>
                                        <p class="text-xs text-gray-300">${(file.size / 1024).toFixed(1)} KB</p>
                                    </div>
                                `;
                                imagePreviews.appendChild(previewDiv);
                                updateFileCount();
                                
                                // Create a hidden input for the file
                                const input = document.createElement('input');
                                input.type = 'file';
                                input.name = 'images[]';
                                input.style.display = 'none';
                                input.files = new FileList([file]);
                                previewDiv.appendChild(input);
                            };
                            reader.readAsDataURL(file);
                        });

                        // Reset the file input to allow selecting the same file again
                        fileInput.value = '';
                    });

                    // Handle image removal
                    window.removeImage = function(button, imageId = null) {
                        const previewDiv = button.closest('.image-preview');
                        if (imageId) {
                            // For existing images, add to deleted images list
                            deletedImages.add(imageId);
                            document.getElementById('deletedImages').value = Array.from(deletedImages).join(',');
                        }
                        previewDiv.remove();
                        updateFileCount();
                    };

                    // Update file counter
                    function updateFileCount() {
                        const count = document.querySelectorAll('#imagePreviews .image-preview').length;
                        fileCount.textContent = `${count}/${maxFiles} files selected`;
                    }

                    // Handle form submission
                    document.getElementById('reviewForm').addEventListener('submit', function(e) {
                        // Form will be submitted normally, no need for fetch API
                        // The hidden deleted_images input will be sent with the form
                    });
                </script>
            </div>
        </div>
    </div>
</div>
@endsection
