<?php

namespace App\Services\Mail;

use Illuminate\Support\Facades\Http;
use RuntimeException;
use Symfony\Component\Mime\Email;

class ZohoMailService
{
    public function send(Email $email): void
    {
        $accountId = (string)config('services.zoho_mail.account_id');

        if ($accountId === '') {
            throw new RuntimeException('Zoho Account ID nije konfigurisan.');
        }

        $to = $this->addresses($email->getTo());

        if ($to === '') {
            throw new RuntimeException('Email nema primaoca.');
        }

        $from = ($email->getFrom()[0] ?? null)?->getAddress()
            ?? config('mail.from.address');

        $html = $email->getHtmlBody();

        if ($html === null) {
            $html = nl2br(htmlspecialchars(
                $email->getTextBody() ?? '',
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            ));
        }

        $payload = [
            'fromAddress' => $from,
            'toAddress' => $to,
            'subject' => $email->getSubject() ?? '',
            'content' => $html,
            'mailFormat' => 'html',
        ];

        $cc = $this->addresses($email->getCc());
        $bcc = $this->addresses($email->getBcc());

        if ($cc !== '') {
            $payload['ccAddress'] = $cc;
        }

        if ($bcc !== '') {
            $payload['bccAddress'] = $bcc;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->accessToken(),
        ])
            ->acceptJson()
            ->asJson()
            ->timeout(15)
            ->post(
                "https://mail.zoho.eu/api/accounts/{$accountId}/messages",
                $payload
            );

        $zohoCode = data_get($response->json(), 'status.code');

        if (!$response->successful() || (int)$zohoCode !== 200) {
            throw new RuntimeException(
                "Zoho slanje nije uspjelo. HTTP: {$response->status()}, " .
                "Zoho status: " . ($zohoCode ?? 'unknown')
            );
        }
    }

    private function accessToken(): string
    {
        $clientId = (string)config('services.zoho_mail.client_id');
        $clientSecret = (string)config('services.zoho_mail.client_secret');
        $refreshToken = (string)config('services.zoho_mail.refresh_token');

        if ($clientId === '' || $clientSecret === '' || $refreshToken === '') {
            throw new RuntimeException('Zoho OAuth podaci nisu konfigurisani.');
        }

        $response = Http::asForm()
            ->timeout(10)
            ->post('https://accounts.zoho.eu/oauth/v2/token', [
                'grant_type' => 'refresh_token',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $refreshToken,
            ]);

        $token = $response->json('access_token');

        if (!$response->successful() || !is_string($token) || $token === '') {
            throw new RuntimeException(
                "Zoho OAuth osvježavanje nije uspjelo. HTTP: {$response->status()}"
            );
        }

        return $token;
    }

    private function addresses(array $addresses): string
    {
        return implode(',', array_map(
            fn($address) => $address->getAddress(),
            $addresses
        ));
    }
}
