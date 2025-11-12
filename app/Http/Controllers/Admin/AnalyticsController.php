<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Bookmark;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->get('range', '30'); // days
        $days = in_array((int) $range, [7, 14, 30, 60, 90]) ? (int) $range : 30;

        // Users
        $userStats = [
            'total' => User::count(),
            'job_seekers' => User::where('user_type', 'job_seeker')->count(),
            'employers' => User::where('user_type', 'employer')->count(),
            'admins' => User::where('user_type', 'admin')->count(),
            'new_in_range' => User::where('created_at', '>=', now()->subDays($days))->count(),
        ];

        $userGrowth = User::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Resumes (from users table)
        $resumeStats = [
            'with_resume' => User::where('user_type', 'job_seeker')->whereNotNull('resume_file')->count(),
            'verified' => User::where('user_type', 'job_seeker')->where('resume_verification_status', 'verified')->count(),
            'pending' => User::where('user_type', 'job_seeker')->where('resume_verification_status', 'pending')->count(),
            'needs_review' => User::where('user_type', 'job_seeker')->where('resume_verification_status', 'needs_review')->count(),
            'rejected' => User::where('user_type', 'job_seeker')->where('resume_verification_status', 'rejected')->count(),
        ];

        // Permits (document_validations table)
        $permitStats = DB::table('document_validations')
            ->selectRaw("sum(case when document_type='business_permit' then 1 else 0 end) as total")
            ->selectRaw("sum(case when document_type='business_permit' and validation_status='approved' then 1 else 0 end) as approved")
            ->selectRaw("sum(case when document_type='business_permit' and validation_status='pending_review' then 1 else 0 end) as pending_review")
            ->selectRaw("sum(case when document_type='business_permit' and validation_status='rejected' then 1 else 0 end) as rejected")
            ->first();

        // Jobs
        $jobStats = [
            'total' => JobPosting::count(),
            'active' => JobPosting::where('status', 'active')->count(),
            'inactive' => JobPosting::where('status', '!=', 'active')->count(),
            'new_in_range' => JobPosting::where('created_at', '>=', now()->subDays($days))->count(),
        ];

        // Applications
        $applicationStats = [
            'total' => Application::count(),
            'today' => Application::whereDate('created_at', today())->count(),
            'in_range' => Application::where('created_at', '>=', now()->subDays($days))->count(),
        ];

        $applicationTrend = Application::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Bookmarks
        $bookmarkStats = [
            'total' => Bookmark::count(),
            'in_range' => Bookmark::where('created_at', '>=', now()->subDays($days))->count(),
        ];

        // Top Bookmarked Jobs from bookmarks table (no foreign key in schema)
        // Group by title/company to surface the most saved listings
        $topBookmarkedJobs = DB::table('bookmarks')
            ->select('title', 'company', DB::raw('COUNT(*) as count'))
            ->groupBy('title', 'company')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Audit Logs
        $auditStats = [
            'total' => AuditLog::count(),
            'in_range' => AuditLog::where('created_at', '>=', now()->subDays($days))->count(),
        ];

        $actionBreakdown = AuditLog::select('action', DB::raw('COUNT(*) as count'))
            ->groupBy('action')
            ->get();

        return view('admin.analytics.index', [
            'range' => $days,
            'userStats' => $userStats,
            'userGrowth' => $userGrowth,
            'resumeStats' => $resumeStats,
            'permitStats' => $permitStats,
            'jobStats' => $jobStats,
            'applicationStats' => $applicationStats,
            'applicationTrend' => $applicationTrend,
            'bookmarkStats' => $bookmarkStats,
            'topBookmarkedJobs' => $topBookmarkedJobs,
            'auditStats' => $auditStats,
            'actionBreakdown' => $actionBreakdown,
        ]);
    }
}
