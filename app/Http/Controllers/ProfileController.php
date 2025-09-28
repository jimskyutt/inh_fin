<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request, User $user = null): View
    {
        // If no user is specified, use the authenticated user
        $user = $user ?? $request->user();
        
        // Allow admin to edit any user, otherwise users can only edit their own profile
        if (auth()->user()->role !== 'Admin' && $user->id !== $request->user()->id) {
            abort(403);
        }
        
        $barangays = \App\Models\Barangay::orderBy('brgy_name')->get();
        
        return view('profile.edit', [
            'user' => $user,
            'barangays' => $barangays,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request, User $user = null): RedirectResponse
    {
        // If no user is specified, use the authenticated user
        $user = $user ?? $request->user();
        
        // Allow admin to update any user, otherwise users can only update their own profile
        if (auth()->user()->role !== 'Admin' && $user->id !== $request->user()->id) {
            abort(403);
        }
        
        // Debug: Log all incoming request data
        \Log::info('Profile update request data:', array_merge($request->all(), [
            'user_id' => $user->id,
            'is_admin_update' => auth()->user()->role === 'Admin'
        ]));
        
        // Debug: Log all uploaded files
        if ($request->hasFile('police_clearance')) {
            \Log::info('Police clearance file found in request', [
                'name' => $request->file('police_clearance')->getClientOriginalName(),
                'size' => $request->file('police_clearance')->getSize(),
                'mime' => $request->file('police_clearance')->getMimeType(),
            ]);
        } else {
            \Log::info('No police_clearance file found in request');
        }

        $validated = $request->validated();
        
        // Debug: Log validated data
        \Log::info('Validated data before processing:', $validated);
        
        // Map form field names to database column names and their storage paths
        $fileFieldMappings = [
            'profile_photo' => ['db' => 'face_img', 'path' => 'face_images'],
            'id_front' => ['db' => 'id_front', 'path' => 'id_cards'],
            'id_back' => ['db' => 'id_back', 'path' => 'id_cards'],
            'police_clearance' => ['db' => 'police_clearance', 'path' => 'police_clearances']
        ];
        
        // Handle file uploads first
        foreach ($fileFieldMappings as $formField => $config) {
            $dbField = $config['db'];
            $storagePath = $config['path'];
            
            if ($request->hasFile($formField)) {
                \Log::info("Processing file upload for: " . $formField);
                
                // Rename and move old file if exists
                if ($user->$dbField && Storage::exists('public/' . $user->$dbField)) {
                    $oldPath = 'public/' . $user->$dbField;
                    $pathInfo = pathinfo($oldPath);
                    $newPath = $pathInfo['dirname'] . '/old_' . Str::slug($user->name) . '_' . $formField . '_' . time() . '.' . ($pathInfo['extension'] ?? 'jpg');
                    
                    try {
                        Storage::move($oldPath, $newPath);
                        \Log::info("Moved old file to: " . $newPath);
                    } catch (\Exception $e) {
                        // Log error but continue with new file upload
                        \Log::error("Error moving old file: " . $e->getMessage());
                    }
                }
                
                // Store the new file in the appropriate directory
                try {
                    $path = $request->file($formField)->store($storagePath, 'public');
                    // Use the database field name as the key
                    $validated[$dbField] = $path;
                    \Log::info("File uploaded successfully to: " . $path);
                    \Log::info("Setting $dbField to: " . $path);
                } catch (\Exception $e) {
                    \Log::error("File upload failed: " . $e->getMessage());
                    return back()->withErrors([$formField => 'Failed to upload file. Please try again.']);
                }
            } else {
                \Log::info("No file uploaded for: " . $formField);
                // Only unset if the field exists in validated but no file was uploaded
                if (array_key_exists($formField, $validated)) {
                    unset($validated[$formField]);
                }
            }
        }
        
        // Map other form fields to database columns
        $otherFieldMappings = [
            'contact_number' => 'contact_number',
            'address' => 'address',
            'street' => 'street',
            'birthday' => 'birthday',
            'sex' => 'sex',
            'civil_status' => 'civil_status'
        ];
        
        foreach ($otherFieldMappings as $formField => $dbField) {
            if (isset($validated[$formField])) {
                $validated[$dbField] = $validated[$formField];
                unset($validated[$formField]);
            }
        }

        // Debug: Log the final data that will be saved
        \Log::info('Final data to be saved:', $validated);

        // Update user data
        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($user->save()) {
            \Log::info('User data saved successfully');
            
            // If admin is updating another user's profile, redirect back to that user's edit page
            if (auth()->user()->role === 'Admin' && $user->id !== auth()->id()) {
                return redirect()->route('profile.edit.user', $user)
                    ->with('status', 'profile-updated');
            }
            
            // Otherwise, redirect to the current user's edit page
            return redirect()->route('profile.edit')
                ->with('status', 'profile-updated');
        }

        \Log::error('Failed to save user data');
        return back()->withErrors(['error' => 'Failed to update profile. Please try again.']);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function show(User $user)
    {
        // For service providers, load the reviews and related data
        if (strtolower($user->role) === 'serviceprovider') {
            // First, load the relationships
            $user->load([
                'reviews' => function($query) {
                    $query->latest();
                },
                'services'
            ]);
            
            // Then load the counts and averages
            $user->loadCount([
                'assignedJobs as completed_jobs_count' => function($query) {
                    $query->where('status', 'completed');
                },
                'reviews as reviews_count'
            ]);
            
            // Calculate average rating from reviews
            $user->loadAvg('reviews as average_rating', 'rating');
            
            // Debug the loaded data
            \Log::info('Loaded service provider data:', [
                'user_id' => $user->id,
                'ratings_count' => $user->ratings_count,
                'average_rating' => $user->average_rating,
                'completed_jobs_count' => $user->completed_jobs_count,
                'ratings_loaded' => $user->relationLoaded('ratings') ? $user->ratings->count() : 0
            ]);
        } else {
            // For homeowners, load job statistics
            $completedJobs = \App\Models\Job::where('homeowner_id', $user->id)
                ->where('status', 'completed')
                ->get();
            
            $jobStats = [
                'total_jobs' => $completedJobs->count(),
                'total_spent' => $completedJobs->sum('budget'),
                'average_spent' => $completedJobs->avg('budget') ?: 0,
                'recent_jobs' => \App\Models\Job::where('homeowner_id', $user->id)
                    ->where('status', 'completed')
                    ->with('service')
                    ->latest()
                    ->take(5)
                    ->get()
            ];
            
            $user->job_stats = (object)$jobStats;
            
            \Log::info('Loaded homeowner job stats:', [
                'user_id' => $user->id,
                'total_jobs' => $jobStats['total_jobs'],
                'total_spent' => $jobStats['total_spent'],
                'average_spent' => $jobStats['average_spent']
            ]);
        }

        return view('profile.show', compact('user'));
    }

    /**
     * Show the settings page.
     */
    public function showSettings()
    {
        return view('profile.settings', [
            'user' => auth()->user()
        ]);
    }

    /**
     * Update the user's username.
     */
    public function updateUsername(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $request->user()->id],
        ]);

        $request->user()->update([
            'username' => $request->username,
        ]);

        $isAdminUpdate = auth()->user()->role === 'Admin' && $request->user()->id !== auth()->id();
        $message = $isAdminUpdate 
            ? "User's username has been updated successfully."
            : 'Your username has been updated successfully.';
            
        return back()->with('success', $message);
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $isAdminUpdate = auth()->user()->role === 'Admin' && $request->user()->id !== auth()->id();
        $message = $isAdminUpdate 
            ? "User's password has been updated successfully."
            : 'Your password has been updated successfully.';
            
        return back()->with('success', $message);
    }
}
