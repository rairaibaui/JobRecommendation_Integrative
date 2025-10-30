<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JobPosting;
use App\Models\Application;

class EmployerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get employer's job postings with application counts
        $jobPostings = JobPosting::where('employer_id', $user->id)
            ->withCount('applications')
            ->orderByDesc('created_at')
            ->get();

        // Hires count for dashboard
        $hiredCount = \App\Models\ApplicationHistory::where('employer_id', $user->id)
            ->where('decision', 'hired')
            ->count();

        return view('employer.dashboard', compact('jobPostings', 'user', 'hiredCount'));
    }
}
