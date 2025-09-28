@extends('layouts.app')

@section('title', $serviceProvider->name . ' | INeedHand')

@section('content')
<div class="container mx-auto px-4 py-8">

    <a href="{{ url()->previous() }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Results
    </a>
    
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Profile Info -->
            <div class="md:w-1/3">
                <div class="text-center">
                    <img src="{{ $serviceProvider->face_img ? asset('storage/' . $serviceProvider->face_img) : 'https://ui-avatars.com/api/?name=' . urlencode($serviceProvider->name) }}"
                         alt="{{ $serviceProvider->name }}" 
                         class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
                    <h1 class="text-2xl font-bold text-gray-800">{{ $serviceProvider->name }}</h1>
                    
                    @if($serviceProvider->services->count() > 0)
                        <div class="mt-2">
                            @foreach($serviceProvider->services as $service)
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1 mb-1">
                                    {{ $service->service_name }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                    
                    <div class="mt-4 text-left">
                        @if($serviceProvider->address)
                            <div class="flex items-center text-gray-600 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $serviceProvider->address }}
                            </div>
                        @endif
                        
                        @if($serviceProvider->contact_number)
                            <div class="flex items-center text-gray-600 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $serviceProvider->contact_number }}
                            </div>
                        @endif
                        
                        @if($serviceProvider->email)
                            <div class="flex items-center text-gray-600">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ $serviceProvider->email }}
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Verification Badge -->
                @if($serviceProvider->status === \App\Models\User::STATUS_VERIFIED)
                    <div class="mt-4 text-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Verified Service Provider
                    </span>
                    
                    @auth
                        <div class="mt-4">
                            <a href="{{ route('messages.index', ['user' => $serviceProvider->id, 'select' => true]) }}" 
                               class="message-btn inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
                               data-user-id="{{ $serviceProvider->id }}" 
                               data-user-name="{{ $serviceProvider->name }}" 
                               data-face-img="{{ $serviceProvider->face_img ? asset('storage/' . $serviceProvider->face_img) : 'https://ui-avatars.com/api/?name=' . urlencode($serviceProvider->name) }}"
                               data-has-conversation="{{ $serviceProvider->has_conversation ? 'true' : 'false' }}">
                               <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                Message
                            </a>
                        </div>
                    @endauth
                </div>
                @endif
            </div>
            
            <!-- Main Content -->
            <div class="md:w-2/3 border-l border-gray-200 pl-6">
                <!-- About Section -->
                <div class="bg-white p-6 rounded-lg shadow mb-6">
                    <h2 class="text-xl font-semibold mb-4">About</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600">Sex</p>
                            <p class="font-medium">{{ ucfirst($serviceProvider->sex ?? 'Not specified') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Age</p>
                            <p class="font-medium">{{ $serviceProvider->age ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Birthday</p>
                            <p class="font-medium">{{ $serviceProvider->birthday ? $serviceProvider->birthday->format('F j, Y') : 'Not specified' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Civil Status</p>
                            <p class="font-medium">{{ $serviceProvider->civil_status ? ucfirst($serviceProvider->civil_status) : 'Not specified' }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Services Section -->
                @if($serviceProvider->services->count() > 0)
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Services Offered</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($serviceProvider->services as $service)
                                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                    <h3 class="font-medium text-gray-800">{{ $service->service_name }}</h3>
                                    @if($service->pivot->description)
                                        <p class="text-sm text-gray-600 mt-1">{{ $service->pivot->description }}</p>
                                    @endif
                                    @if($service->pivot->rate)
                                        <p class="text-sm font-medium text-blue-600 mt-2">
                                            ₱{{ number_format($service->pivot->rate, 2) }} {{ $service->pivot->rate_type }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>


    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <h2 class="text-xl font-semibold text-gray-800">Service Provider Stats</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">Average Rating</span>
                        <div class="flex items-center">
                            <span class="text-2xl font-bold text-gray-900 mr-2">
                                {{ number_format($serviceProvider->average_rating, 1) }}
                            </span>
                            <div class="flex">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($serviceProvider->average_rating))
                                        <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <span class="ml-2 text-sm text-gray-500">
                                ({{ $serviceProvider->ratings_count ?? 0 }} reviews)
                            </span>
                        </div>
                    </div>
                
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jobs Completed</span>
                            <span class="text-xl font-bold text-gray-900">{{ $serviceProvider->completed_jobs_count ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Reviews</h2>
                @if($serviceProvider->ratings->count() > 0)
                    <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                        @foreach($serviceProvider->ratings->take(5) as $rating)
                            <div class="border-b border-gray-200 pb-4">
                                <div class="flex items-center justify-between mb-1">
                                    <div class="font-medium text-gray-900">{{ $rating->user->name ?? 'Anonymous' }}</div>
                                    <div class="flex">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $rating->rating)
                                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                @if($rating->comment)
                                    <p class="text-gray-600 text-sm mt-1">{{ $rating->comment }}</p>
                                @endif
                                <p class="text-xs text-gray-500 mt-1">{{ $rating->created_at->diffForHumans() }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No reviews yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
@endpush

@endsection
