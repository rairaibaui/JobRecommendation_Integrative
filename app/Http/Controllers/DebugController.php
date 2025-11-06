<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DebugController extends Controller
{
    public function checkAuth()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'logged_in' => false,
                'message' => 'No user logged in',
            ]);
        }

        return response()->json([
            'logged_in' => true,
            'email' => $user->email,
            'is_admin' => $user->is_admin,
            'user_type' => $user->user_type,
            'can_access_admin' => $user->is_admin ? 'YES' : 'NO',
        ]);
    }
}
