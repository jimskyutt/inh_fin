@extends('layouts.app')

@section('content')
<div class="w-[800px] " style=" position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); ">
    <div class=" bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-6">Edit Post</h2>

        <form action="{{ route('posts.update', $post) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <!-- Content -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700">Post Content</label>
                    <textarea id="content" name="content" rows="4" 
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 {{ $errors->has('content') ? 'border-red-500' : '' }}"
                              required>{{ old('content', $post->content) }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if(auth()->user()->role !== 'Admin')
                <div class="flex gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-map-marker-alt text-red-500"></i>
                            </div>
                            <select name="barangay_id" class="w-full pl-10 p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" {{ auth()->user()->role !== 'Admin' ? 'required' : '' }}>
                                <option value="" disabled selected>Select Barangay</option>
                                @foreach($barangays as $barangay)
                                    <option value="{{ $barangay->brgy_id }}" 
                                            {{ old('barangay_id', $post->barangay_name) == $barangay->brgy_name ? 'selected' : '' }}>
                                            {{ $barangay->brgy_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="relative">                    
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-tools text-blue-500"></i>
                            </div>
                            <select name="service_id" class="w-full pl-10 p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="" disabled selected>Select Service</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->service_id }}" 
                                            {{ old('service_id', $post->service_name) == $service->service_name ? 'selected' : '' }}>
                                        {{ $service->service_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="w-32">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-green-500 font-medium">₱</span>
                            </div>
                            <input type="number" name="budget" class="w-full pl-8 p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="0" placeholder="" step="0.01" value="{{ old('budget', $post->budget) }}">
                        </div>
                    </div>
                </div>
                @endif

                <div class="flex justify-end space-x-4 pt-4">
                    <button type="submit" class="py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save me-2"></i> Update Post
                    </button>
                    <a href="{{ route('page.newsfeed') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-white bg-gray-500">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>
@endsection
