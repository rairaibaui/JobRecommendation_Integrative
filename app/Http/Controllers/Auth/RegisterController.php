<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
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
        // Determine account type first
        $request->validate([
            'user_type' => 'required|in:job_seeker,employer',
        ]);

        if ($request->user_type === 'employer') {
            // Employer-specific validation
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'company_name' => 'required|string|max:255',
                'job_title' => 'nullable|string|max:255',
                'business_permit' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'password' => [
                    'required',
                    'confirmed',
                    Password::min(8)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols(),
                ],
                'terms' => 'accepted',
            ]);

            // Store business permit
            $permitPath = null;
            if ($request->hasFile('business_permit')) {
                $permitPath = $request->file('business_permit')->store('business_permits', 'public');
            }

            // Generate placeholder phone/location to satisfy schema constraints
            $generatedPhone = strval(random_int(10000000000, 99999999999));
            $location = 'Unknown';

            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'company_name' => $validated['company_name'],
                'job_title' => $validated['job_title'] ?? null,
                'business_permit_path' => $permitPath,
                'phone_number' => $generatedPhone,
                'location' => $location,
                'user_type' => 'employer',
                'password' => Hash::make($validated['password']),
            ]);
        } else {
            // Job seeker validation
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'birthday' => 'nullable|date',
                'phone_number' => 'required|digits:11|numeric|unique:users,phone_number',
                'education_level' => 'nullable|string|max:255',
                'skills' => 'nullable|string',
                'years_of_experience' => 'nullable|integer|min:0',
                'location' => 'required|string|max:255',
                'password' => [
                    'required',
                    'confirmed',
                    Password::min(8)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols(),
                ],
                'terms' => 'accepted',
            ]);

            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'birthday' => $validated['birthday'] ?? null,
                'education_level' => $validated['education_level'] ?? null,
                'skills' => $validated['skills'] ?? null,
                'years_of_experience' => $validated['years_of_experience'] ?? null,
                'location' => $validated['location'],
                'user_type' => 'job_seeker',
                'password' => Hash::make($validated['password']),
            ]);
        }

        // 3. Redirection to login with credentials
        return redirect()->route('login')
            ->with('success', 'Account created successfully! Please login to continue.')
            ->with('email', $request->email)
            ->with('registered', true);
    }
}
