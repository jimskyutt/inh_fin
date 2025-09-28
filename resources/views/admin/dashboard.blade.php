@extends('layouts.app')

@section('title', 'Admin Dashboard | INeedHand')
@php
    $containerWidth = 'w-[100%]';
    $containerHeight = 'h-[calc(99vh-3rem)]';
    $marginBottom = 'mb-[0px]';
    $containerBg = 'bg-none';
@endphp
@section('content')
    <div class="py-5">
        <div class="max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg">
                <div class="text-gray-900 dark:text-gray-100 p-5">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <h1 class="text-2xl font-extrabold text-gray-800 font-['Rajdhani'] tracking-wide">Admin Dashboard</h1>
                    <hr>

                    <!-- Analysis Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <!-- Total Homeowners Card -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="p-4 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300">
                                        <i class="fas fa-home text-xl"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Homeowners</p>
                                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $analysisData['totalHomeowners'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Service Providers Card -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="p-4 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300">
                                        <i class="fas fa-tools text-xl"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Service Providers</p>
                                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $analysisData['totalServiceProviders'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Jobs Completed Card -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="p-4 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-300">
                                        <i class="fas fa-tasks text-xl"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Jobs Completed</p>
                                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $analysisData['totalJobsCompleted'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h1 class="text-2xl font-extrabold text-gray-800 font-['Rajdhani'] tracking-wide">Newly Registered Users</h1>
                    <hr>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-800">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th class="py-3 px-4 text-left">Name</th>
                                    <th class="py-3 px-4 text-left">Email</th>
                                    <th class="py-3 px-4 text-left">Role</th>
                                    <th class="py-3 px-4 text-left">Status</th>
                                    <th class="py-3 px-4 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr class="border-t border-gray-200 dark:border-gray-700">
                                        <td class="py-3 px-4">{{ $user->name }}</td>
                                        <td class="py-3 px-4">{{ $user->email }}</td>
                                        <td class="py-3 px-4">{{ $user->role }}</td>
                                        <td class="py-3 px-4">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                {{ $user->status === 'verified' ? 'bg-green-100 text-green-800' : 
                                                ($user->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }} dark:bg-opacity-20">
                                                {{ ucfirst($user->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 space-x-2">
                                            <a href="{{ route('admin.users.view', $user) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            
                                            @if($user->status !== 'verified')
                                                <form action="{{ route('admin.users.verify', $user) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                                        <i class="fas fa-check"></i> Verify
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if($user->status !== 'rejected')
                                                <form action="{{ route('admin.users.reject', $user) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 px-6 text-center text-gray-500 dark:text-gray-400">No users found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
