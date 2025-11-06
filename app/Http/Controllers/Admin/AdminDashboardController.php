<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\DocumentValidation;
use App\Models\JobPosting;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Show admin dashboard with overview statistics.
     */
    public function index()
    {
        // User Statistics
        $totalUsers = User::count();
        $totalJobSeekers = User::where('user_type', 'job_seeker')->count();
        $totalEmployers = User::where('user_type', 'employer')->count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $activeUsersToday = User::whereDate('last_login_at', today())->count();

        // Job Seeker Statistics
        $jobSeekersWithResume = User::where('user_type', 'job_seeker')
            ->whereNotNull('resume_file')
            ->count();
        $jobSeekersWithoutResume = User::where('user_type', 'job_seeker')
            ->whereNull('resume_file')
            ->count();
        $verifiedResumes = User::where('user_type', 'job_seeker')
            ->where('resume_verification_status', 'verified')
            ->count();
        $resumesNeedingReview = User::where('user_type', 'job_seeker')
            ->where('resume_verification_status', 'needs_review')
            ->count();
        $pendingResumes = User::where('user_type', 'job_seeker')
            ->where('resume_verification_status', 'pending')
            ->count();
        $rejectedResumes = User::where('user_type', 'job_seeker')
            ->whereNotNull('resume_file')
            ->whereJsonContains('verification_flags', 'not_a_resume')
            ->count();

        // Employer Statistics
        $verifiedEmployers = DocumentValidation::where('document_type', 'business_permit')
            ->where('validation_status', 'approved')
            ->distinct('user_id')
            ->count('user_id');
        $pendingPermits = DocumentValidation::where('document_type', 'business_permit')
            ->where('validation_status', 'pending_review')
            ->count();
        $rejectedPermits = DocumentValidation::where('document_type', 'business_permit')
            ->where('validation_status', 'rejected')
            ->count();
        $expiringSoonPermits = DocumentValidation::where('document_type', 'business_permit')
            ->where('validation_status', 'approved')
            ->whereNotNull('permit_expiry_date')
            ->whereBetween('permit_expiry_date', [now()->startOfDay(), now()->addDays(30)->endOfDay()])
            ->count();

        // Job Posting Statistics
        $totalJobPostings = JobPosting::count();
        $activeJobPostings = JobPosting::where('status', 'active')->count();
        $inactiveJobPostings = JobPosting::where('status', 'inactive')->count();
        $jobPostingsThisMonth = JobPosting::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Application Statistics
        $totalApplications = Application::count();
        $pendingApplications = Application::where('status', 'pending')->count();
        $reviewingApplications = Application::where('status', 'reviewing')->count();
        $acceptedApplications = Application::where('status', 'accepted')->count();
        $rejectedApplications = Application::where('status', 'rejected')->count();
        $applicationsThisWeek = Application::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        // Admin Activity Statistics
        $adminUnreadNotifications = Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->count();
        $recentAuditLogs = AuditLog::orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        $totalAuditLogs = AuditLog::count();

        // Recent Activity - Last 5 registered users
        $recentUsers = User::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Recent Activity - Last 5 job postings
        $recentJobPostings = JobPosting::with('employer')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Recent Activity - Last 5 applications
        $recentApplications = Application::with(['user', 'jobPosting'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Chart Data - User registrations over last 30 days
        $userRegistrationData = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Chart Data - Applications over last 30 days
        $applicationData = Application::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return view('admin.dashboard', compact(
            // User stats
            'totalUsers',
            'totalJobSeekers',
            'totalEmployers',
            'newUsersThisMonth',
            'activeUsersToday',

            // Job Seeker stats
            'jobSeekersWithResume',
            'jobSeekersWithoutResume',
            'verifiedResumes',
            'resumesNeedingReview',
            'pendingResumes',
            'rejectedResumes',

            // Employer stats
            'verifiedEmployers',
            'pendingPermits',
            'rejectedPermits',
            'expiringSoonPermits',

            // Job posting stats
            'totalJobPostings',
            'activeJobPostings',
            'inactiveJobPostings',
            'jobPostingsThisMonth',

            // Application stats
            'totalApplications',
            'pendingApplications',
            'reviewingApplications',
            'acceptedApplications',
            'rejectedApplications',
            'applicationsThisWeek',

            // Admin activity
            'adminUnreadNotifications',
            'recentAuditLogs',
            'totalAuditLogs',

            // Recent activity
            'recentUsers',
            'recentJobPostings',
            'recentApplications',

            // Chart data
            'userRegistrationData',
            'applicationData'
        ));
    }
}
