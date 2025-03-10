<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendOTPNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var string
     */
    private string $otp;

    /**
     * @var string
     */
    private string $purpose;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $otp, string $purpose)
    {
        $this->otp = $otp;
        $this->purpose = $purpose;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = match ($this->purpose) {
            'email_verification' => 'Verify Your Email Address',
            'password_reset' => 'Reset Your Password',
            'login' => 'Login Verification Code',
            default => 'Your OTP Code',
        };

        $message = match ($this->purpose) {
            'email_verification' => 'Please use the following code to verify your email address:',
            'password_reset' => 'Please use the following code to reset your password:',
            'login' => 'Please use the following code to complete your login:',
            default => 'Please use the following code:',
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($message)
            ->line('**' . $this->otp . '**')
            ->line('This code will expire in 15 minutes.')
            ->line('If you did not request this code, no further action is required.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'otp' => $this->otp,
            'purpose' => $this->purpose,
        ];
    }
}
