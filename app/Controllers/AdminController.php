<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\ConfigHelper;
use App\Helpers\Database;
use App\Helpers\ActivityLogger;
use App\Helpers\PDFHelper;
use App\Helpers\QRHelper;
use App\Helpers\Upload;

class AdminController
{
    /**
     * Authenticate and authorize session prior to executing admin tasks.
     */
    public function __construct()
    {
        if (!Session::isLoggedIn()) {
            Session::setFlash('error', 'Please log in to access the administrative area.');
            redirect('/login');
        }
    }

    /**
     * Show Administrative Dashboard with Branch-wise data filtering.
     */
    public function dashboard(): void
    {
        $user = Session::user();
        $roleSlug = $user['role_slug'] ?? $user['role'] ?? '';
        $branchId = $user['branch_id'] ?? null;
        $isSuperAdmin = ($roleSlug === 'super_admin' || $roleSlug === 'admin');
        $isBranchAdmin = ($roleSlug === 'branch_admin');
        
        $db = Database::getInstance();
        $params = [];
        $branchFilter = "";
        $branchName = "All Branches";
        
        // ব্রাঞ্চ অ্যাডমিন হলে শুধু তার ব্রাঞ্চের ডাটা
        if ($isBranchAdmin && $branchId) {
            $branchFilter = " WHERE branch_id = ? ";
            $params[] = $branchId;
            
            // ব্রাঞ্চের নাম বের করি
            $branchInfo = $db->getOne("SELECT name FROM branches WHERE id = ?", [$branchId]);
            $branchName = $branchInfo ? $branchInfo : 'Unknown Branch';
            
            // সেশনে ব্রাঞ্চের নাম সেট করি (হেডারে দেখানোর জন্য)
            $_SESSION['branch_name'] = $branchName;
        }
        
        // ============================================================
        // KPI 1: Total registered patients
        // ============================================================
        if ($isBranchAdmin && $branchId) {
            $totalPatients = $db->getOne(
                "SELECT COUNT(*) as c FROM patients WHERE branch_id = ?", 
                [$branchId]
            ) ?? 0;
        } else {
            $totalPatients = $db->getOne("SELECT COUNT(*) as c FROM patients") ?? 0;
        }

        // ============================================================
        // KPI 2: Today's OPD consultations
        // ============================================================
        if ($isBranchAdmin && $branchId) {
            $todayOpd = $db->getOne(
                "SELECT COUNT(*) as c FROM appointments WHERE DATE(date) = CURDATE() AND branch_id = ?",
                [$branchId]
            ) ?? 0;
        } else {
            $todayOpd = $db->getOne(
                "SELECT COUNT(*) as c FROM appointments WHERE DATE(date) = CURDATE()"
            ) ?? 0;
        }

        // ============================================================
        // KPI 3: Active IPD admissions
        // ============================================================
        if ($isBranchAdmin && $branchId) {
            $activeIpd = $db->getOne(
                "SELECT COUNT(*) as c FROM ipd_admissions WHERE status = 'admitted' AND branch_id = ?",
                [$branchId]
            ) ?? 0;
        } else {
            $activeIpd = $db->getOne(
                "SELECT COUNT(*) as c FROM ipd_admissions WHERE status = 'admitted'"
            ) ?? 0;
        }

        // ============================================================
        // KPI 4: Today's collected revenue
        // ============================================================
        if ($isBranchAdmin && $branchId) {
            $todayRevenue = $db->getOne(
                "SELECT COALESCE(SUM(paid_amount), 0) as r FROM billing 
                 WHERE DATE(updated_at) = CURDATE() 
                 AND payment_status IN ('paid','partial') 
                 AND branch_id = ?",
                [$branchId]
            ) ?? 0;
        } else {
            $todayRevenue = $db->getOne(
                "SELECT COALESCE(SUM(paid_amount), 0) as r FROM billing 
                 WHERE DATE(updated_at) = CURDATE() 
                 AND payment_status IN ('paid','partial')"
            ) ?? 0;
        }

        // ============================================================
        // KPI 5: Low stock medicines (from medicine_stocks table)
        // ============================================================
        // medicine_stocks টেবিলে quantity এবং branch_id আছে
        // medicines টেবিলে min_stock_level আছে
        if ($isBranchAdmin && $branchId) {
            $allLowStock = $db->getAll(
                "SELECT 
                    m.id,
                    m.name,
                    m.generic_name,
                    m.sku,
                    m.category,
                    m.unit,
                    m.min_stock_level,
                    m.status,
                    ms.id as stock_id,
                    ms.batch_number,
                    ms.expiry_date,
                    ms.quantity,
                    ms.purchase_price,
                    ms.selling_price,
                    ms.branch_id
                FROM medicines m
                INNER JOIN medicine_stocks ms ON m.id = ms.medicine_id
                WHERE ms.quantity <= m.min_stock_level 
                AND ms.branch_id = ?
                ORDER BY ms.quantity ASC 
                LIMIT 20",
                [$branchId]
            );
        } else {
            $allLowStock = $db->getAll(
                "SELECT 
                    m.id,
                    m.name,
                    m.generic_name,
                    m.sku,
                    m.category,
                    m.unit,
                    m.min_stock_level,
                    m.status,
                    ms.id as stock_id,
                    ms.batch_number,
                    ms.expiry_date,
                    ms.quantity,
                    ms.purchase_price,
                    ms.selling_price,
                    ms.branch_id
                FROM medicines m
                INNER JOIN medicine_stocks ms ON m.id = ms.medicine_id
                WHERE ms.quantity <= m.min_stock_level
                ORDER BY ms.quantity ASC 
                LIMIT 20"
            );
        }
        $lowStockCount = count($allLowStock);
        $lowStockItems = array_slice($allLowStock, 0, 5);

        // ============================================================
        // KPI 6: Recent activity logs
        // ============================================================
        if ($isBranchAdmin && $branchId) {
            $logCount = $db->getOne(
                "SELECT COUNT(*) as c FROM activity_logs 
                 WHERE DATE(created_at) = CURDATE() 
                 AND branch_id = ?",
                [$branchId]
            ) ?? 0;
            
            $recentLogs = $db->getAll(
                "SELECT a.action, a.created_at, u.username 
                 FROM activity_logs a 
                 LEFT JOIN users u ON a.user_id = u.id 
                 WHERE a.branch_id = ? 
                 ORDER BY a.created_at DESC 
                 LIMIT 8",
                [$branchId]
            );
        } else {
            $logCount = $db->getOne(
                "SELECT COUNT(*) as c FROM activity_logs WHERE DATE(created_at) = CURDATE()"
            ) ?? 0;
            
            $recentLogs = $db->getAll(
                "SELECT a.action, a.created_at, u.username 
                 FROM activity_logs a 
                 LEFT JOIN users u ON a.user_id = u.id 
                 ORDER BY a.created_at DESC 
                 LIMIT 8"
            );
        }

        // ============================================================
        // Branches list (Only Super Admin)
        // ============================================================
        $branches = [];
        if ($isSuperAdmin) {
            $branches = $db->getAll("SELECT id, name FROM branches ORDER BY name");
        }

        // ============================================================
        // View Render
        // ============================================================
        view('admin.dashboard', [
            'title'          => $isBranchAdmin ? "Branch Dashboard - {$branchName}" : 'Admin Dashboard',
            'totalPatients'  => $totalPatients,
            'todayOpd'       => $todayOpd,
            'activeIpd'      => $activeIpd,
            'todayRevenue'   => $todayRevenue,
            'lowStockCount'  => $lowStockCount,
            'lowStockItems'  => $lowStockItems,
            'logCount'       => $logCount,
            'recentLogs'     => $recentLogs,
            'branches'       => $branches,
            'isBranchAdmin'  => $isBranchAdmin,
            'isSuperAdmin'   => $isSuperAdmin,
            'branchName'     => $branchName,
            'branchId'       => $branchId,
            'user'           => $user
        ]);
    }

