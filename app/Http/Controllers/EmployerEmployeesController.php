<?php

namespace App\Http\Controllers;

use App\Models\ApplicationHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployerEmployeesController extends Controller
{
    protected function ensureEmployer()
    {
        $user = Auth::user();
        if (!$user || ($user->user_type ?? null) !== 'employer') {
            abort(403, 'Unauthorized');
        }
        return $user;
    }

    public function index(Request $request)
    {
        $employer = $this->ensureEmployer();

        // List of hired applicants (accepted) for this employer
        $employees = ApplicationHistory::where('employer_id', $employer->id)
            ->where('decision', 'hired')
            ->with(['jobSeeker'])
            ->orderByDesc('decision_date')
            ->get();

        $stats = [
            'total' => $employees->count(),
        ];

        return view('employer.employees', [
            'employees' => $employees,
            'stats' => $stats,
            'user' => $employer
        ]);
    }

    public function terminate(Request $request, User $user)
    {
        $employer = $this->ensureEmployer();

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        // Only allow terminating if the user is currently employed and was hired by this employer (by name match)
        if (($user->user_type ?? null) !== 'job_seeker') {
            abort(400, 'Invalid user');
        }

        if (($user->employment_status ?? 'unemployed') !== 'employed') {
            return back()->with('success', 'This job seeker is not currently employed.');
        }

        $employerName = $employer->company_name ?? trim(($employer->first_name.' '.$employer->last_name));
        if ($user->hired_by_company && $employerName && strcasecmp($user->hired_by_company, $employerName) !== 0) {
            abort(403, 'You can only terminate employees you hired.');
        }

        // Find the last hire record to attach context
        $lastHire = ApplicationHistory::where('job_seeker_id', $user->id)
            ->where('employer_id', $employer->id)
            ->where('decision', 'hired')
            ->orderByDesc('decision_date')
            ->first();

        // Update employment fields
        $companyNameBefore = $user->hired_by_company;
        $user->employment_status = 'unemployed';
        $user->hired_by_company = null;
        $user->hired_date = null;
        $user->save();

        // Record termination in history
        ApplicationHistory::create([
            'application_id' => $lastHire->application_id ?? null,
            'employer_id' => $employer->id,
            'job_seeker_id' => $user->id,
            'job_posting_id' => $lastHire->job_posting_id ?? null,
            'job_title' => $lastHire->job_title ?? null,
            'company_name' => $companyNameBefore ?: $employerName,
            'decision' => 'terminated',
            'rejection_reason' => $request->input('reason'),
            'applicant_snapshot' => $lastHire->applicant_snapshot ?? null,
            'job_snapshot' => $lastHire->job_snapshot ?? null,
            'decision_date' => now(),
        ]);

        // Notifications: inform both the job seeker and the employer
        try {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'type' => 'employment_terminated',
                'title' => 'Employment Terminated',
                'message' => "Your employment with {$employerName} has been terminated." . ($request->filled('reason') ? " Reason: " . $request->input('reason') : ''),
                'data' => [
                    'employer_name' => $employerName,
                    'reason' => $request->input('reason'),
                ],
                'read' => false,
            ]);

            \App\Models\Notification::create([
                'user_id' => $employer->id,
                'type' => 'employee_terminated',
                'title' => 'Employee Terminated',
                'message' => "You have terminated employment for {$user->first_name} {$user->last_name}.",
                'data' => [
                    'job_seeker_id' => $user->id,
                    'job_seeker_name' => trim(($user->first_name.' '.$user->last_name)),
                    'reason' => $request->input('reason'),
                ],
                'read' => false,
            ]);
        } catch (\Throwable $e) {
            // Silent fail: do not block termination on notification errors
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Employee has been terminated.']);
        }
        return back()->with('success', 'Employee has been terminated.');
    }
}
