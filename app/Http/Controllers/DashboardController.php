<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $jobs = [
            [
                'title' => 'Frontend Developer',
                'company' => 'Tech Innovations Inc.',
                'location' => 'Mandaluyong City',
                'type' => 'Full-Time',
                'salary' => 2500,
                'description' => 'Work with a talented team to build responsive web apps.',
                'skills' => ['HTML', 'CSS', 'JavaScript', 'Vue.js'],
            ],
            [
                'title' => 'Backend Developer',
                'company' => 'NextGen Solutions',
                'location' => 'Makati City',
                'type' => 'Full-Time',
                'salary' => 3000,
                'description' => 'Develop and maintain APIs using Laravel framework.',
                'skills' => ['PHP', 'Laravel', 'MySQL'],
            ],
            [
                'title' => 'UI/UX Designer',
                'company' => 'Creative Minds Studio',
                'location' => 'Quezon City',
                'type' => 'Part-Time',
                'salary' => 1800,
                'description' => 'Design beautiful user interfaces and improve user experience.',
                'skills' => ['Figma', 'Adobe XD', 'Wireframing'],
            ],
        ];

        return view('dashboard', compact('jobs'));
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
}
