<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\JobPosting;
use App\Models\Application;
use App\Models\ApplicationHistory;

class EmployerAnalyticsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get all job postings for this employer
        $jobPostings = JobPosting::where('employer_id', $user->id)->get();
        $jobPostingIds = $jobPostings->pluck('id');
        
        // Overview Statistics
        $totalJobs = $jobPostings->count();
        $activeJobs = $jobPostings->where('status', 'active')->count();
        $totalApplications = Application::whereIn('job_posting_id', $jobPostingIds)->count();
        $totalEmployees = ApplicationHistory::where('employer_id', $user->id)
            ->where('decision', 'hired')
            ->distinct('job_seeker_id')
            ->count('job_seeker_id');
        
        // Application Status Breakdown
        $applicationsByStatus = Application::whereIn('job_posting_id', $jobPostingIds)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
        
        // Hiring Pipeline Statistics
        $pendingApplications = $applicationsByStatus['pending'] ?? 0;
        $reviewedApplications = $applicationsByStatus['reviewed'] ?? 0;
        $acceptedApplications = $applicationsByStatus['accepted'] ?? 0;
        $rejectedApplications = $applicationsByStatus['rejected'] ?? 0;
        
        // Historical Decisions
        $hiringHistory = ApplicationHistory::where('employer_id', $user->id)
            ->select('decision', DB::raw('count(*) as count'))
            ->groupBy('decision')
            ->get()
            ->pluck('count', 'decision');
        
        $totalHired = $hiringHistory['hired'] ?? 0;
        $totalRejected = $hiringHistory['rejected'] ?? 0;
        $totalTerminated = $hiringHistory['terminated'] ?? 0;
        $totalResigned = $hiringHistory['resigned'] ?? 0;
        
        // Conversion Rates
        $applicationToHireRate = $totalApplications > 0 
            ? round(($totalHired / $totalApplications) * 100, 1) 
            : 0;
        
        $acceptanceToHireRate = $acceptedApplications > 0 
            ? round(($totalHired / $acceptedApplications) * 100, 1) 
            : 0;
        
        // Top Performing Jobs (most applications)
        $topJobs = Application::whereIn('job_posting_id', $jobPostingIds)
            ->select('job_posting_id', DB::raw('count(*) as application_count'))
            ->groupBy('job_posting_id')
            ->orderBy('application_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $job = JobPosting::find($item->job_posting_id);
                return [
                    'title' => $job->title ?? 'Unknown',
                    'location' => $job->location ?? 'N/A',
                    'applications' => $item->application_count
                ];
            });
        
        // Recent Activity (last 7 days)
        $recentApplications = Application::whereIn('job_posting_id', $jobPostingIds)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        
        $recentHires = ApplicationHistory::where('employer_id', $user->id)
            ->where('decision', 'hired')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        
        // Monthly Trends (last 6 months)
        $monthlyData = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth()->toDateString();
            $monthEnd = $month->copy()->endOfMonth()->toDateString();
            
            $monthlyData->push([
                'month' => $month->format('M Y'),
                'applications' => Application::whereIn('job_posting_id', $jobPostingIds)
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->count(),
                'hires' => ApplicationHistory::where('employer_id', $user->id)
                    ->where('decision', 'hired')
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->count(),
            ]);
        }
        
        // Active Retention Rate (employees still active)
        $activeEmployees = $totalEmployees - $totalTerminated - $totalResigned;
        $retentionRate = $totalEmployees > 0 
            ? round(($activeEmployees / $totalEmployees) * 100, 1) 
            : 0;
        
        return view('employer.analytics', compact(
            'user',
            'totalJobs',
            'activeJobs',
            'totalApplications',
            'totalEmployees',
            'pendingApplications',
            'reviewedApplications',
            'acceptedApplications',
            'rejectedApplications',
            'totalHired',
            'totalRejected',
            'totalTerminated',
            'totalResigned',
            'applicationToHireRate',
            'acceptanceToHireRate',
            'topJobs',
            'recentApplications',
            'recentHires',
            'monthlyData',
            'activeEmployees',
            'retentionRate'
        ));
    }
}
