<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class MyApplicationsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $applications = $user->applications()
            ->with(['employer', 'jobPosting.employer'])
            ->latest()
            ->get()
            ->map(function ($application) use ($user) {
                // For accepted applications, check employment status
                if ($application->status === 'accepted') {
                    // Check if there's a termination record for this application
                    $terminationRecord = \App\Models\ApplicationHistory::where('job_seeker_id', $user->id)
                        ->where('application_id', $application->id)
                        ->where('decision', 'terminated')
                        ->first();
                    
                    if ($terminationRecord) {
                        $application->employment_status = 'terminated';
                        $application->termination_date = $terminationRecord->decision_date;
                        $application->termination_reason = $terminationRecord->rejection_reason;
                    } else {
                        // Check current employment status
                        $employerName = $application->employer 
                            ? ($application->employer->company_name ?? trim($application->employer->first_name . ' ' . $application->employer->last_name))
                            : $application->company_name;
                        
                        if ($user->employment_status === 'employed' && 
                            $user->hired_by_company && 
                            $employerName && 
                            strcasecmp($user->hired_by_company, $employerName) === 0) {
                            $application->employment_status = 'currently_working';
                        } else {
                            $application->employment_status = 'resigned';
                        }
                    }
                }
                
                return $application;
            });

        $stats = [
            'total' => $applications->count(),
            'pending' => $applications->where('status', 'pending')->count(),
            'reviewing' => $applications->where('status', 'reviewing')->count(),
            'for_interview' => $applications->where('status', 'for_interview')->count(),
            'interviewed' => $applications->where('status', 'interviewed')->count(),
            'accepted' => $applications->where('status', 'accepted')->count(),
            'rejected' => $applications->where('status', 'rejected')->count(),
        ];

        // Employment history and current hire context for job seekers
        $employmentHistory = collect();
        $currentHire = null;
        if (($user->user_type ?? null) === 'job_seeker') {
            $employmentHistory = \App\Models\ApplicationHistory::where('job_seeker_id', $user->id)
                ->orderByDesc('decision_date')
                ->get();

            $currentHire = \App\Models\ApplicationHistory::where('job_seeker_id', $user->id)
                ->where('decision', 'hired')
                ->orderByDesc('decision_date')
                ->first();
        }

        return view('my-applications', compact('applications', 'stats', 'user', 'employmentHistory', 'currentHire'));
    }

    // Job seeker can withdraw/delete their own application
    public function destroy(Application $application)
    {
        $user = Auth::user();

        // Ensure the application belongs to the current user
        if ($application->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $application->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Application withdrawn successfully']);
        }

        return back()->with('success', 'Application withdrawn successfully');
    }
}
