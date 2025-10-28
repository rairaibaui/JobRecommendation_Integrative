<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // Update general profile info (name, picture)
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    // Change email
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

    // Change phone number
    public function changePhone(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'phone_number' => 'required|string|max:15',
        ]);

        $user->phone_number = $request->phone_number;
        $user->save();

        return redirect()->back()->with('success', 'Phone number updated successfully.');
    }

    // Deactivate Account
    public function deactivate(Request $request)
    {
        $user = Auth::user();
        $user->is_active = false; // or 'status' column in your users table
        $user->save();

        Auth::logout();

        return redirect('/login')->with('success', 'Your account has been deactivated. You can reactivate by logging in again.');
    }
}
