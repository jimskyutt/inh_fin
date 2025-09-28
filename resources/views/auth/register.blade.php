@extends('layouts.guest')

@section('title', 'Register Account | INeedHand')

@php
    $containerWidth = 'w-[800px]';
@endphp
    <script>
        // Disable back/forward navigation
        if (window.history && window.history.pushState) {
            // Add a new history entry
            history.pushState(null, null, document.URL);
            
            // Handle back/forward button
            window.addEventListener('popstate', function(event) {
                // Push the current URL again to prevent navigation
                history.pushState(null, null, document.URL);
            });
            
            // Prevent right-click context menu
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                return false;
            });
            
            // Disable backspace key navigation (except in input fields and textareas)
            document.onkeydown = function(e) {
                if (e.keyCode === 8 && !(e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.isContentEditable)) {
                    e.preventDefault();
                }
            };
        }
    </script>
<style>
    @media screen and (max-width: 428px ) {
        .age input{
            width: 304px;
            text-align: left;
        }
        
    }
</style>
@section('content')
    <form id="registerForm" method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="relative">
        @csrf

        <!-- Back Button -->
        <a href="{{ url('login') }}" class="absolute top-0 left-0 flex items-center text-gray-600 hover:text-indigo-600 transition-colors duration-200">
            <i class="fas fa-arrow-left text-lg mr-1"></i>
            <span class="text-sm font-medium">Back</span>
        </a>

        <!-- Logo -->
        <div class="flex flex-col items-center mb-6">
            <img src="{{ asset('storage/logo/logo.png') }}" alt="Logo" class="h-24 w-auto">
            <h1 class="text-4xl font-extrabold text-gray-800 font-['Rajdhani'] tracking-wide">INeedHand</h1>
        </div>

        <h1 class="text-center mt-5 text-3xl font-extrabold text-gray-800 font-['Rajdhani'] tracking-wide">Register Account</h1>

        <h1 class="mt-5 text-2xl font-extrabold text-gray-800 font-['Rajdhani'] tracking-wide">Personal Information</h1>
        <hr>

        <div class="relative mt-4">
            <div class="relative">
                <input type="text" id="name" name="name" class="block w-full px-4 py-3 pl-14 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" placeholder=" " value="{{ old('name') }}" required autofocus autocomplete="name" onkeyup="capitalizeWords(this)" >
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <i class="fas fa-user"></i>
                </div>
                <label for="name" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2"> Full Name </label>
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <div class="flex flex-col md:flex-row gap-5 mt-4">
            
        <div class="relative flex-1">
    <div class="relative">
        <input type="date" 
               id="birthday" 
               name="birthday" 
               class="block w-full pl-12 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" 
               placeholder=" " 
               value="{{ old('birthday') }}"
               required
               onchange="updateAge(this.value)">
        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
            <i class="fas fa-calendar"></i>
        </div>
        <label for="birthday" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2"> Birthday </label>
    </div>
    <div id="birthday-error" class="mt-1 text-sm text-red-600 hidden">You must be at least 18 years old.</div>
    <x-input-error :messages="$errors->get('birthday')" class="mt-1" />
</div>
<div class="relative age" style="width: 120px;">
    <div class="relative">
        <input type="number" 
               id="age" 
               name="age" 
               class="text-center block w-full pl-12 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" 
               placeholder=" " 
               value="{{ old('age') }}" 
               readonly>
        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
            <i class="fas fa-birthday-cake"></i>
        </div>
        <label for="age" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2"> Age </label>
    </div>
    <x-input-error :messages="$errors->get('age')" class="mt-1" />
