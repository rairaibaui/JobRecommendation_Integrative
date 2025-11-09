<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Log;
use App\Models\ResumeVerificationLog;
use Illuminate\Support\Str;

class ResumeVerificationService
{
    private $flags = [];
    private $score = 100;
    private $notes = [];

    /**
     * Analyze and verify a resume.
     */
    public function verify($resumePath, $user)
    {
        $this->flags = [];
        $this->score = 100;
        $this->notes = [];

        if (!$resumePath || !Storage::disk('public')->exists($resumePath)) {
            return $this->buildResult('needs_review', 0, ['missing_resume'], 'No resume file uploaded');
        }

        // Extract text from the uploaded document. Support PDFs, DOCX, and DOC.
        $fileAbsolute = storage_path('app/public/'.$resumePath);
        $ext = strtolower(pathinfo($fileAbsolute, PATHINFO_EXTENSION));

        $resumeText = '';
        if (in_array($ext, ['pdf'])) {
            // PDF path (existing flow)
            $resumeText = $this->extractTextFromPDF($fileAbsolute);
        } elseif (in_array($ext, ['docx', 'doc'])) {
            // Try native doc/docx extraction first, then fall back to converting to PDF and reuse PDF parsing/OCR.
            $resumeText = $this->extractTextFromDocOrDocx($fileAbsolute, $ext);
        } else {
            // Unknown extension --- attempt PDF parsing anyway (some files may be mislabelled)
            $resumeText = $this->extractTextFromPDF($fileAbsolute);
        }

        if (empty($resumeText)) {
            // Attempt OCR-based retry (pdftoppm/convert + tesseract) before giving up.
            try {
                $ocr = $this->attemptOcrRetry($fileAbsolute);
                if (!empty($ocr)) {
                    $resumeText = $ocr;
                    Log::info('ResumeVerification: used OCR retry text for resume', ['path' => $fileAbsolute, 'chars' => strlen($ocr)]);
                }
            } catch (\Throwable $e) {
                Log::warning('ResumeVerification: OCR retry attempt failed', ['path' => $fileAbsolute, 'error' => $e->getMessage()]);
            }
        }

        if (empty($resumeText)) {
            return $this->buildResult('needs_review', 0, ['unreadable_resume'], 'Resume file could not be read or is empty');
        }

        // First, check if this is actually a resume document
        if (!$this->isActuallyAResume($resumeText)) {
            return $this->buildResult('needs_review', 0, ['not_a_resume'], 'This document does not appear to be a resume. Please upload a proper CV/Resume document.');
        }

        // Only check for basic completeness
        $this->checkBasicCompleteness($resumeText);

        // If resume-specific document validation for extraction is enabled, try to extract fields
        $extracted = [
            'full_name' => null,
            'email' => null,
            'phone' => null,
            'birthday' => null,
            'raw_ai_response' => null,
        ];

        $isResumeValidationEnabled = config('ai.features.document_validation', false)
            && config('ai.document_validation.resume.enabled', false);

        // AI extraction hook intentionally left as a no-op when OpenAI client is not configured.
        // The system will fall back to local regex-based extraction below. If you wish to
        // enable OpenAI extraction, implement a client invocation here using the
        // configured SDK and store raw AI output in 'raw_ai_response'.

        // Fallback extraction using regex if AI extraction not available or incomplete
        if (empty($extracted['email']) && preg_match('/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}/i', $resumeText, $m)) {
            $extracted['email'] = $m[0];
        }

        if (empty($extracted['phone'])) {
            // capture common phone formats (escape hyphen inside char classes to avoid PCRE range issues)
            if (preg_match('/(\+?\d{1,3}[\s\.\-])?\(?\d{2,4}\)?[\s\.\-\/]?\d{3,4}[\s\.\-]?\d{3,4}/', $resumeText, $m)) {
                $extracted['phone'] = $m[0];
            }
        }

        if (empty($extracted['birthday'])) {
            // Look for date of birth patterns
            if (preg_match('/\b(19|20)\d{2}\b/', $resumeText, $m)) {
                // try to pull a nearby date pattern (simple)
                if (preg_match('/(\d{4}[-\/]\d{1,2}[-\/]\d{1,2})/', $resumeText, $m2)) {
                    $extracted['birthday'] = $m2[1];
                }
            }
            // Additional common formats
            if (empty($extracted['birthday']) && preg_match('/\b(\d{1,2}\s+(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Sept|Oct|Nov|Dec)[a-z]*\s+\d{4})\b/i', $resumeText, $m3)) {
                $extracted['birthday'] = $m3[1];
            }
        }

        // Try to heuristically get full name.
        // Strategy:
        // 1) If an email line exists, look 1-3 lines above it for a likely name (preferred).
        // 2) Otherwise, look for a line that looks like a human name: 2-4 words, each word capitalized or all-caps,
        //    and not a common resume heading or short role/descriptor like 'Compassionate care'.
        if (empty($extracted['full_name'])) {
            $lines = preg_split('/\r?\n/', trim($resumeText));

            // find first line containing an email — then try nearby lines above
            $emailLineIdx = null;
            foreach ($lines as $i => $line) {
                if (preg_match('/[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}/i', $line)) {
                    $emailLineIdx = $i;
                    break;
                }
            }

            $isBadHeading = function ($s) {
                // Expanded list of common section headings or labels that should NOT be
                // interpreted as a person's name. Include hobby/interest/language headings
                // and common variations (with slashes, ampersands, etc.).
                return preg_match('/\b(experience|education|skills|objective|summary|contact|about me|profile|references|achievements|certifications|hobby|hobbies|interest|interests|hobbies and interests|languages|language|projects|certificates)\b/i', $s);
            };

            $looksLikeName = function ($s) {
                $s = trim($s);
                if (strlen($s) < 3 || strlen($s) > 80) return false;
                // Avoid lines that contain too many non-letter characters
                if (preg_match('/[^A-Za-z\'\-\.\s]/', $s)) {
                    // allow accented/UTF-8 letters too
                    if (!preg_match('/^[\p{L}0-9\'\-\.\s]+$/u', $s)) return false;
                }
                // Reject obvious section headings that slipped through (e.g. "Hobbies and Interests")
                if (preg_match('/\b(hobby|hobbies|interest|interests|hobbies and interests|languages|language|skills|experience|education|profile|about me)\b/i', $s)) {
                    return false;
                }
                // Count words
                $words = preg_split('/\s+/', $s);
                $wordCount = count($words);
                if ($wordCount < 2 || $wordCount > 4) return false;
                // Reject short role-like lines (single words like "Nurse" or phrases like "Compassionate care")
                if (preg_match('/\b(care|nurse|engineer|developer|manager|assistant|student|consultant|specialist)\b/i', $s)) return false;
                // Accept if words are capitalized (Title Case) or if the line is mostly uppercase
                $titleCase = preg_match('/^[A-Z][a-z\'\-\.]+(?:\s+[A-Z][a-z\'\-\.]*)+$/', $s);
                $allCaps = preg_match('/^[A-Z\s\'\-\.]+$/', $s);
                if ($titleCase || $allCaps) return true;
                // As a fallback, accept if words look alphabetic (covers some locales)
                return preg_match('/^[\p{L}\'\-\.\s]+$/u', $s) === 1;
            };

            if (!is_null($emailLineIdx)) {
                // Look up to 4 lines above the email to find the candidate name (some resumes put the name several lines above contact info)
                for ($k = $emailLineIdx - 1; $k >= max(0, $emailLineIdx - 4); $k--) {
                    // Trim common trailing punctuation (commas, pipes) which often follow a name line
                    $cand = trim($lines[$k]);
                    $cand = trim($cand, " \t\n\r,|-");
                    if ($cand === '') continue;
                    if ($isBadHeading($cand)) continue;
                    if ($looksLikeName($cand)) {
                        $extracted['full_name'] = $cand;
                        break;
                    }
                }
            }

            // Fallback scanning of all lines if not found yet
            if (empty($extracted['full_name'])) {
                foreach ($lines as $line) {
                    $candidate = trim($line);
                    // Remove trailing punctuation that can break name heuristics (commas, pipes)
                    $candidate = trim($candidate, " \t\n\r,|-");
                    if ($candidate === '' || strlen($candidate) < 3) continue;
                    if ($isBadHeading($candidate)) continue;
                    if ($looksLikeName($candidate)) {
                        $extracted['full_name'] = $candidate;
                        break;
                    }
                }
            }
        }

        // Normalize extracted items
        $extractedFullName = $extracted['full_name'] ? trim(preg_replace('/\s+/', ' ', $extracted['full_name'])) : null;
        $extractedEmail = $extracted['email'] ? trim(strtolower($extracted['email'])) : null;
        $extractedPhone = $extracted['phone'] ? trim($extracted['phone']) : null;
        $extractedBirthday = null;
        if (!empty($extracted['birthday'])) {
            try {
                $d = date_create($extracted['birthday']);
                if ($d) $extractedBirthday = $d->format('Y-m-d');
            } catch (\Throwable $e) {
                $extractedBirthday = null;
            }
        }

        // Apply fuzzy matching rules against $user
        $matches = [
            'name' => false,
            'email' => false,
            'phone' => false,
            'birthday' => false,
        ];

        $confidences = [
            'name' => 0,
            'email' => 0,
            'phone' => 0,
            'birthday' => 0,
        ];

        // Name matching: compare full name to first+last
        $profileName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
        if ($extractedFullName) {
            $a = strtolower(preg_replace('/[^a-z0-9]/', '', $profileName));
            $b = strtolower(preg_replace('/[^a-z0-9]/', '', $extractedFullName));
            if ($a && $b) {
                $lev = levenshtein($a, $b);
                $max = max(strlen($a), strlen($b));
                $sim = $max > 0 ? (1 - ($lev / $max)) * 100 : 0;
                $confidences['name'] = (int) round($sim);
                $matches['name'] = $sim >= 80; // threshold
            }
        }

        // Email matching: normalize and allow plus-addressing differences
        if ($extractedEmail) {
            $normalizeEmail = function ($em) {
                $em = strtolower(trim($em));
                if (strpos($em, '+') !== false) {
                    $parts = explode('@', $em);
                    $local = explode('+', $parts[0])[0];
                    $em = $local . '@' . ($parts[1] ?? '');
                }
                return $em;
            };
            $profileEmail = $normalizeEmail($user->email ?? '');
            $extEmail = $normalizeEmail($extractedEmail);
            $confidences['email'] = $profileEmail === $extEmail ? 100 : (strpos($extEmail, '@') !== false && Str::contains($extEmail, explode('@', $profileEmail)[1] ?? '') ? 60 : 10);
            $matches['email'] = $profileEmail === $extEmail;
        }

        // Phone matching: compare last 10 digits
        if ($extractedPhone) {
            $onlyDigits = preg_replace('/[^0-9]/', '', $extractedPhone);
            $profileDigits = preg_replace('/[^0-9]/', '', $user->phone_number ?? '');
            $lastProfile = $profileDigits ? substr($profileDigits, -10) : '';
            $lastExtracted = $onlyDigits ? substr($onlyDigits, -10) : '';
            if ($lastProfile && $lastExtracted) {
                $matches['phone'] = $lastProfile === $lastExtracted;
                $confidences['phone'] = $matches['phone'] ? 100 : (similar_text($lastProfile, $lastExtracted) >= 6 ? 60 : 10);
            }
        }

        // Birthday matching: compare dates
        if ($extractedBirthday) {
            $profileBirthday = null;
            if (!empty($user->birthday)) {
                try { $profileBirthday = (new \DateTime($user->birthday))->format('Y-m-d'); } catch (\Throwable $e) { $profileBirthday = null; }
            }
            if ($profileBirthday) {
                $matches['birthday'] = $profileBirthday === $extractedBirthday;
                $confidences['birthday'] = $matches['birthday'] ? 100 : 10;
            }
        }

        // Annotate detailed mismatch flags and notes so admins/users see exactly which fields differ
        // Note: birthday is informational and often omitted from resumes; do NOT let birthday mismatches
        // drive an automatic rejection. Final decision will be based on core fields: name, email, phone.
        $mismatchFields = [];
        foreach ($matches as $field => $matched) {
            $profileVal = null;
            $extractedVal = null;
            switch ($field) {
                case 'name':
                    $profileVal = $profileName;
                    $extractedVal = $extractedFullName;
                    break;
                case 'email':
                    $profileVal = $user->email ?? null;
                    $extractedVal = $extractedEmail;
                    break;
                case 'phone':
                    $profileVal = $user->phone_number ?? null;
                    $extractedVal = $extractedPhone;
                    break;
                case 'birthday':
                    $profileVal = !empty($user->birthday) ? (new \DateTime($user->birthday))->format('Y-m-d') : null;
                    $extractedVal = $extractedBirthday;
                    break;
            }

            if (!$matched) {
                // For core fields (name/email/phone) record actionable mismatch flags and notes.
                if ($field !== 'birthday') {
                    $mismatchFields[] = $field;
                    $this->flags[] = 'mismatch_' . $field; // e.g. mismatch_name, mismatch_email
                    $this->notes[] = ucfirst($field) . ' mismatch: extracted=' . ($extractedVal ?? 'N/A') . ' profile=' . ($profileVal ?? 'N/A');
                } else {
                    // Do not add birthday mismatch as an actionable flag or note (informational only)
                    // Keep extracted birthday stored in the log but skip flags/notes.
                }
            } else {
                // Positive audit flag for core fields only
                if ($field !== 'birthday') {
                    $this->flags[] = 'match_' . $field;
                }
            }
        }

        // If email mismatched, explicitly flag this as requiring manual admin review and add a structured reason
        $manualReviewReasons = [];
        if (in_array('email', $mismatchFields, true)) {
            $this->flags[] = 'manual_review_email';
            $this->notes[] = 'Requires manual review: email from resume does not match registered profile.';
            $manualReviewReasons['email'] = [
                'extracted' => $extractedEmail,
                'profile' => $user->email ?? null,
            ];
        }

        // Determine overall status using CORE fields (name, email, phone). Birthday is informational only.
        $coreMatchedCount = intval($matches['name']) + intval($matches['email']) + intval($matches['phone']);

        // If the extracted full name exactly equals the profile first+last (normalized), auto-verify immediately.
        $skipFurtherDecision = false;
        if (!empty($extractedFullName) && !empty($profileName)) {
            $normalize = function ($s) {
                return preg_replace('/[^a-z0-9]/', '', strtolower(trim($s)));
            };
            $normProfile = $normalize($profileName);
            $normExtracted = $normalize($extractedFullName);
            if ($normProfile !== '' && $normExtracted === $normProfile) {
                // Only auto-verify if the email also matches the profile AND the user's email is verified.
                if (!empty($matches['email']) && $matches['email'] && method_exists($user, 'hasVerifiedEmail') && $user->hasVerifiedEmail()) {
                    $finalStatus = 'verified';
                    $this->flags[] = 'match_name_exact';
                    $this->notes[] = 'Exact full name match with verified email: auto-verified.';
                } else {
                    // Require manual review when email is not matched or account email is not yet verified
                    $finalStatus = 'needs_review';
                    $this->flags[] = 'manual_review_email';
                    if (!method_exists($user, 'hasVerifiedEmail') || !$user->hasVerifiedEmail()) {
                        $this->flags[] = 'email_unverified';
                        $this->notes[] = 'Email address for this account is not verified; resume cannot be auto-verified until email is verified.';
                    } else {
                        $this->notes[] = 'Exact name match found but email does not match registered profile; requires manual review.';
                    }
                }
                $skipFurtherDecision = true;
            }
        }

        // Decision rules (strict identity verification):
        // - If the account email is NOT verified -> do NOT auto-verify; mark as 'needs_review' and flag 'email_unverified'.
        // - If the account email IS verified -> require exact matches on all three core fields (name, email, phone).
        //   * If ALL three match exactly -> status = 'verified'.
        //   * If ANY of the three do NOT match -> status = 'rejected' with identity-mismatch message.
        // This enforces that verified accounts cannot bypass identity checks.

        // Normalize key values for exact comparison
        $normalizeForCompare = function ($s) {
            return preg_replace('/[^a-z0-9]/', '', strtolower(trim((string) $s)));
        };
        $normProfile = $normalizeForCompare($profileName);
        $normExtracted = $normalizeForCompare($extractedFullName);

        $profileEmailNorm = isset($profileEmail) ? $profileEmail : (isset($user->email) ? strtolower(trim($user->email)) : '');
        $extractedEmailNorm = isset($extEmail) ? $extEmail : $extractedEmail;

        $onlyDigitsExtracted = $extractedPhone ? preg_replace('/[^0-9]/', '', $extractedPhone) : '';
        $onlyDigitsProfile = $user->phone_number ? preg_replace('/[^0-9]/', '', $user->phone_number) : '';
        $lastExtracted = $onlyDigitsExtracted ? substr($onlyDigitsExtracted, -10) : '';
        $lastProfile = $onlyDigitsProfile ? substr($onlyDigitsProfile, -10) : '';

        if (method_exists($user, 'hasVerifiedEmail') && !$user->hasVerifiedEmail()) {
            $finalStatus = 'needs_review';
            $this->flags[] = 'email_unverified';
            $this->notes[] = 'Resume verification pending — email not verified.';
        } else {
            // Account email is verified — require exact identity matches
            $exactName = ($normProfile !== '' && $normExtracted !== '' && $normProfile === $normExtracted);
            $exactEmail = (!empty($profileEmailNorm) && !empty($extractedEmailNorm) && $profileEmailNorm === $extractedEmailNorm);
            $exactPhone = ($lastProfile !== '' && $lastExtracted !== '' && $lastProfile === $lastExtracted);

            if ($exactName && $exactEmail && $exactPhone) {
                $finalStatus = 'verified';
                $this->flags[] = 'match_name_exact';
                $this->flags[] = 'match_email';
                $this->flags[] = 'match_phone';
                $this->notes[] = 'All core fields match exactly; account auto-verified.';
            } else {
                // Reject when any core field mismatches for a verified account
                $finalStatus = 'rejected';
                // Clear previous flags and add identity mismatch flag
                $this->flags = ['identity_mismatch'];
                $this->notes = ['This resume does not belong to you. The personal information does not match your account.'];
                // Lower the score to 0 for rejected identity mismatch
                $this->score = 0;
            }
        }

        // Apply penalties to the quality score for core-field mismatches so the score reflects
        // name/email/phone inconsistencies. This ensures the UI shows <100% when name or
        // phone do not match the user's profile (as requested).
        // Start from the current score (which may have been reduced by completeness checks)
        $penalties = 0;
        if (isset($matches['name']) && $matches['name'] === false) {
            // Name mismatch is significant but not necessarily fatal
            $penalties += 20;
        }
        if (isset($matches['phone']) && $matches['phone'] === false) {
            // Phone mismatch reduces confidence
            $penalties += 15;
        }
        if (isset($matches['email']) && $matches['email'] === false) {
            // Email mismatch is critical for identity; penalize more
            $penalties += 25;
        }

        // Deduct penalties from running score and clamp to [0,100]
        $this->score = max(0, min(100, $this->score - $penalties));

        // Simplify flags per UX rules:
        // - If the account email is NOT verified, show only the 'email_unverified' flag.
        // - Otherwise, show a single representative mismatch reason (email > name > phone) if any
        //   mismatches exist. If all three core fields are mismatched, expose all three. If there
        //   are no mismatches, return an empty flags array (no flag should be shown on the UI).
        $displayFlags = [];
        $coreMismatchFlags = ['mismatch_name', 'mismatch_email', 'mismatch_phone'];
        // Determine which mismatch flags we recorded
        $recordedMismatches = array_values(array_intersect($coreMismatchFlags, $this->flags));

        // If account email not verified -> show only that flag
        if (method_exists($user, 'hasVerifiedEmail') && !$user->hasVerifiedEmail()) {
            $displayFlags = ['email_unverified'];
        } else {
            // If there are no recorded core mismatches, show no flags (clean UI)
            if (count($recordedMismatches) === 0) {
                $displayFlags = [];
            } elseif (count($recordedMismatches) === 3) {
                // All core fields mismatched: show all three
                $displayFlags = $recordedMismatches;
            } else {
                // Pick one representative core mismatch by priority: email, name, phone
                if (in_array('mismatch_email', $this->flags)) {
                    $displayFlags = ['mismatch_email'];
                } elseif (in_array('mismatch_name', $this->flags)) {
                    $displayFlags = ['mismatch_name'];
                } elseif (in_array('mismatch_phone', $this->flags)) {
                    $displayFlags = ['mismatch_phone'];
                } else {
                    // Fallback: no core mismatch but some other flags exist; do not show them by default
                    $displayFlags = [];
                }
            }
        }

        // Save a verification log record (best-effort)
        try {
            ResumeVerificationLog::create([
                'user_id' => $user->id,
                'resume_path' => $resumePath,
                'extracted_full_name' => $extractedFullName,
                'extracted_email' => $extractedEmail,
                'extracted_phone' => $extractedPhone,
                'extracted_birthday' => $extractedBirthday,
                'match_name' => $matches['name'],
                'match_email' => $matches['email'],
                'match_phone' => $matches['phone'],
                'match_birthday' => $matches['birthday'],
                'confidence_name' => $confidences['name'],
                'confidence_email' => $confidences['email'],
                'confidence_phone' => $confidences['phone'],
                'confidence_birthday' => $confidences['birthday'],
                'overall_status' => $finalStatus,
                'notes' => implode('; ', $this->notes),
                'raw_ai_response' => $extracted['raw_ai_response'] ?? null,
            ]);
        } catch (\Throwable $e) {
            // best effort, do not fail verification if logging fails
        }

    // Attach flags/score/notes (kept in result array below)

        // Build final result following existing structure
        $result = [
            'status' => $finalStatus,
            'score' => $this->score,
            'flags' => $displayFlags,
            'notes' => implode('; ', $this->notes),
            'manual_review_reasons' => $manualReviewReasons,
            'verified_at' => $finalStatus === 'verified' ? now() : null,
            'extracted' => [
                'full_name' => $extractedFullName,
                'email' => $extractedEmail,
                'phone' => $extractedPhone,
                'birthday' => $extractedBirthday,
            ],
        ];

        return $result;
    }

