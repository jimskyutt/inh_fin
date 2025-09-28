@forelse ($users as $user)
    <tr class="hover:bg-gray-50" data-user-id="{{ $user->id }}">
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10">
                    <img class="h-10 w-10 rounded-full object-cover" 
                         src="{{ $user->face_img ? asset('storage/' . $user->face_img) : asset('images/default-avatar.png') }}" 
                         alt="{{ $user->name }}">
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                    <div class="text-sm text-gray-500">{{ $user->contact_number ?? 'N/A' }}</div>
                </div>
            </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="editable-field" 
                 data-field="username" 
                 data-user-id="{{ $user->id }}"
                 title="Double click to edit">
                {{ $user->username ?? 'N/A' }}
            </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex items-center">
                <span class="password-display text-gray-400">••••••••</span>
                <input type="password" 
                       class="password-edit hidden w-32 px-2 py-1 text-sm border rounded" 
                       value="" 
                       data-field="password"
                       data-user-id="{{ $user->id }}">
                <button type="button" 
                        class="show-password ml-2 text-blue-600 hover:text-blue-800 text-xs"
                        data-user-id="{{ $user->id }}"
                        title="Click to edit password">
                    Edit
                </button>
                <button type="button" 
                        class="save-password hidden ml-2 text-green-600 hover:text-green-800 text-xs"
                        data-user-id="{{ $user->id }}"
                        title="Save password">
                    Save
                </button>
                <button type="button" 
                        class="cancel-edit hidden ml-2 text-gray-500 hover:text-gray-700 text-xs"
                        data-user-id="{{ $user->id }}"
                        title="Cancel">
                    Cancel
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
            No users found.
        </td>
    </tr>
@endforelse
