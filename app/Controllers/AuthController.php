<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\ActivityLogger;
use App\Helpers\Database;
use App\Helpers\Email;

class AuthController
{
    /**
     * Display the website login page.
     */
    /**
     * Display the single unified login page.
     */
    public function showLogin(): void
    {
        if (Session::isLoggedIn()) {
            $user = Session::user();
            $role = $user['role_slug'] ?? $user['role'] ?? '';
            if ($role === 'doctor') {
                redirect('/doctor');
            } elseif ($role === 'receptionist') {
                redirect('/reception');
            } else {
                redirect('/admin/dashboard');
            }
        }
        view('website.login', ['title' => 'Portal Login']);
    }

    /**
     * Legacy Receptionist login page (redirects to unified single login).
     */
    public function showReceptionLogin(): void
    {
        redirect('/login');
    }

    /**
     * Legacy Doctor login page (redirects to unified single login).
     */
    public function showDoctorLogin(): void
    {
        redirect('/login');
    }

    /**
     * Process authentication credentials.
     */
    public function login(): void
    {
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
            || ($_POST['ajax'] ?? '') === '1';

        // 0. Rate limiting — max 5 attempts per 5 minutes per IP
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $rateLimitKey = 'login_' . preg_replace('/[^0-9a-f_]/', '_', hash('sha256', $ip));
        if (Security::rateLimit($rateLimitKey, 5, 300)) {
            $errorMsg = 'Too many login attempts. Please wait 5 minutes and try again.';
            if ($isAjax) {
                jsonResponse(['success' => false, 'message' => $errorMsg], 429);
            }
            Session::setFlash('error', $errorMsg);
            redirect('/login');
        }

        // 1. Verify CSRF protection token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::verifyCsrfToken($csrfToken)) {
            $errorMsg = 'Security validation failed. Please refresh the page and try again.';
            if ($isAjax) {
                jsonResponse(['success' => false, 'message' => $errorMsg], 403);
            }
            Session::setFlash('error', $errorMsg);
            redirect('/login');
        }

        // 2. Read and sanitize inputs
        $username = Security::sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $rememberMe = isset($_POST['remember_me']);

        if (empty($username) || empty($password)) {
            $errorMsg = 'Username and password fields are required.';
            if ($isAjax) {
                jsonResponse(['success' => false, 'message' => $errorMsg], 400);
            }
            Session::setFlash('old_username', $username);
            Session::setFlash('error', $errorMsg);
            redirect('/login');
        }