    /**
     * Show System Configuration Form.
     * শুধুমাত্র Super Admin এর জন্য অ্যাক্সেসযোগ্য
     */
    public function settings(): void
    {
        $user = Session::user();
        $roleSlug = $user['role_slug'] ?? $user['role'] ?? '';
        
        // ব্রাঞ্চ অ্যাডমিন অ্যাক্সেস করতে পারবে না
        if ($roleSlug === 'branch_admin') {
            Session::setFlash('error', 'Access denied. System settings are only available for Super Admin.');
            redirect('/admin/dashboard');
        }
        
        $settings = ConfigHelper::all();
        view('admin.settings', [
            'title' => 'System Settings',
            'settings' => $settings
        ]);
    }

    /**
     * Process configuration settings update.
     * শুধুমাত্র Super Admin এর জন্য অ্যাক্সেসযোগ্য
     */
    public function saveSettings(): void
    {
        $user = Session::user();
        $roleSlug = $user['role_slug'] ?? $user['role'] ?? '';
        
        // ব্রাঞ্চ অ্যাডমিন অ্যাক্সেস করতে পারবে না
        if ($roleSlug === 'branch_admin') {
            Session::setFlash('error', 'Access denied. System settings are only available for Super Admin.');
            redirect('/admin/dashboard');
        }
        
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security token validation expired. Please try again.');
            redirect('/admin/settings');
        }

