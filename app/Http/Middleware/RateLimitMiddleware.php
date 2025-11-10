<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    /**
     * Handle an incoming request with custom rate limiting.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $type  The type of operation (password_reset, email_verification, profile_update)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $type): Response
    {
        $user = $request->user();

        // For unauthenticated requests, use IP-based limiting
        $identifier = $user ? 'user_' . $user->id : 'ip_' . $request->ip();

        // Define rate limits based on operation type
        $limits = [
            'password_reset' => ['attempts' => 3, 'decay' => 60], // 3 attempts per hour
            'email_verification' => ['attempts' => 5, 'decay' => 60], // 5 attempts per hour
            'profile_update' => ['attempts' => 10, 'decay' => 60], // 10 attempts per hour
            'email_change' => ['attempts' => 3, 'decay' => 60], // 3 attempts per hour
            'phone_change' => ['attempts' => 5, 'decay' => 60], // 5 attempts per hour
        ];

        if (!isset($limits[$type])) {
            Log::warning("Unknown rate limit type: {$type}");
            return $next($request);
        }

        $limit = $limits[$type];
        $key = "rate_limit:{$type}:{$identifier}";
        $decaySeconds = $limit['decay'] * 60; // Convert minutes to seconds

        // Get current attempts count
        $attempts = Cache::get($key, 0);

        // Check if limit exceeded
        if ($attempts >= $limit['attempts']) {
            $remainingTime = Cache::get("{$key}:ttl", 0);

            if ($remainingTime <= 0) {
                // Reset if TTL expired
                Cache::put($key, 1, $decaySeconds);
                Cache::put("{$key}:ttl", now()->addSeconds($decaySeconds)->timestamp, $decaySeconds);
            } else {
                // Return rate limit exceeded response
                $minutes = ceil($remainingTime / 60);
                return response()->json([
                    'message' => "Too many {$type} attempts. Please try again in {$minutes} minute(s).",
                    'retry_after' => $remainingTime
                ], 429)->header('Retry-After', $remainingTime);
            }
        } else {
            // Increment attempts
            Cache::put($key, $attempts + 1, $decaySeconds);

            // Set TTL timestamp if this is the first attempt
            if ($attempts === 0) {
                Cache::put("{$key}:ttl", now()->addSeconds($decaySeconds)->timestamp, $decaySeconds);
            }
        }

        return $next($request);
    }
}