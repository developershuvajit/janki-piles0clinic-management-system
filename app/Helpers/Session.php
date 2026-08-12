<?php
declare(strict_types=1);

namespace App\Helpers;

class Session
{
    /**
     * Start secure session.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $secure = Request::isSecure();

            // Apply secure cookies session configurations if headers not yet sent (important for CLI/tests)
            if (!headers_sent()) {
                session_set_cookie_params([
                    'lifetime' => 0,
                    'path' => '/',
                    'domain' => '',
                    'secure' => $secure,
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
            }
            
            session_start();
        }
    }

    /**
     * Initialize session and check active constraints (Timeout & Remember Me).
     */
    public static function init(): void
    {
        self::start();
        
        if (self::isLoggedIn()) {
            self::checkInactivity();
        } else {
            self::checkRememberMe();
        }
    }

    /**
     * Check if session has expired due to inactivity (15 mins timeout).
     */
    private static function checkInactivity(): void
    {
        $lastActive = self::get('last_active_time');
        $timeout = 900; // 15 minutes in seconds

        if ($lastActive !== null && (time() - (int)$lastActive) > $timeout) {
            $userId = (int)self::get('user_id');
            $username = self::get('username');
            
            // Log session timeout event
            ActivityLogger::log('Session Timeout', "User {$username} was logged out due to inactivity.", $userId);
            
            // Update last logout timestamp in history
            LoginHistory::closeOpenSession($userId, 'timeout');

            self::destroy();
            self::start();
            self::setFlash('error', 'Your session has expired due to inactivity. Please log in again.');
            redirect('/login');
        }

        // Renew active timestamp
        self::set('last_active_time', time());
        
        // Throttled database active time update (once every 60 seconds)
        $lastDbUpdate = self::get('last_db_update', 0);
        if (time() - $lastDbUpdate > 60) {
            try {
                $userId = (int)self::get('user_id');
                Database::execute("UPDATE users SET last_active_at = NOW() WHERE id = :id", ['id' => $userId]);
                self::set('last_db_update', time());
            } catch (\Throwable $e) {
                // Ignore transient db failure during keepalive updates
            }
        }
    }

    /**
     * Check for secure remember me cookie and automatically log user back in.
     */
    private static function checkRememberMe(): void
    {
        $cookie = $_COOKIE['remember_me'] ?? '';
        if (empty($cookie)) {
            return;
        }

        $parts = explode(':', $cookie, 2);
        if (count($parts) !== 2) {
            self::clearRememberMeCookie();
            return;
        }

        list($userId, $token) = $parts;
        $userId = (int)$userId;

        try {
            // Find active user profile
            $user = Database::row(
                "SELECT u.*, r.slug as role_slug 
                 FROM users u 
                 LEFT JOIN roles r ON u.role_id = r.id 
                 WHERE u.id = :id AND u.status = 'active'", 
                ['id' => $userId]
            );
            
            if ($user && !empty($user['remember_token'])) {
                $expectedHash = $user['remember_token'];
                $actualHash = hash('sha256', $token);
                
                if (hash_equals($expectedHash, $actualHash)) {
                    // Map details and log user in
                    $user['role'] = $user['role_slug'] ?? $user['role'] ?? 'admin';
                    self::login($user);
                    
                    // Track login audit history
                    LoginHistory::recordLogin($userId);
                    
                    ActivityLogger::log('Remember Me Auto-Login', "User {$user['username']} auto-logged in via cookie.", $userId);
                    return;
                }
            }
        } catch (\Throwable $e) {
            Logger::error("Remember me authentication exception: " . $e->getMessage());
        }

        // Clear invalid cookie
        self::clearRememberMeCookie();
    }

    /**
     * Clear remember me cookie headers.
     */
    public static function clearRememberMeCookie(): void
    {
        if (isset($_COOKIE['remember_me'])) {
            setcookie('remember_me', '', time() - 3600, '/', '', Request::isSecure(), true);
        }
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Completely clear and destroy session.
     */
    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        session_destroy();
    }

    /**
     * Set a flash message that expires after the next request.
     */
    public static function setFlash(string $key, string $message): void
    {
        self::start();
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Retrieve and clear a flash message.
     */
    public static function getFlash(string $key): ?string
    {
        self::start();
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return null;
    }

    public static function hasFlash(string $key): bool
    {
        self::start();
        return isset($_SESSION['flash'][$key]);
    }

    /**
     * Store login session parameters, regenerating ID to block Session Fixation.
     */
    public static function login(array $user, bool $rememberMe = false): void
    {
        self::start();
        if (!headers_sent()) {
            session_regenerate_id(true);
        }
        self::set('user_id', $user['id']);
        self::set('username', $user['username']);
        self::set('email', $user['email']);
        self::set('role', $user['role_slug'] ?? $user['role'] ?? 'admin');
        self::set('role_slug', $user['role_slug'] ?? $user['role'] ?? '');
        self::set('role_id', $user['role_id'] ?? null);
        self::set('branch_id', $user['branch_id'] ?? null);
        self::set('logged_in', true);
        self::set('last_active_time', time());
        self::set('last_db_update', time());

        $userId = (int)$user['id'];

        if ($rememberMe) {
            $token = bin2hex(random_bytes(32));
            $hash = hash('sha256', $token);
            
            try {
                // Update remember token and login timestamps
                Database::execute(
                    "UPDATE users SET remember_token = :token, last_login_at = NOW(), last_active_at = NOW() WHERE id = :id", 
                    ['token' => $hash, 'id' => $userId]
                );
                
                // Set cookie for 30 days
                setcookie('remember_me', $userId . ':' . $token, time() + 30 * 86400, '/', '', Request::isSecure(), true);
            } catch (\Throwable $e) {
                Logger::error("Failed to update remember token in db: " . $e->getMessage());
            }
        } else {
            try {
                // Update timestamps in DB without remember token
                Database::execute(
                    "UPDATE users SET remember_token = NULL, last_login_at = NOW(), last_active_at = NOW() WHERE id = :id", 
                    ['id' => $userId]
                );
            } catch (\Throwable $e) {
                Logger::error("Failed to update login timestamps in db: " . $e->getMessage());
            }
        }
    }

    /**
     * Logout and destroy session.
     */
    public static function logout(): void
    {
        self::start();
        $userId = (int)self::get('user_id');
        
        if ($userId > 0) {
            try {
                // Clear remember token in DB
                Database::execute("UPDATE users SET remember_token = NULL WHERE id = :id", ['id' => $userId]);
            } catch (\Throwable $e) {
                Logger::error("Logout database updates failed: " . $e->getMessage());
            }

            // Log logout history
            LoginHistory::closeOpenSession($userId, 'logout');
        }
        
        self::clearRememberMeCookie();
        self::destroy();
    }

    /**
     * Validate if session indicates active login.
     */
    public static function isLoggedIn(): bool
    {
        return self::get('logged_in') === true;
    }

    /**
     * Retrieve active session user properties.
     */
    public static function user(): ?array
    {
        if (!self::isLoggedIn()) {
            return null;
        }
        return [
            'id' => self::get('user_id'),
            'username' => self::get('username'),
            'email' => self::get('email'),
            'role' => self::get('role'),
            'role_slug' => self::get('role_slug'),
            'role_id' => self::get('role_id'),
            'branch_id' => self::get('branch_id')
        ];
    }

    /**
     * Branch the active user is scoped to, or null when unrestricted.
     */
    public static function branchId(): ?int
    {
        $branchId = self::get('branch_id');
        return $branchId ? (int)$branchId : null;
    }
}
