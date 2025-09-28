<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceProviderController extends Controller
{
    /**
     * Display a listing of the service providers.
     */
    public function index()
    {
        $serviceProviders = User::where('role', 'ServiceProvider')
            ->withCount('services')
            ->withAvg('reviews', 'rating')
            ->latest()
            ->paginate(10);

        return view('admin.serviceproviders.index', compact('serviceProviders'));
    }

    /**
     * Show the form for editing the specified service provider.
     */
    public function edit(User $user)
    {
        if ($user->role !== 'ServiceProvider') {
            abort(404);
        }

        return view('profile.edit', [
            'user' => $user,
            'barangays' => \App\Models\Barangay::orderBy('brgy_name')->get(),
        ]);
    }

    /**
     * Remove the specified service provider from storage.
     */
    public function destroy(User $user)
    {
        if ($user->role !== 'ServiceProvider') {
            abort(404);
        }

        // Delete profile photo if exists
        if ($user->face_img) {
            Storage::disk('public')->delete($user->face_img);
        }

        // Delete ID photos if exist
        if ($user->id_front) {
            Storage::disk('public')->delete($user->id_front);
        }
        if ($user->id_back) {
            Storage::disk('public')->delete($user->id_back);
        }

        // Delete police clearance if exists
        if ($user->police_clearance) {
            Storage::disk('public')->delete($user->police_clearance);
        }

        // Delete the user
        $user->delete();

        return redirect()->route('admin.serviceproviders.index')
            ->with('success', 'Service provider deleted successfully');
    }
}
