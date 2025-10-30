<?php

namespace App\Http\Controllers;

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

        // Require essential employer settings before creating a job post
        if (empty($user->company_name) || empty($user->phone_number)) {
            return redirect()->route('settings')->withErrors([
                'phone_number' => 'Please complete your Employer Settings (Company Name and Contact Number) before posting a job.'
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

        // Safety check to ensure company contact details exist
        if (empty($user->company_name) || empty($user->phone_number)) {
            return redirect()->route('settings')->withErrors([
                'phone_number' => 'Please add your Company Name and Contact Number in Employer Settings before posting.'
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
            'company_name' => $user->company_name ?? ($user->first_name . ' ' . $user->last_name),
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

        $jobPostings = JobPosting::where('employer_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return view('employer.job-listings', compact('jobPostings', 'user'));
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
}
