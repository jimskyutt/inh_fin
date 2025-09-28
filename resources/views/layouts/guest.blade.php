<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title')</title>

        <!-- Favicon -->
        <link rel="icon" href="{{ asset('storage/logo/logo.png') }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ asset('storage/logo/logo.png') }}" type="image/x-icon">


        
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
    </head>
    <style>
        @media screen and (max-width: 428px) {
            .cont {
                width: 90%;
            }
            
        }
    </style>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div class=" cont {{ $containerWidth ?? 'w-[800px]' }} px-6 p-4 mt-6 mb-6 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                @yield('content')
            </div>
        </div>
        @stack('scripts')
    </body>
    
    <script>
    (function() {
        'use strict';
        
        // Initialize history state
        history.pushState(null, null, window.location.href);
        
        // Method 1: Block History Navigation (Back/Forward buttons)
        function blockHistory(event) {
            history.pushState(null, null, window.location.href);
            return false;
        }
        window.addEventListener('popstate', blockHistory);
        
        // Method 2: Block Keyboard Navigation Shortcuts
        function blockKeyboard(event) {
            // Block Alt+Left Arrow (Back), Alt+Right Arrow (Forward)
            if (event.altKey && (event.keyCode === 37 || event.keyCode === 39)) {
                event.preventDefault();
                event.stopPropagation();
                return false;
            }
            
            // Block Backspace key navigation (when not in input fields)
            if (event.keyCode === 8 && 
                event.target.nodeName !== 'INPUT' && 
                event.target.nodeName !== 'TEXTAREA' &&
                !event.target.isContentEditable) {
                event.preventDefault();
                event.stopPropagation();
                return false;
            }
            
            // Block F5 and Ctrl+R (refresh)
            if ((event.keyCode === 116) || (event.ctrlKey && event.keyCode === 82)) {
                event.preventDefault();
                event.stopPropagation();
                return false;
            }
        }
        document.addEventListener('keydown', blockKeyboard, true);
        
        // Method 3: Block Mouse Navigation Buttons
        function blockMouse(event) {
            // Block mouse back (button 3) and forward (button 4) buttons
            if (event.button === 3 || event.button === 4) {
                event.preventDefault();
                event.stopPropagation();
                return false;
            }
        }
        document.addEventListener('mousedown', blockMouse, true);
        document.addEventListener('mouseup', blockMouse, true);
        
        
        
        // Clean up event listeners when navigating away
        window.addEventListener('unload', function() {
            window.removeEventListener('popstate', blockHistory);
            document.removeEventListener('keydown', blockKeyboard, true);
            document.removeEventListener('mousedown', blockMouse, true);
            document.removeEventListener('mouseup', blockMouse, true);
        });
    })();
    </script>
</html>
