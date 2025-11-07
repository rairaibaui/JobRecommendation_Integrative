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
        $emailRules = $request->input('email') === 'admin'
            ? 'required|string'
            : 'required|email';

        $credentials = $request->validate([
            'email' => $emailRules,
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Redirect admins to admin panel
            if ($user && $user->is_admin) {
                return response()->view('auth.post-login', [
                    'target' => route('admin.dashboard'),
                ]);
            }

            // Decide destination based on role, but show a smooth loader first
            return response()->view('auth.post-login', [
                'target' => $user && $user->user_type === 'employer'
                    ? route('employer.dashboard')
                    : route('dashboard'),
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
