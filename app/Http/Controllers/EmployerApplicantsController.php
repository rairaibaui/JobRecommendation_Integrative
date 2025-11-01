<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployerApplicantsController extends Controller
{
    protected function ensureEmployer()
    {
        $user = Auth::user();
        if (!$user || ($user->user_type ?? null) !== 'employer') {
            abort(403, 'Unauthorized');
        }
        return $user;
    }

    // Show list of applications for employers to manage
    public function index(Request $request)
    {
        $employer = $this->ensureEmployer();

        $status = $request->query('status');

        // Get employer's job postings with their applications
        $jobPostingsQuery = \App\Models\JobPosting::where('employer_id', $employer->id)
            ->withCount(['applications' => function($query) use ($status) {
                // Exclude hired/accepted applications from Applicants page
                $query->where('status', '!=', 'accepted');
                if ($status && in_array($status, ['pending','reviewing','for_interview','interviewed','accepted','rejected'])) {
                    $query->where('status', $status);
                }
            }])
            ->with(['applications' => function($query) use ($status) {
                // Exclude hired/accepted applications from Applicants page
                $query->where('status', '!=', 'accepted');
                $query->orderByDesc('created_at');
                if ($status && in_array($status, ['pending','reviewing','for_interview','interviewed','accepted','rejected'])) {
                    $query->where('status', $status);
                }
            }])
            ->orderByDesc('created_at');

        $jobPostings = $jobPostingsQuery->get();

        // Calculate stats across all applications for this employer's jobs
        $allApplications = Application::whereIn('job_posting_id', 
            \App\Models\JobPosting::where('employer_id', $employer->id)->pluck('id')
        );

        $stats = [
            'total' => $allApplications->count(),
            'pending' => (clone $allApplications)->where('status', 'pending')->count(),
            'reviewing' => (clone $allApplications)->where('status', 'reviewing')->count(),
                'for_interview' => (clone $allApplications)->where('status', 'for_interview')->count(),
                'interviewed' => (clone $allApplications)->where('status', 'interviewed')->count(),
            'accepted' => (clone $allApplications)->where('status', 'accepted')->count(),
            'rejected' => (clone $allApplications)->where('status', 'rejected')->count(),
        ];

        return view('employer.applicants', [
            'jobPostings' => $jobPostings,
            'stats' => $stats,
            'status' => $status,
            'user' => $employer
        ]);
    }

    // Update an application's status (reviewing/accepted/rejected)
    public function updateStatus(Request $request, Application $application)
    {
        $employer = $this->ensureEmployer();

        $request->validate([
                'status' => 'required|in:reviewing,for_interview,interviewed,accepted,rejected',
            'rejection_reason' => 'nullable|string|max:500',
                'interview_date' => 'nullable|date',
                'interview_location' => 'nullable|string|max:255',
                'interview_notes' => 'nullable|string|max:1000',
        ]);

        // Optionally claim the application to this employer if not yet set
        if (!$application->employer_id) {
            $application->employer_id = $employer->id;
            $application->save();
        }

        $newStatus = $request->input('status');
        $rejectionReason = $request->input('rejection_reason');

            // If setting interview, save interview details
            if ($newStatus === 'for_interview' || $newStatus === 'interviewed') {
                if ($request->has('interview_date')) {
                    $application->interview_date = $request->input('interview_date');
                }
                if ($request->has('interview_location')) {
                    $application->interview_location = $request->input('interview_location');
                }
                if ($request->has('interview_notes')) {
                    $application->interview_notes = $request->input('interview_notes');
                }
                $application->save();
                
                // Send notification to job seeker when interview is scheduled
                if ($newStatus === 'for_interview' && $application->interview_date) {
                    $companyName = $employer->company_name ?? ($employer->first_name . ' ' . $employer->last_name);
                    $interviewDate = \Carbon\Carbon::parse($application->interview_date)->format('l, F j, Y \a\t g:i A');
                    
                    \App\Models\Notification::create([
                        'user_id' => $application->user_id,
                        'type' => 'interview_scheduled',
                        'title' => 'Interview Scheduled!',
                        'message' => "You have an interview scheduled with {$companyName} for the position of {$application->job_title} on {$interviewDate}.",
                        'data' => [
                            'application_id' => $application->id,
                            'job_title' => $application->job_title,
                            'company_name' => $companyName,
                            'interview_date' => $application->interview_date,
                            'interview_location' => $application->interview_location,
                            'interview_notes' => $application->interview_notes,
                        ],
                        'read' => false,
                    ]);
                }
            }

        // If hiring (accepting) the applicant, update their employment status
        if ($newStatus === 'accepted') {
            $jobSeeker = \App\Models\User::find($application->user_id);
            if ($jobSeeker && $jobSeeker->user_type === 'job_seeker') {
                $jobSeeker->employment_status = 'employed';
                $jobSeeker->hired_by_company = $employer->company_name ?? $employer->first_name . ' ' . $employer->last_name;
                $jobSeeker->hired_date = now();
                $jobSeeker->save();
            }
            
            // Send acceptance notification
            $companyName = $employer->company_name ?? ($employer->first_name . ' ' . $employer->last_name);
            \App\Models\Notification::create([
                'user_id' => $application->user_id,
                'type' => 'application_accepted',
                'title' => 'Congratulations! You\'re Hired! 🎉',
                'message' => "Congratulations! {$companyName} has accepted your application for the position of {$application->job_title}. Welcome to the team!",
                'data' => [
                    'application_id' => $application->id,
                    'job_title' => $application->job_title,
                    'company_name' => $companyName,
                ],
                'read' => false,
            ]);
        }
        
        // Send rejection notification
        if ($newStatus === 'rejected') {
            $companyName = $employer->company_name ?? ($employer->first_name . ' ' . $employer->last_name);
            \App\Models\Notification::create([
                'user_id' => $application->user_id,
                'type' => 'application_rejected',
                'title' => 'Application Update',
                'message' => "Thank you for your interest in the {$application->job_title} position at {$companyName}. Unfortunately, we have decided to move forward with other candidates at this time.",
                'data' => [
                    'application_id' => $application->id,
                    'job_title' => $application->job_title,
                    'company_name' => $companyName,
                    'rejection_reason' => $rejectionReason,
                ],
                'read' => false,
            ]);
        }

        // Create history record for hired or rejected applicants
        if (in_array($newStatus, ['accepted', 'rejected'])) {
            \App\Models\ApplicationHistory::create([
                'application_id' => $application->id,
                'employer_id' => $employer->id,
                'job_seeker_id' => $application->user_id,
                'job_posting_id' => $application->job_posting_id,
                'job_title' => $application->job_title,
                'company_name' => $application->company_name,
                'decision' => $newStatus === 'accepted' ? 'hired' : 'rejected',
                'rejection_reason' => $newStatus === 'rejected' ? $rejectionReason : null,
                'applicant_snapshot' => $application->resume_snapshot,
                'job_snapshot' => $application->job_data,
                'decision_date' => now(),
            ]);
        }

        $application->updateStatus($newStatus, $employer->id);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Application status updated.');
    }

    // Delete an application
    public function destroy(Application $application)
    {
        $this->ensureEmployer();

        $application->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Application deleted successfully']);
        }

        return back()->with('success', 'Application deleted successfully');
    }

    // View full applicant profile (for employers)
    public function showApplicant(Request $request, Application $application)
    {
        $employer = $this->ensureEmployer();

        // Authorization: application must belong to a job posting owned by this employer
        $jobPosting = \App\Models\JobPosting::find($application->job_posting_id);
        if (!$jobPosting || (int)$jobPosting->employer_id !== (int)$employer->id) {
            abort(403, 'Unauthorized to view this applicant.');
        }

        $applicant = \App\Models\User::find($application->user_id);

        return view('employer.applicant-profile', [
            'application' => $application,
            'applicant' => $applicant,
            'jobPosting' => $jobPosting,
            'user' => $employer
        ]);
    }
}
