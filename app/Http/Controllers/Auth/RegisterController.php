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
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
        // ROLE DETERMINATION:
        // 1) Prefer explicit user_type from the form (the JS toggle sets this).
        // 2) If not provided, infer employer if company_name was supplied (supports JS-disabled clients).
        // 3) Otherwise fall back to business_permit presence for auto-detection.
        $hasBusinessPermit = $request->hasFile('business_permit');
        if ($request->filled('user_type')) {
            $userType = $request->input('user_type');
        } elseif ($request->filled('company_name')) {
            $userType = 'employer';
        } else {
            $userType = $hasBusinessPermit ? 'employer' : 'job_seeker';
        }

        $permitUploaded = false;

        if ($userType === 'employer') {
            // EMPLOYER REGISTRATION
            $rawEmployerPhone = $request->input('employer_phone_number');
            $normalizedEmployerPhone = $this->normalizePhilippinePhone($rawEmployerPhone);

            // Prepare data for validation so unique rule checks normalized value
            $dataForValidation = $request->all();
            if (!empty($normalizedEmployerPhone)) {
                $dataForValidation['employer_phone_number'] = $normalizedEmployerPhone;
            }

            $validator = Validator::make($dataForValidation, [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'company_name' => 'required|string|max:255',
                'job_title' => 'nullable|string|max:255',
                'employer_phone_number' => ['nullable','string','max:20', Rule::unique('users', 'phone_number')],
                'business_permit' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'password' => [
                    'required',
                    'confirmed',
                    Password::min(8)
                        ->letters()
                        ->mixedCase(),
                ],
                'terms' => 'accepted',
            ]);

            if ($validator->fails()) {
                Log::info('Registration validation failed (employer)', ['errors' => $validator->errors()->toArray(), 'input' => $request->except(['password','password_confirmation'])]);
                $preserve = array_merge($request->all(), ['user_type' => 'employer']);
                if (!empty($normalizedEmployerPhone)) $preserve['employer_phone_number'] = $normalizedEmployerPhone;
                return back()->withInput($preserve)->withErrors($validator)->with('user_type', 'employer');
            }

            $validated = $validator->validated();

            // Ensure normalized phone is valid length before storing
            if (!empty($validated['employer_phone_number'])) {
                $normalizedPhone = $this->normalizePhilippinePhone($validated['employer_phone_number']);
                if (empty($normalizedPhone) || strlen($normalizedPhone) !== 11) {
                    $validator->errors()->add('employer_phone_number', 'Please provide a valid Philippine phone number.');
                    $preserve = array_merge($request->all(), ['user_type' => 'employer']);
                    if (!empty($normalizedPhone)) $preserve['employer_phone_number'] = $normalizedPhone;
                    return back()->withInput($preserve)->withErrors($validator)->with('user_type', 'employer');
                }
                $validated['employer_phone_number'] = $normalizedPhone;
            }

            // Defensive check: ensure the email is not already registered (race-safe)
            if (!empty($validated['email']) && User::where('email', $validated['email'])->exists()) {
                $validator->errors()->add('email', 'This email is already registered with another account.');
                $preserve = array_merge($request->all(), ['user_type' => 'employer']);
                if (!empty($normalizedEmployerPhone)) $preserve['employer_phone_number'] = $normalizedEmployerPhone;
                return back()->withInput($preserve)->withErrors($validator)->with('user_type', 'employer');
            }

            try {
                $permitPath = null;
                if ($request->hasFile('business_permit')) {
                    $permitPath = $request->file('business_permit')->store('business_permits/temp', 'public');
                    $finalPath = 'business_permits/'.basename($permitPath);
                    Storage::disk('public')->move($permitPath, $finalPath);
                    $permitPath = $finalPath;
                }

                $phoneNumber = !empty($validated['employer_phone_number'])
                    ? $validated['employer_phone_number']
                    : strval(random_int(10000000000, 99999999999));
                $location = 'Unknown';

                $user = User::create([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'email' => $validated['email'],
                    'company_name' => $validated['company_name'],
                    'job_title' => $validated['job_title'] ?? null,
                    'business_permit_path' => $permitPath ?? null,
                    'phone_number' => $phoneNumber,
                    'location' => $location,
                    'user_type' => 'employer',
                    'password' => Hash::make($validated['password']),
                ]);

                $isDocumentValidationEnabled = config('ai.features.document_validation', false)
                                               && config('ai.document_validation.business_permit.enabled', false);

                if ($isDocumentValidationEnabled && !empty($permitPath) && $user) {
                    $delay = config('ai.document_validation.business_permit.validation_delay_seconds', 10);
                    $isPersonalEmail = preg_match('/@(gmail|yahoo|hotmail|outlook)\.com$/i', $validated['email']) === 1;

                    \App\Jobs\ValidateBusinessPermitJob::dispatch(
                        $user->id,
                        $permitPath,
                        [
                            'company_name' => $validated['company_name'],
                            'email' => $validated['email'],
                            'is_personal_email' => $isPersonalEmail,
                        ]
                    )->delay(now()->addSeconds($delay));
                }

                if ($user && !empty($permitPath)) {
                    AdminNotificationService::notifyPermitUploaded($user);
                    $permitUploaded = true;
                }
            } catch (\Throwable $e) {
                if (isset($permitPath) && $permitPath) {
                    Storage::disk('public')->delete($permitPath);
                }

                $preserve = array_merge($request->all(), ['user_type' => 'employer']);
                return back()
                    ->withInput($preserve)
                    ->with('error', 'Registration failed. Please check your inputs and try again.')
                    ->with('user_type', 'employer');
            }
        } else {
            // JOB SEEKER REGISTRATION
            $rawPhone = $request->input('phone_number');
            $normalizedPhoneForValidation = $this->normalizePhilippinePhone($rawPhone);
            $dataForValidation = $request->all();
            if (!empty($normalizedPhoneForValidation)) $dataForValidation['phone_number'] = $normalizedPhoneForValidation;

            $validator = Validator::make($dataForValidation, [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'birthday' => 'nullable|date',
                'phone_number' => ['required','string','max:20', Rule::unique('users', 'phone_number')],
                'education_level' => 'nullable|string|max:255',
                'skills' => 'nullable|string',
                'years_of_experience' => 'nullable|integer|min:0',
                'location' => 'required|string|max:255',
                'password' => [
                    'required',
                    'confirmed',
                    Password::min(8)
                        ->letters()
                        ->mixedCase(),
                ],
                'terms' => 'accepted',
            ]);

            if ($validator->fails()) {
                Log::info('Registration validation failed (job_seeker)', ['errors' => $validator->errors()->toArray(), 'input' => $request->except(['password','password_confirmation'])]);
                $preserve = array_merge($request->all(), ['user_type' => 'job_seeker']);
                if (!empty($normalizedPhoneForValidation)) $preserve['phone_number'] = $normalizedPhoneForValidation;
                return back()->withInput($preserve)->withErrors($validator);
            }

            $validated = $validator->validated();

            if (empty($validated['phone_number']) || strlen($validated['phone_number']) !== 11) {
                $validator->errors()->add('phone_number', 'Please provide a valid Philippine phone number (e.g. 09171234567 or +639171234567).');
                $preserve = array_merge($request->all(), ['user_type' => 'job_seeker']);
                if (!empty($normalizedPhoneForValidation)) $preserve['phone_number'] = $normalizedPhoneForValidation;
                return back()->withInput($preserve)->withErrors($validator);
            }

            // phone_number is already normalized into $validated via validation

            try {
                $locationValue = isset($validated['location']) ? trim($validated['location']) : null;

                $user = User::create([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'email' => $validated['email'],
                    'phone_number' => $validated['phone_number'],
                    'birthday' => $validated['birthday'] ?? null,
                    'education_level' => $validated['education_level'] ?? null,
                    'skills' => $validated['skills'] ?? null,
                    'years_of_experience' => $validated['years_of_experience'] ?? null,
                    'location' => $locationValue,
                    'user_type' => 'job_seeker',
                    'password' => Hash::make($validated['password']),
                ]);
            } catch (\Throwable $e) {
                $preserve = array_merge($request->all(), ['user_type' => 'job_seeker']);
                return back()
                    ->withInput($preserve)
                    ->with('error', 'Registration failed. Please check your inputs and try again.');
            }
        }

        if ($permitUploaded) {
            $message = 'Account created successfully! Your business permit was uploaded and will be reviewed by our team. Please sign in to continue.';
        } else {
            $message = 'Account created successfully! Please sign in to continue.';
        }

        return redirect()->route('login')
            ->with('success', $message)
            ->with('email', $request->email)
            ->with('registered', true)
            ->with('permit_uploaded', $permitUploaded);
        }

    /**
     * Normalize a Philippine phone number into an 11-digit local format starting with 0.
     * Examples:
     *  +639171234567 -> 09171234567
     *  639171234567  -> 09171234567
     *  9171234567    -> 09171234567
     *  09171234567   -> 09171234567
     */
    protected function normalizePhilippinePhone(?string $raw): ?string
    {
        if (empty($raw)) return null;
        $s = preg_replace('/[^0-9]/', '', $raw);
        if ($s === '') return null;
        // Remove leading + if present (already removed by preg_replace)
        // If starts with '63' and length >= 11, convert to leading 0
        if (strpos($s, '63') === 0 && strlen($s) >= 11) {
            // drop country code 63, prefix 0
            $s = '0' . substr($s, 2);
        }
        // If starts with country code '0' already OK
        if (strlen($s) === 10 && strpos($s, '9') === 0) {
            // local 10-digit without leading zero (e.g., 9171234567) -> add 0
            $s = '0' . $s;
        }
        // final sanity: if it's longer than 11, trim to last 11 digits
        if (strlen($s) > 11) {
            $s = substr($s, -11);
        }
        // if not 11 digits at this point, return as-is (validation will catch it)
        return $s;
    }

    /**
     * Check whether a normalized Philippine phone number is already in use.
     * This method normalizes stored phone numbers on-the-fly to avoid mismatches
     * caused by different formatting (spaces, dashes, +63, etc.). Uses a cursor
     * to avoid loading all users into memory.
     */
    protected function isPhoneInUse(?string $normalizedPhone): bool
    {
        if (empty($normalizedPhone)) {
            return false;
        }

        foreach (User::whereNotNull('phone_number')->cursor() as $user) {
            $stored = $user->phone_number;
            if (empty($stored)) continue;
            $normalizedStored = $this->normalizePhilippinePhone($stored);
            if ($normalizedStored === $normalizedPhone) {
                return true;
            }
        }

        return false;
    }
}
