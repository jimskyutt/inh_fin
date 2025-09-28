@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Job</h2>
            
            <form method="POST" action="{{ route('jobs.update', $job) }}" id="jobForm">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Job Title</label>
                        <div class="mt-1">
                            <input type="text" name="title" id="title" value="{{ old('title', $job->title) }}" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('title') border-red-500 @enderror">
                        </div>
                        @error('title')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <div class="mt-1">
                            <textarea name="description" id="description" rows="4" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('description') border-red-500 @enderror">{{ old('description', $job->description) }}</textarea>
                        </div>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="service_id" class="block text-sm font-medium text-gray-700">Service Type</label>
                        <div class="mt-1 relative">
                            <select name="service_id" id="service_id" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-gray-900 @error('service_id') border-red-500 @enderror">
                                <option value="">Select a service type</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->service_id }}" {{ old('service_id', $job->service_id) == $service->service_id ? 'selected' : '' }}>
                                        {{ $service->service_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                                    <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                        @error('service_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="service_provider_id" class="block text-sm font-medium text-gray-700">Service Provider</label>
                        <div class="mt-1 relative">
                            <select name="service_provider_id" id="service_provider_id" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-gray-900 @error('service_provider_id') border-red-500 @enderror">
                                <option value="">Select a service provider</option>
                                @foreach($serviceProviders as $provider)
                                    <option value="{{ $provider->id }}" {{ old('service_provider_id', $job->service_provider_id) == $provider->id ? 'selected' : '' }}>
                                        {{ $provider->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                                    <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                        @error('service_provider_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="budget" class="block text-sm font-medium text-gray-700">Budget (₱)</label>
                        <div class="mt-1">
                            <input type="number" name="budget" id="budget" min="0" step="0.01" value="{{ old('budget', $job->budget) }}"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('budget') border-red-500 @enderror">
                        </div>
                        @error('budget')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                        <div class="mt-1 relative">
                            <select name="location" id="location" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-gray-900 @error('location') border-red-500 @enderror">
                                <option value="">Select a location</option>
                                @foreach($barangays as $barangay)
                                    <option value="{{ $barangay->brgy_name }}" {{ old('location', $job->location) == $barangay->brgy_name ? 'selected' : '' }}>
                                        {{ $barangay->brgy_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                                    <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                        @error('location')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="scheduled_date" class="block text-sm font-medium text-gray-700">Scheduled Date</label>
                        <div class="mt-1">
                            <input type="date" name="scheduled_date" id="scheduled_date" 
                                value="{{ old('scheduled_date', $job->scheduled_date->format('Y-m-d')) }}" required
                                min="{{ now()->addDay()->format('Y-m-d') }}"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('scheduled_date') border-red-500 @enderror">
                        </div>
                        @error('scheduled_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('jobs.show', $job) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Update Job
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const serviceIdSelect = document.getElementById('service_id');
        const serviceProviderSelect = document.getElementById('service_provider_id');

        function loadServiceProviders(serviceId) {
            if (!serviceId) {
                serviceProviderSelect.innerHTML = '<option value="">Select a service type first</option>';
                return;
            }

            fetch(`/api/service-providers/${serviceId}`)
                .then(response => response.json())
                .then(providers => {
                    let options = '<option value="">Select a service provider</option>';
                    providers.forEach(provider => {
                        options += `<option value="${provider.id}" ${provider.id == serviceProviderSelect.dataset.selected ? 'selected' : ''}>${provider.name}</option>`;
                    });
                    serviceProviderSelect.innerHTML = options;
                })
                .catch(error => {
                    console.error('Error loading service providers:', error);
                    serviceProviderSelect.innerHTML = '<option value="">Error loading providers</option>';
                });
        }

        serviceIdSelect.addEventListener('change', function() {
            loadServiceProviders(this.value);
        });

        // Load initial service providers if a service is already selected
        if (serviceIdSelect.value) {
            serviceProviderSelect.dataset.selected = '{{ old('service_provider_id', $job->service_provider_id) }}';
            loadServiceProviders(serviceIdSelect.value);
        }
    });
</script>
@endpush
@endsection