        // 3. Verify user credentials
        $user = User::verifyCredentials($username, $password);
        if ($user) {
            // Establish session with Remember Me option
            Session::login($user, $rememberMe);
            
            // Log successful attempt in database audit
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
            try {
                Database::execute(
                    "INSERT INTO login_history (user_id, ip_address, user_agent, logged_in_at) VALUES (:user_id, :ip, :ua, NOW())",
                    [
                        'user_id' => $user['id'],
                        'ip' => $ip,
                        'ua' => substr($ua, 0, 255)
                    ]
                );
            } catch (\Throwable $e) {
                // Fail silently for history insertion
            }

            ActivityLogger::log('User Login', "User {$username} logged in successfully from IP: {$ip}.", (int)$user['id']);
            Security::rateLimitClear($rateLimitKey); // Clear failed attempts on success
            
            // Automatic Role-Based Dashboard Redirection
            $roleSlug = $user['role_slug'] ?? $user['role'] ?? '';
            $targetUrl = '/admin/dashboard';
            
            if ($roleSlug === 'doctor') {
                $targetUrl = '/doctor';
            } elseif ($roleSlug === 'receptionist') {
                $targetUrl = '/reception';
            } else {
                $targetUrl = '/admin/dashboard';
            }

            $successMsg = 'Login successful! Redirecting to your dashboard...';
            if ($isAjax) {
                jsonResponse([
                    'success' => true,
                    'message' => $successMsg,
                    'redirect' => site_url($targetUrl)
                ]);
            }
            
            Session::setFlash('success', $successMsg);
            redirect($targetUrl);
        } else {
            ActivityLogger::log('Failed Login Attempt', "Failed login attempt for username: {$username} from IP: {$ip}.");
            
            $errorMsg = 'Invalid username or password.';
            if ($isAjax) {
                jsonResponse(['success' => false, 'message' => $errorMsg], 401);
            }
            
            Session::setFlash('old_username', $username);
            Session::setFlash('error', $errorMsg);
            redirect('/login');
        }
    }

    /**
     * Terminate user session and log out.
     */
    public function logout(): void
    {
        if (Session::isLoggedIn()) {
            $user = Session::user();
            ActivityLogger::log('User Logout', "User {$user['username']} logged out.", (int)$user['id']);
        }
        
        Session::logout();
        redirect('/login');
    }

    /**
     * Reception Portal logout handler.
     */
    public function receptionLogout(): void
    {
        if (Session::isLoggedIn()) {
            $user = Session::user();
            ActivityLogger::log('Reception Logout', "Reception user {$user['username']} logged out.", (int)$user['id']);
        }
        Session::logout();
        redirect('/reception/login');
    }

    /**
     * Doctor Portal logout handler.
     */
    public function doctorLogout(): void
    {
        if (Session::isLoggedIn()) {
            $user = Session::user();
            ActivityLogger::log('Doctor Logout', "Doctor user {$user['username']} logged out.", (int)$user['id']);
        }
        Session::logout();
        redirect('/doctor/login');
    }

    /**
     * Show Forgot Password Form.
     */
    public function showForgotPassword(): void
    {
        if (Session::isLoggedIn()) {
            redirect('/admin/dashboard');
        }
        view('website.forgot_password', ['title' => 'Forgot Password']);
    }

    /**
     * Generate OTP and send via email.
     */
    public function sendOtp(): void
    {
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security validation expired. Please try again.');
            redirect('/forgot-password');
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        if (Security::rateLimit('otp_request_' . hash('sha256', $ip), 5, 900)) {
            Session::setFlash('error', 'Too many reset requests. Please wait 15 minutes and try again.');
            redirect('/forgot-password');
        }

        $email = Security::sanitize($_POST['email'] ?? '');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', 'Please enter a valid email address.');
            redirect('/forgot-password');
        }

        // Verify if user exists with this email address
        $user = Database::row("SELECT * FROM users WHERE email = :email AND status = 'active'", ['email' => $email]);
        if (!$user) {
            // Security best practice: do not confirm or deny if email exists to prevent enumeration
            // But for a clinic system, a direct error helps the staff. Let's output a user-friendly error.
            Session::setFlash('error', 'Email address not found in active records.');
            redirect('/forgot-password');
        }

        // Generate 6-digit OTP code and expiry (15 mins)
        $otp = sprintf("%06d", random_int(0, 999999));
        $expiry = date('Y-m-d H:i:s', time() + 900); // 15 mins

        try {
            Database::execute(
                "UPDATE users SET otp_code = :otp, otp_expires_at = :expiry WHERE id = :id",
                ['otp' => $otp, 'expiry' => $expiry, 'id' => $user['id']]
            );

            // Store in session for validation context
            Session::set('reset_email', $email);

            // Send Email Notification
            $subject = "MedClinic Password Reset OTP Code";
            $body = "<h3>Password Reset Code</h3>"
                  . "<p>You requested a password reset for your MedClinic console account.</p>"
                  . "<p>Your 6-digit OTP verification code is: <strong>{$otp}</strong></p>"
                  . "<p>This code expires in 15 minutes. If you did not make this request, please contact clinic security.</p>";

            Email::send($email, $subject, $body);

            // Log OTP Request
            ActivityLogger::log('OTP Requested', "Password reset OTP requested by user {$user['username']} for email {$email}.", (int)$user['id']);

            Session::setFlash('success', 'A 6-digit verification code has been sent to your email.');
            redirect('/verify-otp');
        } catch (\Throwable $e) {
            Logger::error("Password reset OTP generation failure: " . $e->getMessage());
            Session::setFlash('error', 'System error: Unable to dispatch reset code. Please try again.');
            redirect('/forgot-password');
        }
    }

    /**
     * Show OTP Verification screen.
     */
    public function showVerifyOtp(): void
    {
        if (Session::isLoggedIn()) {
            redirect('/admin/dashboard');
        }
        if (!Session::has('reset_email')) {
            Session::setFlash('error', 'Please request a password reset first.');
            redirect('/forgot-password');
        }
        view('website.verify_otp', ['title' => 'Verify OTP Code']);
    }

    /**
     * Verify submitted OTP code.
     */
    public function verifyOtp(): void
    {
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security validation expired.');
            redirect('/verify-otp');
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        if (Security::rateLimit('otp_verify_' . hash('sha256', $ip), 10, 900)) {
            Session::setFlash('error', 'Too many verification attempts. Please request a new code later.');
            redirect('/forgot-password');
        }

        $email = Session::get('reset_email');
        $code = trim($_POST['otp_code'] ?? '');

        if (empty($email) || empty($code)) {
            Session::setFlash('error', 'Verification code cannot be empty.');
            redirect('/verify-otp');
        }

        // Verify OTP and expiry check
        $user = Database::row(
            "SELECT * FROM users WHERE email = :email AND otp_code = :code AND otp_expires_at > NOW() AND status = 'active'",
            ['email' => $email, 'code' => $code]
        );

        if ($user) {
            try {
                // Clear OTP fields
                Database::execute(
                    "UPDATE users SET otp_code = NULL, otp_expires_at = NULL WHERE id = :id",
                    ['id' => $user['id']]
                );

                // Set password reset authorization flag in session
                Session::set('reset_user_id', $user['id']);

                ActivityLogger::log('OTP Verified', "Password reset OTP successfully verified for email {$email}.", (int)$user['id']);
                Session::setFlash('success', 'Verification successful! Set your new password.');
                redirect('/reset-password');
            } catch (\Throwable $e) {
                Logger::error("Error during OTP verification clean: " . $e->getMessage());
            }
        }

        ActivityLogger::log('Failed OTP Attempt', "Incorrect OTP verification attempt for email {$email}.");
        Session::setFlash('error', 'Invalid or expired verification code.');
        redirect('/verify-otp');
    }

    /**
     * Show password reset form.
     */
    public function showResetPassword(): void
    {
        if (Session::isLoggedIn()) {
            redirect('/admin/dashboard');
        }
        if (!Session::has('reset_user_id')) {
            Session::setFlash('error', 'Please verify your OTP code first.');
            redirect('/forgot-password');
        }
        view('website.reset_password', ['title' => 'Reset Password']);
    }

    /**
     * Save new password.
     */
    public function resetPassword(): void
    {
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security validation expired.');
            redirect('/reset-password');
        }

        $userId = Session::get('reset_user_id');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        if (empty($userId)) {
            Session::setFlash('error', 'Unauthorized reset session. Please request OTP again.');
            redirect('/forgot-password');
        }

        if (strlen($password) < 8) {
            Session::setFlash('error', 'Password must be at least 8 characters long.');
            redirect('/reset-password');
        }

        if ($password !== $confirm) {
            Session::setFlash('error', 'Passwords do not match.');
            redirect('/reset-password');
        }

        try {
            $hash = Security::hashPassword($password);
            Database::execute("UPDATE users SET password_hash = :hash WHERE id = :id", [
                'hash' => $hash,
                'id' => (int)$userId
            ]);

            ActivityLogger::log('Password Reset', "Password reset completed successfully.", (int)$userId);
            
            // Clean session reset keys and regenerate session ID to prevent fixation
            Session::remove('reset_email');
            Session::remove('reset_user_id');
            session_regenerate_id(true);

            Session::setFlash('success', 'Password updated successfully! Please log in.');
            redirect('/login');
        } catch (\Throwable $e) {
            Logger::error("Failed resetting password in db: " . $e->getMessage());
            Session::setFlash('error', 'Unable to reset password. Please try again.');
            redirect('/reset-password');
        }
    }
}
