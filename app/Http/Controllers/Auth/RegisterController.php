<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminNotificationService;
use App\Services\DocumentValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    protected $documentValidationService;

    public function __construct(DocumentValidationService $documentValidationService)
    {
        $this->documentValidationService = $documentValidationService;
    }

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
        // AUTOMATIC ROLE DETECTION:
        // If user uploads business permit → Employer
        // No business permit → Job Seeker
        // This eliminates manual role selection and ensures accuracy
        $hasBusinessPermit = $request->hasFile('business_permit');
        $userType = $hasBusinessPermit ? 'employer' : 'job_seeker';

        if ($userType === 'employer') {
            // EMPLOYER REGISTRATION (Auto-detected via business permit upload)
            // Accepts ANY email domain (Gmail, Yahoo, company emails)
            // Business permit is REQUIRED and will be AI-validated
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'company_name' => 'required|string|max:255',
                'job_title' => 'nullable|string|max:255',
                'employer_phone_number' => 'nullable|string|max:20',
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
            try {
                // Store business permit temporarily for validation
                $permitPath = null;
                if ($request->hasFile('business_permit')) {
                    $permitPath = $request->file('business_permit')->store('business_permits/temp', 'public');
                }

                // Move file from temp to permanent location immediately
                // Allow account creation, validate in background
                $finalPath = 'business_permits/'.basename($permitPath);
                Storage::disk('public')->move($permitPath, $finalPath);
                $permitPath = $finalPath;

                // Generate placeholder phone/location to satisfy schema constraints (if phone not provided)
                $phoneNumber = $validated['employer_phone_number']
                    ? preg_replace('/\D/', '', $validated['employer_phone_number']) // Remove formatting
                    : strval(random_int(10000000000, 99999999999)); // Fallback to random
                $location = 'Unknown';

                $user = User::create([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'email' => $validated['email'],
                    'company_name' => $validated['company_name'],
                    'job_title' => $validated['job_title'] ?? null,
                    'business_permit_path' => $permitPath,
                    'phone_number' => $phoneNumber,
                    'location' => $location,
                    'user_type' => 'employer',
                    'password' => Hash::make($validated['password']),
                ]);
                // Send email verification notification (best-effort)
                try {
                    if (method_exists($user, 'sendEmailVerificationNotification')) {
                        $user->sendEmailVerificationNotification();
                    }
                } catch (\Throwable $e) {
                    // best-effort: do not block registration on email failures
                }

                // Queue AI validation for background processing
                $isDocumentValidationEnabled = config('ai.features.document_validation', false)
                                               && config('ai.document_validation.business_permit.enabled', false);

                if ($isDocumentValidationEnabled && $permitPath && $user) {
                    // Dispatch background job for AI validation with delay
                    $delay = config('ai.document_validation.business_permit.validation_delay_seconds', 10);

                    // Check if Gmail/personal email - apply stricter validation
                    $isPersonalEmail = preg_match('/@(gmail|yahoo|hotmail|outlook)\.com$/i', $validated['email']) === 1;

                    \App\Jobs\ValidateBusinessPermitJob::dispatch(
                        $user->id,
                        $permitPath,
                        [
                            'company_name' => $validated['company_name'],
                            'email' => $validated['email'],
                            'is_personal_email' => $isPersonalEmail, // Flag for stricter validation
                        ]
                    )->delay(now()->addSeconds($delay));
                }

                // Notify admins about the new business permit upload
                if ($user) {
                    AdminNotificationService::notifyPermitUploaded($user);
                }
            } catch (\Throwable $e) {
                // Clean up uploaded file if it exists
                if (isset($permitPath) && $permitPath) {
                    Storage::disk('public')->delete($permitPath);
                }

                return back()
                    ->withInput()
                    ->with('error', 'Registration failed. Please check your inputs and try again.');
            }
        } else {
            // JOB SEEKER REGISTRATION (Auto-detected - no business permit uploaded)
            // Can use any email domain
            // No business permit required
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
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

            try {
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
                // Send email verification notification (best-effort)
                try {
                    if (method_exists($user, 'sendEmailVerificationNotification')) {
                        $user->sendEmailVerificationNotification();
                    }
                } catch (\Throwable $e) {
                    // best-effort: do not block registration on email failures
                }
            } catch (\Throwable $e) {
                return back()
                    ->withInput()
                    ->with('error', 'Registration failed. Please check your inputs and try again.');
            }
        }

        // Redirect to login for both employer and job seeker; require manual sign-in
        return redirect()->route('login')
            ->with('success', 'Account created successfully! Please sign in to continue.')
            ->with('email', $request->email)
            ->with('registered', true);
    }
}
