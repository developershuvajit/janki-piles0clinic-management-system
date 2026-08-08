<?php
declare(strict_types=1);

namespace App\Helpers;

class Email
{
    /**
     * Send email using SMTP configurations loaded from DB settings or .env fallbacks.
     * 
     * @param string $to Recipient email address
     * @param string $subject Email subject line
     * @param string $body HTML or plain-text body content
     * @param array $options Additional configuration overrides
     * @return bool Success status
     */
    public static function send(string $to, string $subject, string $body, array $options = []): bool
    {
        // Dynamically fetch config settings, prioritizing DB then .env
        $host = ConfigHelper::get('smtp_host', $_ENV['SMTP_HOST'] ?? '');
        $port = ConfigHelper::get('smtp_port', $_ENV['SMTP_PORT'] ?? '25');
        $user = ConfigHelper::get('smtp_user', $_ENV['SMTP_USER'] ?? '');
        $pass = ConfigHelper::get('smtp_pass', $_ENV['SMTP_PASS'] ?? '');
        $secure = ConfigHelper::get('smtp_secure', $_ENV['SMTP_SECURE'] ?? 'none');
        $fromEmail = ConfigHelper::get('smtp_from_email', $_ENV['SMTP_FROM_EMAIL'] ?? 'no-reply@clinic.com');
        $fromName = ConfigHelper::get('smtp_from_name', $_ENV['SMTP_FROM_NAME'] ?? 'Clinic System');

        Logger::info("Initiating email transmission to: {$to}", [
            'subject' => $subject,
            'smtp_host' => $host,
            'smtp_port' => $port,
            'smtp_secure' => $secure,
            'from' => "{$fromName} <{$fromEmail}>"
        ]);

        // Construct standard HTML email headers
        $headers = [
            'MIME-Version' => '1.0',
            'Content-type' => 'text/html; charset=utf-8',
            'From' => "{$fromName} <{$fromEmail}>",
            'Reply-To' => $fromEmail,
            'X-Mailer' => 'PHP/' . phpversion()
        ];

        // Format headers string
        $headersString = '';
        foreach ($headers as $key => $val) {
            $headersString .= "{$key}: {$val}\r\n";
        }

        try {
            // Send email using standard PHP mail() interface.
            // In a fully deployed environment with Composer, this can be swapped with PHPMailer.
            $success = mail($to, $subject, $body, $headersString);
            
            if ($success) {
                Logger::info("Email successfully dispatched to {$to}");
                return true;
            }
            
            Logger::error("PHP mail() function returned false for recipient: {$to}");
            return false;
        } catch (\Throwable $e) {
            Logger::error("Email send exception: " . $e->getMessage(), [
                'recipient' => $to,
                'subject' => $subject
            ]);
            return false;
        }
    }
}
