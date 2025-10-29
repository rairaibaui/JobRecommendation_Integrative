<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $jobs = [
            [
                'id' => 1,
                'title' => 'Frontend Developer',
                'company' => 'Tech Innovations Inc.',
                'location' => 'Mandaluyong City',
                'type' => 'Full-Time',
                'salary' => 'PHP 2,500/day',
                'description' => 'Work with a talented team to build responsive web apps.',
                'skills' => ['HTML', 'CSS', 'JavaScript', 'Vue.js'],
                'apply_url' => '#'
            ],
            [
                'id' => 2,
                'title' => 'Backend Developer',
                'company' => 'NextGen Solutions',
                'location' => 'Makati City',
                'type' => 'Full-Time',
                'salary' => 'PHP 3,000/day',
                'description' => 'Develop and maintain APIs using Laravel framework.',
                'skills' => ['PHP', 'Laravel', 'MySQL'],
                'apply_url' => '#'
            ],
            [
                'id' => 3,
                'title' => 'UI/UX Designer',
                'company' => 'Creative Minds Studio',
                'location' => 'Quezon City',
                'type' => 'Part-Time',
                'salary' => 'PHP 1,800/day',
                'description' => 'Design beautiful user interfaces and improve user experience.',
                'skills' => ['Figma', 'Adobe XD', 'Wireframing'],
                'apply_url' => '#'
            ],
        ];

        // determine which jobs are bookmarked by current user
        $bookmarkedTitles = [];
        if (Auth::check()) {
            $bookmarkedTitles = Auth::user()->bookmarks()->pluck('title')->toArray();
        }

        return view('dashboard', compact('jobs', 'bookmarkedTitles'));
    }

    public function recommendation()
    {
        $jobs = [
            [
                'title' => 'Cashier',
                'location' => 'Mandaluyong City',
                'type' => 'Full-time',
                'salary' => 'Php 645/day',
                'description' => 'Handle cash transactions, provide customer service, and maintain a clean and organized checkout area.',
                'skills' => ['Customer Service', 'Cash Handling', 'Basic Math'],
            ],
            [
                'title' => 'Sales Associate',
                'location' => 'Mandaluyong City',
                'type' => 'Full-time',
                'salary' => 'Php 645/day',
                'description' => 'Assist customers, process sales transactions, and maintain store appearance.',
                'skills' => ['Customer Service', 'Cash Handling', 'Sales'],
            ],
        ];

        return view('recommendation', compact('jobs'));
    }

    public function bookmarks()
    {
        $bookmarkedJobs = [];

       return view('bookmarks', [
    'bookmarkedJobs' => collect($bookmarkedJobs)
]);
    }

    public function settings()
    {
        return view('settings');
    }

    public function changePassword()
    {
        return view('change-password');
    }

    public function clearBookmarks(Request $request)
    {
        // Logic to clear bookmarks - assuming bookmarks are stored in session or database
        // For now, we'll clear session bookmarks
        $request->session()->forget('bookmarkedJobs');

        return redirect()->route('settings')->with('success', 'All bookmarks cleared successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('settings')->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('settings')->with('success', 'Password changed successfully!');
    }
}
