<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\ActivityLogger;
use App\Helpers\Database;

class AuthController
{
    /**
     * Show login page
     */
    public function showLogin(): void
    {
        if (Session::isLoggedIn()) {
            $user = Session::user();
            $role = $user['role_slug'] ?? $user['role'] ?? '';
            
            // সবাই অ্যাডমিন ড্যাশবোর্ডে যাবে (ব্রাঞ্চ অ্যাডমিন সহ)
            // শুধু ডক্টর ও রিসেপশনিস্ট আলাদা
            if ($role === 'doctor') {
                redirect('/doctor');
            } elseif ($role === 'receptionist') {
                redirect('/reception');
            } else {
                // super_admin, admin, branch_admin সবাই এখানে
                redirect('/admin/dashboard');
            }
        }
        
        view('website.login', ['title' => 'Portal Login']);
    }

    /**
     * Process login
     */
    public function login(): void
    {
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

        // Rate limiting
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $rateLimitKey = 'login_' . hash('sha256', $ip);
        
        if (Security::rateLimit($rateLimitKey, 1, 60)) {
            $this->handleError('Too many login attempts. Please wait 1 minutes.', $isAjax);
            return;
        }

        // CSRF check
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->handleError('Security validation failed. Please refresh.', $isAjax);
            return;
        }

        // Validate inputs
        $username = Security::sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $rememberMe = isset($_POST['remember_me']);

        if (empty($username) || empty($password)) {
            $this->handleError('Username and password are required.', $isAjax);
            Session::setFlash('old_username', $username);
            return;
        }

        // Verify credentials
        $user = User::verifyCredentials($username, $password);
        
        if (!$user) {
            ActivityLogger::log('Failed Login', "Failed login: {$username} from IP: {$ip}");
            $this->handleError('Invalid username or password.', $isAjax);
            Session::setFlash('old_username', $username);
            return;
        }

        // Login success - Session::login() handles everything
        Session::login($user, $rememberMe);
        
        // Log login
        ActivityLogger::log('User Login', "User {$username} logged in from IP: {$ip}", (int)$user['id']);
        Security::rateLimitClear($rateLimitKey);

        // Get role and redirect - সবাই অ্যাডমিন ড্যাশবোর্ডে (ডক্টর/রিসেপশনিস্ট বাদে)
        $roleSlug = $user['role_slug'] ?? $user['role'] ?? '';
        $targetUrl = $this->getRedirectUrl($roleSlug);

        if ($isAjax) {
            jsonResponse([
                'success' => true,
                'message' => 'Login successful!',
                'redirect' => site_url($targetUrl)
            ]);
        }

        Session::setFlash('success', 'Login successful!');
        redirect($targetUrl);
    }

    /**
     * Logout
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
     * Get redirect URL based on role
     * সবাই অ্যাডমিন ড্যাশবোর্ডে যাবে (ডক্টর/রিসেপশনিস্ট বাদে)
     */
    private function getRedirectUrl(string $role): string
    {
        return match($role) {
            'doctor'        => '/doctor',
            'receptionist'  => '/reception',
            default         => '/admin/dashboard' // super_admin, admin, branch_admin সবাই এখানে
        };
    }

    /**
     * Handle errors
     */
    private function handleError(string $message, bool $isAjax): void
    {
        if ($isAjax) {
            jsonResponse(['success' => false, 'message' => $message], 400);
        }
        Session::setFlash('error', $message);
        redirect('/login');
    }
}