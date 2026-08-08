<?php
declare(strict_types=1);

namespace App\Helpers;

class Logger
{
    /**
     * Log a message with a specific severity level.
     */
    public static function log(string $level, string $message, array $context = []): void
    {
        try {
            $logDir = LOGS_PATH;
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
            
            $logFile = $logDir . '/app.log';
            $timestamp = date('Y-m-d H:i:s');
            $contextStr = !empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '';
            $formattedMessage = sprintf("[%s] [%s] %s%s%s", $timestamp, strtoupper($level), $message, $contextStr, PHP_EOL);
            
            file_put_contents($logFile, $formattedMessage, FILE_APPEND);
        } catch (\Throwable $e) {
            // Fallback to PHP's error_log if writing to app.log fails
            error_log("Failed to write to app.log: " . $e->getMessage() . " Original message: " . $message);
        }
    }

    public static function info(string $message, array $context = []): void
    {
        self::log('info', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::log('warning', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::log('error', $message, $context);
    }
}
