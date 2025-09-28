<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        \Log::info('UserController@index called', [
            'search' => $request->search,
            'role' => $request->role,
            'isAjax' => $request->ajax(),
            'hasAjaxParam' => $request->has('ajax')
        ]);

        $query = User::query();
        
        // Filter by role if specified
        if ($request->has('role') && !empty($request->role)) {
            $query->where('role', $request->role);
        }
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%");
            });
        }
        
        $users = $query->latest()->paginate(15)->withQueryString();
        $role = $request->get('role');

        // For AJAX requests, return JSON response
        if ($request->ajax() || $request->has('ajax')) {
            $html = view('admin.users.partials.user_rows', compact('users'))->render();
            
            \Log::info('Returning AJAX response', [
                'html_length' => strlen($html),
                'next_page_url' => $users->nextPageUrl(),
                'users_count' => $users->count()
            ]);
            
            return response()->json([
                'html' => $html,
                'next_page_url' => $users->nextPageUrl()
            ]);
        }

        return view('admin.users.index', compact('users', 'role'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:Homeowner,ServiceProvider,Admin'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        // Log the incoming request data
        \Log::info('Update request received', [
            'user_id' => $user->id,
            'request_data' => $request->all(),
            'is_ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson(),
            'content_type' => $request->header('Content-Type'),
            'accept' => $request->header('Accept')
        ]);

        // Handle individual field updates via AJAX
        if ($request->ajax() || $request->wantsJson()) {
            $field = $request->input('field');
            $value = $request->input($field);
            
            // Log the field and value being updated
            \Log::info('Processing field update', [
                'field' => $field,
                'value' => $value,
                'has_field_param' => $request->has('field'),
                'has_value_param' => $request->has($field)
            ]);
            
            try {
                // Validate the field being updated
                $validatedData = $request->validate([
                    'field' => ['required', 'string', 'in:username,password'],
                    $field => [
                        'required',
                        'string',
                        function ($attribute, $value, $fail) use ($field, $user) {
                            if ($field === 'username') {
                                // Check for unique username except current user
                                $exists = User::where('username', $value)
                                    ->where('id', '!=', $user->id)
                                    ->exists();
                                if ($exists) {
                                    $fail('The username has already been taken.');
                                }
                            } elseif ($field === 'password' && strlen($value) < 8) {
                                $fail('The password must be at least 8 characters.');
                            }
                        },
                    ],
                ]);

                // Update the specific field
                if ($field === 'password') {
                    $user->update([
                        'password' => Hash::make($value)
                    ]);
                } else {
                    $user->update([
                        $field => $value
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => ucfirst($field) . ' updated successfully.'
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            } catch (\Exception $e) {
                \Log::error('Error updating user: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the user.'
                ], 500);
            }
        }

        // Handle regular form submission
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:Homeowner,ServiceProvider,Admin'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
