<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use App\Models\Job;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class JobsController extends Controller
{
    public function index(Request $request)
    {
        // Get the authenticated user
        $user = auth()->user();
        
        // Query for jobs that are not deleted by owner or provider
        $query = Job::with(['homeowner', 'service', 'serviceProvider']);
        
        if ($user->role === 'Homeowner') {
            $query->where('homeowner_id', $user->id)
                  ->where('deleted_by_owner', false);
        } elseif ($user->role === 'ServiceProvider') {
            $query->where('service_provider_id', $user->id)
                  ->where('deleted_by_provider', false);
        } else {
            // For other roles, only show non-deleted jobs
            $query->where('deleted_by_owner', false);
        }
        
        $query->orderBy('created_at', 'desc');
        
        // Handle search query
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhereHas('service', function($q) use ($search) {
                      $q->where('service_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('serviceProvider', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $jobs = $query->paginate(10);
        
        // Get reviews for the Rate & Review tab
        $reviewsQuery = Review::query();
        
        if ($user->role === 'Homeowner') {
            $reviewsQuery->where('homeowner_id', $user->id);
        } elseif ($user->role === 'ServiceProvider') {
            $reviewsQuery->where('service_provider_id', $user->id)
                        ->where('status', 'completed');
        }
        
        $reviews = $reviewsQuery->orderBy('created_at', 'desc')->get();
        
        // Return JSON for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'html' => view('jobs.partials.job-table', ['jobs' => $jobs, 'showActions' => true])->render(),
                'reviewsHtml' => view('jobs.partials.review-table', ['reviews' => $reviews])->render(),
                'counts' => [
                    'ongoing' => $jobs->whereIn('status', ['pending', 'ongoing'])->count(),
                    'completed' => $jobs->where('status', 'completed')->count(),
                    'cancelled' => $jobs->where('status', 'cancelled')->count(),
                    'review' => $reviews->where('status', 'pending')->count(),
                ]
            ]);
        }
        
        // Preserve search parameter in pagination links
        if ($request->has('search')) {
            $jobs->appends(['search' => $request->search]);
        }
        
        return view('jobs.index', compact('jobs', 'reviews'));
    }

    public function create()
    {
        $services = Service::all();
        $serviceProviders = User::where('role', User::ROLE_SERVICE_PROVIDER)->get();
        $barangays = Barangay::orderBy('brgy_name')->get();
        return view('jobs.create', compact('services', 'serviceProviders', 'barangays'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'service_id' => 'required|exists:services,service_id',
            'service_provider_id' => 'required|exists:users,id',
            'budget' => 'nullable|numeric|min:0',
            'location' => 'required|string',
            'scheduled_date' => 'required|date|after:today',
        ]);

        $validated['homeowner_id'] = auth()->id();
        $validated['status'] = Job::STATUS_PENDING;

        Job::create($validated);

        return redirect()->route('jobs.index')->with('success', 'Job posted successfully!');
    }

    public function show(Job $job)
    {
        $user = auth()->user();
        
        // Check if user is authorized (homeowner or assigned service provider)
        $isHomeowner = $job->homeowner_id === $user->id;
        $isAssignedProvider = $job->service_provider_id === $user->id;
        
        if (!$isHomeowner && !$isAssignedProvider) {
            abort(403, 'Unauthorized action.');
        }

        return view('jobs.show', compact('job'));
    }

    public function getServiceProviders($serviceId)
    {
        try {
            // Ensure service_id is numeric
            $serviceId = intval($serviceId);
            
            if ($serviceId <= 0) {
                return response()->json([], 400);
            }

            $providers = User::where('role', 'Serviceprovider')
                ->whereHas('services', function($query) use ($serviceId) {
                    $query->where('provider_services.service_id', $serviceId);
                })
                ->get()
                ->map(function($provider) {
                    return [
                        'id' => $provider->id,
                        'name' => $provider->name
                    ];
                });

            return response()->json($providers);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load providers'], 500);
        }
    }

    public function edit(Job $job)
    {
        // Ensure only the job owner can edit
        if ($job->homeowner_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $services = Service::all();
        $serviceProviders = User::where('role', 'Serviceprovider')
            ->whereHas('services', function($query) use ($job) {
                $query->where('provider_services.service_id', $job->service_id);
            })
            ->get();
            
        $barangays = Barangay::orderBy('brgy_name')->get();
        
        return view('jobs.edit', compact('job', 'services', 'serviceProviders', 'barangays'));
    }

    public function update(Request $request, Job $job)
    {
        $user = auth()->user();
        
        // Check if user is authorized (homeowner or assigned service provider)
        $isHomeowner = $job->homeowner_id === $user->id;
        $isAssignedProvider = $job->service_provider_id === $user->id;
        
        if (!$isHomeowner && !$isAssignedProvider) {
            abort(403, 'Unauthorized action.');
        }

        // Handle status updates
        if ($request->has('status')) {
            $status = $request->status;
            
            // Validate the status transition
            $validStatuses = ['pending', 'upcoming', 'completed', 'cancelled'];
            if (!in_array($status, $validStatuses)) {
                return back()->with('error', 'Invalid status.');
            }
            
            // Additional validation for service providers
            if ($isAssignedProvider) {
                // Service provider can only update to upcoming, completed, or cancelled
                if (!in_array($status, ['upcoming', 'completed', 'cancelled'])) {
                    return back()->with('error', 'You can only update the job to Upcoming, Completed, or Cancelled.');
                }
                
                // Can't cancel a completed job
                if ($job->status === 'completed' && $status === 'cancelled') {
                    return back()->with('error', 'Cannot cancel a completed job.');
                }
            }
            
            // Additional validation for homeowners
            if ($isHomeowner && $status === 'upcoming' && $job->status !== 'pending') {
                return back()->with('error', 'Only pending jobs can be set to upcoming.');
            }
            
            $job->update(['status' => $status]);
            
            $redirectRoute = $isHomeowner ? 'jobs.index' : 'jobs.index';
            $message = 'Job has been ';
            $message .= $status === 'upcoming' ? 'confirmed' : 'cancelled';
            $message .= ' successfully!';
            
            return redirect()->route($redirectRoute)->with('success', $message);
        }
        
        // Only homeowners can update job details
        if (!$isHomeowner) {
            abort(403, 'Only the job owner can update job details.');
        }

        // Handle regular job updates (only for homeowners)
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'service_id' => 'required|exists:services,service_id',
            'service_provider_id' => 'required|exists:users,id',
            'budget' => 'nullable|numeric|min:0',
            'location' => 'required|string',
            'scheduled_date' => 'required|date|after:today',
        ]);

        $job->update($validated);

        return redirect()->route('jobs.show', $job)->with('success', 'Job updated successfully!');
    }

    
    public function destroy(Request $request, Job $job)
    {
        $user = auth()->user();
        
        // Check if user is authorized (homeowner or assigned service provider)
        $isHomeowner = $user->id === $job->homeowner_id;
        $isAssignedProvider = $user->id === $job->service_provider_id;
        
        if (!$isHomeowner && !$isAssignedProvider) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow deletion of cancelled or completed jobs
        if (!in_array($job->status, ['cancelled', 'completed'])) {
            return redirect()->back()
                ->with('error', 'Only cancelled or completed jobs can be deleted.');
        }

        try {
            // Start a database transaction
            \DB::beginTransaction();

            // Update the appropriate deletion flag based on user role
            if ($isAssignedProvider) {
                $job->update(['deleted_by_provider' => true]);
                $message = 'Job has been removed from your list.';
            } else {
                $job->update(['deleted_by_owner' => true]);
                $message = 'Job has been deleted successfully.';
            }

            // Only create a review entry if this is marking a job as completed
            if ($request->has('status') && $request->status === 'completed') {
                Review::create([
                    'job_id' => $job->id,
                    'homeowner_id' => $job->homeowner_id,
                    'service_provider_id' => $job->service_provider_id,
                    'service_id' => $job->service_id,
                    'homeowner_name' => $job->homeowner->name,
                    'service_provider_name' => $job->serviceProvider->name ?? 'Unknown',
                    'service_name' => $job->service->service_name ?? 'N/A',
                    'location' => $job->location,
                    'scheduled_date' => $job->scheduled_date,
                    'rating' => 0,
                    'status' => 'pending',
                    'review' => null,
                ]);
            }

            // Commit the transaction
            \DB::commit();

            return redirect()->back()
                ->with('success', $message);

        } catch (\Exception $e) {
            // Rollback the transaction on error
            \DB::rollBack();
            \Log::error('Error completing job: ' . $e->getMessage(), [
                'job_id' => $job->id,
                'exception' => $e
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while completing the job. Please try again.');
        }
    }

    /**
     * Cancel a job.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function cancel(Job $job)
    {
        // Check if the authenticated user is the homeowner who posted the job
        if (auth()->id() !== $job->homeowner_id) {
            abort(403, 'Unauthorized action.');
        }

        $job->update([
            'status' => 'cancelled',
            'cancelled_at' => now()
        ]);

        return redirect()->route('jobs.index')
            ->with('success', 'Job has been cancelled successfully!');
    }
}
