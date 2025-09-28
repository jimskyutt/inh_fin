@extends('layouts.app')

@section('content')
<x-success />
<div class="p-10 mt-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="">
                <h2 class="text-lg font-medium text-gray-900">Account Settings</h2>
                <p class="mt-1 text-sm text-gray-600">Update your account's username and password.</p>

                <div class="flex gap-3 justify-between" style="width: 100%;"> 
                    <div class="flex-1 mt-6 border-t border-gray-200 pt-6">
                        <h2 class="text-lg font-medium text-gray-900">Update Username</h2>
                        <form method="POST" action="{{ route('profile.update-username') }}" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="text" name="username" id="username" 
                                        value="{{ old('username', $user->username) }}" 
                                        class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                                        required>
                                </div>
                                @error('username')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Update Username
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Password Update Form -->
                    <div class="flex-1 mt-6 border-t border-gray-200 pt-6">
                        <h2 class="text-lg font-medium text-gray-900">Update Password</h2>
                        <form method="POST" action="{{ route('profile.update-password') }}" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="relative">
                                <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                                <div class="mt-1 relative">
                                    <input type="password" name="current_password" id="current_password" 
                                        class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-md sm:text-sm border-gray-300 pr-10"
                                        required>
                                    <div class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer" id="oldpasswordToggle">
                                        <i class="fas fa-eye"></i>
                                    </div>
                                </div>
                                @error('current_password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="relative">
                                <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                                <div class="mt-1 relative">
                                    <input type="password" name="password" id="password" 
                                        class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-md sm:text-sm border-gray-300 pr-10"
                                        required>
                                    <div class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer" id="newpasswordToggle">
                                        <i class="fas fa-eye"></i>
                                    </div>
                                </div>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="relative">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                                <div class="mt-1 relative">
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                        class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-md sm:text-sm border-gray-300 pr-10"
                                        required>
                                    <div class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer" id="confpasswordToggle">
                                        <i class="fas fa-eye"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const oldpasswordToggle = document.getElementById('oldpasswordToggle');
        const oldpasswordInput = document.getElementById('current_password');
        let isOldPasswordVisible = false;

        oldpasswordToggle.addEventListener('click', function() {
            isOldPasswordVisible = !isOldPasswordVisible;
            oldpasswordInput.type = isOldPasswordVisible ? 'text' : 'password';
            
            // Toggle between eye and eye-slash icons
            const icon = oldpasswordToggle.querySelector('i');
            icon.className = isOldPasswordVisible ? 'fas fa-eye-slash' : 'fas fa-eye';
        });

        const passwordToggle = document.getElementById('newpasswordToggle');
        const passwordInput = document.getElementById('password');
        let isPasswordVisible = false;

        passwordToggle.addEventListener('click', function() {
            isPasswordVisible = !isPasswordVisible;
            passwordInput.type = isPasswordVisible ? 'text' : 'password';
            
            // Toggle between eye and eye-slash icons
            const icon = passwordToggle.querySelector('i');
            icon.className = isPasswordVisible ? 'fas fa-eye-slash' : 'fas fa-eye';
        });

        const confPassword = document.getElementById('confpasswordToggle');
        const passwordConfInput = document.getElementById('password_confirmation');
        let isPasswordConfVisible = false;

        confPassword.addEventListener('click', function() {
            isPasswordConfVisible = !isPasswordConfVisible;
            passwordConfInput.type = isPasswordConfVisible ? 'text' : 'password';
            const icon = confPassword.querySelector('i');
            icon.className = isPasswordConfVisible ? 'fas fa-eye-slash' : 'fas fa-eye';
        });
    });
</script>
@endpush
@endsection
