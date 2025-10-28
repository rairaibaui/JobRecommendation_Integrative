<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Update general profile info.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validate only the fields you want
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'birthday' => 'nullable|date',
            'education_level' => 'nullable|string|max:255',
            'skills' => 'nullable|string|max:500',
            'years_of_experience' => 'nullable|integer|min:0',
            'location' => 'nullable|string|max:255',
        ]);

        // Update user fields in database
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

        // Save fields in session for automatic display in profile settings
        session([
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

        session(['email' => $request->email]); // Save in session

        return redirect()->back()->with('success', 'Email updated successfully.');
    }

    /**
     * Deactivate account.
     */
    public function deactivate(Request $request)
    {
        $user = Auth::user();

        // Assuming you have a 'is_active' column in users table
        $user->update([
            'is_active' => false,
        ]);

        Auth::logout();

        return redirect('/login')->with('success', 'Your account has been deactivated. You can reactivate by logging in again.');
    }
}
