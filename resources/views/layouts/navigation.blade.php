<nav x-data="{ open: false, unreadNotifications: {{ auth()->user()->unreadNotifications()->count() }} }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 fixed w-full z-50 top-0 left-0">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            
            <div class="flex gap-5">
                <div class="flex items-center">
                    <img src="{{ asset('storage/logo/logo.png') }}" alt="Logo" style="width: 40px;">
                    <h1 class="text-2xl font-extrabold text-gray-800 font-['Rajdhani'] tracking-wide">INeedHand</h1>
                </div>

                <div class="hidden gap-3 sm:flex">
                    @if(auth()->user()->role === 'Admin')
                        <!-- Admin Navigation -->
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" style="font-size: 15px;">
                            <i class="fas fa-home text-gray-400 group-hover:text-gray-500 mr-2" style="font-size: 1.25rem"></i>
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        <x-nav-link :href="route('page.newsfeed')" :active="request()->routeIs('page.newsfeed')" style="font-size: 15px;">
                            <i class="fas fa-newspaper text-gray-400 group-hover:text-gray-500 mr-2" style="font-size: 1.25rem"></i>
                            {{ __('NewsFeed') }}
                        </x-nav-link>
                    
                        <x-nav-link :href="route('admin.homeowners.index')" :active="request()->routeIs('admin.homeowners.*')" style="font-size: 15px;">
                            <i class="fas fa-users text-gray-400 group-hover:text-gray-500 mr-2" style="font-size: 1.25rem"></i>
                            {{ __('Homeowners') }}
                        </x-nav-link>

                        <x-nav-link :href="route('admin.serviceproviders.index')" :active="request()->routeIs('admin.serviceproviders.*')" style="font-size: 15px;">
                            <i class="fas fa-tools text-gray-400 group-hover:text-gray-500 mr-2" style="font-size: 1.25rem"></i>
                            {{ __('Service Providers') }}
                        </x-nav-link>

                        <x-nav-link :href="route('admin.users.index')" :active="request()->is('admin/users') && !request()->has('role')" style="font-size: 15px;">
                            <i class="fas fa-users-cog text-gray-400 group-hover:text-gray-500 mr-2" style="font-size: 1.25rem"></i>
                            {{ __('All Users') }}
                        </x-nav-link>
                    
                    @elseif(auth()->user()->role === 'ServiceProvider')
                        <!-- Service Provider Navigation -->
                        <x-nav-link :href="route('service-provider.dashboard')" :active="request()->routeIs('service-provider.dashboard')" style="font-size: 15px;">
                            <i class="fas fa-home text-gray-400 group-hover:text-gray-500 mr-2" style="font-size: 1.25rem"></i>
                            {{ __('Home') }}
                        </x-nav-link>
                        
                        <x-nav-link :href="route('page.newsfeed')" :active="request()->routeIs('page.newsfeed')" style="font-size: 15px;">
                            <i class="fas fa-newspaper text-gray-400 group-hover:text-gray-500 mr-2" style="font-size: 1.25rem"></i>
                            {{ __('Job Posts') }}
                        </x-nav-link>
                         
                        <x-nav-link :href="route('jobs.index')" :active="request()->routeIs('service-provider.my-jobs')" style="font-size: 15px;">
                            <i class="fas fa-tasks text-gray-400 group-hover:text-gray-500 mr-2" style="font-size: 1.25rem"></i>
                            {{ __('My Jobs') }}
                        </x-nav-link>
                    
                    @elseif(auth()->user()->role === 'Homeowner')
                        <!-- Homeowner Navigation -->
                        <x-nav-link :href="route('page.newsfeed')" :active="request()->routeIs('page.newsfeed')" style="font-size: 15px;">
                            <i class="fas fa-home text-gray-400 group-hover:text-gray-500 mr-2" style="font-size: 1.25rem"></i>
                            {{ __('Home') }}
                        </x-nav-link>
                    
                        <x-nav-link :href="route('service-providers.index')" :active="request()->routeIs('service-providers.*')" style="font-size: 15px;">
                            <i class="fas fa-users text-gray-400 group-hover:text-gray-500 mr-2" style="font-size: 1.25rem"></i>
                            {{ __('Service Providers') }}
                        </x-nav-link>
                        
                        <x-nav-link :href="route('jobs.index')" :active="request()->routeIs('jobs.*')" style="font-size: 15px;">
                            <i class="fas fa-briefcase text-gray-400 group-hover:text-gray-500 mr-2" style="font-size: 1.25rem"></i>
                            {{ __('Jobs') }}
                        </x-nav-link>
                    @endif
                    <x-nav-link :href="route('messages.index')" :active="request()->routeIs('messages.*')" style="font-size: 15px;">
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-gray-400 group-hover:text-gray-500 mr-2" style="font-size: 1.25rem"></i>
                                @php
                                    $unreadCount = auth()->user()->conversations()
                                        ->withCount(['messages as unread_count' => function($query) {
                                            $query->where('receiver_id', auth()->id())
                                                  ->whereNull('read_at');
                                        }])
                                        ->get()
                                        ->sum('unread_count');
                                @endphp
                                <span id="unread-messages-count" class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1 -translate-y-1 bg-red-500 rounded-full {{ $unreadCount > 0 ? '' : 'hidden' }}" style="margin-left: -25px; margin-right: 5px; min-width: 20px; transition: opacity 0.2s ease-in-out;">
                                    {{ $unreadCount }}
                                </span>
                                <span>{{ __('Messages') }}</span>
                            </div>
                        </x-nav-link>       
                        <x-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')" style="font-size: 15px;">
                            <div class="flex items-center">
                                <i class="fas fa-bell text-gray-400 group-hover:text-gray-500 mr-2" style="font-size: 1.25rem"></i>
                                {{ __('Notifications') }}
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1 -translate-y-1 bg-red-500 rounded-full" style="margin-left: -110px; margin-right: 5px; min-width: 20px; transition: opacity 0.2s ease-in-out;">
                                        {{ auth()->user()->unreadNotifications->count() }}
                                    </span>
                                @endif
                            </div>
                        </x-nav-link>

                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <div class="hidden sm:flex sm:items-center sm:ml-6 relative z-50">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:ring-0 focus:ring-offset-0 transition ease-in-out duration-150">
                                <div class="flex-shrink-0 w-8 h-8">
                                    <img 
                                        src="{{ Auth::user()->face_img ? asset('storage/' . Auth::user()->face_img) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}" 
                                        alt="{{ Auth::user()->name }}" 
                                        class="w-full h-full rounded-full object-cover"
                                    />
                                </div>
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            @if(auth()->user()->role !== 'Admin')
                            <x-dropdown-link :href="route('profile.show', ['user' => Auth::id()])" class="flex items-center">
                                <i class="fas fa-user-edit mr-2"></i>
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            @endif

                            @if(auth()->user()->role == 'Admin')
                            <x-dropdown-link :href="route('admin.reports.index')" class="flex items-center">
                                <i class="fas fa-user-edit mr-2"></i>
                                {{ __('Reports') }}
                            </x-dropdown-link>
                            @endif

                            <x-dropdown-link :href="route('profile.settings')" class="flex items-center">
                                <i class="fas fa-cog mr-2"></i>
                                {{ __('Settings') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();"
                                        class="flex items-center w-full">
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Hamburger -->
                <div class="-mr-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.show', ['user' => Auth::id()])">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
    // Track the current unread count to avoid unnecessary updates
    let currentUnreadCount = {{ $unreadCount }};
    let updateTimeout;

    // Update unread message count with debounce
    function updateUnreadMessageCount(force = false) {
        // Clear any pending updates
        if (updateTimeout) {
            clearTimeout(updateTimeout);
        }

        // Schedule the update with a small delay to prevent rapid updates
        updateTimeout = setTimeout(() => {
            fetch('{{ route("messages.ajax.unread-count") }}?t=' + new Date().getTime())
                .then(response => response.json())
                .then(data => {
                    const newCount = parseInt(data.count || 0);
                    const badge = document.getElementById('unread-messages-count');
                    
                    // Only update if the count has changed
                    if (newCount !== currentUnreadCount || force) {
                        currentUnreadCount = newCount;
                        
                        if (badge) {
                            if (newCount > 0) {
                                badge.textContent = newCount;
                                badge.classList.remove('hidden');
                            } else {
                                badge.classList.add('hidden');
                            }
                            
                            // Trigger a reflow to ensure the transition works
                            void badge.offsetWidth;
                        }
                    }
                })
                .catch(error => console.error('Error updating unread count:', error));
        }, 100); // Small delay to batch rapid updates
    }
    
    // Initialize when the DOM is fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Initial update (with force to ensure it runs at least once)
        updateUnreadMessageCount(true);
        
        // Set up periodic updates (every 30 seconds)
        setInterval(() => updateUnreadMessageCount(), 30000);
        
        // Listen for new message events
        if (window.Echo) {
            window.Echo.private(`user.${window.Laravel.userId}`)
                .listen('MessageSent', (e) => {
                    if (e.message.receiver_id === window.Laravel.userId) {
                        updateUnreadMessageCount();
                    }
                });
        }
    });
</script>
