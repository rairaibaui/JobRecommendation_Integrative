<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyApplicationsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $applications = $user->applications()
            ->with(['employer', 'jobPosting.employer'])
            ->latest()
            ->get();

        $stats = [
            'total' => $applications->count(),
            'pending' => $applications->where('status', 'pending')->count(),
            'reviewing' => $applications->where('status', 'reviewing')->count(),
            'for_interview' => $applications->where('status', 'for_interview')->count(),
            'interviewed' => $applications->where('status', 'interviewed')->count(),
            'accepted' => $applications->where('status', 'accepted')->count(),
            'rejected' => $applications->where('status', 'rejected')->count(),
        ];

        return view('my-applications', compact('applications', 'stats', 'user'));
    }

    // Job seeker can withdraw/delete their own application
    public function destroy(Application $application)
    {
        $user = Auth::user();

        // Ensure the application belongs to the current user
        if ($application->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $application->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Application withdrawn successfully']);
        }

        return back()->with('success', 'Application withdrawn successfully');
    }
}
