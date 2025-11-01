<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class WorkHistoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user || ($user->user_type ?? null) !== 'job_seeker') {
            abort(403, 'Unauthorized');
        }

        $employmentHistory = \App\Models\ApplicationHistory::where('job_seeker_id', $user->id)
            ->orderByDesc('decision_date')
            ->get();

        return view('work-history', compact('employmentHistory', 'user'));
    }
}
