<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResumeVerified extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $user;
    public $score;
    public $status;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $status = 'verified', int $score = 100)
    {
        $this->user = $user;
        $this->status = $status;
        $this->score = $score;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match ($this->status) {
            'verified' => '✅ Resume Verified - Ready to Apply for Jobs!',
            'needs_review' => '⚠️ Resume Under Admin Review',
            'rejected' => '❌ Resume Verification Failed',
            'incomplete' => '⚠️ Resume Incomplete - Action Required',
            default => 'Resume Verification Update',
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.resume-verified',
            with: [
                'user' => $this->user,
                'status' => $this->status,
                'score' => $this->score,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
