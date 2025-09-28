<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * Display a listing of the reports.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get total counts
        $totalHomeowners = User::where('role', 'homeowner')->count();
        $totalServiceProviders = User::where('role', 'serviceprovider')->count();
        $totalCompletedJobs = Job::where('status', 'completed')->count();
        $totalCancelledJobs = Job::where('status', 'cancelled')->count();

        
        // Get jobs completed by month for the last 6 months
        $jobsByMonth = Job::select(
                DB::raw('DATE_FORMAT(updated_at, "%Y-%m") as month'),
                DB::raw('count(*) as total')
            )
            ->where('status', 'completed')
            ->where('updated_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Get homeowners with at least one completed job
        $homeownersWithJobCounts = User::where('role', 'homeowner')
            ->withCount(['homeownerJobs as total_jobs' => function($query) {
                $query->where('status', 'completed');
            }])
            ->having('total_jobs', '>', 0)
            ->orderBy('total_jobs', 'desc')
            ->get();

        // Get service providers with at least one completed job
        $serviceProvidersWithJobCounts = User::where('role', 'serviceprovider')
            ->withCount(['jobs as total_jobs' => function($query) {
                $query->where('status', 'completed');
            }])
            ->having('total_jobs', '>', 0)
            ->orderBy('total_jobs', 'desc')
            ->get();

        return view('admin.reports.index', [
            'totalHomeowners' => $totalHomeowners,
            'totalServiceProviders' => $totalServiceProviders,
            'totalCompletedJobs' => $totalCompletedJobs,
            'totalCancelledJobs' => $totalCancelledJobs,
            'jobsByMonth' => $jobsByMonth,
            'homeownersWithJobCounts' => $homeownersWithJobCounts,
            'serviceProvidersWithJobCounts' => $serviceProvidersWithJobCounts
        ]);
    }
}
