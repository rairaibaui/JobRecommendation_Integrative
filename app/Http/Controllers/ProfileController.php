<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Update general profile info.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validate inputs
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'birthday' => 'nullable|date',
            'education_level' => 'nullable|string|max:255',
            'skills' => 'nullable|string|max:500',
            'years_of_experience' => 'nullable|integer|min:0',
            'location' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        // Handle profile picture upload or removal
        if ($request->has('remove_picture') && $request->remove_picture) {
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $user->profile_picture = null;
        } elseif ($request->hasFile('profile_picture')) {
            // Delete old picture if exists
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $user->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        // Update other profile fields
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone_number' => $request->phone_number,
            'birthday' => $request->birthday,
            'education_level' => $request->education_level,
            'skills' => $request->skills,
            'years_of_experience' => $request->years_of_experience,
            'location' => $request->location,
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Change email.
     */
    public function changeEmail(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);

        $user->update([
            'email' => $request->email,
        ]);

        return redirect()->back()->with('success', 'Email updated successfully.');
    }

    /**
     * Deactivate account.
     */
    public function deactivate(Request $request)
    {
        $user = Auth::user();

        // Set account inactive (assuming 'is_active' column exists)
        $user->update([
            'is_active' => false,
        ]);

        Auth::logout();

        return redirect('/login')->with('success', 'Your account has been deactivated.');
    }
}