        $fields = [
            'smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_secure', 
            'smtp_from_email', 'smtp_from_name', 
            'whatsapp_api_url', 'whatsapp_api_key', 'whatsapp_sender_number',
            'app_name', 'app_url', 'timezone', 'date_format'
        ];

        $updated = 0;
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $value = Security::sanitize($_POST[$field]);
                if (ConfigHelper::set($field, $value)) {
                    $updated++;
                }
            }
        }

        ActivityLogger::log(
            'Settings Update', 
            "Successfully updated {$updated} dynamic system configurations.",
            null,
            $user['branch_id'] ?? null
        );
        Session::setFlash('success', 'System configurations successfully updated.');
        redirect('/admin/settings');
    }

    /**
     * Show Activity Audit Logs.
     * ব্রাঞ্চ অ্যাডমিন শুধু তার ব্রাঞ্চের লগ দেখতে পারবে
     */
    public function logs(): void
    {
        $user = Session::user();
        $roleSlug = $user['role_slug'] ?? $user['role'] ?? '';
        $branchId = $user['branch_id'] ?? null;
        $isBranchAdmin = ($roleSlug === 'branch_admin');
        
        $db = Database::getInstance();
        $logs = [];
        
        if ($isBranchAdmin && $branchId) {
            // ব্রাঞ্চ অ্যাডমিন শুধু তার ব্রাঞ্চের লগ দেখবে
            $logs = $db->getAll(
                "SELECT a.*, u.username 
                 FROM activity_logs a 
                 LEFT JOIN users u ON a.user_id = u.id 
                 WHERE a.branch_id = ? 
                 ORDER BY a.created_at DESC 
                 LIMIT 100",
                [$branchId]
            );
        } else {
            // সুপার অ্যাডমিন সব লগ দেখবে
            $logs = $db->getAll(
                "SELECT a.*, u.username 
                 FROM activity_logs a 
                 LEFT JOIN users u ON a.user_id = u.id 
                 ORDER BY a.created_at DESC 
                 LIMIT 100"
            );
        }
        
        view('admin.logs', [
            'title' => 'System Activity Logs',
            'logs' => $logs,
            'isBranchAdmin' => $isBranchAdmin,
            'branchName' => $_SESSION['branch_name'] ?? 'All Branches'
        ]);
    }

    /**
     * Verify PDF generation wrapper by downloading a test report.
     */
    public function pdfTest(): void
    {
        $user = Session::user();
        $title = "Clinic System Foundation Verification";
        
        $content = "This document validates that the PDF Library (FPDF Wrapper) is correctly initialized.\n\n"
            . "System Details:\n"
            . "- User context: " . ($user['username'] ?? 'unknown') . "\n"
            . "- Timestamp: " . date('Y-m-d H:i:s') . "\n"
            . "- Environment: " . ($_ENV['APP_ENV'] ?? 'development') . "\n"
            . "- Host URL: " . site_url() . "\n\n"
            . "Database configuration and error logging are functioning properly.";
            
        PDFHelper::generateSimplePDF($title, $content, 'system_verification_report.pdf', 'I');
    }

    /**
     * Verify QR generation by displaying a test page.
     */
    public function qrTest(): void
    {
        $data = $_GET['data'] ?? site_url('/login');
        $qrUrl = QRHelper::generate($data);
        
        view('admin.qr_test', [
            'title' => 'QR Code Verification',
            'qrUrl' => $qrUrl,
            'data' => $data
        ]);
    }

    /**
     * Verify file upload system via standard post attachment.
     */
    public function uploadTest(): void
    {
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security verification failed.');
            redirect('/admin/dashboard');
        }

        if (empty($_FILES['test_file']['name'])) {
            Session::setFlash('error', 'Please choose a file to test upload.');
            redirect('/admin/dashboard');
        }

        $uploader = new Upload([
            'maxSize' => 2 * 1024 * 1024 // Limit test uploads to 2MB
        ]);

        $result = $uploader->file($_FILES['test_file'], 'test_uploads');

        if ($result['success']) {
            $user = Session::user();
            ActivityLogger::log(
                'File Upload Verification', 
                "Uploaded file saved as: " . $result['saved_as'],
                null,
                $user['branch_id'] ?? null
            );
            Session::setFlash('success', "File uploaded successfully! Hashed path: " . $result['path']);
        } else {
            Session::setFlash('error', "Upload failed: " . $result['error']);
        }

        redirect('/admin/dashboard');
    }

    /**
     * Show user profile page.
     */
     

    public function profile(): void
        {
            $user = Session::user();
            $userId = (int)($user['id'] ?? 0);
            
            if (!$userId) {
                Session::setFlash('error', 'User not found.');
                redirect('/admin/dashboard');
            }
            
            $db = Database::getInstance();
            $userData = $db->getRow("SELECT * FROM users WHERE id = ?", [$userId]);
            
            // ব্রাঞ্চের নাম বের করি
            $branchName = '';
            if (!empty($userData['branch_id'])) {
                $branchInfo = $db->getOne("SELECT name FROM branches WHERE id = ?", [$userData['branch_id']]);
                $branchName = $branchInfo ? $branchInfo : '';
            }
            
            // রোলের নাম বের করি
            $roleName = '';
            if (!empty($userData['role_id'])) {
                $roleInfo = $db->getOne("SELECT name FROM roles WHERE id = ?", [$userData['role_id']]);
                $roleName = $roleInfo ? $roleInfo : '';
            }
            
            view('admin.profile', [
                'title' => 'My Profile',
                'user' => $userData,
                'branchName' => $branchName,
                'roleName' => $roleName
            ]);
    }

