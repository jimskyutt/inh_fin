<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class HomeownerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of the homeowners.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $homeowners = User::where('role', 'Homeowner')
            ->latest()
            ->paginate(15);
            
        return view('admin.homeowners.index', ['homeowners' => $homeowners]);
    }

    /**
     * Show the form for creating a new homeowner.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.homeowners.create');
    }

    /**
     * Store a newly created homeowner in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'Homeowner',
            'email_verified_at' => now(), // Auto-verify new users created by admin
        ]);

        return redirect()->route('admin.homeowners.index')
            ->with('status', 'Homeowner created successfully.');
    }

    /**
     * Display the specified homeowner.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
        return view('admin.homeowners.show', compact('user'));
    }

    /**
     * Show the form for editing the specified homeowner.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('admin.homeowners.edit', compact('user'));
    }

    /**
     * Update the specified homeowner in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        // Update password if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old profile photo if exists
            if ($user->face_img) {
                Storage::delete($user->face_img);
            }
            
            // Store new profile photo
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $updateData['face_img'] = $path;
        }

        $user->update($updateData);

        return redirect()->route('admin.homeowners.index')
            ->with('status', 'Homeowner updated successfully.');
    }

    /**
     * Remove the specified homeowner from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        
        $user->delete();
        
        return redirect()->route('admin.homeowners.index')
            ->with('status', 'Homeowner deleted successfully.');
    }
}
