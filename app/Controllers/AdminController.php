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
     * Show Administrative Dashboard.
     */
    public function dashboard(): void
    {
        // KPI 1: Total registered patients
        $totalPatients = Database::row("SELECT COUNT(*) as c FROM patients")['c'] ?? 0;

        // KPI 2: Today's OPD consultations
        $todayOpd = Database::row(
            "SELECT COUNT(*) as c FROM appointments WHERE date = CURDATE()"
        )['c'] ?? 0;

        // KPI 3: Active IPD admissions
        $activeIpd = Database::row(
            "SELECT COUNT(*) as c FROM ipd_admissions WHERE status = 'admitted'"
        )['c'] ?? 0;

        // KPI 4: Today's collected revenue
        $todayRevenue = Database::row(
            "SELECT COALESCE(SUM(paid_amount), 0) as r FROM billing WHERE DATE(updated_at) = CURDATE() AND payment_status IN ('paid','partial')"
        )['r'] ?? 0;

        // KPI 5: Low stock medicines
        $allLowStock = \App\Models\Inventory::getLowStockItems();
        $lowStockCount = count($allLowStock);
        $lowStockItems = array_slice($allLowStock, 0, 5);

        // KPI 6: Recent activity logs
        $logCount = Database::row("SELECT COUNT(*) as c FROM activity_logs WHERE DATE(created_at) = CURDATE()")['c'] ?? 0;

        $recentLogs = Database::all(
            "SELECT a.action, a.created_at, u.username 
             FROM activity_logs a 
             LEFT JOIN users u ON a.user_id = u.id 
             ORDER BY a.created_at DESC 
             LIMIT 8"
        );

        view('admin.dashboard', [
            'title'         => 'Admin Dashboard',
            'totalPatients' => $totalPatients,
            'todayOpd'      => $todayOpd,
            'activeIpd'     => $activeIpd,
            'todayRevenue'  => $todayRevenue,
            'lowStockCount' => $lowStockCount,
            'lowStockItems' => $lowStockItems,
            'logCount'      => $logCount,
            'recentLogs'    => $recentLogs,
        ]);
    }


    /**
     * Show System Configuration Form.
     */
    public function settings(): void
    {
        $settings = ConfigHelper::all();
        view('admin.settings', [
            'title' => 'System Settings',
            'settings' => $settings
        ]);
    }

    /**
     * Process configuration settings update.
     */
    public function saveSettings(): void
    {
        Security::requireCsrfToken('/admin/settings', 'Security token validation expired. Please try again.');

        $fields = [
            'smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_secure', 
            'smtp_from_email', 'smtp_from_name', 
            'whatsapp_api_url', 'whatsapp_api_key', 'whatsapp_sender_number'
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

        ActivityLogger::log('Settings Update', "Successfully updated {$updated} dynamic system configurations.");
        Session::setFlash('success', 'System configurations successfully updated.');
        redirect('/admin/settings');
    }

    /**
     * Show Activity Audit Logs.
     */
    public function logs(): void
    {
        $sql = "SELECT a.*, u.username 
                FROM activity_logs a 
                LEFT JOIN users u ON a.user_id = u.id 
                ORDER BY a.created_at DESC 
                LIMIT 100";
        $logs = Database::all($sql);
        
        view('admin.logs', [
            'title' => 'System Activity Logs',
            'logs' => $logs
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
        Security::requireCsrfToken('/admin/dashboard', 'Security verification failed.');

        if (empty($_FILES['test_file']['name'])) {
            Session::setFlash('error', 'Please choose a file to test upload.');
            redirect('/admin/dashboard');
        }

        $uploader = new Upload([
            'maxSize' => 2 * 1024 * 1024 // Limit test uploads to 2MB
        ]);

        $result = $uploader->file($_FILES['test_file'], 'test_uploads');

        if ($result['success']) {
            ActivityLogger::log('File Upload Verification', "Uploaded file saved as: " . $result['saved_as']);
            Session::setFlash('success', "File uploaded successfully! Hashed path: " . $result['path']);
        } else {
            Session::setFlash('error', "Upload failed: " . $result['error']);
        }

        redirect('/admin/dashboard');
    }
}
