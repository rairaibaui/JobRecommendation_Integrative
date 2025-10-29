<?php

namespace App\Http\Controllers;

class RecommendationController extends Controller
{
    public function index()
    {
        // Halimbawa data, pwedeng palitan galing sa DB
        $jobs = [
            [
                'title' => 'Frontend Developer',
                'location' => 'Mandaluyong',
                'type' => 'Full-time',
                'salary' => 'Php 30,000',
                'description' => 'Develop web front-end applications.',
                'skills' => ['HTML', 'CSS', 'JavaScript', 'React'],
            ],
            [
                'title' => 'Backend Developer',
                'location' => 'Makati',
                'type' => 'Full-time',
                'salary' => 'Php 35,000',
                'description' => 'Build API endpoints and manage database.',
                'skills' => ['PHP', 'Laravel', 'MySQL'],
            ],
        ];

        // Determine which jobs are bookmarked by current user (by title)
        $bookmarkedTitles = [];
        if (auth()->check()) {
            $bookmarkedTitles = auth()->user()->bookmarks()->pluck('title')->toArray();
        }

        // Pass to the Blade view
        return view('recommendation', ['jobs' => $jobs, 'bookmarkedTitles' => $bookmarkedTitles]);
    }
}
