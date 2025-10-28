<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Update general profile info including profile picture.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validate fields
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'birthday' => 'nullable|date',
            'education_level' => 'nullable|string|max:255',
            'skills' => 'nullable|string|max:500',
            'years_of_experience' => 'nullable|integer|min:0',
            'location' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        // Update other fields
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
     * Change password.
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Check current password
        if (!\Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->password = \Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Password updated successfully.');
    }

    /**
     * Deactivate or delete account.
     */
    public function deactivate(Request $request)
    {
        $user = Auth::user();

        // Assuming you have a 'is_active' column or you can delete
        $user->update([
            'is_active' => false,
        ]);

        Auth::logout();

        return redirect('/login')->with('success', 'Your account has been deactivated.');
    }
}
