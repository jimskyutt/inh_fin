@extends('layouts.app')

@section('title', 'Jobs' . ' | ' . config('app.name'))
@section('content')
@php
use App\Models\Job;
@endphp

<div class="container mx-auto px-4 py-8" x-data="{
    activeTab: '{{ request('tab', 'ongoing') }}',
    searchQuery: '{{ request('search', '') }}',
    
    // Store the original jobs data
    jobs: {
        ongoing: @js($jobs->where('status', 'ongoing')->values()),
        completed: @js($jobs->where('status', 'completed')->values()),
        cancelled: @js($jobs->where('status', 'cancelled')->values())
    },
    
    // Store reviews data
    reviews: @js($reviews),
    
    // Computed property for filtered jobs based on active tab and search query
    get filteredJobs() {
        const query = this.searchQuery.toLowerCase().trim();
        
        if (this.activeTab === 'review') {
            if (!query) return this.reviews;
            
            return this.reviews.filter(review => {
                const jobTitle = (review.job?.title || '').toLowerCase();
                const serviceName = (review.service?.service_name || '').toLowerCase();
                const providerName = (review.service_provider?.name || '').toLowerCase();
                const reviewContent = (review.review || '').toLowerCase();
                
                return (
                    jobTitle.includes(query) ||
                    serviceName.includes(query) ||
                    providerName.includes(query) ||
                    reviewContent.includes(query) ||
                    (review.rating && review.rating.toString().includes(query))
                );
            });
        }
        
        let tabJobs = this.jobs[this.activeTab] || [];
        // Filter out deleted jobs first
        tabJobs = tabJobs.filter(job => !job.deleted_by_owner);
        if (!query) return tabJobs;
        
        return tabJobs.filter(job => {
            const title = (job.title || '').toLowerCase();
            const description = (job.description || '').toLowerCase();
            const location = (job.location || '').toLowerCase();
            const serviceName = (job.service?.service_name || '').toLowerCase();
            const providerName = (job.service_provider?.name || '').toLowerCase();
            
            return (
                title.includes(query) ||
                description.includes(query) ||
                location.includes(query) ||
                serviceName.includes(query) ||
                providerName.includes(query) ||
                (job.budget && job.budget.toString().includes(query)) ||
                (job.scheduled_date && job.scheduled_date.toLowerCase().includes(query))
            );
        });
    },
    
    // Get count for each tab
    getTabCount(tab) {
        if (tab === 'review') {
            return this.getFilteredTabJobs(tab).length;
        }
        // Filter out jobs where deleted_by_owner is true before counting
        return this.getFilteredTabJobs(tab).filter(job => !job.deleted_by_owner).length;
    },
    
    // Get filtered jobs/reviews for a specific tab
    getFilteredTabJobs(tab) {
        const query = this.searchQuery.toLowerCase().trim();
        
        if (tab === 'review') {
            if (!query) return this.reviews;
            
            return this.reviews.filter(review => {
                const jobTitle = (review.job?.title || '').toLowerCase();
                const serviceName = (review.service?.service_name || '').toLowerCase();
                const providerName = (review.service_provider?.name || '').toLowerCase();
                const reviewContent = (review.review || '').toLowerCase();
                
                return (
                    jobTitle.includes(query) ||
                    serviceName.includes(query) ||
                    providerName.includes(query) ||
                    reviewContent.includes(query) ||
                    (review.rating && review.rating.toString().includes(query))
                );
            });
        }
        
        const tabJobs = this.jobs[tab] || [];
        if (!query) return tabJobs;
        
        return tabJobs.filter(job => {
            const title = (job.title || '').toLowerCase();
            const description = (job.description || '').toLowerCase();
            const location = (job.location || '').toLowerCase();
            const serviceName = (job.service?.service_name || '').toLowerCase();
            const providerName = (job.service_provider?.name || '').toLowerCase();
            
            return (
                title.includes(query) ||
                description.includes(query) ||
                location.includes(query) ||
                serviceName.includes(query) ||
                providerName.includes(query) ||
                (job.budget && job.budget.toString().includes(query)) ||
                (job.scheduled_date && job.scheduled_date.toLowerCase().includes(query))
            );
        });
    },
    
    // Initialize the component
    init() {
        // Initialize search query from URL if present
        const urlParams = new URLSearchParams(window.location.search);
        this.searchQuery = urlParams.get('search') || '';
        
        // Initialize active tab from URL if present
        const tab = urlParams.get('tab');
        if (['ongoing', 'completed', 'cancelled', 'review'].includes(tab)) {
            this.activeTab = tab;
        }
        
        // Update URL when search query changes
        this.$watch('searchQuery', (value) => {
            const url = new URL(window.location);
            if (value) {
                url.searchParams.set('search', value);
            } else {
                url.searchParams.delete('search');
            }
            window.history.pushState({}, '', url);
        });
        
        // Update URL when tab changes
        this.$watch('activeTab', (value) => {
            const url = new URL(window.location);
            url.searchParams.set('tab', value);
            window.history.pushState({}, '', url);
        });
    }
}" x-init="init()">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            @if(auth()->user()->role === 'ServiceProvider')
                My Assigned Jobs
            @else
                My Jobs
            @endif
        </h1>
        @auth
            @if(auth()->user()->role === 'Homeowner')
                <div class="flex space-x-4">
                    <a href="{{ route('jobs.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Schedule Job
                    </a>
                </div>
            @endif
        @endauth
    </div>
    <x-success />

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ auth()->user()->role === 'Homeowner' ? 'Service Provider' : 'Homeowner' }}
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule Date</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $filteredJobs = $jobs->reject(function($job) {
                            return in_array($job->status, ['completed', 'cancelled']);
                        });
                    @endphp
                    @forelse($filteredJobs as $job)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <div class="flex items-center">
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $job->title }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ optional($job->service)->service_name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if(auth()->user()->role === 'Homeowner' && $job->serviceProvider)
                                    <div class="text-sm font-medium text-gray-900">{{ $job->serviceProvider->name ?? 'N/A' }}</div>
                                @else
                                    <div class="text-sm font-medium text-gray-900">{{ $job->homeowner->name ?? 'N/A' }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex items-center">
                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $job->location ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    ₱{{ $job->budget == (int)$job->budget ? number_format($job->budget, 0) : number_format($job->budget, 2) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'ongoing' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ][$job->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                                    {{ str_replace('_', ' ', ucfirst($job->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex items-center">
                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $job->scheduled_date ? \Carbon\Carbon::parse($job->scheduled_date)->format('M d, Y h:i A') : 'Not set' }}
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <!-- View Button (Always visible) -->
                                    <a href="{{ route('jobs.show', $job) }}" class="text-indigo-600 hover:text-indigo-900" title="View Details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    @if($job->status === 'pending')
                                        @if(auth()->user()->role !== 'Homeowner')
                                            <!-- Confirm Button (Only for pending jobs and not for homeowners) -->
                                            <button type="button" 
                                                    class="text-green-600 hover:text-green-900" 
                                                    title="Confirm Job" 
                                                    onclick="showConfirmModal('{{ $job->id }}')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                        @endif

                                        <!-- Reject Button with Modal -->
                                        <button type="button" 
                                                class="text-red-600 hover:text-red-900" 
                                                title="Reject Job" 
                                                onclick="showRejectModal('{{ $job->id }}')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <!-- Hidden form for rejection -->
                                        <form id="rejectForm{{ $job->id }}" action="{{ route('jobs.update', $job) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="cancelled">
                                        </form>
                                        
                                        <!-- Hidden form for submission -->
                                        <form id="rejectForm{{ $job->id }}" action="{{ route('jobs.cancel', $job) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('PATCH')
                                        </form>
                                    @elseif($job->status === 'upcoming' || $job->status === 'ongoing')
                                        <!-- Reject Button for ongoing/upcoming jobs -->
                                        <button type="button" 
                                                class="text-red-600 hover:text-red-900" 
                                                title="Reject Job" 
                                                onclick="showRejectModal('{{ $job->id }}')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <!-- Hidden form for rejection -->
                                        <form id="rejectForm{{ $job->id }}" action="{{ route('jobs.update', $job) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="cancelled">
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg class="h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">
                                        @if(auth()->user()->role === 'ServiceProvider')
                                            No jobs assigned to you yet
                                        @else
                                            No jobs found
                                        @endif
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        @if(auth()->user()->role === 'ServiceProvider')
                                            Check back later for new job opportunities
                                        @else
                                            Create your first job post to get started
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jobs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $jobs->links() }}
            </div>
        @endif
    </div>

    <!-- Tabs Navigation -->
    <div class="mt-12">
        <div class="border-b border-gray-200 flex items-center justify-between">
            <nav class="-mb-px flex space-x-8">
                <button @click="activeTab = 'ongoing'" 
                        :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'ongoing', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'ongoing' }" 
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Ongoing
                    <span class="bg-gray-100 text-gray-600 ml-2 py-0.5 px-2 rounded-full text-xs" x-text="getFilteredTabJobs('ongoing').length"></span>
                </button>
                <button @click="activeTab = 'completed'" 
                        :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'completed', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'completed' }" 
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Completed
                    <span class="bg-gray-100 text-gray-600 ml-2 py-0.5 px-2 rounded-full text-xs" x-text="getFilteredTabJobs('completed').length"></span>
                </button>
                <button @click="activeTab = 'cancelled'" 
                        :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'cancelled', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'cancelled' }" 
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Cancelled
                    <span class="bg-gray-100 text-gray-600 ml-2 py-0.5 px-2 rounded-full text-xs" x-text="getFilteredTabJobs('cancelled').length"></span>
                </button>
                <button @click="activeTab = 'review'" 
                        :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'review', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'review' }" 
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Rate & Review
                    <span class="bg-gray-100 text-gray-600 ml-2 py-0.5 px-2 rounded-full text-xs" x-text="getFilteredTabJobs('review').length"></span>
                </button>
            </nav>
            
            <!-- Search Input -->
            <div class="relative w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input 
                    type="text" 
                    x-model="searchQuery" 
                    placeholder="Search jobs..."
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
            </div>
        </div>

        <!-- Tab Panels -->
        <div class="mt-6">
            <!-- Ongoing Tab -->
            <div x-show="activeTab === 'ongoing'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div x-data="{
                    get jobs() { return getFilteredTabJobs('ongoing'); },
                    showActions: true
                }">
                    @include('jobs.partials.job-table', ['showActions' => true])
                </div>
            </div>

            <!-- Completed Tab -->
            <div x-show="activeTab === 'completed'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak>
                <div x-data="{
                    get jobs() { return getFilteredTabJobs('completed'); },
                    showActions: false
                }">
                    @include('jobs.partials.completed-job-table', ['showActions' => false])
                </div>
            </div>

            <!-- Cancelled Tab -->
            <div x-show="activeTab === 'cancelled'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak>
                <div x-data="{
                    get jobs() { return getFilteredTabJobs('cancelled'); },
                    showActions: false
                }">
                    @include('jobs.partials.cancelled-job-table', ['showActions' => false])
                </div>
            </div>

            <!-- Rate & Review Tab -->
            <div x-show="activeTab === 'review'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak>
                <div x-data="{
                    get jobs() { return getFilteredTabJobs('review'); }
                }">
                    @include('jobs.partials.review-table')
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

