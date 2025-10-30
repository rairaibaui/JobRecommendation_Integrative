<?php

namespace App\Http\Controllers;

use App\Models\ApplicationHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployerEmployeesController extends Controller
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

        // List of hired applicants (accepted) for this employer
        $employees = ApplicationHistory::where('employer_id', $employer->id)
            ->where('decision', 'hired')
            ->orderByDesc('decision_date')
            ->get();

        $stats = [
            'total' => $employees->count(),
        ];

        return view('employer.employees', compact('employees', 'stats'));
    }
}
