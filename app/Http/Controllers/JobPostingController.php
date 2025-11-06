<?php

namespace App\Http\Controllers;

use App\Models\DocumentValidation;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobPostingController extends Controller
{
    // Show form to create a new job posting
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = \App\Models\User::find(Auth::id());
        if ($user->user_type !== 'employer') {
            abort(403, 'Only employers can post jobs');
        }

        // Check if business permit is validated
        $validation = DocumentValidation::where('user_id', $user->id)
            ->where('document_type', 'business_permit')
            ->orderByDesc('created_at')
            ->first();

        if (!$validation || !$validation->is_valid || $validation->validation_status !== 'approved') {
            $message = 'Your business permit is pending verification. ';

            if (!$validation) {
                $message .= 'Please upload your business permit in your profile settings.';
            } elseif ($validation->validation_status === 'rejected') {
                $message .= 'Your business permit was rejected. Please upload a valid business permit.';
            } else {
                $message .= 'Please wait for verification to complete.';
            }

            return redirect()->route('employer.dashboard')->withErrors([
                'validation' => $message,
            ]);
        }

        // Check if business permit has expired
        if ($validation->permit_expiry_date && $validation->permit_expiry_date < now()) {
            return redirect()->route('employer.dashboard')->withErrors([
                'validation' => 'Your business permit has expired. Please upload a new valid permit to continue posting jobs.',
            ]);
        }

        // Enforce posting only for the approved business name
        $approvedCompanyName = $validation->ai_analysis['approved_company_name'] ?? null;
        if ($approvedCompanyName && $user->company_name !== $approvedCompanyName) {
            return redirect()->route('employer.dashboard')->withErrors([
                'validation' => "Your account is approved for '{$approvedCompanyName}'. You cannot post jobs under a different business name. To operate another business, register a separate employer account with its own permit.",
            ]);
        }

        // Require essential employer settings before creating a job post
        if (empty($user->company_name) || empty($user->phone_number) || empty($user->address)) {
            return redirect()->route('settings')->withErrors([
                'validation' => 'Please complete your Employer Settings (Company Name, Contact Number, and Full Company Address) before posting a job.',
            ]);
        }

        return view('employer.job-create', compact('user'));
    }

    // Store a new job posting
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = \App\Models\User::find(Auth::id());
        if ($user->user_type !== 'employer') {
            abort(403, 'Only employers can post jobs');
        }

        // Check if business permit is validated
        $validation = DocumentValidation::where('user_id', $user->id)
            ->where('document_type', 'business_permit')
            ->orderByDesc('created_at')
            ->first();

        if (!$validation || !$validation->is_valid || $validation->validation_status !== 'approved') {
            $message = 'Your business permit is pending verification. ';

            if (!$validation) {
                $message .= 'Please upload your business permit in your profile settings.';
            } elseif ($validation->validation_status === 'rejected') {
                $message .= 'Your business permit was rejected. Please upload a valid business permit.';
            } else {
                $message .= 'Please wait for verification to complete.';
            }

            return redirect()->route('employer.dashboard')->withErrors([
                'validation' => $message,
            ]);
        }

        // Check if business permit has expired
        if ($validation->permit_expiry_date && $validation->permit_expiry_date < now()) {
            return redirect()->route('employer.dashboard')->withErrors([
                'validation' => 'Your business permit has expired. Please upload a new valid permit to continue posting jobs.',
            ]);
        }

        // Enforce posting only for the approved business name
        $approvedCompanyName = $validation->ai_analysis['approved_company_name'] ?? null;
        if ($approvedCompanyName && $user->company_name !== $approvedCompanyName) {
            return redirect()->route('employer.dashboard')->withErrors([
                'validation' => "Your account is approved for '{$approvedCompanyName}'. You cannot post jobs under a different business name. To operate another business, register a separate employer account with its own permit.",
            ]);
        }

        // Safety check to ensure company contact details exist
        if (empty($user->company_name) || empty($user->phone_number) || empty($user->address)) {
            return redirect()->route('settings')->withErrors([
                'validation' => 'Please complete your Company Name, Contact Number, and Full Address in Employer Settings before posting.',
            ]);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'type' => 'required|string|in:Full-time,Part-time,Contract,Internship,Freelance',
            'salary' => 'nullable|string|max:255',
            'description' => 'required|string',
            'skills' => 'nullable|string', // Comma-separated skills
            'status' => 'nullable|in:active,draft',
        ]);

        // Convert comma-separated skills to array
        $skills = [];
        if (!empty($validated['skills'])) {
            $skills = array_map('trim', explode(',', $validated['skills']));
        }

        $jobPosting = JobPosting::create([
            'employer_id' => $user->id,
            'company_name' => $user->company_name ?? ($user->first_name.' '.$user->last_name),
            'title' => $validated['title'],
            'location' => $validated['location'] ?? 'Mandaluyong',
            'type' => $validated['type'],
            'salary' => $validated['salary'] ?? 'Negotiable',
            'description' => $validated['description'],
            'skills' => $skills,
            'status' => $validated['status'] ?? 'active',
        ]);

        return redirect()->route('employer.dashboard')->with('success', 'Job posted successfully!');
    }

    // Show employer's job postings
    public function index()
    {
        $user = Auth::user();
        if ($user->user_type !== 'employer') {
            abort(403);
        }

        // Fetch latest business permit validation for badge display
        $validation = DocumentValidation::where('user_id', $user->id)
            ->where('document_type', 'business_permit')
            ->orderByDesc('created_at')
            ->first();

        $jobPostings = JobPosting::where('employer_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return view('employer.job-listings', compact('jobPostings', 'user', 'validation'));
    }

    // Delete a job posting
    public function destroy(JobPosting $jobPosting)
    {
        $user = Auth::user();
        if ($jobPosting->employer_id !== $user->id) {
            abort(403);
        }

        $jobPosting->delete();

        return back()->with('success', 'Job posting deleted successfully');
    }

    // Show edit form
    public function edit(JobPosting $jobPosting)
    {
        $user = Auth::user();
        if ($jobPosting->employer_id !== $user->id) {
            abort(403);
        }

        return view('employer.job-edit', compact('jobPosting', 'user'));
    }

    // Update job posting
    public function update(Request $request, JobPosting $jobPosting)
    {
        $user = Auth::user();
        if ($jobPosting->employer_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'type' => 'required|string|in:Full-time,Part-time,Contract,Internship,Freelance',
            'salary' => 'nullable|string|max:255',
            'description' => 'required|string',
            'skills' => 'nullable|string', // Comma-separated skills
            'status' => 'nullable|in:active,draft,closed',
        ]);

        // Convert comma-separated skills to array
        $skills = [];
        if (!empty($validated['skills'])) {
            $skills = array_map('trim', explode(',', $validated['skills']));
        }

        $jobPosting->update([
            'title' => $validated['title'],
            'location' => $validated['location'] ?? 'Mandaluyong',
            'type' => $validated['type'],
            'salary' => $validated['salary'] ?? 'Negotiable',
            'description' => $validated['description'],
            'skills' => $skills,
            'status' => $validated['status'] ?? $jobPosting->status,
        ]);

        return redirect()->route('employer.jobs')->with('success', 'Job posting updated successfully!');
    }

    // Update job status (close/reopen job)
    public function updateStatus(Request $request, JobPosting $jobPosting)
    {
        $user = Auth::user();
        if ($jobPosting->employer_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:active,closed,draft',
        ]);

        $jobPosting->update(['status' => $validated['status']]);

        $statusMessages = [
            'closed' => 'Job posting closed - no longer accepting applications',
            'active' => 'Job posting is now active and accepting applications',
            'draft' => 'Job posting saved as draft',
        ];

        return back()->with('success', $statusMessages[$validated['status']] ?? 'Job status updated');
    }
}
