<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password; 

class RegisterController extends Controller
{
    /**
     * Display the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register'); 
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(Request $request)
    {
        // 1. Validation Rules (Dito na-update ang phone_number)
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', 
            'date_of_birth' => 'required|date',
            
            // NEW: Added unique:users for phone_number
            'phone_number' => 'required|digits:11|numeric|unique:users', 
            
            'education_level' => 'nullable|string|max:255',
            'skills' => 'nullable|string',
            'years_of_experience' => 'nullable|numeric|min:0',
            'location' => 'required|string|max:255', 
            'password' => [
                'required',
                'confirmed', 
                Password::min(8) 
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'terms' => 'accepted',
            'user_type' => 'required|in:job_seeker,employer',
        ]);

        // 2. Create User (Tama na ang logic nito)
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'date_of_birth' => $request->date_of_birth,
            'education_level' => $request->education_level,
            'skills' => $request->skills,
            'years_of_experience' => $request->years_of_experience,
            'location' => $request->location,
            'user_type' => $request->user_type,
            'password' => Hash::make($request->password),
        ]);

        // 3. Redirection (Redirection sa Login Page)
        return redirect()->route('login')->with('success', 'Account created successfully! Please sign in using your new credentials.'); 
    }
}