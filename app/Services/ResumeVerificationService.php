<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

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

        // Extract text from PDF
        $resumeText = $this->extractTextFromPDF(storage_path('app/public/'.$resumePath));

        if (empty($resumeText)) {
            return $this->buildResult('needs_review', 0, ['unreadable_resume'], 'Resume file could not be read or is empty');
        }

        // First, check if this is actually a resume document
        if (!$this->isActuallyAResume($resumeText)) {
            return $this->buildResult('needs_review', 0, ['not_a_resume'], 'This document does not appear to be a resume. Please upload a proper CV/Resume document.');
        }

        // Only check for basic completeness
        $this->checkBasicCompleteness($resumeText);

        // Determine status
        $status = $this->determineStatusSimple();

        return $this->buildResult($status, $this->score, $this->flags, implode('; ', $this->notes));
    }

    /**
     * Extract text from PDF resume.
     */
    private function extractTextFromPDF($filePath)
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();

            return trim($text);
        } catch (\Exception $e) {
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
        $hasEmail = preg_match('/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}/', $text);
        $hasPhone = preg_match('/\b\d{3,4}[\s-]?\d{3}[\s-]?\d{4}\b/', $text);

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
        if (preg_match('/\b\d{3,4}[\s-]?\d{3}[\s-]?\d{4}\b/', $text)) {
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
}
