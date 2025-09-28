<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title')</title>

        <link rel="icon" href="{{ asset('storage/logo/logo.png') }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ asset('storage/logo/logo.png') }}" type="image/x-icon">
        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
        <!-- Font Awesome -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

        <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <!-- Scripts -->
        <!-- Add Laravel JavaScript object with authenticated user ID -->
        <script>
            window.Laravel = {!! json_encode([
                'userId' => auth()->id(),
                'user' => auth()->user(),
                'signedIn' => auth()->check()
            ]) !!};
        </script>
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/job-posts.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
       
            @include('layouts.navigation')

            <div class=" cont {{ $containerWidth ?? 'w-[1200px]' }} cont {{ $containerHeight ?? 'h-[500px]' }} cont {{ $marginTop ?? 'mt-[50px]' }} {{ $marginBottom ?? 'mb-[20px]' }} cont {{ $containerBg ?? 'bg-white' }} px-2 p-2 dark:bg-gray-800 overflow-hidden sm:rounded-lg">
                @yield('content')
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