/**
 * Update Profile / Change Password.
 */
    

    public function updateProfile(): void
{
    $userId = (int)Session::get('user_id');

    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        Session::setFlash('error', 'Security validation failed.');
        redirect('/admin/profile');
    }

    $password = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirm'] ?? '';

    if (!empty($password)) {
        if (strlen($password) < 8) {
            Session::setFlash('error', 'New password must be at least 8 characters long.');
            redirect('/admin/profile');
        }
        if ($password !== $confirm) {
            Session::setFlash('error', 'Passwords do not match.');
            redirect('/admin/profile');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $db = Database::getInstance();
        // এখানে 'password_hash' ব্যবহার করুন, 'password' না
        $db->execute("UPDATE users SET password_hash = ? WHERE id = ?", [$hash, $userId]);
        
        ActivityLogger::log('Password Updated', "User ID {$userId} updated their password.");
        Session::setFlash('success', 'Password updated successfully.');
    } else {
        Session::setFlash('info', 'No changes were made to password.');
    }

    redirect('/admin/profile');
}





    /**
     * Get branch-wise data helper method.
     * This method can be used by other controllers to filter data by branch.
     */
    public static function getBranchFilter(): array
    {
        $user = Session::user();
        $roleSlug = $user['role_slug'] ?? $user['role'] ?? '';
        $branchId = $user['branch_id'] ?? null;
        
        $isBranchAdmin = ($roleSlug === 'branch_admin');
        $isSuperAdmin = ($roleSlug === 'super_admin' || $roleSlug === 'admin');
        
        return [
            'isBranchAdmin' => $isBranchAdmin,
            'isSuperAdmin' => $isSuperAdmin,
            'branchId' => $branchId,
            'hasBranchFilter' => ($isBranchAdmin && $branchId),
            'branchFilterSql' => ($isBranchAdmin && $branchId) ? " branch_id = {$branchId} " : "1=1",
            'branchFilterParam' => ($isBranchAdmin && $branchId) ? $branchId : null
        ];
    }
}