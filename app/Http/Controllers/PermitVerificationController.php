<?php

namespace App\Http\Controllers;

use App\Models\EmployerDocument;
use Smalot\PdfParser\Parser;
use App\Models\DocumentValidation;
use App\Jobs\ValidateBusinessPermitJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Spatie\PdfToText\Pdf as PdfToText;
use ThiagoAlessio\TesseractOCR\TesseractOCR;
use App\Services\ResistantDocumentService;

class PermitVerificationController extends Controller
{
    /**
     * Show the permit upload form.
     */
    public function showForm()
    {
        return view('permit.upload');
    }

    /**
     * Handle permit upload with strict validation.
     */
    public function upload(Request $request)
    {
        // 1️⃣ Basic validation: must be a PDF
        $request->validate([
            'permit' => 'required|file|mimes:pdf|max:5120', // 5MB limit
        ], [
            'permit.mimes' => 'Only PDF files are allowed.',
            'permit.max' => 'File size must not exceed 5MB.',
        ]);

        $file = $request->file('permit');

        // 2️⃣ Optional: extract text content (use Smalot parser first, then fallbacks)
        $pdfText = '';
        try {
            // Use Smalot parser for more reliable PDF text extraction when available
            $parser = new Parser();
            $pdfObj = $parser->parseFile($file->getPathname());
            $pdfText = (string) $pdfObj->getText();
        } catch (\Throwable $e) {
            Log::warning('Smalot PDF parser failed: ' . $e->getMessage());
            try {
                $tempPath = $file->getPathname();
                $pdfText = (string) @PdfToText::getText($tempPath);
            } catch (\Throwable $ex) {
                Log::warning('PdfToText quick extraction failed: ' . $ex->getMessage());
                try {
                    $pdfText = file_get_contents($file->getPathname());
                } catch (\Throwable $__) {
                    $pdfText = '';
                }
            }
        }

        $pdfText = strtolower(strip_tags((string) $pdfText));

        // 3️⃣ List of valid business document keywords
        $validDocuments = [
            'business permit',
            "mayor’s permit",
            'mayors permit',
            'dti registration',
            'barangay clearance',
            'barangay locational clearance',
        ];

        $isValid = false;
        foreach ($validDocuments as $docType) {
            if (str_contains($pdfText, strtolower($docType))) {
                $isValid = true;
                break;
            }
        }

        // 4️⃣ Reject random/unrelated files
        if (! $isValid) {
            return back()->withErrors([
                'permit' => 'The uploaded file is not a valid business document. Please upload an official permit or clearance.',
            ]);
        }

        // 5️⃣ Store and mark as pending review
        $user = $request->user();
        $folder = 'business_permits/'.($user->id ?? 'unknown');
        $path = $file->store($folder, 'public');

        // Create validation & document records
        DocumentValidation::create([
            'user_id' => $user->id,
            'document_type' => 'business_permit',
            'file_path' => $path,
            'validation_status' => 'pending_review',
            'reason' => 'Uploaded by employer via upload form',
        ]);

        EmployerDocument::create([
            'employer_id' => $user->id,
            'document_type' => 'Business Permit',
            'file_path' => $path,
            'status' => 'PENDING',
            'raw_text' => $pdfText,
        ]);

        // Optional: notify admin or queue AI verification job
        if (config('ai.features.document_validation', false) && config('ai.document_validation.business_permit.enabled', false)) {
            ValidateBusinessPermitJob::dispatch($user->id, $path);
        }

        return redirect()->back()->with('success', 'Your document has been uploaded and is pending review.');
    }
}
