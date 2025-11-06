<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $userType = $request->get('type', 'all');
        $search = $request->get('search');
        $status = $request->get('status');

        // Base query
        $query = User::query();

        // Filter by user type
        if ($userType !== 'all') {
            $query->where('user_type', $userType);
        }

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Status filter (for job seekers)
        if ($status && $userType === 'job_seeker') {
            $query->where('resume_verification_status', $status);
        }

        // Get users with pagination
        $users = $query->latest()->paginate(20);

        // Statistics
        $stats = [
            'total' => User::count(),
            'job_seekers' => User::where('user_type', 'job_seeker')->count(),
            'employers' => User::where('user_type', 'employer')->count(),
            'admins' => User::where('user_type', 'admin')->count(),
            'verified_resumes' => User::where('user_type', 'job_seeker')
                                     ->where('resume_verification_status', 'verified')->count(),
            'active_today' => User::whereDate('last_login_at', today())->count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)
                                   ->whereYear('created_at', now()->year)->count(),
        ];

        // User growth data (last 7 days)
        $growthData = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subDays(7))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return view('admin.users.index', compact('users', 'stats', 'userType', 'growthData'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        // Get user-specific stats
        if ($user->user_type === 'job_seeker') {
            $applications = $user->applications()->count();
            $bookmarks = $user->bookmarks()->count();
        } elseif ($user->user_type === 'employer') {
            $jobPostings = $user->jobPostings()->count();
            $applications = DB::table('applications')
                ->join('job_postings', 'applications.job_posting_id', '=', 'job_postings.id')
                ->where('job_postings.employer_id', $user->id)
                ->count();
        }

        return view('admin.users.show', compact('user'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->user_type === 'admin') {
            return back()->with('error', 'Cannot delete admin users.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
