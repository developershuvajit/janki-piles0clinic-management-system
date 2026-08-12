<?php
declare(strict_types=1);

namespace App\Helpers;

class Security
{
    /**
     * Generate or fetch the CSRF token from active session.
     */
    public static function generateCsrfToken(): string
    {
        Session::start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Timing-safe verification of the CSRF token.
     */
    public static function verifyCsrfToken(?string $token): bool
    {
        Session::start();
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Verify the submitted CSRF token, or flash an error and redirect away.
     */
    public static function requireCsrfToken(string $redirectPath, string $message = 'Security token expired.'): void
    {
        if (self::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            return;
        }

        Session::setFlash('error', $message);
        redirect($redirectPath);
    }

    /**
     * Recursively sanitizes user input (removes tags, trims whitespace).
     */
    public static function sanitize(mixed $data): mixed
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::sanitize($value);
            }
            return $data;
        }

        if (is_string($data)) {
            // Strip script tags (including content) first
            $data = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $data);
            // Strip all remaining HTML tags
            return trim(strip_tags($data));
        }

        return $data;
    }

    /**
     * Securely hash password using BCRYPT.
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verify password hash.
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Simple file-based rate limiter.
     *
     * @param string $key      Unique identifier (e.g., 'login_<ip>')
     * @param int    $maxAttempts Maximum allowed attempts in the window
     * @param int    $windowSeconds Time window in seconds
     * @return bool  Returns TRUE if rate limit is exceeded (block request)
     */
    public static function rateLimit(string $key, int $maxAttempts = 5, int $windowSeconds = 300): bool
    {
        $cacheDir = ROOT_PATH . '/logs/rate_limits';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Sanitize key for filesystem safety
        $safeKey  = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $key);
        $filePath = $cacheDir . '/' . $safeKey . '.json';

        $now     = time();
        $data    = [];

        if (file_exists($filePath)) {
            $raw = file_get_contents($filePath);
            $data = json_decode($raw, true) ?: [];
        }

        // Purge timestamps outside the window
        $data = array_filter($data, fn($ts) => ($now - $ts) < $windowSeconds);
        $data = array_values($data);

        // Check limit
        if (count($data) >= $maxAttempts) {
            return true; // Rate limit exceeded
        }

        // Record this attempt
        $data[] = $now;
        file_put_contents($filePath, json_encode($data), LOCK_EX);

        return false; // Within limit
    }

    /**
     * Clear all rate-limit attempts for a given key (call on successful auth).
     */
    public static function rateLimitClear(string $key): void
    {
        $cacheDir = ROOT_PATH . '/logs/rate_limits';
        $safeKey  = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $key);
        $filePath = $cacheDir . '/' . $safeKey . '.json';
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * Detect dangerous double-extensions (e.g. shell.php.jpg).
     */
    public static function hasDoubleExtension(string $filename): bool
    {
        $dangerous = ['php', 'php3', 'php4', 'php5', 'phtml', 'phps', 'phar',
                      'asp', 'aspx', 'jsp', 'py', 'rb', 'pl', 'sh', 'cgi', 'svg'];
        $parts = explode('.', strtolower(basename($filename)));
        // If filename has more than 2 parts, check intermediate extensions
        if (count($parts) > 2) {
            // Check all extensions except the last one
            for ($i = 1; $i < count($parts) - 1; $i++) {
                if (in_array($parts[$i], $dangerous, true)) {
                    return true;
                }
            }
        }
        // Also check if ANY part is a dangerous extension (double ext like file.php.jpg)
        foreach (array_slice($parts, 1, -1) as $part) {
            if (in_array($part, $dangerous, true)) {
                return true;
            }
        }
        return false;
    }
}
