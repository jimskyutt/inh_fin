@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Post a New Job</h2>
            
            <form method="POST" action="{{ route('jobs.store') }}" id="jobForm">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Job Title</label>
                        <div class="mt-1">
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
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
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('description') border-red-500 @enderror">
                                {{ old('description') }}
                            </textarea>
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
                                    <option value="{{ $service->service_id }}" {{ old('service_id') == $service->service_id ? 'selected' : '' }}>
                                        {{ $service->service_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                                    <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
                                <option value="" class="text-gray-900">Select a service provider</option>
                                @foreach($serviceProviders as $provider)
                                    @if($provider->services->pluck('id')->contains(old('service_id') ?? $service->id ?? null))
                                        <option value="{{ $provider->id }}" class="text-gray-900" {{ old('service_provider_id') == $provider->id ? 'selected' : '' }}>
                                            {{ $provider->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                                    <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                        @error('service_provider_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="budget" class="block text-sm font-medium text-gray-700">Budget (Optional)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" step="0.01" name="budget" id="budget" value="{{ old('budget') }}"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm pl-7 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('budget') border-red-500 @enderror">
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
                                <option value="" class="text-gray-900">Select a barangay</option>
                                @foreach($barangays as $barangay)
                                    <option value="{{ $barangay->brgy_name }}" class="text-gray-900" {{ old('location') == $barangay->brgy_name ? 'selected' : '' }}>
                                        {{ $barangay->brgy_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                                    <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
                            <input type="date" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date') }}" required
                                min="{{ now()->addDay()->format('Y-m-d') }}"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('scheduled_date') border-red-500 @enderror">
                        </div>
                        @error('scheduled_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const serviceSelect = document.getElementById('service_id');
                            const providerSelect = document.getElementById('service_provider_id');
                            const form = document.getElementById('jobForm');

                            serviceSelect.addEventListener('change', async function() {
                                const selectedServiceId = this.value;
                                
                                if (!selectedServiceId) {
                                    providerSelect.innerHTML = '<option value="">Select a service provider</option>';
                                    return;
                                }

                                try {
                                    const response = await fetch(`/api/service-providers/${selectedServiceId}`);
                                    const providers = await response.json();

                                    // Clear existing options
                                    providerSelect.innerHTML = '<option value="">Select a service provider</option>';

                                    // Add new options
                                    providers.forEach(provider => {
                                        const option = document.createElement('option');
                                        option.value = provider.id;
                                        option.textContent = provider.name;
                                        option.className = 'text-gray-900';
                                        providerSelect.appendChild(option);
                                    });
                                } catch (error) {
                                    console.error('Error fetching providers:', error);
                                    providerSelect.innerHTML = '<option value="">Error loading providers</option>';
                                }
                            });

                            // Initialize providers if a service is pre-selected
                            if (serviceSelect.value) {
                                serviceSelect.dispatchEvent(new Event('change'));
                            }
                        });
                    </script>

                    <div class="flex justify-end">
                        <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Post Job
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
