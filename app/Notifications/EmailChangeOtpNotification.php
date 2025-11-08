<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;

class EmailChangeOtpNotification extends Notification
{
    use Queueable;

    public $otp;
    public $expiresAt;

    public function __construct(string $otp, $expiresAt)
    {
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

        return (new MailMessage)
            ->subject('Your verification code')
            ->greeting('Hello!')
            ->line('A request was received to change the email on your account. Use the code below to confirm the change:')
            ->line('')
            ->line('Code: '.$this->otp)
            ->line('This code will expire at '.$expires.'.')
            ->line('If you did not request this change, please ignore this message.');
    }
}
