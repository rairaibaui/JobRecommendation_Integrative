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
        $employerName = $employer->company_name ?? trim(($employer->first_name.' '.$employer->last_name));

        // Get all hire record IDs that have been terminated (not just job seeker IDs)
        // This allows the same person to have multiple employment records
        $terminatedHireRecordIds = ApplicationHistory::where('employer_id', $employer->id)
            ->where('decision', 'terminated')
            ->get()
            ->map(function ($termination) use ($employer) {
                // Find the matching hire record that this termination refers to
                $hireRecord = ApplicationHistory::where('employer_id', $employer->id)
                    ->where('job_seeker_id', $termination->job_seeker_id)
                    ->where('decision', 'hired')
                    ->where('decision_date', '<=', $termination->decision_date)
                    ->orderByDesc('decision_date')
                    ->first();
                
                return $hireRecord ? $hireRecord->id : null;
            })
            ->filter()
            ->toArray();

        // List of ALL hire records (each hire is a separate employment/position)
        // Show only those that:
        // 1. Haven't been terminated (this specific hire record)
        // 2. Employee is still employed AND working for this employer (for their CURRENT position)
        $allHireRecords = ApplicationHistory::where('employer_id', $employer->id)
            ->where('decision', 'hired')
            ->whereNotIn('id', $terminatedHireRecordIds)
            ->with(['jobSeeker'])
            ->orderByDesc('decision_date')
            ->get();

        // Filter to only show CURRENT employment records
        // A hire record is "current" only if it's the MOST RECENT hire for that person
        $employees = $allHireRecords->filter(function ($record) use ($employerName, $employer) {
            $jobSeeker = $record->jobSeeker;
            if (!$jobSeeker) return false;
            
            // First, employee must be currently employed with this employer
            if ($jobSeeker->employment_status !== 'employed' ||
                !$jobSeeker->hired_by_company ||
                strcasecmp($jobSeeker->hired_by_company, $employerName) !== 0) {
                return false;
            }
            
            // Second, this must be the MOST RECENT hire record for this person
            // If there's a newer hire, this old position should be in resigned/terminated
            $isLatestHire = !ApplicationHistory::where('employer_id', $employer->id)
                ->where('job_seeker_id', $record->job_seeker_id)
                ->where('decision', 'hired')
                ->where('decision_date', '>', $record->decision_date)
                ->exists();
            
            return $isLatestHire;
        })
        ->values(); // Re-index the collection

        // Get IDs of hire records that are currently active (in accepted employees)
        $activeHireRecordIds = $employees->pluck('id')->toArray();

        // Get resigned records (hire records where employee is no longer with this employer)
        // IMPORTANT: Only include records that are NOT currently active AND NOT terminated
        $resignedHireRecords = $allHireRecords->filter(function ($record) use ($employerName, $activeHireRecordIds, $employer) {
            $jobSeeker = $record->jobSeeker;
            if (!$jobSeeker) return false;
            
            // Skip if this is currently an active employment
            if (in_array($record->id, $activeHireRecordIds)) return false;
            
            // Check if there's a NEWER hire record for this same person
            // If so, this old record should stay as resigned, not move to active
            $newerHireExists = ApplicationHistory::where('employer_id', $employer->id)
                ->where('job_seeker_id', $record->job_seeker_id)
                ->where('decision', 'hired')
                ->where('decision_date', '>', $record->decision_date)
                ->exists();
            
            // If a newer hire exists, this old position is truly resigned/ended
            if ($newerHireExists) return true;
            
            // Otherwise, check if employee left this position
            // Include if unemployed OR employed elsewhere
            return ($jobSeeker->employment_status !== 'employed') ||
                   (!$jobSeeker->hired_by_company) ||
                   (strcasecmp($jobSeeker->hired_by_company, $employerName) !== 0);
        })
        ->map(function ($resignation) {
            $resignation->hire_date = $resignation->decision_date;
            $resignation->is_terminated = false;
            $resignation->rejection_reason = 'Employee resigned';
            return $resignation;
        });

        // List of terminated employment records
        $terminatedRecords = ApplicationHistory::where('employer_id', $employer->id)
            ->where('decision', 'terminated')
            ->with(['jobSeeker'])
            ->orderByDesc('decision_date')
            ->get()
            ->map(function ($termination) use ($employer) {
                // Find the original hire date for this specific employment
                $hireRecord = ApplicationHistory::where('employer_id', $employer->id)
                    ->where('job_seeker_id', $termination->job_seeker_id)
                    ->where('decision', 'hired')
                    ->where('decision_date', '<=', $termination->decision_date)
                    ->orderByDesc('decision_date')
                    ->first();
                
                $termination->hire_date = $hireRecord ? $hireRecord->decision_date : null;
                $termination->is_terminated = true;
                return $termination;
            });

        // Merge terminated and resigned employees
        $resignedEmployees = $terminatedRecords->merge($resignedHireRecords)
            ->sortByDesc('decision_date')
            ->values();

        // Count UNIQUE employees (not total records)
        $stats = [
            'total' => $employees->pluck('job_seeker_id')->unique()->count(),
            'resigned' => $resignedEmployees->pluck('job_seeker_id')->unique()->count(),
        ];

        return view('employer.employees', [
            'employees' => $employees,
            'resignedEmployees' => $resignedEmployees,
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
