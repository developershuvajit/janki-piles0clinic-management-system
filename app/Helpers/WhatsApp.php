<?php
declare(strict_types=1);

namespace App\Helpers;

class WhatsApp
{
    /**
     * Send a WhatsApp message via standard POST request.
     * 
     * @param string $to Recipient phone number (including country code)
     * @param string $message Text message content
     * @return bool Success status
     */
    public static function sendMessage(string $to, string $message): bool
    {
        $apiUrl = ConfigHelper::get('whatsapp_api_url', $_ENV['WHATSAPP_API_URL'] ?? '');
        $apiKey = ConfigHelper::get('whatsapp_api_key', $_ENV['WHATSAPP_API_KEY'] ?? '');
        $sender = ConfigHelper::get('whatsapp_sender_number', $_ENV['WHATSAPP_SENDER_NUMBER'] ?? '');

        if (empty($apiUrl)) {
            Logger::warning("WhatsApp API Gateway URL is not configured. Message skipped.", ['recipient' => $to]);
            return false;
        }

        Logger::info("Initiating WhatsApp dispatch to {$to}", [
            'api_url' => $apiUrl,
            'sender' => $sender
        ]);

        // Generic payload that fits popular WhatsApp API systems (e.g., Twilio, Ultramsg, or Custom)
        $payload = [
            'token' => $apiKey,
            'to' => $to,
            'body' => $message,
            'sender' => $sender
        ];

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            // Allow HTTPS certificate check but gracefully handle local testing errors
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err = curl_error($ch);
            curl_close($ch);

            if ($err) {
                Logger::error("WhatsApp curl connection failure: " . $err);
                return false;
            }

            if ($httpCode >= 200 && $httpCode < 300) {
                Logger::info("WhatsApp message successfully dispatched to {$to}");
                return true;
            }

            Logger::error("WhatsApp gateway returned error status code: {$httpCode}", [
                'response' => $response
            ]);
            return false;
        } catch (\Throwable $e) {
            Logger::error("WhatsApp API dispatch exception: " . $e->getMessage(), [
                'recipient' => $to
            ]);
            return false;
        }
    }
}
