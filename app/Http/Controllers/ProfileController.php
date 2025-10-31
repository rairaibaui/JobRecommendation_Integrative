<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Update only profile picture.
     */
    public function update(Request $request)
    {
        try {
            /** @var User $user */
            $user = User::find(Auth::id());
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // Validate all profile fields
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'birthday' => 'nullable|date',
                'phone_number' => 'nullable|string|max:20',
                'education_level' => 'nullable|string|max:255',
                'skills' => 'nullable|string',
                'summary' => 'nullable|string',
                'education' => 'nullable|array',
                'experiences' => 'nullable|array',
                'languages' => 'nullable|string',
                'portfolio_links' => 'nullable|string',
                'availability' => 'nullable|string',
                'resume_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
                'years_of_experience' => 'nullable|numeric|min:0',
                'location' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'remove_picture' => 'nullable|boolean',
            ]);

            // Track what was updated
            $pictureUpdated = false;
            $detailsUpdated = false;

            // Update profile details using fillable fields
            $data = $request->only([
                'first_name',
                'last_name',
                'phone_number',
                'summary',
                'languages',
                'portfolio_links',
                'availability',
                'education_level',
                'skills',
                'years_of_experience',
                'location',
                'address',
            ]);

            // Debug: Log the birthday value
            Log::info('Profile Update - Birthday from request:', [
                'birthday_in_data' => $data['birthday'] ?? 'not set',
                'birthday_from_request' => $request->birthday ?? 'not set',
                'all_request_data' => $request->all(),
            ]);

            // Handle education array
            if ($request->has('education')) {
                $education = collect($request->education)->map(function ($item) {
                    return is_string($item) ? json_decode($item, true) : $item;
                })->toArray();
                $data['education'] = $education;
            }

            // Handle experiences array
            if ($request->has('experiences')) {
                $experiences = collect($request->experiences)->map(function ($item) {
                    return is_string($item) ? json_decode($item, true) : $item;
                })->toArray();
                $data['experiences'] = $experiences;
            }

            $user->fill($data);
            $user->education_level = $request->education_level;
            $user->skills = $request->skills;
            $user->years_of_experience = $request->years_of_experience;
            $user->location = $request->location;
            $user->address = $request->address;

            // Explicitly set date_of_birth to ensure it's saved
            if ($request->has('birthday') && $request->birthday) {
                $user->date_of_birth = $request->birthday;
                Log::info('Date of birth explicitly set:', ['date_of_birth' => $user->date_of_birth]);
            }

            // Always mark as updated since we're handling arrays
            $detailsUpdated = true;

            // Handle profile picture
            if ($request->has('remove_picture') && $request->remove_picture) {
                if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                $user->profile_picture = null;
                $pictureUpdated = true;
            }

            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                $path = $request->file('profile_picture')->store('profile_pictures', 'public');
                $user->profile_picture = $path;
                $pictureUpdated = true;
            }

            // Handle resume file upload
            if ($request->hasFile('resume_file')) {
                if ($user->resume_file && Storage::disk('public')->exists($user->resume_file)) {
                    Storage::disk('public')->delete($user->resume_file);
                }
                $rpath = $request->file('resume_file')->store('resumes', 'public');
                $user->resume_file = $rpath;
            }

            try {
                $user->save();

                // Prepare message
                if ($pictureUpdated && $detailsUpdated) {
                    $message = 'Profile details and picture updated successfully.';
                } elseif ($pictureUpdated) {
                    $message = 'Profile picture updated successfully.';
                } else {
                    $message = 'Profile details updated successfully.';
                }

                // If the request expects JSON (AJAX), return JSON response
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $message,
                        'user' => $user->fresh(),
                    ]);
                }

                return redirect()->back()->with('success', $message);
            } catch (\Exception $e) {
                Log::error('Profile update error: '.$e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save profile: '.$e->getMessage(),
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Profile update error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating profile: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Return resume/profile as JSON for the apply flow.
     */
    public function resume(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'birthday' => $user->birthday,
            'location' => $user->location,
            'address' => $user->address,
            'summary' => $user->summary,
            'education' => $user->education ?? [],
            'experiences' => $user->experiences ?? [],
            'skills' => $user->skills,
            'languages' => $user->languages,
            'portfolio_links' => $user->portfolio_links,
            'availability' => $user->availability,
            'profile_picture' => $user->profile_picture ? asset('storage/'.$user->profile_picture) : null,
            'employment_status' => $user->employment_status ?? 'unemployed',
            'hired_by_company' => $user->hired_by_company,
            'hired_date' => $user->hired_date ? $user->hired_date->format('Y-m-d') : null,
        ]);
    }

    public function changeEmail(Request $request)
    {
        /** @var User $user */
        $user = User::find(Auth::id());

        $request->validate([
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);

        $user->email = $request->email;
        $user->save();

        return redirect()->back()->with('success', 'Email updated successfully.');
    }
    // Keep other methods (changeEmail, deactivate, etc.) as is

    /**
     * Job seeker resigns from current employment.
     */
    public function resign(Request $request)
    {
        /** @var User $user */
        $user = User::find(Auth::id());
        if (!$user || ($user->user_type ?? null) !== 'job_seeker') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        if (($user->employment_status ?? 'unemployed') !== 'employed') {
            return redirect()->back()->with('success', 'Your status is already set to not employed.');
        }

        // Capture employer context from the last hire
        $lastHire = \App\Models\ApplicationHistory::where('job_seeker_id', $user->id)
            ->where('decision', 'hired')
            ->orderByDesc('decision_date')
            ->first();

        $companyNameBefore = $user->hired_by_company;

        // Update employment fields
        $user->employment_status = 'unemployed';
        $user->hired_by_company = null;
        $user->hired_date = null;
        $user->save();

        // Record resignation in history for traceability
        \App\Models\ApplicationHistory::create([
            'application_id' => $lastHire->application_id ?? null,
            'employer_id' => $lastHire->employer_id ?? null,
            'job_seeker_id' => $user->id,
            'job_posting_id' => $lastHire->job_posting_id ?? null,
            'job_title' => $lastHire->job_title ?? null,
            'company_name' => $companyNameBefore,
            'decision' => 'resigned',
            'rejection_reason' => $request->input('reason'),
            'applicant_snapshot' => $lastHire->applicant_snapshot ?? null,
            'job_snapshot' => $lastHire->job_snapshot ?? null,
            'decision_date' => now(),
        ]);

        // Notify the employer about the resignation
        if ($lastHire && $lastHire->employer_id) {
            $employerUser = User::find($lastHire->employer_id);
            if ($employerUser) {
                $userName = trim($user->first_name.' '.$user->last_name) ?: $user->email;
                $jobTitle = $lastHire->job_title ?? 'Unknown Position';

                \App\Models\Notification::create([
                    'user_id' => $employerUser->id,
                    'type' => 'employee_resigned',
                    'title' => 'Employee Resigned',
                    'message' => "{$userName} has resigned from the position of {$jobTitle}.",
                    'read' => false,
                ]);
            }
        }

        // Also notify the job seeker (self) that the resignation was recorded
        try {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'type' => 'resignation_recorded',
                'title' => 'Resignation Submitted',
                'message' => $companyNameBefore
                    ? "Your resignation from {$companyNameBefore} has been recorded. You are now set to Seeking Employment."
                    : 'Your resignation has been recorded. You are now set to Seeking Employment.',
                'data' => [
                    'company_name' => $companyNameBefore,
                    'reason' => $request->input('reason')
                ],
                'read' => false,
            ]);
        } catch (\Throwable $e) {
            // best effort; do not fail flow
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'You have resigned. Your status is now set to actively seeking.']);
        }

        return redirect()->back()->with('success', 'You have resigned. You can now apply for new job postings.');
    }

    /**
     * Update employer profile (company details).
     */
    public function updateEmployer(Request $request)
    {
        /** @var User $user */
        $user = User::find(Auth::id());
        if (!$user || ($user->user_type ?? null) !== 'employer') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'company_name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'business_permit' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'remove_picture' => 'nullable|boolean',
        ]);

        $user->company_name = $request->company_name;
        if ($request->filled('first_name')) {
            $user->first_name = $request->first_name;
        }
        if ($request->filled('last_name')) {
            $user->last_name = $request->last_name;
        }
        if ($request->filled('job_title')) {
            $user->job_title = $request->job_title;
        }
        $user->phone_number = $request->phone_number; // required
        $user->address = $request->address;

        // Handle profile picture removal
        if ($request->has('remove_picture') && $request->remove_picture) {
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $user->profile_picture = null;
        }

        // Company logo/profile picture
        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        // Business permit upload
        if ($request->hasFile('business_permit')) {
            if ($user->business_permit_path && Storage::disk('public')->exists($user->business_permit_path)) {
                Storage::disk('public')->delete($user->business_permit_path);
            }
            $bpath = $request->file('business_permit')->store('business_permits', 'public');
            $user->business_permit_path = $bpath;
        }

        $user->save();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Employer profile updated successfully.']);
        }

        return redirect()->route('settings')->with('success', 'Employer profile updated successfully.');
    }
}
