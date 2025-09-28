@extends('layouts.app')

@section('title', 'Service Providers | INeedHand')

@section('content')
<div class="container mx-auto px-4 py-8">

    <div class="flex flex-col md:flex-row gap-6">
        <div class="w-full md:w-1/4">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h2 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-2">Filters</h2>
                
                <!-- Search -->
                <div class="mb-6">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" id="search" name="search" placeholder="Search by name or service..."
                           class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Services Filter -->
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Services</h3>
                    <div class="space-y-2 max-h-60 overflow-y-auto">
                        @foreach($services as $service)
                            <div class="flex items-center">
                                <input id="service-{{ $service->service_id }}" name="services[]" type="checkbox" 
                                       value="{{ $service->service_id }}"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="service-{{ $service->service_id }}" class="ml-2 text-sm text-gray-700">
                                    {{ $service->service_name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Location Filter -->
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Location</h3>
                    <select id="location" name="location" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="">All Locations</option>
                        @foreach($barangays as $barangay)
                            <option value="{{ $barangay->id }}" {{ request('location') == $barangay->brgy_id ? 'selected' : '' }}>
                                {{ $barangay->brgy_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Rating Filter -->
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Minimum Rating</h3>
                    <select id="rating" name="rating" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="">Any Rating</option>
                        <option value="5">5 ★★★★★</option>
                        <option value="4">4 ★★★★☆ & Up</option>
                        <option value="3">3 ★★★☆☆ & Up</option>
                        <option value="2">2 ★★☆☆☆ & Up</option>
                        <option value="1">1 ★☆☆☆☆ & Up</option>
                    </select>
                </div>

                <button type="button" id="apply-filters" 
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Apply Filters
                </button>
                <button type="button" id="reset-filters" 
                        class="mt-2 w-full bg-gray-100 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Reset
                </button>
            </div>
        </div>

        <div class="w-full md:w-3/4">
            
            <div class="space-y-4">
                @forelse($serviceProviders as $provider)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row">
                                <div class="md:w-1/6 flex justify-center mb-4 md:mb-0">
                                    <img src="{{ $provider->face_img ? asset('storage/' . $provider->face_img) : 'https://ui-avatars.com/api/?name=' . urlencode($provider->name) }}" alt="{{ $provider->name }}" class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">
                                </div>

                                <div class="md:ml-6 flex-1">
                                    <div class="flex flex-col md:flex-row md:justify-between">
                                        <div>
                                            <h2 class="text-xl font-semibold text-gray-800">{{ $provider->name }}</h2>
                                            <div class="flex items-center mt-1">
                                                <!-- Rating Stars -->
                                                <div class="flex items-center">
                                                    @php
                                                        $rating = $provider->average_rating ?? 0;
                                                        $fullStars = floor($rating);
                                                        $hasHalfStar = $rating - $fullStars >= 0.5;
                                                    @endphp
                                                    
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $fullStars)
                                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                            </svg>
                                                        @elseif($i === $fullStars + 1 && $hasHalfStar)
                                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                                <defs>
                                                                    <linearGradient id="half-star" x1="0" x2="50%" y1="0" y2="0">
                                                                        <stop offset="50%" stop-color="currentColor" />
                                                                        <stop offset="50%" stop-color="#D1D5DB" />
                                                                    </linearGradient>
                                                                </defs>
                                                                <path fill="url(#half-star)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                            </svg>
                                                        @else
                                                            <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                            </svg>
                                                        @endif
                                                    @endfor
                                                    <span class="text-gray-600 text-sm ml-1">
                                                        ({{ $provider->ratings_count ?? 0 }})
                                                    </span>
                                                </div>
                                                <span class="mx-2 text-gray-400">•</span>
                                                <span class="text-sm text-gray-600">{{ $provider->jobs_completed ?? 0 }} jobs completed</span>
                                            </div>
                                        </div>
                                        
                                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                            <div class="flex space-x-3">
                                                <a href="{{ route('service-providers.show', $provider) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    View Profile
                                                </a>
                                                <a href="#" 
                                                   class="message-btn inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" 
                                                   data-user-id="{{ $provider->id }}" 
                                                   data-user-name="{{ $provider->name }}" 
                                                   data-face-img="{{ $provider->face_img ? asset('storage/' . $provider->face_img) : 'https://ui-avatars.com/api/?name=' . urlencode($provider->name) }}"
                                                   data-has-conversation="{{ $provider->has_conversation ? 'true' : 'false' }}">
                                                    Message
                                                </a>
                                            </div>
                                            @if($provider->status === \App\Models\User::STATUS_VERIFIED)
                                                <div>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Verified
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Services -->
                                    @if($provider->services->isNotEmpty())
                                        <div class="mt-3">
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($provider->services as $service)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $service->service_name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Location -->
                                    <div class="mt-3 flex items-center text-sm text-gray-500">
                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ $provider->street }},
                                        {{ $provider->address }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow-md p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900">No service providers found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter to find what you're looking for.</p>
                        <div class="mt-6">
                            <button type="button" id="clear-filters" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Clear all filters
                            </button>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($serviceProviders->hasPages())
                <div class="mt-8">
                    {{ $serviceProviders->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const applyFiltersBtn = document.getElementById('apply-filters');
    const resetFiltersBtn = document.getElementById('reset-filters');
    const searchInput = document.getElementById('search');
    
    // Handle Apply Filters button click
    applyFiltersBtn.addEventListener('click', function() {
        // Get the current URL
        const url = new URL(window.location.href.split('?')[0]);
        
        // Add search parameter if exists
        if (searchInput.value) {
            url.searchParams.set('search', searchInput.value);
        } else {
            url.searchParams.delete('search');
        }
        
        // Add service filters
        const serviceCheckboxes = document.querySelectorAll('input[name="services[]"]:checked');
        url.searchParams.delete('services[]');
        serviceCheckboxes.forEach(checkbox => {
            url.searchParams.append('services[]', checkbox.value);
        });
        
        // Add other filters (location, rating) if needed
        const locationSelect = document.getElementById('location');
        if (locationSelect.value) {
            url.searchParams.set('location', locationSelect.value);
        } else {
            url.searchParams.delete('location');
        }
        
        const ratingSelect = document.getElementById('rating');
        if (ratingSelect.value) {
            url.searchParams.set('rating', ratingSelect.value);
        } else {
            url.searchParams.delete('rating');
        }
        
        // Navigate to the filtered URL
        window.location.href = url.toString();
    });
    
    // Handle Reset Filters button click
    resetFiltersBtn.addEventListener('click', function() {
        window.location.href = window.location.pathname;
    });
    
    // Preserve form state on page load
    function setInitialFormState() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Set search input
        if (urlParams.has('search')) {
            searchInput.value = urlParams.get('search');
        }
        
        // Set checked services
        const serviceParams = urlParams.getAll('services[]');
        document.querySelectorAll('input[name="services[]"]').forEach(checkbox => {
            checkbox.checked = serviceParams.includes(checkbox.value);
        });
        
        // Set location select
        const locationSelect = document.getElementById('location');
        if (urlParams.has('location')) {
            locationSelect.value = urlParams.get('location');
        }
        
        // Set rating select
        const ratingSelect = document.getElementById('rating');
        if (urlParams.has('rating')) {
            ratingSelect.value = urlParams.get('rating');
        }
    }
    
    // Initialize form state
    setInitialFormState();
    
    // Handle message button clicks
    document.querySelectorAll('.message-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');
            const faceImg = this.getAttribute('data-face-img');
            const hasConversation = this.getAttribute('data-has-conversation') === 'true';
            
            // Redirect to messages page with user ID and select flag
            let url = '{{ route("messages.index") }}?user=' + userId;
            
            if (hasConversation) {
                url += '&select=true';
            }
            
            // Store user data in session storage for the messages page
            sessionStorage.setItem('selectedUser', JSON.stringify({
                id: userId,
                name: userName,
                faceImg: faceImg
            }));
            
            window.location.href = url;
        });
    });
});
</script>
@endsection
