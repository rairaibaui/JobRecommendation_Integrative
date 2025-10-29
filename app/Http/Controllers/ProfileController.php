<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Update only profile picture.
     */
    public function update(Request $request)
    {
        try {
            /** @var User $user */
            $user = User::find(Auth::id());
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            
            // Validate all profile fields
            $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthday' => 'nullable|date',
            'phone_number' => 'nullable|string|max:20',
            'education_level' => 'nullable|string|max:255',
            'skills' => 'nullable|string',
            'summary' => 'nullable|string',
            'education' => 'nullable|array',
            'experiences' => 'nullable|array',
            'languages' => 'nullable|string',
            'portfolio_links' => 'nullable|string',
            'availability' => 'nullable|string',
            'resume_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'years_of_experience' => 'nullable|numeric|min:0',
            'location' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_picture' => 'nullable|boolean',
        ]);

        // Track what was updated
        $pictureUpdated = false;
        $detailsUpdated = false;

        // Update profile details using fillable fields
        $data = $request->only([
            'first_name',
            'last_name',
            'birthday',
            'phone_number',
            'summary',
            'languages',
            'portfolio_links',
            'availability',
            'education_level',
            'skills',
            'years_of_experience',
            'location',
            'address'
        ]);
        
        // Handle education array
        if ($request->has('education')) {
            $education = collect($request->education)->map(function ($item) {
                return is_string($item) ? json_decode($item, true) : $item;
            })->toArray();
            $data['education'] = $education;
        }
        
        // Handle experiences array
        if ($request->has('experiences')) {
            $experiences = collect($request->experiences)->map(function ($item) {
                return is_string($item) ? json_decode($item, true) : $item;
            })->toArray();
            $data['experiences'] = $experiences;
        }
        
        $user->fill($data);
        $user->education_level = $request->education_level;
        $user->skills = $request->skills;
        $user->years_of_experience = $request->years_of_experience;
        $user->location = $request->location;
        $user->address = $request->address;

        // Always mark as updated since we're handling arrays
        $detailsUpdated = true;

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

        // Handle resume file upload
        if ($request->hasFile('resume_file')) {
            if ($user->resume_file && Storage::disk('public')->exists($user->resume_file)) {
                Storage::disk('public')->delete($user->resume_file);
            }
            $rpath = $request->file('resume_file')->store('resumes', 'public');
            $user->resume_file = $rpath;
        }

            try {
                $user->save();
                
                // Prepare message
                if ($pictureUpdated && $detailsUpdated) {
                    $message = 'Profile details and picture updated successfully.';
                } elseif ($pictureUpdated) {
                    $message = 'Profile picture updated successfully.';
                } else {
                    $message = 'Profile details updated successfully.';
                }

                // If the request expects JSON (AJAX), return JSON response
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $message,
                        'user' => $user->fresh()
                    ]);
                }

                return redirect()->back()->with('success', $message);
                
            } catch (\Exception $e) {
                Log::error('Profile update error: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save profile: ' . $e->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Profile update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Return resume/profile as JSON for the apply flow
     */
    public function resume(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'birthday' => $user->birthday,
            'location' => $user->location,
            'address' => $user->address,
            'summary' => $user->summary,
            'education' => $user->education ?? [],
            'experiences' => $user->experiences ?? [],
            'skills' => $user->skills,
            'languages' => $user->languages,
            'portfolio_links' => $user->portfolio_links,
            'availability' => $user->availability,
            'profile_picture' => $user->profile_picture ? asset('storage/' . $user->profile_picture) : null,
        ]);
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
