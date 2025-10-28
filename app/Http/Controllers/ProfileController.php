<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Update general profile info and profile picture.
     */
    public function update(Request $request)
    {
        try {
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
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'remove_picture' => 'nullable|boolean',
            ]);

            // Update general fields
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

            // Remove current profile picture if checkbox is checked
            if ($request->has('remove_picture') && $request->remove_picture == 1) {
                if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                $user->profile_picture = null;
            }

            // Upload new profile picture if file is provided
            if ($request->hasFile('profile_picture')) {
                // Delete old picture first if exists
                if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                    Storage::disk('public')->delete($user->profile_picture);
                }

                $file = $request->file('profile_picture');
                $path = $file->store('profile_pictures', 'public');
                $user->profile_picture = $path;
            }

            $user->save();

            return redirect()->back()->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Profile update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Profile update failed: '.$e->getMessage()]);
        }
    }

    // ... rest of your methods (changeEmail, deactivate) remain the same
}