</div>

            <div class="relative flex-1">
                <div class="relative">
                    <select id="sex" name="sex" class="block w-full pl-14 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 transition duration-200 ease-in-out peer" required>
                        <option value="male" {{ old('sex') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('sex') == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fas fa-venus-mars"></i>
                    </div>
                    <label for="sex" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2"> Sex </label>
                </div>
                <x-input-error :messages="$errors->get('sex')" class="mt-1" />
            </div>

            <div class="relative flex-1">
                <div class="relative">
                    <select id="civil_status" name="civil_status" class="block w-full pl-14 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 transition duration-200 ease-in-out peer" required>
                        <option value="single" {{ old('civil_status') == 'single' ? 'selected' : '' }}>Single</option>
                        <option value="married" {{ old('civil_status') == 'married' ? 'selected' : '' }}>Married</option>
                        <option value="divorced" {{ old('civil_status') == 'divorced' ? 'selected' : '' }}>Divorced</option>
                        <option value="widowed" {{ old('civil_status') == 'widowed' ? 'selected' : '' }}>Widowed</option>
                    </select>
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fas fa-heart"></i>
                    </div>
                    <label for="civil_status" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2"> Civil Status </label>
                </div>
                <x-input-error :messages="$errors->get('civil_status')" class="mt-1" />
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-4 mt-4">

            <div class="relative group flex-1">
                <div class="relative">
                    <input type="email" id="email" name="email" class="block w-full pl-12 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" placeholder=" " value="{{ old('email') }}" required autocomplete="username">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 group-hover:text-indigo-600 transition-colors duration-200">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <label for="email" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2">
                        Email Address
                    </label>
                </div>
                <p class="mt-2 text-sm text-gray-500">A verification link will be sent to your email after registration.</p>
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div class="relative flex-1">
                <div class="relative">
                    <input type="tel" id="contact_number" name="contact_number" class="block w-full pl-12 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" placeholder=" " value="{{ old('contact_number') }}" required >
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fas fa-phone"></i>
                    </div>
                    <label for="contact_number" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2"> Contact Number </label>
                </div>
                <div id="contact_number-error" class="mt-1 text-sm text-red-600 hidden"></div>
                <x-input-error :messages="$errors->get('contact_number')" class="mt-1" />
            </div>

        </div>

        <div class="flex flex-col md:flex-row gap-4 mt-4">
            <div class="relative group flex-1">
                <div class="relative">
                    <select id="address" name="address" class="block w-full pl-12 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" required >
                        <option value="" disabled selected></option>
                        @foreach($barangays as $barangay)
                        <option value="{{ $barangay->brgy_name }}" {{ old('address') == $barangay->brgy_name ? 'selected' : '' }}>
                            {{ $barangay->brgy_name }}
                        </option>
                        @endforeach
                    </select>
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <label for="address" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2"> Barangay </label>
                </div>
                <x-input-error :messages="$errors->get('address')" class="mt-1" />
            </div>

    
            <div class="relative group flex-1">
                <div class="relative">
                    <input type="text" id="street" name="street" class="block w-full pl-12 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" placeholder=" " value="{{ old('street') }}" required />
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fas fa-road"></i>
                    </div>
                    <label for="street" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2"> Street </label>
                </div>
                <x-input-error :messages="$errors->get('street')" class="mt-1" />
            </div>
        </div>

        <h1 class="mt-5 text-2xl font-extrabold text-gray-800 font-['Rajdhani'] tracking-wide">Face and Documents</h1>
        <hr>

        <div class="flex flex-col md:flex-row gap-4 mt-4">

            <div class="flex-1">
                <x-input-label for="face-capture" :value="__('Face Capture')" />
                <div class="mt-1 flex justify-center">
                    <div class="relative">
                        <video id="video" width="320" height="240" autoplay class="border rounded-md hidden"></video>
                        <canvas id="canvas" width="320" height="240" class="border rounded-md hidden"></canvas>
                        <div id="face_capture_placeholder" class="bg-gray-100 flex justify-center items-center border rounded-md" style="width: 320px; height: 240px;">
                            <div class="text-center">
                                <svg class="h-16 w-16 text-gray-400 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-2 flex justify-center">
                    <button type="button" id="startCamera" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-camera mr-2"></i> Start Camera
                    </button>
                    <input type="hidden" name="face_image" id="faceImage">
                </div>
                <x-input-error :messages="$errors->get('face_image')" class="mt-2" />
            </div>

            <div class="flex-1">
                <x-input-label for="police_clearance" :value="__('Police Clearance')" />
                <div class="mt-1 flex justify-center">
                    <div class="relative">
                        <img id="police_clearance_preview" src="" alt="Police Clearance Preview" class="border rounded-md hidden" style="width: 320px; height: 240px; object-fit: contain;">
                        <div id="police_clearance_placeholder" class="bg-gray-100 flex justify-center items-center border rounded-md" style="width: 320px; height: 240px;">
                            <div class="text-center">
                                <svg class="h-16 w-16 text-gray-400 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No file chosen</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-2 flex justify-center">
                    <button type="button" id="police_clearance_btn" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-upload mr-2"></i> Upload File
                    </button>
                    <span id="police_clearance_file" class="hidden"></span>
                    <input type="file" id="police_clearance" name="police_clearance" accept="image/*" class="hidden" required>
                </div>
                <x-input-error :messages="$errors->get('police_clearance')" class="mt-2" />
            </div>
            
        </div>

        <div class="flex flex-col md:flex-row gap-1 mt-4">
            
            <div class="flex-1">
                <x-input-label for="id_front" :value="__('ID Card (Front)')" />
                <div class="relative">
                    <div class="flex justify-center">
                        <img id="id_front_preview" src="" alt="ID Front Preview" class="border rounded-md hidden" style="width: 350px; height: 210px; object-fit: contain;">
                        <div id="id_front_placeholder" class="bg-gray-100 flex justify-center items-center border rounded-md" style="width: 350px; height: 210px;">
                            <div class="text-center">
                                <svg class="h-16 w-16 text-gray-400 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">Front side</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 flex flex-col items-center">
                        <button type="button" id="id_front_btn" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-upload mr-2"></i> Upload Front
                        </button>
                        <span id="id_front_file" class="hidden"></span>
                        <input type="file" id="id_front" name="id_front" accept="image/*" class="hidden" required>
                    </div>
                    <x-input-error :messages="$errors->get('id_front')" class="mt-2" />
                </div>
            </div>

            <div class="flex-1">
                <x-input-label for="id_back" :value="__('ID Card (Back)')" />
                <div class="relative">
                    <div class="flex justify-center">
                        <img id="id_back_preview" src="" alt="ID Back Preview" class="border rounded-md hidden" style="width: 350px; height: 210px; object-fit: contain;">
                        <div id="id_back_placeholder" class="bg-gray-100 flex justify-center items-center border rounded-md" style="width: 350px; height: 210px;">
                            <div class="text-center">
                                <svg class="h-16 w-16 text-gray-400 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">Back side</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 flex flex-col items-center">
                        <button type="button" id="id_back_btn" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-upload mr-2"></i> Upload Back
                        </button>
                        <span id="id_back_file" class="hidden"></span>
                        <input type="file" id="id_back" name="id_back" accept="image/*" class="hidden" required>
                    </div>
                    <x-input-error :messages="$errors->get('id_back')" class="mt-2" />
                </div>
            </div>

        </div>

        <h1 class="mt-5 text-2xl font-extrabold text-gray-800 font-['Rajdhani'] tracking-wide">Account Credentials</h1>
        <hr>

        <div class="flex flex-col md:flex-row gap-4 mt-5">
            
            <div class="flex-1">
                <div class="relative">
                    <select id="role" name="role" class="block w-full pl-14 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 transition duration-200 ease-in-out peer" required >
                        <option value="Homeowner" {{ old('role') == 'Homeowner' ? 'selected' : '' }}>Homeowner</option>
                        <option value="ServiceProvider" {{ old('role') == 'ServiceProvider' ? 'selected' : '' }}>Service Provider</option>
                    </select>
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fas fa-user-tag"></i>
                    </div>
                    <label for="role" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2"> User Type </label>
                </div>
                <x-input-error :messages="$errors->get('role')" class="mt-1" />
            </div>

            <div class="flex-1">
                <div class="relative">
                    <x-text-input id="username" name="username" type="text" class="block w-full px-4 py-3 pl-14 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 transition duration-200 ease-in-out peer" value="{{ old('username') }}" required autocomplete="username" placeholder=" "/>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-400"></i>
                    </div>
                    <label for="username" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2">Username</label>
                </div>
                <x-input-error :messages="$errors->get('username')" class="mt-2" />
            </div>

        </div>

        <div class="flex flex-col md:flex-row gap-4 mt-4">
            
            <div class="flex-1">
                <div class="relative">
                    <x-text-input id="password" name="password" type="password" class="block w-full px-4 py-3 pl-14 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 transition duration-200 ease-in-out peer" required autocomplete="new-password" placeholder=" "/>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer" id="passwordToggle">
                        <i class="fas fa-eye"></i>
                    </div>
                    <label for="password" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2"> Password </label>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div class="flex-1">
                <div class="relative">
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block w-full px-4 py-3 pl-14 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 transition duration-200 ease-in-out peer" required placeholder=" " />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer" id="confpasswordToggle">
                        <i class="fas fa-eye"></i>
                    </div>
                    <label for="password_confirmation" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2"> Confirm Password </label>
                </div>
            </div>

        </div>

        <div id="servicesSection" class="hidden mb-6 p-4">
            <div class="flex justify-between items-center mb-3">
                <label class="block text-gray-700 text-sm font-bold">Services Offered</label>
                <span id="servicesCounter" class="text-sm text-gray-600">0/3 services selected</span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-3" id="servicesContainer">
            </div>
            <div id="servicesError" class="mt-1 text-sm text-red-600 hidden">You can select up to 3 services only.</div>
            @error('services')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col items-center justify-center mt-5">
             
            <x-primary-button class="">
                {{ __('Register') }}
            </x-primary-button>

            <a class="mt-2 text-blue-600 rounded-md " href="{{ route('login') }}">
                {{ __('Already have an account?') }}
            </a>

        </div>
        <script>
document.addEventListener('DOMContentLoaded', function() {
    // Fields to validate
    const fields = ['email', 'contact_number', 'username'];
    const debounceTime = 500; // milliseconds
    
    // Function to check field uniqueness
    const checkField = async function(field, value) {
        if (!value) return;
        
        try {
            const response = await fetch('/check-unique', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ field, value })
            });
            
            const data = await response.json();
            const input = document.getElementById(field);
            let errorDiv = document.getElementById(`${field}_error`);
            
            // Create error div if it doesn't exist
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'mt-1 text-sm text-red-600';
                errorDiv.id = `${field}_error`;
                input.parentNode.appendChild(errorDiv);
            }
            
            if (data.exists) {
                input.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
                input.classList.remove('border-gray-300', 'focus:border-indigo-500', 'focus:ring-indigo-200');
                errorDiv.textContent = data.message;
                errorDiv.classList.remove('hidden');
            } else {
                input.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
                input.classList.add('border-gray-300', 'focus:border-indigo-500', 'focus:ring-indigo-200');
                errorDiv.textContent = '';
                errorDiv.classList.add('hidden');
            }
        } catch (error) {
            console.error('Validation error:', error);
        }
    };

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), wait);
        };
    }

    // Add event listeners
    fields.forEach(field => {
        const input = document.getElementById(field);
        if (input) {
            const debouncedCheck = debounce(checkField, debounceTime);
            
            input.addEventListener('blur', () => checkField(field, input.value.trim()));
            input.addEventListener('input', (e) => {
                const value = input.value.trim();
                debouncedCheck(field, value);
                
                if (value === '') {
                    input.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
                    input.classList.add('border-gray-300', 'focus:border-indigo-500', 'focus:ring-indigo-200');
                    const errorDiv = document.getElementById(`${field}_error`);
                    if (errorDiv) {
                        errorDiv.textContent = '';
                        errorDiv.classList.add('hidden');
                    }
                }
            });
        }
    });

    // Contact number validation
    const contactNumberInput = document.getElementById('contact_number');
    const contactNumberError = document.getElementById('contact_number-error');

    function validateContactNumber(value) {
        if (!value) return 'Contact number is required';
        if (!/^09\d{9}$/.test(value)) {
            return 'Contact number must start with 09 and be 11 digits long';
        }
        return '';
    }

    contactNumberInput.addEventListener('input', function(e) {
        // Only allow numbers
        this.value = this.value.replace(/\D/g, '');
        
        // Limit to 11 digits
        if (this.value.length > 11) {
            this.value = this.value.slice(0, 11);
        }
        
        // Validate
        const error = validateContactNumber(this.value);
        if (error) {
            contactNumberError.textContent = error;
            contactNumberError.classList.remove('hidden');
            this.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
        } else {
            contactNumberError.classList.add('hidden');
            this.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
        }
    });

    // Update form submission to validate contact number
    const form = document.getElementById('registerForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const contactNumber = contactNumberInput.value.trim();
            const error = validateContactNumber(contactNumber);
            
            if (error) {
                e.preventDefault();
                contactNumberError.textContent = error;
                contactNumberError.classList.remove('hidden');
                contactNumberInput.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
                contactNumberInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }
});
</script>
    </form>

    <!-- Verification Status Modal -->
    <div id="verificationModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
            <div class="text-center">
                <div id="verificationIcon" class="mx-auto flex items-center justify-center h-16 w-16 mb-4">
                    <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600"></div>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2" id="verificationTitle">Account Verification</h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-500" id="verificationMessage">Your account is pending verification. Please wait while we verify your details.</p>
                </div>
                <div class="mt-4" id="verificationActions">
                    <!-- Will be shown when verified -->
                    <a href="{{ route('login') }}" id="proceedToLogin" class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 hidden">
                        Proceed to Login
                    </a>
                    <!-- Will be shown when rejected -->
                    <button type="button" id="closeModal" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm hidden">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const cameraBtn = document.getElementById('startCamera');
            const faceImage = document.getElementById('faceImage');
            const facePlaceholder = document.getElementById('face_capture_placeholder');
            let stream = null;
            let isCaptured = false;

            // Start camera when button is clicked
            cameraBtn.addEventListener('click', async () => {
                try {

                    if (!stream && !isCaptured) {
                    
                        stream = await navigator.mediaDevices.getUserMedia({ 
                            video: { 
                                width: 320,
                                height: 240,
                                facingMode: 'user'  // Front camera only
                            },
                            audio: false 
                        });

                        video.srcObject = stream;
                        video.classList.remove('hidden');
                        facePlaceholder.classList.add('hidden');
                        cameraBtn.innerHTML = '<i class="fas fa-camera mr-2"></i> Capture';
                        cameraBtn.classList.remove('bg-gray-800');
                        cameraBtn.classList.add('bg-indigo-600', 'hover:bg-indigo-500');
                        return;
                    }

                    if (stream && !isCaptured) {

                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                
                        // Convert to JPEG format with 0.92 quality (good balance between quality and size)
                        const jpegData = canvas.toDataURL('image/jpeg', 0.92);
                        console.log('JPEG data length:', jpegData.length);
                        console.log('JPEG data starts with:', jpegData.substring(0, 50) + '...');
                        
                        faceImage.value = jpegData;
                        console.log('Face image input value set. Length:', faceImage.value.length);
                
                        video.classList.add('hidden');
                        canvas.classList.remove('hidden');
                        cameraBtn.innerHTML = '<i class="fas fa-redo mr-2"></i> Retake';
                        cameraBtn.classList.remove('bg-indigo-600', 'hover:bg-indigo-500');
                        cameraBtn.classList.add('bg-amber-600', 'hover:bg-amber-500');
                        isCaptured = true;

                        stream.getTracks().forEach(track => track.stop());
                        stream = null;
                        return;
                    }

                    if (isCaptured) {
                        canvas.classList.add('hidden');
                        facePlaceholder.classList.remove('hidden');
                        cameraBtn.innerHTML = '<i class="fas fa-camera mr-2"></i> Start Camera';
                        cameraBtn.classList.remove('bg-amber-600', 'hover:bg-amber-500');
                        cameraBtn.classList.add('bg-gray-800');
                        isCaptured = false;
                    }   
                } catch (err) {
                    console.error('Error accessing camera:', err);
                    alert('Could not access the camera. Please ensure you have given camera permissions.');
                
                    video.classList.add('hidden');
                    canvas.classList.add('hidden');
                    facePlaceholder.classList.remove('hidden');
                    cameraBtn.innerHTML = '<i class="fas fa-camera mr-2"></i> Start Camera';
                    cameraBtn.classList.remove('bg-indigo-600', 'hover:bg-indigo-500', 'bg-amber-600', 'hover:bg-amber-500');
                    cameraBtn.classList.add('bg-gray-800');

                    if (stream) {
                        stream.getTracks().forEach(track => track.stop());
                        stream = null;
                    }
                    isCaptured = false;
                }
            });

            window.addEventListener('beforeunload', () => {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
            });

    
            const form = document.getElementById('registerForm');
            const modal = document.getElementById('verificationModal');
            const verificationIcon = document.getElementById('verificationIcon');
            const verificationTitle = document.getElementById('verificationTitle');
            const verificationMessage = document.getElementById('verificationMessage');
            const proceedToLogin = document.getElementById('proceedToLogin');
            const closeModal = document.getElementById('closeModal');
            let pollInterval;
            let userId = null;

            // Handle form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const faceImage = document.getElementById('faceImage');
                console.log('Form submitted. Face image value:', {
                    hasValue: !!faceImage.value,
                    startsWithJPEG: faceImage.value.startsWith('data:image/jpeg;base64,'),
                    valueLength: faceImage.value ? faceImage.value.length : 0
                });

                if (!faceImage.value || !faceImage.value.startsWith('data:image/jpeg;base64,')) {
                    console.error('Invalid or missing face image');
                    alert('Please capture your face image before submitting the form.');
                    document.getElementById('face-capture').scrollIntoView({ behavior: 'smooth' });
                    return false;
                }
                // Show loading state
                const submitButton = form.querySelector('button[type="submit"]');
                const originalButtonText = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = 'Processing...';
                
                // Create FormData object
                const formData = new FormData();
                
                // Add all form fields except face_image
                const formElements = form.elements;
                for (let i = 0; i < formElements.length; i++) {
                    const element = formElements[i];
                    if (element.name) {
                        if (element.type === 'checkbox' && element.name === 'services[]') {
                            // Handle checkboxes separately
                            if (element.checked) {
                                formData.append('services[]', element.value);
                            }
                        } else if (element.type === 'file' && element.files.length > 0) {
                            formData.append(element.name, element.files[0]);
                        } else if (element.name !== 'face_image' && element.type !== 'file') {
                            formData.append(element.name, element.value);
                        }
                    }
                }

                // Add face_image as a string
                if (faceImage.value) {
                    formData.append('face_image', faceImage.value);
                }
                
                // Send the request
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(async response => {
                    const data = await response.json();
                    console.log('Server response:', {
                        status: response.status,
                        statusText: response.statusText,
                        data: data
                    });
                    
                    if (!response.ok) {
                        throw new Error(data.message || 'Registration failed');
                    }
                    return data;
                })
                .then(data => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else if (data.user) {
                        // Show verification modal
                        showVerificationModal();
                        // Start polling for verification status
                        startPolling(data.user.id);
                    }
                })
                .catch(error => {
                    console.error('Registration error:', error);
                    if (error.response) {
                        error.response.json().then(errData => {
                            console.error('Error details:', errData);
                        });
                    }
                    alert('Registration failed: ' + (error.message || 'Please check the form and try again.'));
                })
                .finally(() => {
                    // Reset button state
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                });
            });

            // Show verification modal
            function showVerificationModal() {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            // Hide verification modal
            function hideVerificationModal() {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            // Start polling for verification status
            function startPolling(userId) {
                // Initial check
                checkVerificationStatus(userId);
                
                // Set up polling every 5 seconds
                pollInterval = setInterval(() => checkVerificationStatus(userId), 5000);
            }

            // Check verification status
            function checkVerificationStatus(userId) {
                fetch(`/api/check-verification/${userId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'verified') {
                        updateModalForVerified();
                        clearInterval(pollInterval);
                    } else if (data.status === 'rejected') {
                        updateModalForRejected();
                        clearInterval(pollInterval);
                    }
                    // If still pending, do nothing and wait for next poll
                })
                .catch(error => {
                    console.error('Error checking verification status:', error);
                    // Don't stop polling on error, just log it and continue
                });
            }

            // Update modal for verified status
            function updateModalForVerified() {
                verificationIcon.innerHTML = `
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                `;
                verificationIcon.classList.remove('bg-blue-100');
                verificationIcon.classList.add('bg-green-100');
                verificationTitle.textContent = 'Account Verified!';
                verificationMessage.textContent = 'Your account has been successfully verified.';
                proceedToLogin.classList.remove('hidden');
            }

            // Update modal for rejected status
            function updateModalForRejected() {
                verificationIcon.innerHTML = `
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                `;
                verificationIcon.classList.remove('bg-blue-100');
                verificationIcon.classList.add('bg-red-100');
                verificationTitle.textContent = 'Verification Rejected';
                verificationMessage.textContent = 'Sorry, your account verification was not approved. Please contact support for more information.';
                closeModal.classList.remove('hidden');
            }

            // Close modal button handler
            closeModal.addEventListener('click', hideVerificationModal);

            // Age calculation from birthday
            const birthdayInput = document.getElementById('birthday');
            const ageInput = document.getElementById('age');
            const birthdayError = document.getElementById('birthday-error');


            function calculateAge(birthDate) {
                const today = new Date();
                const birthDateObj = new Date(birthDate);
                let age = today.getFullYear() - birthDateObj.getFullYear();
                const monthDiff = today.getMonth() - birthDateObj.getMonth();
                
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDateObj.getDate())) {
                    age--;
                }
                
                return age;
            }

            function validateAge(age) {
                if (age <= 0) {
                    return { valid: false, message: 'Please enter a valid birth date' };
                } else if (age < 18) {
                    return { valid: false, message: 'You must be at least 18 years old to register' };
                }
                return { valid: true };
            }

            function updateAgeValidation() {

                if (!birthdayInput.value) {
            
                    birthdayInput.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
                    ageInput.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
                    birthdayError.classList.add('hidden');
                    return;
                }

                const age = calculateAge(birthdayInput.value);
                const validation = validateAge(age);

                if (!validation.valid) {

                    birthdayInput.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
                    ageInput.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
                
                    birthdayError.textContent = validation.message;
                    birthdayError.classList.remove('hidden');
                
                    document.querySelector('form').addEventListener('submit', function(e) {
                        if (!validateAge(calculateAge(birthdayInput.value)).valid) {
                            e.preventDefault();
                        }
                    }, false);
                } else {
                    birthdayInput.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
                    ageInput.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
                    birthdayError.classList.add('hidden');
                }
            }

            if (birthdayInput) {

                birthdayInput.addEventListener('change', function() {
                    if (this.value) {
                        const age = calculateAge(this.value);
                        ageInput.value = age;
                        updateAgeValidation();
                    }
                });

                if (birthdayInput.value) {
                    const age = calculateAge(birthdayInput.value);
                    ageInput.value = age;
                    updateAgeValidation();
                }
            }

            // File upload button handlers
            function setupFileUpload(inputId, buttonId, fileNameSpanId, previewId, placeholderId) {
                const input = document.getElementById(inputId);
                const button = document.getElementById(buttonId);
                const fileNameSpan = document.getElementById(fileNameSpanId);
                const preview = document.getElementById(previewId);
                const placeholder = document.getElementById(placeholderId);
                
                button.addEventListener('click', () => input.click());
                
                input.addEventListener('change', (e) => {
                    if (input.files.length > 0) {
                        button.textContent = 'Re-upload';
                        fileNameSpan.textContent = input.files[0].name;
                        
                        // Show preview
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.src = e.target.result;
                            preview.classList.remove('hidden');
                            placeholder.classList.add('hidden');
                        };
                        reader.readAsDataURL(input.files[0]);
                    } else {
                        button.textContent = 'Upload File';
                        fileNameSpan.textContent = '';
                        preview.src = '';
                        preview.classList.add('hidden');
                        placeholder.classList.remove('hidden');
                    }
                });
            }
            
            // Set up all file uploads with previews
            setupFileUpload('police_clearance', 'police_clearance_btn', 'police_clearance_file', 'police_clearance_preview', 'police_clearance_placeholder');
            setupFileUpload('id_front', 'id_front_btn', 'id_front_file', 'id_front_preview', 'id_front_placeholder');
            setupFileUpload('id_back', 'id_back_btn', 'id_back_file', 'id_back_preview', 'id_back_placeholder');

            const roleSelect = document.getElementById('role');
            const servicesSection = document.getElementById('servicesSection');
            const servicesContainer = document.getElementById('servicesContainer');

            // Toggle services section based on role selection
            roleSelect.addEventListener('change', function() {
                if (this.value === 'ServiceProvider') {
                    servicesSection.classList.remove('hidden');
                    loadServices();
                } else {
                    servicesSection.classList.add('hidden');
                }
            });

            // Load services via AJAX
            function loadServices() {
                if (servicesContainer.children.length > 0) return; // Already loaded

                const maxServices = 3;
                let selectedCount = 0;
                const servicesCounter = document.getElementById('servicesCounter');
                const servicesError = document.getElementById('servicesError');

                fetch('{{ route("services.list") }}')
                    .then(response => response.json())
                    .then(services => {
                        servicesContainer.innerHTML = services.map(service => `
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="service_${service.service_id}" 
                                       name="services[]" 
                                       value="${service.service_id}"
                                       class="service-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="service_${service.service_id}" class="ml-2 block text-sm text-gray-700">
                                    ${service.service_name}
                                </label>
                            </div>
                        `).join('');

                        // Add event listeners to checkboxes
                        const checkboxes = document.querySelectorAll('.service-checkbox');
                        
                        checkboxes.forEach(checkbox => {
                            // Handle existing selections (if any)
                            if (checkbox.checked) {
                                selectedCount++;
                                updateCounter();
                            }

                            // Add change event listener
                            checkbox.addEventListener('change', function() {
                                if (this.checked) {
                                    if (selectedCount < maxServices) {
                                        selectedCount++;
                                        servicesError.classList.add('hidden');
                                    } else {
                                        this.checked = false;
                                        servicesError.classList.remove('hidden');
                                        return;
                                    }
                                } else {
                                    selectedCount--;
                                    servicesError.classList.add('hidden');
                                }
                                
                                updateCounter();
                                updateCheckboxStates();
                            });
                        });

                        function updateCounter() {
                            servicesCounter.textContent = `${selectedCount}/${maxServices} services selected`;
                        }

                        function updateCheckboxStates() {
                            const checkboxes = document.querySelectorAll('.service-checkbox:not(:checked)');
                            checkboxes.forEach(checkbox => {
                                checkbox.disabled = selectedCount >= maxServices;
                            });
                        }

                        // Initial update
                        updateCounter();
                        updateCheckboxStates();
                    })
                    .catch(error => console.error('Error loading services:', error));
            }
        });
    </script>
    <script>
        function capitalizeWords(input) {
            input.value = input.value
                .toLowerCase()
                .split(' ')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
        }
    </script>

    <script>
    function calculateAge(birthday) {
        const birthDate = new Date(birthday);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        return age;
    }

    function updateAge(birthday) {
        const ageInput = document.getElementById('age');
        const birthdayInput = document.getElementById('birthday');
        const errorElement = document.getElementById('birthday-error');
        
        if (!birthday) {
            // Reset if no date is selected
            ageInput.value = '';
            birthdayInput.classList.remove('border-red-500');
            errorElement.classList.add('hidden');
            return;
        }

        const age = calculateAge(birthday);
        ageInput.value = age;

        if (age <= 0) {
            showError('Please enter a valid birth date');
        } else if (age < 18) {
            showError('You must be at least 18 years old');
        } else {
            hideError();
        }

        function showError(message) {
            birthdayInput.classList.add('border-red-500');
            ageInput.classList.add('border-red-500');
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
            
            // Prevent form submission
            document.querySelector('form').addEventListener('submit', function(e) {
                if (calculateAge(birthdayInput.value) <= 0 || calculateAge(birthdayInput.value) < 18) {
                    e.preventDefault();
                }
            }, false);
        }

        function hideError() {
            birthdayInput.classList.remove('border-red-500');
            ageInput.classList.remove('border-red-500');
            errorElement.classList.add('hidden');
        }
    }

    // Initialize age on page load if birthday is already set
    document.addEventListener('DOMContentLoaded', function() {
        const birthdayInput = document.getElementById('birthday');
        if (birthdayInput.value) {
            updateAge(birthdayInput.value);
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordToggle = document.getElementById('passwordToggle');
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
