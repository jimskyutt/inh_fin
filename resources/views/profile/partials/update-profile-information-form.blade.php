<section>

    @php
        $isAdminEditing = auth()->user()->role === 'Admin' && $user->id !== auth()->id();
        $route = $isAdminEditing ? route('profile.update.user', $user) : route('profile.update');
    @endphp
    
    <form method="post" action="{{ $route }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('patch')
        
        @if($isAdminEditing)
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h2a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            You are editing this profile as an administrator.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <h2 class="font-bold text-gray-800 font-['Rajdhani']">Personal Information</h2>
        <hr>

        <div class="relative">
                <div class="relative">
                    <input type="text" id="name" name="name" 
                        class="block w-full px-4 py-3 pl-14 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" 
                        placeholder=" " 
                        value="{{ old('name', $user->name) }}" 
                        required 
                        autofocus 
                        autocomplete="name"
                        onkeyup="this.value = this.value.replace(/[^a-zA-Z\s]/g, ''); this.value = this.value.replace(/\s+/g, ' ').trim(); this.value = this.value.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(' ');"
                    >
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fas fa-user"></i>
                    </div>
                    <label for="name" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2"> 
                        Full Name 
                    </label>
                </div>
                <x-input-error class="mt-1" :messages="$errors->get('name')" />
            </div>

        <div class="flex flex-col md:flex-row gap-5 mt-4">
            
            <div class="relative flex-1">
                <div class="relative">
                    <input type="date" id="birthday" name="birthday" class="block w-full pl-12 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" 
                        placeholder=" " 
                        value="{{ old('birthday', $user->birthday ? \Carbon\Carbon::parse($user->birthday)->format('Y-m-d') : '') }}" 
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

            <div class="relative flex-1">
                <div class="relative">
                    <input type="number" id="age" name="age" 
                        class="block w-full px-4 py-3 pl-14 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" 
                        placeholder=" " 
                        value="{{ old('age', $user->age) }}" 
                        min="18" 
                        max="120" 
                        required
                        readonly
                    >
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fas fa-birthday-cake"></i>
                    </div>
                    <label for="age" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2">
                        Age
                    </label>
                </div>
                <x-input-error class="mt-1" :messages="$errors->get('age')" />
            </div>
            
            

            <div class="relative flex-1">
                <div class="relative">
                    <select id="sex" name="sex" 
                        class="block w-full pl-14 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 transition duration-200 ease-in-out peer" 
                        required
                    >
                        <option value="male" {{ old('sex', $user->sex) == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('sex', $user->sex) == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('sex', $user->sex) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fas fa-venus-mars"></i>
                    </div>
                    <label for="sex" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2">
                        Sex
                    </label>
                </div>
                <x-input-error class="mt-1" :messages="$errors->get('sex')" />
            </div>

            <div class="relative flex-1">
                <div class="relative">
                    <select id="civil_status" name="civil_status" 
                        class="block w-full pl-14 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 transition duration-200 ease-in-out peer" 
                        required
                    >
                        <option value="single" {{ old('civil_status', $user->civil_status) == 'single' ? 'selected' : '' }}>Single</option>
                        <option value="married" {{ old('civil_status', $user->civil_status) == 'married' ? 'selected' : '' }}>Married</option>
                        <option value="divorced" {{ old('civil_status', $user->civil_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                        <option value="widowed" {{old('civil_status', $user->civil_status) == 'widowed' ? 'selected' : ''}}>Widowed</option>
                    </select>
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fas fa-venus-mars"></i>
                    </div>
                    <label for="civil_status" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2">
                        Civil Status
                    </label>
                </div>
                <x-input-error class="mt-1" :messages="$errors->get('civil_status')" />
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-5 mt-4">
            <div class="relative flex-1">
                <div class="relative">
                    <input type="email" id="email" name="email" 
                        class="block w-full px-4 py-3 pl-14 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" 
                        placeholder=" " 
                        value="{{ old('email', $user->email) }}" 
                        required 
                        autocomplete="email"
                    >
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <label for="email" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2">
                        Email Address
                    </label>
                </div>
                <x-input-error class="mt-1" :messages="$errors->get('email')" />
                
                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2">
                        <p class="text-sm text-yellow-600">
                            {{ __('Your email address is unverified.') }}
                            <button form="send-verification" class="text-indigo-600 hover:text-indigo-500 underline">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 text-sm text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Contact Number -->
            <div class="relative flex-1">
                <div class="relative">
                    <input type="tel" id="contact_number" name="contact_number" 
                        class="block w-full px-4 py-3 pl-14 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" 
                        placeholder=" " 
                        value="{{ old('contact_number', $user->contact_number) }}" 
                        required 
                        autocomplete="tel"
                        oninput="this.value = this.value.replace(/[^0-9+]/g, '').replace(/(\..*?)\./g, '$1');"
                    >
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fas fa-phone"></i>
                    </div>
                    <label for="contact_number" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2">
                        Contact Number
                    </label>
                </div>
                <x-input-error class="mt-1" :messages="$errors->get('contact_number')" />
            </div>

        </div>

        <div class="flex flex-col md:flex-row gap-5 mt-4">

            <div class="relative group flex-1">
                <div class="relative">
                    <select id="address" name="address" class="block w-full pl-12 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" required >
                        <option value="" disabled {{ !$user->address ? 'selected' : '' }}></option>
                        @foreach($barangays as $barangay)
                        <option value="{{ $barangay->brgy_name }}" {{ old('address', $user->address) == $barangay->brgy_name ? 'selected' : '' }}>
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

            <div class="relative flex-1">
                <div class="relative">
                    <input type="text" id="street" name="street" class="block w-full pl-12 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition duration-200 ease-in-out peer" placeholder=" " value="{{ old('street', $user->street) }}" required />
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fas fa-road"></i>
                    </div>
                    <label for="street" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-500 transition-all duration-200 ease-in-out pointer-events-none peer-focus:top-0 peer-focus:left-3 peer-focus:px-1 peer-focus:bg-white peer-focus:text-xs peer-focus:text-indigo-600 peer-focus:-translate-y-1/2 peer-[:not(:placeholder-shown)]:top-0 peer-[:not(:placeholder-shown)]:left-3 peer-[:not(:placeholder-shown)]:px-1 peer-[:not(:placeholder-shown)]:bg-white peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:-translate-y-1/2"> Street </label>
                </div>
                <x-input-error :messages="$errors->get('street')" class="mt-1" />
            </div>

        </div>

        <h2 class="text-2xl font-bold text-gray-800 font-['Rajdhani'] tracking-wide">Profile Photo</h2>
        <hr class="my-2">

        <div class="flex flex-col md:flex-row gap-4 mt-4">
            <div class="flex-1">
                <x-input-label for="profile_photo" :value="__('Profile Picture')" />
                <div class="mt-1 flex justify-center">
                    <div class="relative">
                        @if($user->face_img)
                            <img id="profile_photo_preview" src="{{ asset('storage/' . $user->face_img) }}" alt="Profile Photo Preview" class="border rounded-md" style="width: 320px; height: 240px; object-fit: contain;">
                            <div id="profile_photo_placeholder" class="bg-gray-100 hidden justify-center items-center border rounded-md" style="width: 320px; height: 240px;">
                        @else
                            <img id="profile_photo_preview" src="" alt="Profile Photo Preview" class="border rounded-md hidden" style="width: 320px; height: 240px; object-fit: contain;">
                            <div id="profile_photo_placeholder" class="bg-gray-100 flex justify-center items-center border rounded-md" style="width: 320px; height: 240px;">
                        @endif
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
                    <button type="button" id="profile_photo_btn" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-upload mr-2"></i> Upload File
                    </button>
                    <span id="profile_photo_file" class="hidden"></span>
                    <input type="file" id="profile_photo" name="profile_photo" accept="image/*" class="hidden" {{ !$user->face_img ? 'required' : '' }}>
                </div>
                <x-input-error :messages="$errors->get('profile_photo')" class="mt-2" />
            </div>

            <div class="flex-1">
                <x-input-label for="police_clearance" :value="__('Police Clearance')" />
                <div class="mt-1 flex justify-center">
                    <div class="relative">
                        @if($user->police_clearance)
                            <img id="police_clearance_preview" src="{{ asset('storage/' . $user->police_clearance) }}" alt="Police Clearance Preview" class="border rounded-md" style="width: 320px; height: 240px; object-fit: contain;">
                            <div id="police_clearance_placeholder" class="bg-gray-100 hidden justify-center items-center border rounded-md" style="width: 320px; height: 240px;">
                        @else
                            <img id="police_clearance_preview" src="" alt="Police Clearance Preview" class="border rounded-md hidden" style="width: 320px; height: 240px; object-fit: contain;">
                            <div id="police_clearance_placeholder" class="bg-gray-100 flex justify-center items-center border rounded-md" style="width: 320px; height: 240px;">
                        @endif
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
                    <input type="file" id="police_clearance" name="police_clearance" accept="image/*" class="hidden" {{ !$user->police_clearance ? 'required' : '' }}>
                </div>
                <x-input-error :messages="$errors->get('police_clearance')" class="mt-2" />
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-4 mt-4">
            <div class="flex-1">
                <x-input-label for="id_front" :value="__('ID Card (Front)')" />
                <div class="mt-1 flex justify-center">
                    <div class="relative">
                        @if($user->id_front)
                            <img id="id_front_preview" src="{{ asset('storage/' . $user->id_front) }}" alt="ID Front Preview" class="border rounded-md" style="width: 320px; height: 240px; object-fit: contain;">
                            <div id="id_front_placeholder" class="bg-gray-100 hidden justify-center items-center border rounded-md" style="width: 320px; height: 240px;">
                        @else
                            <img id="id_front_preview" src="" alt="ID Front Preview" class="border rounded-md hidden" style="width: 320px; height: 240px; object-fit: contain;">
                            <div id="id_front_placeholder" class="bg-gray-100 flex justify-center items-center border rounded-md" style="width: 320px; height: 240px;">
                        @endif
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
                    <button type="button" id="id_front_btn" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-upload mr-2"></i> Upload File
                    </button>
                    <span id="id_front_file" class="hidden"></span>
                    <input type="file" id="id_front" name="id_front" accept="image/*" class="hidden" {{ !$user->id_front ? 'required' : '' }}>
                </div>
                <x-input-error :messages="$errors->get('id_front')" class="mt-2" />
            </div>

            <div class="flex-1">
                <x-input-label for="id_back" :value="__('ID Card (Back)')" />
                <div class="mt-1 flex justify-center">
                    <div class="relative">
                        @if($user->id_back)
                            <img id="id_back_preview" src="{{ asset('storage/' . $user->id_back) }}" alt="ID Back Preview" class="border rounded-md" style="width: 320px; height: 240px; object-fit: contain;">
                            <div id="id_back_placeholder" class="bg-gray-100 hidden justify-center items-center border rounded-md" style="width: 320px; height: 240px;">
                        @else
                            <img id="id_back_preview" src="" alt="ID Back Preview" class="border rounded-md hidden" style="width: 320px; height: 240px; object-fit: contain;">
                            <div id="id_back_placeholder" class="bg-gray-100 flex justify-center items-center border rounded-md" style="width: 320px; height: 240px;">
                        @endif
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
                    <button type="button" id="id_back_btn" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-upload mr-2"></i> Upload File
                    </button>
                    <span id="id_back_file" class="hidden"></span>
                    <input type="file" id="id_back" name="id_back" accept="image/*" class="hidden" {{ !$user->id_back ? 'required' : '' }}>
                </div>
                <x-input-error :messages="$errors->get('id_back')" class="mt-2" />
            </div>
        </div>

            


        <!-- Save Button -->
        <div class="flex justify-center mt-8">
            <button type="submit" 
                class="px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
            >
                Save Changes
            </button>
        </div>
    </form>

    <!-- Verification Email Form -->
    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
        <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="hidden">
            @csrf
        </form>
    @endif


    <script>
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
            setupFileUpload('profile_photo', 'profile_photo_btn', 'profile_photo_file', 'profile_photo_preview', 'profile_photo_placeholder');
            setupFileUpload('police_clearance', 'police_clearance_btn', 'police_clearance_file', 'police_clearance_preview', 'police_clearance_placeholder');
            setupFileUpload('id_front', 'id_front_btn', 'id_front_file', 'id_front_preview', 'id_front_placeholder');
            setupFileUpload('id_back', 'id_back_btn', 'id_back_file', 'id_back_preview', 'id_back_placeholder');

    </script>
</section>
