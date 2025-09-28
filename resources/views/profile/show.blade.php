@extends('layouts.app')

@section('title', $user->name . ' | INeedHand')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Back Button - Only show if not a service provider -->
    @if(strtolower(auth()->user()->role) !== 'serviceprovider')
    <a href="{{ url()->previous() }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back
    </a>
    @endif
    
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Profile Info -->
            <div class="md:w-1/3">
                <div class="text-center">
                    <img src="{{ $user->face_img ? asset('storage/' . $user->face_img) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                         alt="{{ $user->name }}" 
                         class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
                    <h1 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h1>
                    
                    @if($user->services->count() > 0)
                        <div class="mt-2">
                            @foreach($user->services as $service)
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1 mb-1">
                                    {{ $service->service_name }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                    
                    <div class="mt-4 text-left">
                        @if($user->address)
                            <div class="flex items-center text-gray-600 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $user->address }}
                            </div>
                        @endif
                        
                        @if($user->contact_number)
                            <div class="flex items-center text-gray-600 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $user->contact_number }}
                            </div>
                        @endif
                        
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            {{ $user->email }}
                        </div>
                    </div>
                </div>
                
                @if($user->id === auth()->id())
                    <div class="mt-6 text-center">
                        <a href="{{ route('profile.edit') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Profile
                        </a>
                    </div>
                @endif
            </div>
            
            <!-- Main Content -->
            <div class="md:w-2/3 border-l border-gray-200 pl-6">
                <!-- About Section -->
                <div class="bg-white p-6 rounded-lg shadow mb-6">
                    <h2 class="text-xl font-semibold mb-4">About</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($user->sex)
                        <div>
                            <p class="text-gray-600">Sex</p>
                            <p class="font-medium">{{ ucfirst($user->sex) }}</p>
                        </div>
                        @endif
                        @if($user->birthday)
                        <div>
                            <p class="text-gray-600">Age</p>
                            <p class="font-medium">{{ $user->age ?? 'N/A' }}</p>
                        </div>
                        @endif
                        @if($user->birthday)
                        <div>
                            <p class="text-gray-600">Birthday</p>
                            <p class="font-medium">{{ $user->birthday->format('F j, Y') }}</p>
                        </div>
                        @endif
                        @if($user->civil_status)
                        <div>
                            <p class="text-gray-600">Civil Status</p>
                            <p class="font-medium">{{ ucfirst($user->civil_status) }}</p>
                        </div>
                        @endif
                        @if($user->bio)
                        <div class="md:col-span-2">
                            <p class="text-gray-600">Bio</p>
                            <p class="text-gray-700">{{ $user->bio }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Member Since -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Member Since</h2>
                    <p class="text-gray-600">{{ $user->created_at->format('F j, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@if(strtolower($user->role) === 'homeowner' && isset($user->job_stats) && $user->job_stats->total_jobs > 0)
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Job Stats -->
            <div class="space-y-4">
                <h2 class="text-xl font-semibold text-gray-800">Job Statistics</h2>
                <div class="space-y-3">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm font-medium text-gray-500">Total Jobs Completed</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $user->job_stats->total_jobs }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm font-medium text-gray-500">Total Amount Spent</p>
                        <p class="text-2xl font-bold text-gray-900">₱{{ number_format($user->job_stats->total_spent, $user->job_stats->total_spent == floor($user->job_stats->total_spent) ? 0 : 2) }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm font-medium text-gray-500">Average per Job</p>
                        <p class="text-2xl font-bold text-gray-900">₱{{ number_format($user->job_stats->average_spent, $user->job_stats->average_spent == floor($user->job_stats->average_spent) ? 0 : 2) }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Recent Jobs -->
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Jobs</h2>
                <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                    @forelse($user->job_stats->recent_jobs as $job)
                        <div class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $job->title }}</h3>
                                    @if($job->serviceProvider)
                                    <p class="text-sm text-gray-600 font-medium mt-1">
                                        {{ $job->serviceProvider->name }}
                                    </p>
                                    @endif
                                    <p class="text-sm text-gray-500">
                                        {{ optional($job->service)->service_name ?? 'N/A' }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $job->scheduled_date ? $job->scheduled_date->format('M j, Y') : 'No date set' }}
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ ucfirst($job->status) }}
                                </span>
                            </div>
                            <div class="mt-2">
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Budget:</span> 
                                    ₱ {{ number_format($job->budget, $job->budget == floor($job->budget) ? 0 : 2) }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No recent jobs found.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@if(strtolower($user->role) === 'serviceprovider')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Stats -->
            <div class="space-y-4">
                <h2 class="text-xl font-semibold text-gray-800">Service Provider Stats</h2>
                <div class="space-y-3">
                    @if(isset($user->reviews_count) && $user->reviews_count > 0)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">Average Rating</span>
                        <div class="flex items-center">
                            <span class="text-2xl font-bold text-gray-900 mr-2">
                                {{ number_format($user->average_rating, 1) }}
                            </span>
                            <div class="flex">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($user->average_rating))
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
                                ({{ $user->reviews_count }} reviews)
                            </span>
                        </div>
                    </div>
                    @else
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-gray-600">No reviews yet</p>
                    </div>
                    @endif
                    
                    @if(isset($user->completed_jobs_count) && $user->completed_jobs_count > 0)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jobs Completed</span>
                            <span class="text-xl font-bold text-gray-900">{{ $user->completed_jobs_count }}</span>
                        </div>
                    </div>
                    @else
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-gray-600">No completed jobs yet</p>
                    </div>
                    @endif
                </div>
            </div>

            @if($user->reviews && $user->reviews->count() > 0)
            <!-- Recent Reviews -->
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Reviews</h2>
                <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                    @foreach($user->reviews->take(5) as $review)
                        <div class="border-b border-gray-200 pb-4">
                            <div class="flex items-center justify-between mb-1">
                                <div class="font-medium text-gray-900">{{ $review->homeowner_name ?? 'Anonymous' }}</div>
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
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
                            @if($review->review)
                                <p class="text-gray-600 text-sm mt-1">{{ $review->review }}</p>
                            @endif
                            <p class="text-xs text-gray-500 mt-1">{{ $review->created_at->diffForHumans() }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            @else
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Reviews</h2>
                <p class="text-gray-500">No reviews yet</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

@endsection