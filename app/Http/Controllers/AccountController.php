<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\AuditLog;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    /**
     * Delete user account and all associated data.
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();

        // Log the deletion attempt
        Log::info('Account deletion initiated', [
            'user_id' => $user->id,
            'user_type' => $user->user_type,
            'email' => $user->email,
            'ip' => $request->ip(),
        ]);

        try {
            DB::beginTransaction();

            // Create audit log before deletion
            AuditLog::create([
                'user_id' => $user->id,
                'event' => 'account_deleted',
                'title' => 'Account Deleted',
                'message' => "{$user->user_type} account deleted: {$user->email}".
                            ($user->user_type === 'employer' ? " ({$user->company_name})" : " ({$user->name})"),
                'data' => json_encode([
                    'user_type' => $user->user_type,
                    'email' => $user->email,
                    'name' => $user->name,
                    'company_name' => $user->company_name ?? null,
                    'deleted_at' => now()->toDateTimeString(),
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            if ($user->user_type === 'employer') {
                // Delete employer-specific data
                $this->deleteEmployerData($user);
            } elseif ($user->user_type === 'job_seeker') {
                // Delete job seeker-specific data
                $this->deleteJobSeekerData($user);
            }

            // Delete uploaded files
            $this->deleteUserFiles($user);

            // Logout user before deletion
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Finally, delete the user account
            $user->delete();

            DB::commit();

            Log::info('Account successfully deleted', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return redirect()->route('login')
                ->with('success', 'Your account has been permanently deleted. We\'re sorry to see you go.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Account deletion failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Failed to delete account. Please contact support if this persists.');
        }
    }

    /**
     * Delete employer-specific data.
     */
    private function deleteEmployerData(User $user)
    {
        // Get all job postings
        $jobPostings = JobPosting::where('employer_id', $user->id)->get();

        foreach ($jobPostings as $job) {
            // Delete all applications for this job
            Application::where('job_posting_id', $job->id)->delete();

            // Delete the job posting
            $job->delete();
        }

        // Delete document validations (business permits)
        $user->documentValidations()->delete();

        Log::info('Employer data deleted', [
            'user_id' => $user->id,
            'job_postings_deleted' => $jobPostings->count(),
        ]);
    }

    /**
     * Delete job seeker-specific data.
     */
    private function deleteJobSeekerData(User $user)
    {
        // Delete all job applications
        $applicationsCount = Application::where('user_id', $user->id)->count();
        Application::where('user_id', $user->id)->delete();

        // Delete bookmarks
        $user->bookmarks()->delete();

        Log::info('Job seeker data deleted', [
            'user_id' => $user->id,
            'applications_deleted' => $applicationsCount,
        ]);
    }

    /**
     * Delete user's uploaded files.
     */
    private function deleteUserFiles(User $user)
    {
        $deletedFiles = [];

        // Delete profile picture
        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
            $deletedFiles[] = 'profile_picture';
        }

        // Delete resume (for job seekers)
        if ($user->resume_file && Storage::disk('public')->exists($user->resume_file)) {
            Storage::disk('public')->delete($user->resume_file);
            $deletedFiles[] = 'resume_file';
        }

        // Delete business permit (for employers)
        if ($user->business_permit_path && Storage::disk('public')->exists($user->business_permit_path)) {
            Storage::disk('public')->delete($user->business_permit_path);
            $deletedFiles[] = 'business_permit';
        }

        if (!empty($deletedFiles)) {
            Log::info('User files deleted', [
                'user_id' => $user->id,
                'files' => $deletedFiles,
            ]);
        }
    }
}