    /**
     * Extract text from .doc or .docx files.
     * Tries docx native parsing, then antiword for .doc, then LibreOffice/soffice conversion to PDF and parse.
     */
    private function extractTextFromDocOrDocx(string $filePath, string $ext): string
    {
        // Quick existence check
        if (!file_exists($filePath)) return '';

        // Try DOCX native parsing
        if ($ext === 'docx') {
            $text = $this->extractTextFromDocx($filePath);
            if (!empty($text)) return $text;
        }

        // Try antiword for old .doc binary format
        if ($ext === 'doc') {
            $text = $this->extractTextFromDoc($filePath);
            if (!empty($text)) return $text;
        }

        // Try converting to PDF using soffice/libreoffice and reuse PDF extractor (best-effort)
        $pdf = $this->convertToPdfUsingSoffice($filePath);
        if ($pdf && file_exists($pdf)) {
            $txt = $this->extractTextFromPDF($pdf);
            // cleanup the temp pdf
            @unlink($pdf);
            if (!empty($txt)) return $txt;
        }

        // As a last resort, attempt OCR retry on the original file (some converters accept docs)
        try {
            $ocr = $this->attemptOcrRetry($filePath);
            if (!empty($ocr)) return $ocr;
        } catch (\Throwable $e) {
            Log::warning('ResumeVerification: OCR retry for doc/docx failed', ['path' => $filePath, 'error' => $e->getMessage()]);
        }

        return '';
    }

