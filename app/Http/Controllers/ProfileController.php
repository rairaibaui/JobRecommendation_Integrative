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

        // Validate only profile picture and remove flag
        $request->validate([
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_picture' => 'nullable|boolean',
        ]);

        // Remove current profile picture if checkbox is checked
        if ($request->has('remove_picture') && $request->remove_picture) {
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

            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile picture updated successfully.');
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
