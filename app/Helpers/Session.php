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
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
                || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
            
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
        $timeout = 900;

        if ($lastActive !== null && (time() - (int)$lastActive) > $timeout) {
            $userId = (int)self::get('user_id');
            $username = self::get('username');
            
            ActivityLogger::log('Session Timeout', "User {$username} was logged out due to inactivity.", $userId);
            
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
                // Fail silently
            }

            self::destroy();
            self::start();
            self::setFlash('error', 'Your session has expired due to inactivity. Please log in again.');
            redirect('/login');
        }

        self::set('last_active_time', time());
        
        $lastDbUpdate = self::get('last_db_update', 0);
        if (time() - $lastDbUpdate > 60) {
            try {
                $userId = (int)self::get('user_id');
                Database::execute("UPDATE users SET last_active_at = NOW() WHERE id = :id", ['id' => $userId]);
                self::set('last_db_update', time());
            } catch (\Throwable $e) {
                // Fail silently
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
            $user = Database::row(
                "SELECT u.*, r.slug as role_slug, b.id as branch_id, b.name as branch_name 
                 FROM users u 
                 LEFT JOIN roles r ON u.role_id = r.id 
                 LEFT JOIN branches b ON u.branch_id = b.id
                 WHERE u.id = :id AND u.status = 'active'", 
                ['id' => $userId]
            );
            
            if ($user && !empty($user['remember_token'])) {
                $expectedHash = $user['remember_token'];
                $actualHash = hash('sha256', $token);
                
                if (hash_equals($expectedHash, $actualHash)) {
                    $user['role'] = $user['role_slug'] ?? $user['role'] ?? 'admin';
                    self::login($user);
                    
                    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
                    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
                    Database::execute(
                        "INSERT INTO login_history (user_id, ip_address, user_agent, logged_in_at) VALUES (:user_id, :ip, :ua, NOW())", 
                        [
                            'user_id' => $userId,
                            'ip' => $ip,
                            'ua' => substr($ua, 0, 255)
                        ]
                    );
                    
                    ActivityLogger::log('Remember Me Auto-Login', "User {$user['username']} auto-logged in via cookie.", $userId);
                    return;
                }
            }
        } catch (\Throwable $e) {
            // Fail silently
        }

        self::clearRememberMeCookie();
    }

    /**
     * Clear remember me cookie headers.
     */
    public static function clearRememberMeCookie(): void
    {
        if (isset($_COOKIE['remember_me'])) {
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
                || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
            setcookie('remember_me', '', time() - 3600, '/', '', $secure, true);
        }
    }

    /**
     * Set a session value.
     */
    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session value.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if session key exists.
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a session key.
     */
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
     * Auto plays sound notification based on message type.
     */
    public static function setFlash(string $key, string $message): void
    {
        self::start();
        $_SESSION['flash'][$key] = $message;
        
        // Store sound notification type for auto-play
        // Valid types: success, error, warning, info
        $soundType = match($key) {
            'success' => 'success',
            'error', 'danger' => 'error',
            'warning' => 'warning',
            default => 'info'
        };
        
        $_SESSION['_sound_notification'] = [
            'type' => $soundType,
            'timestamp' => time()
        ];
    }

    /**
     * Get sound notification data and clear it
     */
    public static function getSoundNotification(): ?array
    {
        self::start();
        if (isset($_SESSION['_sound_notification'])) {
            $data = $_SESSION['_sound_notification'];
            unset($_SESSION['_sound_notification']);
            return $data;
        }
        return null;
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

    /**
     * Get all flash messages and clear them.
     */
    public static function getFlashes(): array
    {
        self::start();
        $flashes = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flashes;
    }

    /**
     * Check if flash message exists.
     */
    public static function hasFlash(string $key): bool
    {
        self::start();
        return isset($_SESSION['flash'][$key]);
    }

    /**
     * Check if any flash messages exist.
     */
    public static function hasAnyFlash(): bool
    {
        self::start();
        return !empty($_SESSION['flash']);
    }

    /**
     * Store login session parameters.
     */
    public static function login(array $user, bool $rememberMe = false): void
    {
        self::start();
        if (!headers_sent()) {
            session_regenerate_id(true);
        }
        
        // Basic user info
        self::set('user_id', $user['id']);
        self::set('username', $user['username']);
        self::set('email', $user['email']);
        self::set('role', $user['role_slug'] ?? $user['role'] ?? 'admin');
        self::set('role_slug', $user['role_slug'] ?? $user['role'] ?? '');
        self::set('role_id', $user['role_id'] ?? null);
        
        // Branch info
        self::set('branch_id', $user['branch_id'] ?? null);
        self::set('branch_name', $user['branch_name'] ?? null);
        self::set('branch_code', $user['branch_code'] ?? null);
        
        // Role flags
        $roleSlug = $user['role_slug'] ?? $user['role'] ?? '';
        self::set('is_branch_admin', ($roleSlug === 'branch_admin'));
        self::set('is_super_admin', in_array($roleSlug, ['super_admin', 'admin']));
        self::set('is_doctor', ($roleSlug === 'doctor'));
        self::set('is_receptionist', ($roleSlug === 'receptionist'));
        
        // Session flags
        self::set('logged_in', true);
        self::set('last_active_time', time());
        self::set('last_db_update', time());

        $userId = (int)$user['id'];

        if ($rememberMe) {
            $token = bin2hex(random_bytes(32));
            $hash = hash('sha256', $token);
            
            try {
                Database::execute(
                    "UPDATE users SET remember_token = :token, last_login_at = NOW(), last_active_at = NOW() WHERE id = :id", 
                    ['token' => $hash, 'id' => $userId]
                );
                
                $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
                    || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
                setcookie('remember_me', $userId . ':' . $token, time() + 30 * 86400, '/', '', $secure, true);
            } catch (\Throwable $e) {
                // Fail silently
            }
        } else {
            try {
                Database::execute(
                    "UPDATE users SET remember_token = NULL, last_login_at = NOW(), last_active_at = NOW() WHERE id = :id", 
                    ['id' => $userId]
                );
            } catch (\Throwable $e) {
                // Fail silently
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
                Database::execute("UPDATE users SET remember_token = NULL WHERE id = :id", ['id' => $userId]);
                
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
                // Fail silently
            }
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
            'branch_id' => self::get('branch_id'),
            'branch_name' => self::get('branch_name'),
            'branch_code' => self::get('branch_code'),
            'is_branch_admin' => self::get('is_branch_admin', false),
            'is_super_admin' => self::get('is_super_admin', false),
            'is_doctor' => self::get('is_doctor', false),
            'is_receptionist' => self::get('is_receptionist', false)
        ];
    }

    /**
     * Check if current user is branch admin
     */
    public static function isBranchAdmin(): bool
    {
        return self::get('is_branch_admin', false) === true;
    }

    /**
     * Check if current user is super admin
     */
    public static function isSuperAdmin(): bool
    {
        return self::get('is_super_admin', false) === true;
    }

    /**
     * Check if current user is doctor
     */
    public static function isDoctor(): bool
    {
        return self::get('is_doctor', false) === true;
    }

    /**
     * Check if current user is receptionist
     */
    public static function isReceptionist(): bool
    {
        return self::get('is_receptionist', false) === true;
    }

    /**
     * Get current user's branch ID
     */
    public static function getBranchId(): ?int
    {
        return self::get('branch_id');
    }

    /**
     * Get current user's branch name
     */
    public static function getBranchName(): ?string
    {
        return self::get('branch_name');
    }

    /**
     * Get current user's role
     */
    public static function getRole(): ?string
    {
        return self::get('role_slug');
    }

    /**
     * Get current user's role name
     */
    public static function getRoleName(): ?string
    {
        return self::get('role');
    }

    /**
     * Check if user has specific role
     */
    public static function hasRole(string $role): bool
    {
        return self::get('role_slug') === $role;
    }

    /**
     * Check if user has any of the given roles
     */
    public static function hasAnyRole(array $roles): bool
    {
        $userRole = self::get('role_slug');
        return in_array($userRole, $roles);
    }
}