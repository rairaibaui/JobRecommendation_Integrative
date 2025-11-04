<?php

namespace App\Mail;

use App\Models\DocumentValidation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BusinessPermitValidated extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $user;
    public $validation;
    public $isApproved;
    public $isRejected;
    public $requiresReview;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, DocumentValidation $validation)
    {
        $this->user = $user;
        $this->validation = $validation;
        $this->isApproved = $validation->validation_status === 'approved';
        $this->isRejected = $validation->validation_status === 'rejected';
        $this->requiresReview = $validation->validation_status === 'pending_review';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match ($this->validation->validation_status) {
            'approved' => '✅ Business Permit Verified - Account Approved',
            'rejected' => '❌ Business Permit Verification Failed',
            'pending_review' => '⚠️ Business Permit Under Review',
            default => 'Business Permit Verification Update',
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
            view: 'emails.business-permit-validated',
            with: [
                'user' => $this->user,
                'validation' => $this->validation,
                'isApproved' => $this->isApproved,
                'isRejected' => $this->isRejected,
                'requiresReview' => $this->requiresReview,
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
