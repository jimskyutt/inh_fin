@extends('layouts.app')

@section('title', 'View User | INeedHand')
@php
    $containerWidth = 'w-[800px]';
    $containerHeight = 'h-[screen]';
    $marginTop = 'mt-[80px]';
@endphp
<style>
    .container {
        width: 800px;
        border-radius: 0.5rem;
        padding: 2rem;
    }
</style>
@section('content')

<div class="container">
    <a href="{{ route('admin.dashboard') }}" class="flex items-center text-gray-600 hover:text-indigo-600 transition-colors duration-200">
        <i class="fas fa-arrow-left text-lg mr-1"></i>
        <span class="text-sm font-medium">Back</span>
    </a>
    <div class="flex flex-col items-center mb-6">
        <img src="{{ asset('storage/logo/logo.png') }}" alt="Logo" class="h-24 w-auto">
        <h1 class="text-4xl font-extrabold text-gray-800 font-['Rajdhani'] tracking-wide">INeedHand</h1>
    </div>

    <h1 class="text-center mt-5 text-3xl font-extrabold text-gray-800 font-['Rajdhani'] tracking-wide">User Registration Details</h1>
    
    <h1 class="mt-5 text-2xl font-extrabold text-gray-800 font-['Rajdhani'] tracking-wide">Personal Information</h1>
    <hr>

    <div class="relative mt-4">
        <div class="relative">
            <input type="text" id="name" name="name" class="block w-full px-4 py-3 pl-14 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" placeholder=" " value="{{ $user->name}}" readonly>
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <i class="fas fa-user"></i>
                </div>
                <label for="name" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2"> Full Name </label>
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-5 mt-4">
            
            <div class="relative flex-1">
                <div class="relative">
                    <input type="text" id="birthday" name="birthday" class="block w-full pl-12 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" value="{{ \Carbon\Carbon::parse($user->birthday)->format('d/m/y') }}" readonly>
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <label for="birthday" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2"> Birthday </label>
                </div>
            </div>

            <div class="relative age" style="width: 120px;">
                <div class="relative"> 
                    <input type="number" id="age" name="age" class="text-center block w-full pl-12 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" placeholder=" " value="{{ $user->age }}" readonly>
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fas fa-birthday-cake"></i>
                    </div>
                    <label for="age" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2"> Age </label>
                </div>
            </div>

            <div class="relative flex-1">
                <div class="relative">
                    <input type="text" id="sex" name="sex" class="block w-full pl-12 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" value="{{ ucfirst($user->sex) }}" readonly>   
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fas fa-venus-mars"></i>
                    </div>
                    <label for="sex" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2"> Sex </label>
                </div>
            </div>

            <div class="relative flex-1">
                <div class="relative">
                    <input type="text" id="civil_status" name="civil_status" class="block w-full pl-12 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" value="{{ ucfirst($user->civil_status) }}" readonly>
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fas fa-heart"></i>
                    </div>
                    <label for="civil_status" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2"> Civil Status </label>
                </div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-4 mt-4">

            <div class="relative group flex-1">
                <div class="relative">
                    <input type="email" id="email" name="email" class="block w-full pl-12 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" placeholder=" " value="{{ $user->email }}" readonly>
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 group-hover:text-indigo-600 transition-colors duration-200">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <label for="email" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2">
                        Email Address
                    </label>
                </div>
            </div>

            <div class="relative flex-1">
                <div class="relative">
                    <input type="tel" id="contact_number" name="contact_number" class="block w-full pl-12 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" placeholder=" " value="{{ $user->contact_number }}" readonly >
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
                    <input type="text" id="address" name="address" class="block w-full pl-12 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" value="{{ ucfirst($user->address) }}" readonly>    
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <label for="address" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2"> Barangay </label>
                </div>
            </div>

    
            <div class="relative group flex-1">
                <div class="relative">
                    <input type="text" id="street" name="street" class="block w-full pl-12 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" placeholder=" " value="{{ $user->street }}" readonly />
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


    <div class="flex flex-col md:flex-row gap-4 mt-1">

        <div class="flex-1">
            <div class="relative">  
                <label for="face_img" class="text-gray-500">Face Image</label>
                <div class="flex justify-center">
                    <img id="face_img" src="{{ asset('storage/' . $user->face_img) }}" alt="Face Image Preview" class="border rounded-md" style="width: 350px; height: 210px; object-fit: cover;">
                    <div id="face_img_placeholder" class="hidden"></div>
                </div>
            </div>
        </div>

        <div class="flex-1">
            <div class="relative">
                <label for="police_clearance" class="text-gray-500">Police Clearance</label>
                <div class="flex justify-center">
                    <img id="police_clearance_preview" src="{{ asset('storage/' . $user->police_clearance) }}" alt="Police Clearance Preview" class="border rounded-md" style="width: 350px; height: 210px; object-fit: cover;">
                    <div id="police_clearance_placeholder" class="hidden"></div>
                </div>
            </div>
        </div>
        
    </div>



    <div class="flex flex-col md:flex-row gap-1 mt-4">
        <div class="flex-1">
            <div class="relative">
                <label for="id_front" class="text-gray-500">ID Card (Front)</label>
                <div class="flex justify-center">
                    <img id="id_front_preview" src="{{ asset('storage/' . $user->id_front) }}" alt="ID Front Preview" class="border rounded-md" style="width: 350px; height: 210px; object-fit: cover;">
                    <div id="id_front_placeholder" class="hidden"></div>
                </div>
            </div>
        </div>

        <div class="flex-1">
            <div class="relative">
                <label for="id_back" class="text-gray-500">ID Card (Back)</label>
                <div class="flex justify-center">
                    <img id="id_back_preview" src="{{ asset('storage/' . $user->id_back) }}" alt="ID Back Preview" class="border rounded-md" style="width: 350px; height: 210px; object-fit: cover;">
                    <div id="id_back_placeholder" class="hidden"></div>
                </div>  
            </div>
        </div>

    </div>



    <h1 class="mt-5 text-2xl font-extrabold text-gray-800 font-['Rajdhani'] tracking-wide">Account Credentials</h1>
    <hr>
    
    <div class="flex flex-col md:flex-row gap-4 mt-5">            
        <div class="flex-1">
            <div class="relative">
                <input type="role" id="role" name="role" class="block w-full pl-12 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" placeholder=" " value="{{ $user->role }}" readonly>
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <i class="fas fa-user-tag"></i>
                </div>
                <label for="role" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2"> User Type </label>
            </div>
        </div>

        <div class="flex-1">
            <div class="relative">
                <input id="username" name="username" type="text" class="block w-full px-4 py-3 pl-14 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 transition duration-200 ease-in-out peer" value="{{ $user->username }}" readonly/>
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-user text-gray-400"></i>
                </div>
                <label for="username" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2">Username</label>
            </div>
        </div>
    </div>

    @if($user->role === 'ServiceProvider')
        <div class="mt-8">
            <h1 class="text-2xl font-extrabold text-gray-800 font-['Rajdhani'] tracking-wide">Services Offered</h1>
            <hr class="my-4">

            @if($user->services->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                    @foreach($user->services as $service)
                        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                                    <i class="fas fa-tools"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $service->service_name }}</h3>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                This service provider hasn't added any services yet.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <div class="mt-8 flex justify-end space-x-3">
        @if($user->status !== 'verified')
            <form action="{{ route('admin.users.verify', $user) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    <i class="fas fa-check"></i> Verify User
                </button>
            </form>
        @endif
                    
        @if($user->status !== 'rejected')
            <form action="{{ route('admin.users.reject', $user) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    <i class="fas fa-times"></i> Reject User
                </button>
            </form>
        @endif
    </div>

</div>

@endsection
