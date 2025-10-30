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
            // Set optional company_name if available in job_data
            if (is_array($request->job_data)) {
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
