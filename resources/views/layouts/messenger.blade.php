<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Messages</title>
    
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    
    <!-- Scripts will be injected by Vite -->
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="w-full md:w-1/3 lg:w-1/4 bg-white border-r border-gray-200 flex flex-col h-full">
            <!-- Header -->
            <div class="p-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h1 class="text-xl font-semibold">Messages</h1>
                    <a href="{{ route('messages.create') }}" class="text-blue-500 hover:text-blue-700">
                        <i class="fas fa-edit text-xl"></i>
                    </a>
                </div>
                <!-- Search -->
                <div class="mt-4 relative">
                    <input type="text" placeholder="Search messages" 
                           class="w-full px-4 py-2 bg-gray-100 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-search absolute right-3 top-2.5 text-gray-400"></i>
                </div>
            </div>
            
            <!-- Conversation List -->
            <div class="flex-1 overflow-y-auto" id="conversation-list">
                @yield('conversations')
            </div>
            
            <!-- User Profile -->
            <div class="p-4 border-t border-gray-200">
                <div class="flex items-center">
                    <img src="{{ Auth::user()->face_img ? asset('storage/' . Auth::user()->face_img) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}" 
                         alt="{{ Auth::user()->name }}" 
                         class="w-10 h-10 rounded-full object-cover">
                    <div class="ml-3">
                        <p class="font-medium">{{ Auth::user()->name }}</p>
                        <p class="text-sm text-gray-500">Active now</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Chat Area -->
        <div class="hidden md:flex flex-col flex-1 bg-gray-50">
            @yield('content', '<div class="flex items-center justify-center h-full text-gray-500">
                <div class="text-center">
                    <i class="fas fa-comment-dots text-5xl mb-4"></i>
                    <h2 class="text-xl font-semibold">Select a conversation</h2>
                    <p>or start a new one</p>
                </div>
            </div>')
        </div>
    </div>

    @stack('scripts')
</body>
</html>
