@extends('layouts.app')

@section('title', 'Users Management | INeedHand Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col space-y-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Users Management</h1>
        
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <!-- Search Input -->
            <div class="relative w-full md:w-96">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" 
                       id="searchInput" 
                       placeholder="Search by name, username, or email..." 
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       value="{{ request('search') }}">
            </div>
            
            <!-- Role Filter -->
            <div class="flex space-x-2">
                <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => ''])) }}" 
                   class="px-4 py-2 rounded-md {{ !request('role') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    All Users
                </a>
                <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => 'Homeowner'])) }}" 
                   class="px-4 py-2 rounded-md {{ request('role') === 'Homeowner' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    Homeowners
                </a>
                <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => 'ServiceProvider'])) }}" 
                   class="px-4 py-2 rounded-md {{ request('role') === 'ServiceProvider' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    Service Providers
                </a>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Password</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="usersTableBody">
                    @include('admin.users.partials.user_rows', ['users' => $users])
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4">
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Update User Status
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Are you sure you want to update this user's status?
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                <form id="statusForm" method="POST" class="inline-flex w-full justify-center">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                        Update Status
                    </button>
                </form>
                <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    console.log('Search script loaded');
    
    document.addEventListener('DOMContentLoaded', function() {
        // Username inline editing
        document.addEventListener('dblclick', function(e) {
            const target = e.target.closest('.editable-field');
            if (!target) return;
            
            e.preventDefault();
            const currentValue = target.textContent.trim();
            const field = target.dataset.field;
            const userId = target.dataset.userId;
            
            // Create input field
            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'w-full px-2 py-1 text-sm border rounded';
            input.value = currentValue === 'N/A' ? '' : currentValue;
            
            // Replace content with input field
            target.innerHTML = '';
            target.appendChild(input);
            input.focus();
            
            // Handle Enter key to save
            const handleKeyDown = (e) => {
                if (e.key === 'Enter') {
                    saveField(userId, field, input.value.trim());
                } else if (e.key === 'Escape') {
                    target.textContent = currentValue;
                }
            };
            
            // Handle click outside
            const handleClickOutside = (e) => {
                if (!target.contains(e.target)) {
                    target.textContent = currentValue;
                    document.removeEventListener('click', handleClickOutside);
                }
            };
            
            input.addEventListener('keydown', handleKeyDown);
            setTimeout(() => {
                document.addEventListener('click', handleClickOutside);
            }, 0);
        });
        
        // Password editing
        document.addEventListener('click', function(e) {
            // Show password edit field
            if (e.target.classList.contains('show-password')) {
                const userId = e.target.dataset.userId;
                const row = e.target.closest('tr');
                
                // Hide show button, show edit controls
                e.target.classList.add('hidden');
                row.querySelector('.password-display').classList.add('hidden');
                row.querySelector('.password-edit').classList.remove('hidden');
                row.querySelector('.save-password').classList.remove('hidden');
                row.querySelector('.cancel-edit').classList.remove('hidden');
                
                // Focus the password field
                row.querySelector('.password-edit').focus();
            }
            
            // Save password
            if (e.target.classList.contains('save-password')) {
                const userId = e.target.dataset.userId;
                const row = e.target.closest('tr');
                const passwordInput = row.querySelector('.password-edit');
                const newPassword = passwordInput.value.trim();
                
                if (newPassword) {
                    saveField(userId, 'password', newPassword);
                }
                
                // Reset UI
                resetPasswordUI(row);
            }
            
            // Cancel password edit
            if (e.target.classList.contains('cancel-edit')) {
                const row = e.target.closest('tr');
                resetPasswordUI(row);
            }
        });
        
        // Handle Enter key in password field
        document.addEventListener('keydown', function(e) {
            if (e.target.classList.contains('password-edit') && e.key === 'Enter') {
                const row = e.target.closest('tr');
                const userId = e.target.dataset.userId;
                const newPassword = e.target.value.trim();
                
                if (newPassword) {
                    saveField(userId, 'password', newPassword);
                }
                
                resetPasswordUI(row);
            }
        });
    });
    
    // Reset password UI to default state
    function resetPasswordUI(row) {
        const container = row.querySelector('td:last-child');
        container.querySelector('.password-edit').classList.add('hidden');
        container.querySelector('.password-edit').value = '';
        container.querySelector('.password-display').classList.remove('hidden');
        container.querySelector('.show-password').classList.remove('hidden');
        container.querySelector('.save-password').classList.add('hidden');
        container.querySelector('.cancel-edit').classList.add('hidden');
    }
    
    // Save field via AJAX
    function saveField(userId, field, value) {
        // Get CSRF token from meta tag
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Prepare form data
        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('field', field);
        formData.append(field, value);
        
        console.log('Sending request with data:', {
            field: field,
            value: value,
            _method: 'PUT'
        });
        
        // Use the correct admin URL
        fetch(`/admin/users/${userId}`, {
            method: 'POST', // Using POST with _method=PUT for better compatibility
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async response => {
            const data = await response.json();
            
            if (!response.ok) {
                // Handle HTTP error status codes
                let errorMessage = 'An error occurred';
                if (data.errors) {
                    // Handle validation errors
                    errorMessage = Object.values(data.errors).flat().join(' ');
                } else if (data.message) {
                    errorMessage = data.message;
                }
                throw new Error(errorMessage);
            }
            
            return data;
        })
        .then(data => {
            // Update UI on success
            const row = document.querySelector(`tr[data-user-id="${userId}"]`);
            if (field === 'username') {
                const fieldElement = row.querySelector(`[data-field="${field}"]`);
                fieldElement.textContent = value || 'N/A';
            }
            // Show success message
            showToast('User updated successfully', 'success');
        })
        .catch(error => {
            console.error('Error:', error);
            showToast(error.message || 'An error occurred', 'error');
        });
    }
    
    // Show toast notification
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-md text-white ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        // Remove toast after 3 seconds
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // Live search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        let searchTimeout;

        // Function to handle search
        const handleSearch = (e) => {
            clearTimeout(searchTimeout);
            const searchValue = e.target.value.trim();
            
            searchTimeout = setTimeout(() => {
                console.log('Searching for:', searchValue);
                const currentUrl = new URL(window.location.href);
                
                if (searchValue) {
                    currentUrl.searchParams.set('search', searchValue);
                } else {
                    currentUrl.searchParams.delete('search');
                }
                
                // Update the URL without page reload for better UX
                window.history.pushState({}, '', currentUrl);
                
                // Make AJAX request to get filtered results
                fetchUsers(searchValue);
            }, 500); // 500ms debounce
        };

        // Add event listeners
        searchInput.addEventListener('input', handleSearch);
        searchInput.addEventListener('keyup', handleSearch); // Add keyup as fallback
        
        // Handle browser back/forward buttons
        window.addEventListener('popstate', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const searchValue = urlParams.get('search') || '';
            searchInput.value = searchValue;
            fetchUsers(searchValue);
        });
    });
    
    function fetchUsers(searchTerm) {
        console.log('fetchUsers called with searchTerm:', searchTerm);
        const url = new URL(window.location.href);
        const role = url.searchParams.get('role') || '';
        
        // Show loading state
        const tbody = document.getElementById('usersTableBody');
        tbody.innerHTML = '<tr><td colspan="3" class="px-6 py-4 text-center">Loading...</td></tr>';
        
        // Build query parameters
        const params = new URLSearchParams();
        if (searchTerm) params.append('search', searchTerm);
        if (role) params.append('role', role);
        
        // Get CSRF token from meta tag
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const requestUrl = `/admin/users?${params.toString()}&ajax=1`;
        console.log('Making request to:', requestUrl);
        
        // Make the AJAX request
        fetch(requestUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data);
            if (data.html) {
                document.getElementById('usersTableBody').innerHTML = data.html;
            } else {
                throw new Error('Invalid response format');
            }
        })
        .catch(error => {
            console.error('Error fetching users:', error);
            document.getElementById('usersTableBody').innerHTML = `
                <tr>
                    <td colspan="3" class="px-6 py-4 text-center text-red-500">
                        Error loading users: ${error.message}
                    </td>
                </tr>`;
        });
    }
</script>
@endpush
@endsection
