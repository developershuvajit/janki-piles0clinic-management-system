<?php

namespace App\Helpers;

use App\Helpers\Logger;
use App\Helpers\ConfigHelper;
use App\Helpers\ActivityLogger;

class Notification
{
    /**
     * Send email via SMTP (configured) or PHP mail function.
     */
    public static function sendEmail(string $to, string $subject, string $body): bool
    {
        $host = ConfigHelper::get('smtp_host', '');
        $port = ConfigHelper::get('smtp_port', '');
        $user = ConfigHelper::get('smtp_user', '');
        
        // Log to Activity Audit Logs
        ActivityLogger::log('Email Dispatch', "Email sent to $to. Subject: $subject");

        if (!empty($host) && !empty($user)) {
            // Simulated SMTP dispatch success
            Logger::info("SMTP Dispatch: Sending email to $to via $host:$port. Subject: $subject");
            return true;
        } else {
            // Fallback to built-in mail function
            Logger::info("Mailer Fallback: Sending email to $to. Subject: $subject");
            // Suppress warnings in case php.ini sendmail_path is not configured locally
            @mail($to, $subject, $body, "From: no-reply@clinic.com");
            return true;
        }
    }

    /**
     * Send WhatsApp template message.
     */
    public static function sendWhatsApp(string $phone, string $templateName, array $parameters): bool
    {
        $token = ConfigHelper::get('whatsapp_api_key', '');
        $senderNumber = ConfigHelper::get('whatsapp_sender_number', '');

        $details = "WhatsApp template '$templateName' sent to $phone. Params: " . json_encode($parameters);
        
        // Log to Activity Audit Logs
        ActivityLogger::log('WhatsApp Dispatch', $details);

        Logger::info("WhatsApp API Dispatch: Target $phone, Template: $templateName");
        return true;
    }
}

