<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployerApplicantsController extends Controller
{
    protected function ensureEmployer()
    {
        $user = Auth::user();
        if (!$user || ($user->user_type ?? null) !== 'employer') {
            abort(403, 'Unauthorized');
        }
        return $user;
    }

    // Show list of applications for employers to manage
    public function index(Request $request)
    {
        $this->ensureEmployer();

        $status = $request->query('status');

        $query = Application::query()->orderByDesc('created_at');

        // Optional: filter by status
        if ($status && in_array($status, ['pending','reviewing','accepted','rejected'])) {
            $query->where('status', $status);
        }

        // Show all for now since job postings aren't linked; in the future, filter by employer_id/company
        $applications = $query->paginate(10);

        // Stats
        $stats = [
            'total' => Application::count(),
            'pending' => Application::where('status', 'pending')->count(),
            'reviewing' => Application::where('status', 'reviewing')->count(),
            'accepted' => Application::where('status', 'accepted')->count(),
            'rejected' => Application::where('status', 'rejected')->count(),
        ];

        return view('employer.applicants', compact('applications', 'stats', 'status'));
    }

    // Update an application's status (reviewing/accepted/rejected)
    public function updateStatus(Request $request, Application $application)
    {
        $employer = $this->ensureEmployer();

        $request->validate([
            'status' => 'required|in:reviewing,accepted,rejected',
        ]);

        // Optionally claim the application to this employer if not yet set
        if (!$application->employer_id) {
            $application->employer_id = $employer->id;
            $application->save();
        }

        $application->updateStatus($request->input('status'), $employer->id);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Application status updated.');
    }
}
