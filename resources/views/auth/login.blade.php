@extends('layouts.guest')

@section('title', 'Login | INeedHand')

@php
    $containerWidth = 'w-[400px]';
@endphp

<style>
    @media screen and (max-width: 428px) {

        .login-container {
            width: 100%;
            max-width: 100%;
            padding: 0 1rem;
        }
        
        .logo-container img {
            height: 80px !important;
        }
        
        .input-container {
            width: 100%;
        }
        
        .input-container input {
            width: 100% !important;
            padding-left: 3rem !important;
        }
        
        .input-container .icon {
            left: 1rem !important;
        }
        
        .auth-links {
            flex-direction: column;
            gap: 0.75rem;
            align-items: flex-start;
        }
        
        .auth-links a {
            width: 100%;
            text-align: center;
            padding: 0.5rem 0;
        }
    }
</style>

@section('content')

<x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="login-container">
        @csrf

        <div class="flex flex-col items-center mb-6 logo-container">
            <img src="{{ asset('storage/logo/logo.png') }}" alt="Logo" class="h-24 w-auto">
            <h1 class="text-4xl font-extrabold font-['Rajdhani'] tracking-wide bg-clip-text text-transparent" style="background-image: url('{{ asset('storage/logo/lemery.png') }}'); background-size: cover; background-position: center;">INeedHand</h1>
        </div>

        <div class="relative mt-4 input-container">
            <div class="relative">
                <input type="text" id="username" name="username" class="block w-full px-4 py-3 pl-14 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" placeholder=" " value="{{ old('username') }}" required autofocus autocomplete="username">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 icon">
                    <i class="fas fa-user"></i>
                </div>
                <label for="username" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2"> Username </label>
            </div>
            <x-input-error :messages="$errors->get('username')" class="mt-1" />
        </div>

        <div class="relative mt-4 input-container">
            <div class="relative">
                <input type="password" id="password" name="password" class="block w-full px-4 py-3 pl-14 pr-12 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" placeholder=" " required autocomplete="current-password">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <i class="fas fa-lock"></i>
                </div>
                <div class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer" id="passwordToggle">
                    <i class="fas fa-eye"></i>
                </div>
                <label for="password" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2"> Password </label>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="flex flex-col sm:flex-row items-end justify-end mt-4 gap-4">

            @if (Route::has('password.request'))
                <a class="text-sm text-indigo-600 hover:text-indigo-900 w-full sm:w-auto text-center sm:text-right" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <div class="flex items-center justify-end mt-4">
            <button type="submit" class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition duration-200 ease-in-out text-base sm:text-sm">
                {{ __('Log in') }}
            </button>
        </div>

        <div class="flex flex-col items-center justify-center mt-6 gap-3 text-center">
            <p class="text-sm text-gray-600">{{ __("Don't have an account?") }}</p>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="w-full py-2 px-4 border border-indigo-600 text-indigo-600 hover:bg-indigo-50 rounded-lg transition duration-200 ease-in-out text-sm font-medium">
                    {{ __('Create an Account') }}
                </a>
            @endif
        </div>

        
    </form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordToggle = document.getElementById('passwordToggle');
        const passwordInput = document.getElementById('password');
        let isPasswordVisible = false;

        passwordToggle.addEventListener('click', function() {
            isPasswordVisible = !isPasswordVisible;
            passwordInput.type = isPasswordVisible ? 'text' : 'password';
            
            const icon = passwordToggle.querySelector('i');
            icon.className = isPasswordVisible ? 'fas fa-eye-slash' : 'fas fa-eye';
        });
    });
</script>
@endpush
