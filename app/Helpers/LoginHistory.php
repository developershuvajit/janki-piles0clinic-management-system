<?php
declare(strict_types=1);

namespace App\Helpers;

class LoginHistory
{
    /**
     * Record a successful login in the audit history.
     */
    public static function recordLogin(int $userId): void
    {
        try {
            Database::execute(
                "INSERT INTO login_history (user_id, ip_address, user_agent, logged_in_at) VALUES (:user_id, :ip, :ua, NOW())",
                [
                    'user_id' => $userId,
                    'ip' => Request::clientIp(),
                    'ua' => substr(Request::userAgent(), 0, 255)
                ]
            );
        } catch (\Throwable $e) {
            Logger::error("Failed recording login history entry: " . $e->getMessage(), ['user_id' => $userId]);
        }
    }

    /**
     * Stamp the logout time on the latest open history entry of a user.
     *
     * @param string $context Short label used when logging failures.
     */
    public static function closeOpenSession(int $userId, string $context = 'logout'): void
    {
        try {
            $lastHistory = Database::row(
                "SELECT id FROM login_history WHERE user_id = :user_id AND logged_out_at IS NULL ORDER BY id DESC LIMIT 1",
                ['user_id' => $userId]
            );
            if ($lastHistory) {
                Database::execute(
                    "UPDATE login_history SET logged_out_at = NOW() WHERE id = :id",
                    ['id' => $lastHistory['id']]
                );
            }
        } catch (\Throwable $e) {
            Logger::error("Failed updating history logout timestamp during {$context}: " . $e->getMessage(), ['user_id' => $userId]);
        }
    }
}
