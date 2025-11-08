<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;

class ContactChangeOtpNotification extends Notification
{
    use Queueable;

    public $type;
    public $newValue;
    public $otp;
    public $expiresAt;

    public function __construct(string $type, string $newValue, string $otp, $expiresAt)
    {
        $this->type = $type;
        $this->newValue = $newValue;
        $this->otp = $otp;
        $this->expiresAt = $expiresAt;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $expires = $this->expiresAt instanceof Carbon ? $this->expiresAt->toDayDateTimeString() : (string) $this->expiresAt;
        $label = $this->type === 'phone' ? 'phone number' : 'contact';

        return (new MailMessage)
            ->subject('Your verification code')
            ->greeting('Hello!')
            ->line("A request was received to change your {$label} to {$this->newValue}.")
            ->line('Use the code below to confirm the change:')
            ->line('')
            ->line('Code: '.$this->otp)
            ->line('This code will expire at '.$expires.'.')
            ->line('If you did not request this change, please contact support.');
    }
}
