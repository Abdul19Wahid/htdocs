<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use RuntimeException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

/**
 * Sends mail through Brevo's HTTPS API (https://api.brevo.com) instead of
 * SMTP. This exists specifically because InfinityFree — and most free
 * shared hosts — block outbound SMTP ports (25/465/587), so a normal
 * MAIL_MAILER=smtp configuration silently never delivers anything no
 * matter how correct the credentials are. Outbound HTTPS (port 443) is
 * allowed, so an HTTP-API-based provider is the reliable option here.
 *
 * No extra Composer package required — this only uses Laravel's built-in
 * Http client, which matters because this app's shared hosting makes it
 * awkward to `composer require` and reliably get a full vendor/ re-upload.
 */
class BrevoApiTransport extends AbstractTransport
{
    public function __construct(protected string $apiKey)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        $envelope = $message->getEnvelope();

        $sender = $envelope->getSender();

        $payload = [
            'sender' => ['email' => $sender->getAddress(), 'name' => $sender->getName() ?: null],
            'to' => array_map(fn ($addr) => array_filter([
                'email' => $addr->getAddress(),
                'name' => $addr->getName() ?: null,
            ]), $envelope->getRecipients()),
            'subject' => $email->getSubject(),
            'htmlContent' => $email->getHtmlBody() ?: '<p>'.e($email->getTextBody()).'</p>',
        ];

        if ($email->getTextBody()) {
            $payload['textContent'] = $email->getTextBody();
        }

        $response = Http::withHeaders([
            'api-key' => $this->apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', $payload);

        if ($response->failed()) {
            throw new RuntimeException(
                'Brevo API mail send failed: '.$response->status().' '.$response->body()
            );
        }
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}
