@extends('layouts.app')

@section('title', 'Edit Profile | INeedHand')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Back Button -->
    <button onclick="window.history.back()" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6 cursor-pointer">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back
    </button>
    
    <div class="bg-white rounded-lg shadow-lg p-6" style="width: 800px; margin: 0 auto;">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Profile</h1>
        
        <div class="space-y-8">
            <!-- Update Profile Information -->
            <div class="border-b border-gray-200 pb-8">
                @include('profile.partials.update-profile-information-form', ['user' => $user])
            </div>
        </div>
    </div>
</div>
@endsection
