<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Update only profile picture.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Validate all profile fields
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthday' => 'nullable|date',
            'phone_number' => 'nullable|string|max:20',
            'education_level' => 'nullable|string|max:255',
            'skills' => 'nullable|string',
            'years_of_experience' => 'nullable|numeric|min:0',
            'location' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_picture' => 'nullable|boolean',
        ]);

        // Track what was updated
        $pictureUpdated = false;
        $detailsUpdated = false;

        // Update profile details
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->birthday = $request->birthday;
        $user->phone_number = $request->phone_number;
        $user->education_level = $request->education_level;
        $user->skills = $request->skills;
        $user->years_of_experience = $request->years_of_experience;
        $user->location = $request->location;
        $user->address = $request->address;

        // Check if any profile details were changed
        if ($user->isDirty([
            'first_name', 'last_name', 'birthday', 'phone_number',
            'education_level', 'skills', 'years_of_experience', 
            'location', 'address'
        ])) {
            $detailsUpdated = true;
        }

        // Handle profile picture
        if ($request->has('remove_picture') && $request->remove_picture) {
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $user->profile_picture = null;
            $pictureUpdated = true;
        }

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
            $pictureUpdated = true;
        }

        $user->save();

        // Return appropriate success message based on what was updated
        if ($pictureUpdated && $detailsUpdated) {
            return redirect()->back()->with('success', 'Profile details and picture updated successfully.');
        } elseif ($pictureUpdated) {
            return redirect()->back()->with('success', 'Profile picture updated successfully.');
        } else {
            return redirect()->back()->with('success', 'Profile details updated successfully.');
        }
    }

    public function changeEmail(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);

        $user->email = $request->email;
        $user->save();

        return redirect()->back()->with('success', 'Email updated successfully.');
    }
    // Keep other methods (changeEmail, deactivate, etc.) as is
}
