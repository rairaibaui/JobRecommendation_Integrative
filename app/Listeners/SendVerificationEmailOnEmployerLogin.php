<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SendVerificationEmailOnEmployerLogin
{
    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        try {
            $user = $event->user;

            // Historically we auto-sent verification emails to employers on login.
            // Change of policy: do NOT auto-send verification emails. Employers must
            // explicitly request verification from Settings. This prevents accidental
            // verification emails being issued and gives employers control over when
            // they verify their account.
            if (($user->user_type ?? null) === 'employer' && (method_exists($user, 'hasVerifiedEmail') ? !$user->hasVerifiedEmail() : true)) {
                $userId = method_exists($user, 'getAuthIdentifier') ? $user->getAuthIdentifier() : null;
                Log::info('Auto-send verification suppressed for employer on login', ['user_id' => $userId]);
            }
        } catch (\Throwable $e) {
            Log::error('Error in SendVerificationEmailOnEmployerLogin listener: '.$e->getMessage());
        }
    }
}
