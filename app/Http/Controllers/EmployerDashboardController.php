<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployerDashboardController extends Controller
{
    public function index()
    {
        // Placeholder data for employer dashboard - can be replaced with actual job postings from database
        $jobPostings = [
            [
                'id' => 1,
                'title' => 'Senior Developer',
                'department' => 'Engineering',
                'type' => 'Full-Time',
                'salary' => 'PHP 80,000/month',
                'posted_date' => '2025-10-15',
                'applications' => 12,
                'status' => 'Active'
            ],
            [
                'id' => 2,
                'title' => 'Marketing Manager',
                'department' => 'Marketing',
                'type' => 'Full-Time',
                'salary' => 'PHP 60,000/month',
                'posted_date' => '2025-10-20',
                'applications' => 8,
                'status' => 'Active'
            ],
            [
                'id' => 3,
                'title' => 'Sales Representative',
                'department' => 'Sales',
                'type' => 'Part-Time',
                'salary' => 'PHP 25,000/month',
                'posted_date' => '2025-10-25',
                'applications' => 5,
                'status' => 'Active'
            ],
        ];

        $user = Auth::user();
        
        return view('employer.dashboard', compact('jobPostings', 'user'));
    }
}
