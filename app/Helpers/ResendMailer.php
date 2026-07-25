<?php

namespace App\Helpers;

/**
 * ResendMailer - Mengirim email via Resend API (HTTPS port 443)
 * Digunakan sebagai pengganti SMTP yang diblokir oleh Railway.
 * Dokumentasi API: https://resend.com/docs/api-reference/emails/send-email
 */
class ResendMailer
{
    private string $apiKey;
    private string $apiUrl = 'https://api.resend.com/emails';

    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = $apiKey 
            ?: getenv('RESEND_API_KEY') 
            ?: ($_ENV['RESEND_API_KEY'] ?? '');
    }

    /**
     * Cek apakah Resend API Key tersedia
     */
    public static function isAvailable(): bool
    {
        $key = getenv('RESEND_API_KEY') ?: ($_ENV['RESEND_API_KEY'] ?? '');
        return !empty($key);
    }

    /**
     * Kirim email via Resend API
     *
     * @param string $to       Alamat email penerima
     * @param string $toName   Nama penerima
     * @param string $subject  Subjek email
     * @param string $htmlBody Isi email dalam format HTML
     * @param string|null $from    Alamat pengirim (opsional, default: onboarding@resend.dev)
     * @param string|null $fromName Nama pengirim (opsional)
     * @return array ['success' => bool, 'message' => string, 'id' => string|null]
     */
    public function send(
        string $to,
        string $toName,
        string $subject,
        string $htmlBody,
        ?string $from = null,
        ?string $fromName = null
    ): array {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => 'RESEND_API_KEY belum dikonfigurasi.',
                'id'      => null
            ];
        }

        // Resend free tier: gunakan onboarding@resend.dev sebagai pengirim
        // Jika sudah verifikasi domain, bisa pakai domain sendiri
        $fromAddress = $from ?: 'onboarding@resend.dev';
        $senderName  = $fromName ?: 'Casual Steps';
        $fromField   = "{$senderName} <{$fromAddress}>";

        $payload = json_encode([
            'from'    => $fromField,
            'to'     => ["{$toName} <{$to}>"],
            'subject' => $subject,
            'html'    => $htmlBody
        ]);

        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json'
            ],
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        $response   = curl_exec($ch);
        $httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError  = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return [
                'success' => false,
                'message' => 'cURL Error: ' . $curlError,
                'id'      => null
            ];
        }

        $result = json_decode($response, true);

        if ($httpCode === 200 && isset($result['id'])) {
            return [
                'success' => true,
                'message' => 'Email berhasil dikirim via Resend.',
                'id'      => $result['id']
            ];
        }

        $errorMsg = $result['message'] ?? $result['error'] ?? "HTTP {$httpCode}: {$response}";
        return [
            'success' => false,
            'message' => 'Resend API Error: ' . $errorMsg,
            'id'      => null
        ];
    }
}
