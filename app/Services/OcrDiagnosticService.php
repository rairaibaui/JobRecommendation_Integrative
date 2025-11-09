<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OcrDiagnosticService
{
    /**
     * Binaries to check for OCR/PDF text extraction.
     * Order matters: pdftotext is lightweight; pdftoppm+tesseract is fallback.
     *
     * @var string[]
     */
    protected $binaries = [
        'pdftotext',
        'pdftoppm',
        'tesseract',
    ];

    /**
     * Check availability of configured binaries.
     *
     * @return array ['ok' => bool, 'missing' => array]
     */
    public function checkBinaries(): array
    {
        $missing = [];

        foreach ($this->binaries as $bin) {
            if (!$this->isBinaryAvailable($bin)) {
                $missing[] = $bin;
            }
        }

        return ['ok' => empty($missing), 'missing' => $missing];
    }

    /**
     * Throttled admin notification for missing binaries.
     * This will create an AuditLog and Notifications for admins once per 24 hours.
     *
     * @param array $missing
     * @return void
     */
    public function warnAdminsOnce(array $missing): void
    {
        if (empty($missing)) {
            return;
        }

        $cacheKey = 'ocr_missing_warned_'.md5(implode(',', $missing));

        // Throttle to once per day by default
        $alreadyWarned = Cache::get($cacheKey);
        if ($alreadyWarned) {
            return;
        }

        try {
            $msg = 'OCR binaries missing on host: '.implode(', ', $missing).'. Automatic resume OCR retries cannot proceed until these are installed.';

            // Audit entry
            try {
                \App\Models\AuditLog::create([
                    'user_id' => null,
                    'event' => 'ocr_binaries_missing',
                    'title' => 'OCR Binaries Missing',
                    'message' => $msg,
                    'data' => json_encode(['missing' => $missing]),
                ]);
            } catch (\Throwable $__e) {
                // best-effort
                Log::warning('Failed to create AuditLog for OCR diagnostics: '.$__e->getMessage());
            }

            // Notify admins
            $admins = \App\Models\User::where('is_admin', true)->get();
            foreach ($admins as $admin) {
                try {
                    \App\Models\Notification::create([
                        'user_id' => $admin->id,
                        'type' => 'warning',
                        'title' => 'OCR Tools Missing',
                        'message' => $msg,
                        'read' => false,
                        'data' => ['missing' => $missing],
                    ]);
                } catch (\Throwable $__n) {
                    // best-effort
                }
            }

            // Cache the warning for 24 hours
            Cache::put($cacheKey, true, 60 * 24);
        } catch (\Throwable $e) {
            Log::error('OcrDiagnosticService::warnAdminsOnce failed: '.$e->getMessage());
        }
    }

    /**
     * Check whether a binary is available in PATH.
     * Works across Linux/macOS and Windows.
     *
     * @param string $bin
     * @return bool
     */
    protected function isBinaryAvailable(string $bin): bool
    {
        // Prefer PHP_OS_FAMILY when available
        $osFamily = defined('PHP_OS_FAMILY') ? PHP_OS_FAMILY : (stripos(PHP_OS, 'WIN') === 0 ? 'Windows' : 'Linux');

        if (strtolower($osFamily) === 'windows') {
            // Windows: use 'where'
            $cmd = "where $bin 2>&1";
        } else {
            // Unix-like: use command -v
            $cmd = "command -v $bin 2>/dev/null";
        }

        exec($cmd, $output, $returnVar);
        return $returnVar === 0 && !empty($output);
    }
}
