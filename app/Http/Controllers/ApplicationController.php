<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Application;

class ApplicationController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'job_title' => 'required|string|max:255',
            'job_data' => 'nullable|array',
            'resume_snapshot' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'message' => 'Validation failed: ' . $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $app = new Application();
            $app->user_id = $user->id;
            $app->job_title = $request->job_title;
            $app->job_data = $request->job_data;
            $app->resume_snapshot = $request->resume_snapshot;
            $app->save();

            return response()->json([
                'success' => true, 
                'message' => 'Application submitted successfully!',
                'application_id' => $app->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to save application: ' . $e->getMessage()
            ], 500);
        }
    }
}