    /**
     * Extract text from a .docx file by reading the document.xml from the archive.
     */
    private function extractTextFromDocx(string $filePath): string
    {
        try {
            $zip = new \ZipArchive();
            if ($zip->open($filePath) === true) {
                $index = $zip->locateName('word/document.xml');
                if ($index !== false) {
                    $xml = $zip->getFromIndex($index);
                    $zip->close();
                    if ($xml) {
                        // Strip XML tags and decode entities
                        $text = preg_replace('/<[^>]+>/', ' ', $xml);
                        $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
                        $text = trim(preg_replace('/\s+/', " ", $text));
                        return $text;
                    }
                } else {
                    $zip->close();
                }
            }
        } catch (\Throwable $e) {
            Log::warning('ResumeVerification: docx extraction failed', ['path' => $filePath, 'error' => $e->getMessage()]);
        }
        return '';
    }

    /**
     * Extract text from old binary .doc files using antiword if available.
     */
    private function extractTextFromDoc(string $filePath): string
    {
        try {
            if (!function_exists('exec')) return '';
            $which = PHP_OS_FAMILY === 'Windows' ? 'where' : 'which';
            @exec($which . ' antiword 2>&1', $out, $ret);
            if ($ret === 0) {
                $cmd = 'antiword -m UTF-8 ' . escapeshellarg($filePath);
                @exec($cmd, $lines, $r);
                if ($r === 0 && is_array($lines)) {
                    $txt = trim(implode("\n", $lines));
                    if ($txt !== '') return $txt;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('ResumeVerification: antiword extraction failed', ['path' => $filePath, 'error' => $e->getMessage()]);
        }
        return '';
    }

    /**
     * Convert a document (doc/docx/other) to PDF using soffice/libreoffice if available.
     * Returns the path to the generated PDF or empty string on failure.
     */
    private function convertToPdfUsingSoffice(string $filePath): string
    {
        try {
            if (!function_exists('exec')) return '';
            $which = PHP_OS_FAMILY === 'Windows' ? 'where' : 'which';
            // Check for soffice or libreoffice
            @exec($which . ' soffice 2>&1', $outS, $rS);
            $cmdBin = null;
            if ($rS === 0) {
                $cmdBin = 'soffice';
            } else {
                @exec($which . ' libreoffice 2>&1', $outL, $rL);
                if ($rL === 0) $cmdBin = 'libreoffice';
            }

            if (empty($cmdBin)) {
                return '';
            }

            $tmpDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'resume_convert_' . uniqid();
            @mkdir($tmpDir, 0755, true);
            // Run conversion
            $cmd = sprintf('%s --headless --convert-to pdf --outdir %s %s 2>&1', $cmdBin, escapeshellarg($tmpDir), escapeshellarg($filePath));
            @exec($cmd, $out, $ret);
            if ($ret === 0) {
                // Look for a .pdf in the tmpDir with same base name
                $base = pathinfo($filePath, PATHINFO_FILENAME);
                $candidates = glob($tmpDir . DIRECTORY_SEPARATOR . $base . '*.pdf');
                if (count($candidates) > 0) {
                    // Return first candidate
                    return $candidates[0];
                }
                // If conversion created a single PDF with different name, pick the first pdf
                $all = glob($tmpDir . DIRECTORY_SEPARATOR . '*.pdf');
                if (count($all) > 0) {
                    return $all[0];
                }
            }
            // Cleanup on failure
            @array_map('unlink', glob($tmpDir . DIRECTORY_SEPARATOR . '*'));
            @rmdir($tmpDir);
        } catch (\Throwable $e) {
            Log::warning('ResumeVerification: soffice conversion failed', ['path' => $filePath, 'error' => $e->getMessage()]);
        }
        return '';
    }

    /**
     * Extract text from PDF resume.
     */
    private function extractTextFromPDF($filePath)
    {
        try {
            // Diagnostics: ensure file exists and capture metadata
            if (!file_exists($filePath)) {
                Log::warning('ResumeVerification: PDF file not found', ['path' => $filePath]);
                return '';
            }

            $size = filesize($filePath) ?: 0;
            $mime = @mime_content_type($filePath) ?: 'unknown';
            Log::info('ResumeVerification: attempting PDF parse', ['path' => $filePath, 'size' => $size, 'mime' => $mime]);

            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();

            $text = trim($text);

            if ($text === '') {
                Log::warning('ResumeVerification: PDF parsed but returned empty text, attempting pdftotext fallback', ['path' => $filePath]);

                // Fallback: try pdftotext (part of poppler) if available on the system
                try {
                    if (function_exists('exec')) {
                        // Check if pdftotext exists
                        $whichCmd = (stripos(PHP_OS, 'WIN') === 0) ? 'where' : 'which';
                        $check = null;
                        @exec($whichCmd . ' pdftotext 2>&1', $checkOutput, $checkReturn);
                        $hasPdftotext = is_array($checkOutput) && count($checkOutput) > 0 && $checkReturn === 0;

                        if ($hasPdftotext) {
                            $cmd = 'pdftotext -layout -enc UTF-8 ' . escapeshellarg($filePath) . ' -';
                            $out = null;
                            $ret = null;
                            @exec($cmd, $out, $ret);
                            if ($ret === 0 && is_array($out)) {
                                $pdftxt = implode("\n", $out);
                                $pdftxt = trim($pdftxt);
                                if ($pdftxt !== '') {
                                    Log::info('ResumeVerification: pdftotext fallback succeeded', ['path' => $filePath, 'chars' => strlen($pdftxt)]);
                                    return $pdftxt;
                                }
                            }
                        } else {
                            Log::info('ResumeVerification: pdftotext not found on host, skipping fallback', ['path' => $filePath]);
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning('ResumeVerification: pdftotext fallback error', ['path' => $filePath, 'message' => $e->getMessage()]);
                }

                // no fallback produced text
                Log::warning('ResumeVerification: PDF parse and fallbacks produced no text', ['path' => $filePath]);
            } else {
                Log::info('ResumeVerification: PDF parsed successfully', ['path' => $filePath, 'chars' => strlen($text)]);
            }

            return $text;
        } catch (\Exception $e) {
            // Log detailed exception for debugging
            Log::error('ResumeVerification: PDF parse exception', ['path' => $filePath, 'message' => $e->getMessage(), 'exception' => $e]);
            return '';
        }
    }

    /**
     * Check if the document is actually a resume/CV.
     */
    private function isActuallyAResume($text)
    {
        $text = strtolower($text);
        $matchCount = 0;

        // Resume-specific keywords and sections that should be present
        $resumeIndicators = [
            // Common resume headers
            'resume' => 1,
            'curriculum vitae' => 1,
            'cv' => 0.5,

            // Professional sections (at least 2-3 should be present)
            'objective' => 1,
            'summary' => 0.5,
            'professional summary' => 1,
            'career objective' => 1,
            'work experience' => 2,
            'professional experience' => 2,
            'employment history' => 2,
            'education' => 2,
            'educational background' => 2,
            'skills' => 1,
            'technical skills' => 1,
            'core competencies' => 1,
            'qualifications' => 1,
            'certifications' => 0.5,
            'achievements' => 0.5,
            'awards' => 0.5,
            'references' => 0.5,

            // Job-related terms
            'position' => 0.5,
            'responsibilities' => 1,
            'duties' => 0.5,
            'projects' => 0.5,
        ];

        // Check for resume indicators
        foreach ($resumeIndicators as $keyword => $weight) {
            if (stripos($text, $keyword) !== false) {
                $matchCount += $weight;
            }
        }

        // Check for contact information patterns (strong indicator)
    $hasEmail = preg_match('/[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}/', $text);
    $hasPhone = preg_match('/\b\d{3,4}[\s\-]?\d{3}[\s\-]?\d{4}\b/', $text);

        if ($hasEmail || $hasPhone) {
            ++$matchCount;
        }

        // Check for date patterns (indicating work/education history)
        $datePatterns = preg_match_all('/\b(19|20)\d{2}\b/', $text, $matches);
        if ($datePatterns >= 2) {
            ++$matchCount;
        }

        // Check for common non-resume document indicators
        $nonResumeIndicators = [
            'terms and conditions',
            'privacy policy',
            'invoice',
            'receipt',
            'contract',
            'agreement',
            'manual',
            'guide',
            'chapter',
            'table of contents',
            'bibliography',
            'abstract',
            'conclusion',
            'introduction',
            'methodology',
        ];

        $nonResumeCount = 0;
        foreach ($nonResumeIndicators as $indicator) {
            if (stripos($text, $indicator) !== false) {
                ++$nonResumeCount;
            }
        }

        // If it has many non-resume indicators, likely not a resume
        if ($nonResumeCount >= 3) {
            return false;
        }

        // Need at least 4 points worth of resume indicators to be considered a resume
        return $matchCount >= 4;
    }

    /**
     * Check for completeness - Contact info mismatch is allowed.
     */
    private function checkBasicCompleteness($text)
    {
        $text = strtolower($text);

        // Check for contact info (email, phone, or address) - Just presence, not matching profile
        $hasContact = false;
        if (preg_match('/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}/', $text)) {
            $hasContact = true;
        }
    if (preg_match('/\b\d{3,4}[\s\-]?\d{3}[\s\-]?\d{4}\b/', $text)) {
            $hasContact = true;
        }
        if (stripos($text, 'address') !== false || stripos($text, 'location') !== false) {
            $hasContact = true;
        }

        // Note: We allow contact info to not match profile - user might use different contact details
        if (!$hasContact) {
            $this->flags[] = 'missing_contact';
            $this->score -= 20; // Reduced penalty since it's informational only
            $this->notes[] = 'Contact information appears to be missing (Note: Contact details do not need to match your profile)';
        }

        // Check for education
        $educationKeywords = ['education', 'degree', 'university', 'college', 'school', 'graduated', 'bachelor', 'master', 'diploma', 'certificate'];
        $hasEducation = false;
        foreach ($educationKeywords as $keyword) {
            if (stripos($text, $keyword) !== false) {
                $hasEducation = true;
                break;
            }
        }
        if (!$hasEducation) {
            $this->flags[] = 'missing_education';
            $this->score -= 30;
            $this->notes[] = 'No education information found in resume';
        }

        // Check for experience or skills (fresh graduates might not have work experience)
        $experienceKeywords = ['experience', 'worked', 'employment', 'position', 'role', 'years', 'internship', 'volunteer'];
        $skillsKeywords = ['skills', 'proficient', 'competencies', 'abilities', 'expertise', 'technical skills'];

        $hasExperience = false;
        $hasSkills = false;

        foreach ($experienceKeywords as $keyword) {
            if (stripos($text, $keyword) !== false) {
                $hasExperience = true;
                break;
            }
        }

        foreach ($skillsKeywords as $keyword) {
            if (stripos($text, $keyword) !== false) {
                $hasSkills = true;
                break;
            }
        }

        // Either experience OR skills should be present
        if (!$hasExperience && !$hasSkills) {
            $this->flags[] = 'missing_experience_and_skills';
            $this->score -= 30;
            $this->notes[] = 'No work experience or skills section found in resume';
        } elseif (!$hasExperience) {
            $this->flags[] = 'missing_experience';
            $this->score -= 10; // Reduced penalty if skills are present
            $this->notes[] = 'No work experience found (acceptable for fresh graduates with skills listed)';
        }

        // Check minimum length
        if (strlen($text) < 200) {
            $this->flags[] = 'too_short';
            $this->score -= 10;
            $this->notes[] = 'Resume content is too brief';
        }

        // Check for basic readability
        if (strlen($text) < 50) {
            $this->flags[] = 'unreadable';
            $this->score -= 40;
            $this->notes[] = 'Resume appears to be unreadable or mostly empty';
        }
    }

    /**
     * Check for duplicate or copied content.
     */

    /**
     * Check for potentially fake information.
     */

    /**
     * Check resume quality.
     */

    /**
     * Check if resume appears to be AI-generated.
     */

    /**
     * Determine status based on score and flags.
     */
    private function determineStatusSimple()
    {
        // Critical flags that always require review
        $criticalFlags = ['missing_resume', 'unreadable_resume', 'not_a_resume', 'unreadable'];
        foreach ($criticalFlags as $flag) {
            if (in_array($flag, $this->flags)) {
                return 'needs_review';
            }
        }

        // If missing both education AND experience/skills, needs review
        if (in_array('missing_education', $this->flags) && in_array('missing_experience_and_skills', $this->flags)) {
            return 'needs_review';
        }

        // Score-based determination
        if ($this->score >= 70) {
            return 'verified'; // Auto-verify if score is good enough
        } elseif ($this->score >= 50) {
            return 'pending'; // Pending for minor issues
        } else {
            return 'needs_review'; // Manual review needed for low scores
        }
    }

    /**
     * Build result array.
     */
    private function buildResult($status, $score, $flags, $notes)
    {
        return [
            'status' => $status,
            'score' => max(0, min(100, $score)),
            'flags' => $flags,
            'notes' => $notes,
            'verified_at' => $status === 'verified' ? now() : null,
        ];
    }

    /**
     * Attempt OCR-based extraction using system tools (pdftoppm/convert + tesseract).
     * Returns extracted text or empty string on failure.
     */
    private function attemptOcrRetry(string $filePath): string
    {
        try {
            if (!file_exists($filePath)) {
                return '';
            }

            if (!function_exists('exec')) {
                Log::info('ResumeVerification: exec() not available, skipping OCR retry', ['path' => $filePath]);
                return '';
            }

            $tmpBase = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'resume_ocr_' . uniqid();
            @mkdir($tmpBase, 0755, true);

            $images = [];
            $which = PHP_OS_FAMILY === 'Windows' ? 'where' : 'which';

            // Try pdftoppm first
            @exec("$which pdftoppm 2>&1", $out, $ret);
            if ($ret === 0 && is_array($out) && count($out) > 0) {
                $outPrefix = $tmpBase . DIRECTORY_SEPARATOR . 'page';
                $cmd = 'pdftoppm -png ' . escapeshellarg($filePath) . ' ' . escapeshellarg($outPrefix) . ' 2>&1';
                @exec($cmd, $cmdOut, $cmdRet);
                if ($cmdRet === 0) {
                    foreach (glob($outPrefix . '-*.png') as $png) {
                        $images[] = $png;
                    }
                }
            } else {
                // Try ImageMagick (magick or convert)
                @exec("$which magick 2>&1", $mout, $mret);
                $useMagick = ($mret === 0 && is_array($mout));
                if (!$useMagick) {
                    @exec("$which convert 2>&1", $cout, $cret);
                    $useMagick = ($cret === 0 && is_array($cout));
                }

                if ($useMagick) {
                    $outPattern = $tmpBase . DIRECTORY_SEPARATOR . 'page.png';
                    $cmd = 'magick convert -density 300 ' . escapeshellarg($filePath) . ' ' . escapeshellarg($outPattern) . ' 2>&1';
                    @exec($cmd, $cmdOut, $cmdRet);
                    foreach (glob($tmpBase . DIRECTORY_SEPARATOR . '*.png') as $png) {
                        $images[] = $png;
                    }
                }
            }

            if (empty($images)) {
                Log::info('ResumeVerification: no image converters available or conversion produced no images; skipping OCR', ['path' => $filePath]);
                @array_map('unlink', glob($tmpBase . DIRECTORY_SEPARATOR . '*'));
                @rmdir($tmpBase);
                return '';
            }

            // Ensure tesseract exists
            @exec("$which tesseract 2>&1", $tout, $tret);
            if ($tret !== 0) {
                Log::info('ResumeVerification: tesseract not found; cannot OCR images', ['path' => $filePath]);
                @array_map('unlink', glob($tmpBase . DIRECTORY_SEPARATOR . '*'));
                @rmdir($tmpBase);
                return '';
            }

            $fullText = '';
            foreach ($images as $img) {
                $cmd = 'tesseract ' . escapeshellarg($img) . ' stdout -l eng 2>&1';
                @exec($cmd, $ocrOut, $ocrRet);
                if ($ocrRet === 0 && is_array($ocrOut)) {
                    $pageText = implode("\n", $ocrOut);
                    $pageText = trim($pageText);
                    if ($pageText !== '') {
                        $fullText .= $pageText . "\n";
                    }
                }
                unset($ocrOut);
            }

            foreach (glob($tmpBase . DIRECTORY_SEPARATOR . '*') as $f) { @unlink($f); }
            @rmdir($tmpBase);

            $fullText = trim($fullText);
            if ($fullText !== '') {
                Log::info('ResumeVerification: OCR retry produced text', ['path' => $filePath, 'chars' => strlen($fullText)]);
                return $fullText;
            }
        } catch (\Throwable $e) {
            Log::warning('ResumeVerification: OCR retry failed', ['path' => $filePath, 'error' => $e->getMessage()]);
        }

        return '';
    }
}
