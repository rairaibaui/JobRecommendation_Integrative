<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    public function index()
    {
        $bookmarks = Auth::user()->bookmarks()->latest()->get();
        return view('bookmarks', ['bookmarks' => $bookmarks]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'job.title' => 'required|string',
            'job.location' => 'nullable|string',
            'job.type' => 'nullable|string',
            'job.salary' => 'nullable|string',
            'job.description' => 'nullable|string',
            'job.skills' => 'nullable|array',
            'job.company' => 'nullable|string',
            'job.employer_name' => 'nullable|string',
            'job.employer_email' => 'nullable|string',
            'job.employer_phone' => 'nullable|string',
            'job.posted_date' => 'nullable|string'
        ]);

        $existing = Auth::user()->bookmarks()->where('title', $data['job']['title'])->first();
        if ($existing) {
            return response()->json(['success' => false, 'message' => 'Already bookmarked'], 400);
        }

        $bookmark = Auth::user()->bookmarks()->create([
            'title' => $data['job']['title'],
            'location' => $data['job']['location'] ?? null,
            'type' => $data['job']['type'] ?? null,
            'salary' => $data['job']['salary'] ?? null,
            'description' => $data['job']['description'] ?? null,
            'skills' => $data['job']['skills'] ?? [],
            'company' => $data['job']['company'] ?? null,
            'employer_name' => $data['job']['employer_name'] ?? null,
            'employer_email' => $data['job']['employer_email'] ?? null,
            'employer_phone' => $data['job']['employer_phone'] ?? null,
            'posted_date' => $data['job']['posted_date'] ?? null
        ]);

        return response()->json(['success' => true, 'bookmark' => $bookmark]);
    }

    public function destroy(Request $request)
    {
        $data = $request->validate([
            'job.title' => 'required|string'
        ]);

        Auth::user()->bookmarks()->where('title', $data['job']['title'])->delete();

        return response()->json(['success' => true]);
    }
}
