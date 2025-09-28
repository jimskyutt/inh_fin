<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\Barangay;
use App\Models\Job;
use App\Models\Post;
use App\Models\Review;
use Illuminate\Http\Request;

class ServiceProviderController extends Controller
{
    public function index(Request $request)
    {
        // Debug the incoming request
        \Log::debug('Filter request data:', [
            'services' => $request->services,
            'search' => $request->search,
            'all_params' => $request->all()
        ]);

        $query = User::where('role', User::ROLE_SERVICE_PROVIDER)
            ->where('status', User::STATUS_VERIFIED)
            ->with('services')
            ->withAvg('ratings as average_rating', 'rating');

        // Filter by services if provided
        if ($request->has('services') && !empty($request->services)) {
            $query->whereHas('services', function($q) use ($request) {
                $q->whereIn('services.service_id', $request->services);
            });
        }

        // Search by name if provided
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('username', 'like', "%$search%");
            });
        }

        // Filter by location if provided
        if ($request->has('location') && $request->location) {
            $query->where('barangay_id', $request->location);
        }

        // Filter by minimum rating if provided
        if ($request->has('rating') && $request->rating) {
            $query->whereHas('ratings', function($q) use ($request) {
                $q->selectRaw('avg(rating) as avg_rating')
                  ->havingRaw('avg(rating) >= ?', [$request->rating]);
            });
        }

        // Apply sorting
        $sort = $request->get('sort', 'rating');
        switch ($sort) {
            case 'newest':
                $query->latest();
                break;
            case 'most-hired':
                $query->withCount('hires as hires_count')->orderBy('hires_count', 'desc');
                break;
            case 'rating':
            default:
                $query->orderBy('average_rating', 'desc');
                break;
        }

        $serviceProviders = $query->paginate(10);
        $services = Service::orderBy('service_name')->get();
        $barangays = Barangay::orderBy('brgy_name')->get();
            
        // Debug the final results
        \Log::debug('Query results:', [
            'total' => $serviceProviders->total(),
            'items' => $serviceProviders->items()
        ]);
        
        return view('service-provider.index', compact('serviceProviders', 'services', 'barangays'));
    }

    public function show(User $serviceProvider)
    {
        $serviceProvider->loadCount(['assignedJobs as completed_jobs_count' => function($query) {
            $query->where('status', 'completed');
        }])
        ->loadAvg('ratings as average_rating', 'rating')
        ->load(['ratings.user', 'services'])
        ->append('age'); // This will make the age accessor available
        
        // Check if there's an existing conversation with the authenticated user
        if (auth()->check()) {
            $hasConversation = $serviceProvider->conversations()
                ->whereHas('participants', function($q) {
                    $q->where('user_id', auth()->id());
                })
                ->orderBy('conversations.updated_at', 'desc')
                ->exists();
                
            $serviceProvider->setAttribute('has_conversation', $hasConversation);
        }

        return view('service-provider.show', compact('serviceProvider'));
    }

    /**
     * Display the service provider's dashboard with analytics.
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Get job statistics
        $totalJobs = $user->jobs()->count();
        $ongoingJobs = $user->jobs()->where('status', 'ongoing')->count();
        $completedJobs = $user->jobs()->where('status', 'completed')->count();
        
        // Calculate average income from completed jobs
        $averageIncome = $user->jobs()
            ->where('status', 'completed')
            ->avg('budget') ?? 0;
            
        // Get average rating from reviews using service provider's name
        $averageRating = Review::where('service_provider_name', $user->name)
            ->avg('rating') ?? 0;
        
        // Get ongoing jobs list for the dashboard
        $ongoingJobsList = $user->jobs()
            ->where('status', 'ongoing')
            ->with('service')
            ->orderBy('scheduled_date', 'asc')
            ->take(5)
            ->get();
            
        // Get recent jobs for the dashboard
        $recentJobs = $user->jobs()
            ->with(['homeowner', 'service'])
            ->latest()
            ->take(3)
            ->get();
        
        return view('service-provider.dashboard', [
            'totalJobs' => $totalJobs,
            'ongoingJobs' => $ongoingJobs,
            'completedJobs' => $completedJobs,
            'averageIncome' => $averageIncome,
            'averageRating' => $averageRating,
            'recentJobs' => $recentJobs,
            'ongoingJobsList' => $ongoingJobsList
        ]);
    }

    /**
     * Display a listing of available job posts for service providers.
     */
    public function jobPosts()
    {
        // Get posts from the posts table with user relationship
        $posts = \App\Models\Post::with(['user', 'comments', 'likes'])
            ->latest()
            ->paginate(10);

        return view('service-provider.job-posts', [
            'posts' => $posts
        ]);
    }
}
