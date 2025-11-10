<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class InputSanitization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Sanitize all input data
        $this->sanitizeInput($request);

        return $next($request);
    }

    /**
     * Sanitize input data to prevent XSS and other injection attacks.
     */
    private function sanitizeInput(Request $request): void
    {
        $input = $request->all();

        // Recursively sanitize arrays and objects
        $sanitized = $this->sanitizeValue($input);

        // Replace the request input with sanitized data
        $request->merge($sanitized);
    }

    /**
     * Recursively sanitize a value.
     */
    private function sanitizeValue($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'sanitizeValue'], $value);
        }

        if (is_string($value)) {
            return $this->sanitizeString($value);
        }

        return $value;
    }

    /**
     * Sanitize a string value.
     */
    private function sanitizeString(string $value): string
    {
        // Remove null bytes
        $value = str_replace("\0", '', $value);

        // Basic XSS prevention - remove script tags and common XSS vectors
        $value = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $value);
        $value = preg_replace('/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/i', '', $value);
        $value = preg_replace('/<object\b[^<]*(?:(?!<\/object>)<[^<]*)*<\/object>/i', '', $value);
        $value = preg_replace('/<embed\b[^<]*(?:(?!<\/embed>)<[^<]*)*<\/embed>/i', '', $value);

        // Remove javascript: and vbscript: protocols
        $value = preg_replace('/javascript:/i', '', $value);
        $value = preg_replace('/vbscript:/i', '', $value);
        $value = preg_replace('/data:/i', '', $value);

        // Remove event handlers
        $value = preg_replace('/on\w+\s*=/i', '', $value);

        // Remove potentially dangerous HTML attributes
        $value = preg_replace('/(style|class|id)\s*=\s*["\'][^"\']*["\']/i', '', $value);

        // Log suspicious input for monitoring
        if ($this->containsSuspiciousPatterns($value)) {
            Log::warning('Suspicious input detected', [
                'input' => substr($value, 0, 100),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'url' => request()->fullUrl(),
            ]);
        }

        return $value;
    }

    /**
     * Check if string contains suspicious patterns.
     */
    private function containsSuspiciousPatterns(string $value): bool
    {
        $suspiciousPatterns = [
            '/<script/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload=/i',
            '/onerror=/i',
            '/eval\(/i',
            '/document\./i',
            '/window\./i',
            '/location\./i',
            '/alert\(/i',
            '/prompt\(/i',
            '/confirm\(/i',
            '/<iframe/i',
            '/<object/i',
            '/<embed/i',
            '/<meta/i',
            '/<link/i',
            '/<base/i',
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        return false;
    }
}