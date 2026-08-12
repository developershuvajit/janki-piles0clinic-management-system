<?php
declare(strict_types=1);

namespace App\Helpers;

class Request
{
    /**
     * Detect an AJAX / XHR request.
     */
    public static function isAjax(): bool
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || ($_POST['ajax'] ?? '') === '1';
    }

    /**
     * Client IP address of the current request.
     */
    public static function clientIp(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Client user agent string of the current request.
     */
    public static function userAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    }

    /**
     * Whether the request is served over HTTPS.
     */
    public static function isSecure(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    }

    /**
     * Sanitize a set of POST fields.
     *
     * @param array<string, string> $fields Field name mapped to its default value.
     * @return array<string, mixed>
     */
    public static function sanitizedPost(array $fields): array
    {
        $data = [];
        foreach ($fields as $field => $default) {
            $data[$field] = Security::sanitize($_POST[$field] ?? $default);
        }
        return $data;
    }

    /**
     * Read a positive integer POST value, or null when absent/empty.
     */
    public static function postInt(string $field): ?int
    {
        return !empty($_POST[$field]) ? (int)$_POST[$field] : null;
    }
}
