<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\DocumentValidation;
use App\Models\EmployerDocument;
use App\Models\Notification;
use App\Models\User;
use Smalot\PdfParser\Parser;
use App\Jobs\ValidateBusinessPermitJob;
use Spatie\PdfToText\Pdf as PdfToText;
use ThiagoAlessio\TesseractOCR\TesseractOCR;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EmployerPermitController extends Controller
{
    public function resubmitPermit(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user->user_type ?? null) !== 'employer') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'business_permit' => 'required|file|mimes:pdf|max:5120',
        ]);

        try {
            // quick whitelist check using Smalot if possible
            try {
                $parser = new Parser();
                $pdf = $parser->parseFile($request->file('business_permit')->getPathname());
                $text = strtolower($pdf->getText() ?: '');
                $validKeywords = [
                    'business permit', "mayor's permit", 'dti registration', 'barangay clearance', 'barangay locational clearance',
                ];
                $ok = false;
                foreach ($validKeywords as $kw) {
                    if (str_contains($text, strtolower($kw))) {
                        $ok = true;
                        break;
                    }
                }
                if (! $ok) {
                    return redirect()->back()->with('error', 'The uploaded file does not look like a business permit or clearance.');
                }
            } catch (\Throwable $e) {
                Log::info('resubmitPermit: Smalot parse skipped: '.$e->getMessage());
            }

            // Call external verifier service synchronously BEFORE storing the file (so blocked files are never saved)
            $uploadedFile = $request->file('business_permit');
            $verifierUrl = env('VERIFIER_SERVICE_URL') ?: getenv('VERIFIER_SERVICE_URL') ?: null;
            $verifierResponse = null;
            if ($verifierUrl) {
                try {
                    // attach as a stream resource to satisfy Guzzle multipart expectations
                    $stream = @fopen($uploadedFile->getPathname(), 'r');
                    $response = Http::timeout(15)->attach('file', $stream, $uploadedFile->getClientOriginalName())
                        ->post($verifierUrl, ['email' => $user->email]);
                    if (is_resource($stream)) @fclose($stream);
                    if ($response->ok()) {
                        $body = (string) $response->body();
                        $decoded = json_decode($body, true);
                        $verifierResponse = is_array($decoded) ? $decoded : null;
                    } else {
                        Log::error('Verifier service returned non-200: ' . $response->status());
                        $verifierResponse = null;
                    }
                } catch (\Throwable $e) {
                    Log::error('Verifier service call failed: ' . $e->getMessage());
                    $verifierResponse = null;
                }
            }

            // If verifier explicitly BLOCKED the upload, return error without storing
            if (is_array($verifierResponse) && isset($verifierResponse['status']) && strtoupper($verifierResponse['status']) === 'BLOCKED') {
                $reason = $verifierResponse['reason'] ?? 'Blocked by document verifier.';
                return redirect()->back()->with('error', 'Upload blocked: ' . $reason);
            }

            // Not blocked: store file and proceed to create records; include extracted ai data when available
            $folder = 'business_permits/'.$user->id;
            $permitPath = $uploadedFile->store($folder, 'public');
            $absolutePath = Storage::disk('public')->path($permitPath);
            $fileHash = file_exists($absolutePath) ? hash_file('sha256', $absolutePath) : null;

            $user->business_permit_path = $permitPath;
            $user->save();

            $permitExpiry = null;
            $employerCity = $user->employer->city ?? ($user->city ?? null);
            if ($employerCity && strcasecmp(trim($employerCity), 'Mandaluyong') === 0) {
                $permitExpiry = Carbon::create(2025, 12, 31)->toDateString();
            }

            $ai_confidence = null;
            $ai_extracted = null;
            if (is_array($verifierResponse)) {
                $ai_confidence = $verifierResponse['ai_confidence'] ?? null;
                $ai_extracted = $verifierResponse['extracted_data'] ?? null;
            }

            $initial = DocumentValidation::create([
                'user_id' => $user->id,
                'document_type' => 'business_permit',
                'file_path' => $permitPath,
                'file_hash' => $fileHash,
                'is_valid' => false,
                // ensure confidence_score is an integer and not null to satisfy migrations
                'confidence_score' => is_numeric($ai_confidence) ? intval($ai_confidence) : 0,
                'validation_status' => 'pending_review',
                'reason' => 'Uploaded by employer',
                'ai_analysis' => $ai_extracted ? json_encode($ai_extracted) : null,
                'validated_by' => 'system',
                'validated_at' => now(),
                'permit_expiry_date' => $permitExpiry,
            ]);

            EmployerDocument::create([
                'email' => $user->email,
                'file_path' => $permitPath,
                'document_type' => 'BUSINESS_PERMIT',
                'status' => 'PENDING',
                'permit_expiry_date' => $permitExpiry,
                // DB expects an integer, so default to 0 when ai_confidence is missing or non-numeric
                'confidence_score' => is_numeric($ai_confidence) ? intval($ai_confidence) : 0,
            ]);

            AuditTrail::create([
                'validation_id' => $initial->id,
                'admin_id' => null,
                'action' => 'reupload_permit',
                'notes' => 'Employer re-uploaded business permit',
                'metadata' => ['file_path' => $permitPath, 'file_hash' => $fileHash],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Notification::create([
                'user_id' => $user->id,
                'type' => 'info',
                'title' => 'Business Permit Submitted',
                'message' => 'Your business permit has been received and is pending review.',
                'read' => false,
            ]);

            // notify admins
            $admins = User::where('is_admin', true)->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'warning',
                    'title' => 'New Business Permit Uploaded',
                    'message' => ($user->company_name ?? 'An employer') . ' uploaded a business permit.',
                    'read' => false,
                ]);
            }

            if (config('ai.document_validation.business_permit.enabled', false)) {
                $delay = config('ai.document_validation.business_permit.validation_delay_seconds', 10);
                ValidateBusinessPermitJob::dispatch($user->id, $permitPath, ['source' => 'resubmit'])->delay(now()->addSeconds($delay));
            }

            // ensure session flash is set explicitly before redirecting so tests reliably see it
            try {
                \Illuminate\Support\Facades\Session::flash('success', 'Permit submitted and queued for review.');
                Log::info('resubmitPermit: flashed success to session');
            } catch (\Throwable $e) {
                Log::info('Session flash failed: '.$e->getMessage());
            }
            Log::info('resubmitPermit: returning back() with success');
            return back()->with('success', 'Permit submitted and queued for review.');
        } catch (\Throwable $e) {
            Log::error('resubmitPermit failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Upload failed, please try again.');
        }
    }

    public function upload(Request $request)
    {
        $request->validate(['permit' => 'required|mimes:pdf|max:5120']);
        $file = $request->file('permit');
        $filePath = $file->getPathname();

        // extract text (Smalot -> spatie -> tesseract)
        $text = '';
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $text = (string) $pdf->getText();
        } catch (\Throwable $e) {
            Log::info('Smalot failed: '.$e->getMessage());
            try {
                $text = (string) @PdfToText::getText($filePath);
            } catch (\Throwable $e2) {
                Log::info('PdfToText failed: '.$e2->getMessage());
                try {
                    $text = (new TesseractOCR($filePath))->run();
                } catch (\Throwable $e3) {
                    Log::info('Tesseract failed: '.$e3->getMessage());
                    $text = '';
                }
            }
        }

        $normalized = strtolower(trim((string) $text));

        // unreadable
        if (! preg_match('/[A-Za-z0-9]/', $normalized)) {
            $path = $file->store('business_permits/' . ($request->user()->id ?? 'unknown'), 'public');
            $absolutePath = Storage::disk('public')->path($path);
            $fileHash = file_exists($absolutePath) ? hash_file('sha256', $absolutePath) : null;

            $permitExpiry = null;
            $employerCity = $request->user()->employer->city ?? ($request->user()->city ?? null);
            if ($employerCity && strcasecmp(trim($employerCity), 'Mandaluyong') === 0) {
                $permitExpiry = Carbon::create(2025, 12, 31)->toDateString();
            }

            DocumentValidation::create([
                'user_id' => $request->user()->id,
                'document_type' => 'business_permit',
                'file_path' => $path,
                'file_hash' => $fileHash,
                'is_valid' => false,
                'confidence_score' => 0,
                'validation_status' => 'pending_review',
                'reason' => 'Unreadable PDF - no text extracted',
                'ai_analysis' => null,
                'validated_by' => 'system',
                'validated_at' => now(),
                'permit_expiry_date' => $permitExpiry,
            ]);

            EmployerDocument::create([
                'email' => $request->user()->email,
                'file_path' => $path,
                'document_type' => 'UNKNOWN',
                'status' => 'REJECTED',
                'confidence_score' => 0,
                'raw_text' => $text,
            ]);

            return back()->with('error', 'Uploaded PDF appears unreadable and was rejected.');
        }

        // keywords
        $validKeywords = ['business permit', "mayor's permit", 'dti registration', 'barangay clearance', 'barangay locational clearance'];
        $matched = [];
        foreach ($validKeywords as $kw) {
            if (str_contains($normalized, strtolower($kw))) $matched[] = $kw;
        }
        $keywordCount = count($matched);
        $keywordScore = ($keywordCount / max(1, count($validKeywords))) * 100;

        // fields
        $issuedDate = null; $validUntil = null; $ownerName = null; $hasSignature = false;
        $datePatterns = ['/([0-9]{4}-[0-9]{2}-[0-9]{2})/', '/([0-9]{1,2}\/([0-9]{1,2})\/([0-9]{2,4}))/'];
        foreach ($datePatterns as $pat) {
            if (preg_match($pat, $text, $m)) {
                try { $d = Carbon::parse($m[0]); $issuedDate = $issuedDate ?? $d->toDateString(); } catch (\Throwable$e){}
            }
        }
        if (preg_match('/(owner|business name|company name)[:\s]*([A-Za-z0-9\-\.\&\(\)\,\s]{3,200})/i', $text, $m)) {
            $ownerName = trim($m[count($m)-1]);
        }
        if (preg_match('/(signature|signed by|signatory)/i', $text)) $hasSignature = true;

        $totalFields = 4; $presentFields = 0;
        if ($issuedDate) $presentFields++; if ($validUntil) $presentFields++; if ($ownerName) $presentFields++; if ($hasSignature) $presentFields++;
        $fieldScore = ($presentFields / $totalFields) * 100;
        $combinedConfidence = ($fieldScore + $keywordScore) / 2.0;
        $threshold = 50.0;

        $path = $file->store('business_permits/' . ($request->user()->id ?? 'unknown'), 'public');
        $absolutePath = Storage::disk('public')->path($path);
        $fileHash = file_exists($absolutePath) ? hash_file('sha256', $absolutePath) : null;

        $permitExpiry = $validUntil ?? null;
        $employerCity = $request->user()->employer->city ?? ($request->user()->city ?? null);
        if ($employerCity && strcasecmp(trim($employerCity), 'Mandaluyong') === 0) $permitExpiry = Carbon::create(2025,12,31)->toDateString();

        if ($combinedConfidence < $threshold) {
            $status = 'REJECTED';
            $userMessage = 'Uploaded permit did not meet the minimum confidence threshold and was rejected.';
        } elseif ($presentFields === $totalFields && $keywordCount > 0) {
            $status = 'PENDING';
            $userMessage = 'Uploaded permit looks complete and has been queued for admin review.';
        } else {
            $status = 'NEEDS_REVIEW';
            $userMessage = 'Uploaded permit is missing some fields and has been flagged for manual review.';
        }

        DocumentValidation::create([
            'user_id' => $request->user()->id,
            'document_type' => 'business_permit',
            'file_path' => $path,
            'file_hash' => $fileHash,
            'is_valid' => ($status === 'PENDING'),
            'confidence_score' => round($combinedConfidence,2),
            'validation_status' => 'pending_review',
            'reason' => 'Automated scan. Keywords: '.implode(', ',$matched).'. Fields: '.$presentFields.'/'.$totalFields,
            'ai_analysis' => null,
            'validated_by' => 'system',
            'validated_at' => now(),
            'permit_expiry_date' => $permitExpiry,
        ]);

        EmployerDocument::create([
            'email' => $request->user()->email,
            'file_path' => $path,
            'document_type' => ($keywordCount>0?'BUSINESS_PERMIT':'UNKNOWN'),
            'owner_name' => $ownerName,
            'issued_date' => $issuedDate,
            'valid_until' => $validUntil ?? $permitExpiry,
            'status' => $status,
            'confidence_score' => round($combinedConfidence,2),
            'raw_text' => $text,
        ]);

        $user = $request->user(); $user->business_permit_path = $path; $user->save();

        if (class_exists(ValidateBusinessPermitJob::class)) ValidateBusinessPermitJob::dispatch($request->user()->id, $path, ['source'=>'employer_upload']);

        if ($status === 'REJECTED') return back()->with('error', $userMessage);
        return back()->with('success', $userMessage);
    }

}
