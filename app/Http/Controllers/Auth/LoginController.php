<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Allow 'admin' username without @ symbol for admin account
        $emailInput = $request->input('email');
        
        // Convert 'admin' to full email address
        if ($emailInput === 'admin') {
            $emailInput = 'admin@jobrecommendation.ph';
        }
        
        // Validate the original input (allow 'admin' as string, others as email)
        $emailRules = $request->input('email') === 'admin'
            ? 'required|string'
            : 'required|email';

        $request->validate([
            'email' => $emailRules,
            'password' => 'required',
        ]);

        // Use the converted email for authentication
        $credentials = [
            'email' => $emailInput,
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Redirect admins to admin panel
            if ($user && $user->is_admin) {
                return response()->view('auth.post-login', [
                    'target' => route('admin.dashboard'),
                ]);
            }

            // If the verification flow placed a desired post-verification redirect in
            // the session (e.g. user clicked verification link while logged out),
            // honor it and then remove it from the session. Otherwise decide
            // destination based on role, but show a smooth loader first.
            $postVerifyTarget = session()->pull('post_verify_redirect');
            $target = $postVerifyTarget ?: ($user && $user->user_type === 'employer'
                ? route('employer.dashboard')
                : route('dashboard'));

            return response()->view('auth.post-login', [
                'target' => $target,
            ]);
        }

        return back()->withErrors([
            'password' => 'Incorrect password.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
