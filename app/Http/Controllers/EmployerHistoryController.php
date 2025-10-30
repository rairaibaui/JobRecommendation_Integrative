<?php

namespace App\Http\Controllers;

use App\Models\ApplicationHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployerHistoryController extends Controller
{
    protected function ensureEmployer()
    {
        $user = Auth::user();
        if (!$user || ($user->user_type ?? null) !== 'employer') {
            abort(403, 'Unauthorized');
        }
        return $user;
    }

    public function index(Request $request)
    {
        $employer = $this->ensureEmployer();

    $decision = $request->query('decision'); // 'hired' | 'rejected' | 'terminated' | 'resigned'

        $historyQuery = ApplicationHistory::where('employer_id', $employer->id)
            ->with(['jobSeeker', 'jobPosting'])
            ->orderByDesc('decision_date');

        if ($decision && in_array($decision, ['hired', 'rejected', 'terminated', 'resigned'])) {
            $historyQuery->where('decision', $decision);
        }

        $history = $historyQuery->paginate(20);

        // Stats
        $stats = [
            'total' => ApplicationHistory::where('employer_id', $employer->id)->count(),
            'hired' => ApplicationHistory::where('employer_id', $employer->id)->hired()->count(),
            'rejected' => ApplicationHistory::where('employer_id', $employer->id)->rejected()->count(),
            'terminated' => ApplicationHistory::where('employer_id', $employer->id)->where('decision', 'terminated')->count(),
            'resigned' => ApplicationHistory::where('employer_id', $employer->id)->where('decision', 'resigned')->count(),
        ];

        return view('employer.history', compact('history', 'stats', 'decision'));
    }
}

