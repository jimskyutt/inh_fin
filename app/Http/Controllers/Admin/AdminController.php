<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function dashboard()
    {
        $users = User::where('role', '!=', User::ROLE_ADMIN)
                    ->where('status', '!=', 'verified')
                    ->latest()
                    ->paginate(10);
        
        // Analysis data
        $totalHomeowners = User::where('role', 'Homeowner')->count();
        $totalServiceProviders = User::where('role', 'ServiceProvider')->count();
        $totalJobsCompleted = \App\Models\Job::where('status', \App\Models\Job::STATUS_COMPLETED)->count();
        
        $analysisData = [
            'totalHomeowners' => $totalHomeowners,
            'totalServiceProviders' => $totalServiceProviders,
            'totalJobsCompleted' => $totalJobsCompleted
        ];
                    
        return view('admin.dashboard', compact('users', 'analysisData'));
    }

    public function updateStatus(User $user, $status)
    {
        if (!in_array($status, [User::STATUS_VERIFIED, User::STATUS_REJECTED])) {
            return back()->with('error', 'Invalid status');
        }

        $user->update(['status' => $status]);
        
        return back()->with('success', 'User status updated successfully');
    }

    /**
     * Display the specified user's details in read-only mode.
     */
    public function viewUser(User $user)
    {
        $barangays = \App\Models\Barangay::all();
        $services = \App\Models\Service::all();
        
        return view('admin.users.view', [
            'user' => $user,
            'barangays' => $barangays,
            'services' => $services,
            'readonly' => true, // This will be used to make the form read-only
        ]);
    }
}