<!-- Confirm Job Modal -->
<div id="confirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Confirm Job</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">Are you sure you want to confirm this job?</p>
            </div>
            <div class="items-center px-4 py-3">
                <form id="confirmJobForm" method="POST" class="inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="upcoming">
                    <button type="submit" class="py-2 bg-green-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        Yes, Confirm Job
                    </button>
                </form>
                <button onclick="hideConfirmModal()" class="mt-2 py-2 bg-white text-gray-700 text-base font-medium rounded-md w-full border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Confirmation Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-3">Reject Job</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">Are you sure you want to reject this job? This action cannot be undone.</p>
            </div>
            <div class="flex px-4 py-3 gap-3">
                <button id="confirmReject" class="py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    Yes, Reject Job
                </button>
                <button type="button" onclick="hideRejectModal()" class="py-2 bg-white text-gray-700 text-base font-medium rounded-md w-full border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentFormId = '';
    
    function showConfirmModal(jobId) {
        const form = document.getElementById('confirmJobForm');
        form.action = `/jobs/${jobId}`;
        document.getElementById('confirmModal').classList.remove('hidden');
    }
    
    function hideConfirmModal() {
        document.getElementById('confirmModal').classList.add('hidden');
    }
    
    function showRejectModal(formId) {
        currentFormId = 'rejectForm' + formId;
        document.getElementById('rejectModal').classList.remove('hidden');
    }
    
    function hideRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }
    
    document.getElementById('confirmReject').addEventListener('click', function() {
        if (currentFormId) {
            document.getElementById(currentFormId).submit();
        }
    });
    
    // Close modals when clicking outside of them
    window.onclick = function(event) {
        const rejectModal = document.getElementById('rejectModal');
        const confirmModal = document.getElementById('confirmModal');
        
        if (event.target === rejectModal) {
            hideRejectModal();
        }
        
        if (event.target === confirmModal) {
            hideConfirmModal();
        }
    }
</script>

@endsection
