<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\DocumentValidation;
use App\Models\JobPosting;
use Illuminate\Support\Facades\Auth;

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

        // Get business permit validation status
        $validation = DocumentValidation::where('user_id', $user->id)
            ->where('document_type', 'business_permit')
            ->orderByDesc('created_at')
            ->first();

        return view('employer.dashboard', compact('jobPostings', 'user', 'hiredCount', 'validation'));
    }
}
