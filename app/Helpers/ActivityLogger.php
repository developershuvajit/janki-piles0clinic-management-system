<?php
declare(strict_types=1);

namespace App\Helpers;

class ActivityLogger
{
    /**
     * Log user or system action to database activity_logs.
     */
    public static function log(string $action, string $details = '', ?int $userId = null): bool
    {
        try {
            // Auto-detect logged-in user if not provided
            if ($userId === null && Session::isLoggedIn()) {
                $userId = (int)Session::get('user_id');
            }

            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

            $sql = "INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent, created_at) 
                    VALUES (:user_id, :action, :details, :ip_address, :user_agent, NOW())";
            
            return Database::execute($sql, [
                'user_id' => $userId,
                'action' => $action,
                'details' => $details,
                'ip_address' => $ipAddress,
                'user_agent' => substr($userAgent, 0, 255)
            ]);
        } catch (\Throwable $e) {
            // Log database logging failure to file logger
            Logger::error("Failed to write activity log to database: " . $e->getMessage(), [
                'action' => $action,
                'details' => $details,
                'user_id' => $userId
            ]);
            return false;
        }
    }
}
