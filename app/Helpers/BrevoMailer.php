<?php

namespace App\Helpers;

/**
 * BrevoMailer - Mengirim email via Brevo (Sendinblue) API (HTTPS port 443)
 * Digunakan sebagai pengganti SMTP yang diblokir oleh Railway.
 * Dokumentasi API: https://developers.brevo.com/reference/sendtransacemail
 */
class BrevoMailer
{
    private string $apiKey;
    private string $apiUrl = 'https://api.brevo.com/v3/smtp/email';

    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = $apiKey 
            ?: getenv('BREVO_API_KEY') 
            ?: ($_ENV['BREVO_API_KEY'] ?? '');
    }

    /**
     * Cek apakah Brevo API Key tersedia
     */
    public static function isAvailable(): bool
    {
        $key = getenv('BREVO_API_KEY') ?: ($_ENV['BREVO_API_KEY'] ?? '');
        return !empty($key);
    }

    /**
     * Kirim email via Brevo API
     *
     * @param string $to         Alamat email penerima
     * @param string $toName     Nama penerima
     * @param string $subject    Subjek email
     * @param string $htmlBody   Isi email dalam format HTML
     * @param string|null $fromEmail Alamat pengirim (default: SMTP_USER dari env)
     * @param string|null $fromName  Nama pengirim (default: Casual Steps)
     * @return array ['success' => bool, 'message' => string, 'id' => string|null]
     */
    public function send(
        string $to,
        string $toName,
        string $subject,
        string $htmlBody,
        ?string $fromEmail = null,
        ?string $fromName = null
    ): array {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => 'BREVO_API_KEY belum dikonfigurasi.',
                'id'      => null
            ];
        }

        // Gunakan email pengirim dari parameter atau dari env SMTP_USER
        $senderEmail = $fromEmail 
            ?: getenv('SMTP_USER') 
            ?: ($_ENV['SMTP_USER'] ?? 'admincs04@gmail.com');
        $senderName  = $fromName ?: 'Casual Steps';

        $payload = json_encode([
            'sender'      => [
                'name'  => $senderName,
                'email' => $senderEmail
            ],
            'to'          => [
                [
                    'email' => $to,
                    'name'  => $toName
                ]
            ],
            'subject'     => $subject,
            'htmlContent' => $htmlBody
        ]);

        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'api-key: ' . $this->apiKey,
                'Content-Type: application/json',
                'Accept: application/json'
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

        // Brevo returns 201 on success with messageId
        if ($httpCode === 201 && isset($result['messageId'])) {
            return [
                'success' => true,
                'message' => 'Email berhasil dikirim via Brevo.',
                'id'      => $result['messageId']
            ];
        }

        $errorMsg = $result['message'] ?? $result['error'] ?? "HTTP {$httpCode}: {$response}";
        return [
            'success' => false,
            'message' => 'Brevo API Error: ' . $errorMsg,
            'id'      => null
        ];
    }
}
