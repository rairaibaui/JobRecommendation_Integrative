<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        // Block job applications if the job seeker does not have a verified resume
        if ($user->user_type === 'job_seeker' && (($user->resume_verification_status ?? 'pending') !== 'verified')) {
            return response()->json([
                'success' => false,
                'message' => 'You must have a verified resume before applying. Please upload and verify your resume in Settings.',
            ], 403);
        }

        // Check if job seeker is already employed
        if ($user->user_type === 'job_seeker' && $user->employment_status === 'employed') {
            return response()->json([
                'success' => false,
                'message' => 'You are currently employed by '.($user->hired_by_company ?? 'a company').'. You cannot apply for other jobs while employed.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'job_title' => 'required|string|max:255',
            'job_posting_id' => 'nullable|exists:job_postings,id',
            'job_data' => 'nullable|array',
            'resume_snapshot' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: '.$validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $app = new Application();
            $app->user_id = $user->id;
            $app->job_title = $request->job_title;
            $app->job_posting_id = $request->job_posting_id; // Link to job posting if provided
            $app->job_data = $request->job_data;
            $app->resume_snapshot = $request->resume_snapshot;
            
            // Set employer_id and company_name from job posting if available
            if ($request->job_posting_id) {
                $jobPosting = \App\Models\JobPosting::find($request->job_posting_id);
                if ($jobPosting) {
                    $app->employer_id = $jobPosting->employer_id;
                    // Get company name from employer
                    if ($jobPosting->employer) {
                        $app->company_name = $jobPosting->employer->company_name ?? 
                                           trim($jobPosting->employer->first_name . ' ' . $jobPosting->employer->last_name);
                    }
                }
            }
            
            // Fallback: Set company_name from job_data if not already set
            if (!$app->company_name && is_array($request->job_data)) {
                $app->company_name = $request->job_data['company_name'] ?? ($request->job_data['company'] ?? null);
            }
            
            // Status defaults to 'pending' via migration default; set timestamp for clarity
            $app->status_updated_at = now();
            $app->save();

            // Create notification for submission
            Notification::create([
                'user_id' => $user->id,
                'type' => 'application_submitted',
                'title' => 'Application Submitted',
                'message' => "You submitted an application for {$app->job_title}.",
                'data' => [
                    'application_id' => $app->id,
                    'job_title' => $app->job_title,
                    'company_name' => $app->company_name,
                    'status' => $app->status ?? 'pending',
                ],
            ]);

            // Notify employer about new application
            if ($app->job_posting_id) {
                $jobPosting = \App\Models\JobPosting::find($app->job_posting_id);
                if ($jobPosting && $jobPosting->employer_id) {
                    $applicantName = trim($user->first_name.' '.$user->last_name) ?: $user->email;

                    Notification::create([ 
                        'user_id' => $jobPosting->employer_id,
                        'type' => 'new_application',
                        'title' => 'New Application Received',
                        'message' => "{$applicantName} has applied for {$app->job_title}.",
                        'link' => route('employer.applicants'),
                        'data' => [
                            'application_id' => $app->id,
                            'job_title' => $app->job_title,
                            'applicant_name' => $applicantName,
                            'applicant_id' => $user->id,
                        ],
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully!',
                'application_id' => $app->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save application: '.$e->getMessage(),
            ], 500);
        }
    }
}
