<?php

namespace App\Services;

use App\Mail\MessageConfirmation;
use App\Mail\MessageResponse;
use App\Mail\OrganizationNotification;
use App\Models\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Class EmailService
 *
 * Handles email sending with support for multiple providers.
 *
 * @package App\Services
 */
class EmailService
{
    /**
     * Send a message response email.
     *
     * @param string $to The recipient email address.
     * @param string $response The response message.
     * @return bool Whether the email was sent successfully.
     */
    public function sendMessageResponse(string $to, string $response): bool
    {
        $mailable = new MessageResponse($response);
        return $this->send($to, $mailable);
    }

    /**
     * Send an email.
     *
     * @param string $to The recipient email address.
     * @param object $mailable The mailable instance.
     * @return bool Whether the email was sent successfully.
     */
    public function send(string $to, object $mailable): bool
    {
        try {
            $provider = config('mail.default', 'smtp');

            Log::info("Sending email via {$provider} provider", [
                'to' => $to,
                'mailable' => get_class($mailable),
            ]);

            Mail::to($to)->send($mailable);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email', [
                'error' => $e->getMessage(),
                'to' => $to,
                'mailable' => get_class($mailable),
            ]);
            report($e);
            return false;
        }
    }

    /**
     * Send a message confirmation email.
     *
     * @param string $to The recipient email address.
     * @param string $name The recipient name.
     * @return bool Whether the email was sent successfully.
     */
    public function sendMessageConfirmation(string $to, string $name): bool
    {
        $mailable = new MessageConfirmation($name);
        return $this->send($to, $mailable);
    }

    /**
     * Send an organization notification email.
     *
     * @param string $to The recipient email address.
     * @param Message $message The message instance.
     * @return bool Whether the email was sent successfully.
     */
    public function sendOrganizationNotification(string $to, Message $message): bool
    {
        $mailable = new OrganizationNotification($message);
        return $this->send($to, $mailable);
    }

    /**
     * Get the available email providers.
     *
     * @return array The available email providers.
     */
    public function getAvailableProviders(): array
    {
        return [
            'smtp' => 'SMTP',
            'mailgun' => 'Mailgun',
            'ses' => 'Amazon SES',
            'postmark' => 'Postmark',
            'sendgrid' => 'SendGrid',
        ];
    }

    /**
     * Get the current email provider.
     *
     * @return string The current email provider.
     */
    public function getCurrentProvider(): string
    {
        return config('mail.default', 'smtp');
    }
}
