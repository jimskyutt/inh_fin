@extends('layouts.app')

@section('title', 'Service Provider Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-5 lg:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Jobs</p>
                    <p class="text-2xl font-bold">{{ $totalJobs }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm font-medium">Ongoing Jobs</p>
                    <p class="text-2xl font-bold">{{ $ongoingJobs }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm font-medium">Completed Jobs</p>
                    <p class="text-2xl font-bold">{{ $completedJobs }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 384 512" fill="currentColor">
                        <path d="M64 32C46.3 32 32 46.3 32 64l0 64c-17.7 0-32 14.3-32 32s14.3 32 32 32l0 32c-17.7 0-32 14.3-32 32s14.3 32 32 32l0 64 0 96c0 17.7 14.3 32 32 32s32-14.3 32-32l0-64 80 0c68.4 0 127.7-39 156.8-96l19.2 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-.7 0c.5-5.3 .7-10.6 .7-16s-.2-10.7-.7-16l.7 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-19.2 0C303.7 71 244.4 32 176 32L64 32zm190.4 96L96 128l0-32 80 0c30.5 0 58.2 12.2 78.4 32zM96 192l190.9 0c.7 5.2 1.1 10.6 1.1 16s-.4 10.8-1.1 16L96 224l0-32zm158.4 96c-20.2 19.8-47.9 32-78.4 32l-80 0 0-32 158.4 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm font-medium">Avg. Income</p>
                    <div class="flex items-center">
                        <p class="text-2xl font-bold">₱{{ number_format($averageIncome, $averageIncome == floor($averageIncome) ? 0 : 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-yellow-400">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <i class="fas fa-star text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm font-medium">Average Rating</p>
                    <div class="flex items-center">
                        <p class="text-2xl font-bold mr-2">{{ number_format($averageRating, 1) }}</p>
                        <span class="text-gray-400">/ 5</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                <h1 class="text-xl font-extrabold text-gray-800 font-['Rajdhani'] tracking-wide">Ongoing Jobs</h1>
                </div>
                @if($ongoingJobsList->count() > 0)
                    <div class="space-y-4">
                        @foreach($ongoingJobsList as $job)
                            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        @if($job->homeowner)
                                            <p class="text-sm text-gray-700 mt-1">
                                                <span class="font-medium">Homeowner:</span> {{ $job->homeowner->name }}
                                            </p>
                                        @endif
                                        <h3 class="mt-2 font-medium text-gray-900">{{ $job->title }}</h3>
                                        @if($job->description)
                                            <div class="text-sm text-gray-600">
                                                <p class="mt-1">{{ $job->description }}</p>
                                            </div>
                                        @endif
                                        <p class="mt-4 text-sm text-gray-500">
                                            {{ optional($job->service)->service_name ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-5">
                                        <span class="text-sm font-medium">₱{{ number_format($job->budget, 0) }}</span>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $job->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ str_replace('_', ' ', ucfirst($job->status)) }}
                                        </span>
                                    </div>
                                </div>

                                <p class="text-sm text-gray-500">
                                    {{ $job->scheduled_date ? \Carbon\Carbon::parse($job->scheduled_date)->format('M d, Y') : 'No date set' }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No ongoing jobs found.</p>
                @endif
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-xl font-extrabold text-gray-800 font-['Rajdhani'] tracking-wide">Recent Jobs</h1>
                </div>
                @if($recentJobs->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentJobs as $job)
                            <div class="border-b pb-4 last:border-b-0 last:pb-0">
                                <div class="flex justify-between items-start">
                                    <div>
                                        @if($job->homeowner)
                                            <p class="text-sm text-gray-700 mt-1">
                                                <span class="font-medium">Homeowner:</span> {{ $job->homeowner->name }}
                                            </p>
                                        @endif
                                        <h3 class="mt-2 font-medium text-gray-900">{{ $job->title }}</h3>
                                        <p class="text-sm text-gray-500">
                                            {{ optional($job->service)->service_name ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($job->status === 'completed') bg-green-100 text-green-800
                                        @elseif($job->status === 'cancelled') bg-red-100 text-red-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ str_replace('_', ' ', ucfirst($job->status)) }}
                                    </span>
                                </div>
                                <div class="mt-1 text-sm text-gray-500">
                                    {{ $job->created_at->diffForHumans() }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No recent jobs found.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection