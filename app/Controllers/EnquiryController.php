<?php

namespace App\Controllers;

use App\Helpers\Session;
use App\Helpers\Security;
use App\Models\Enquiry;

class EnquiryController
{
    /**
     * Display all leads inside the CRM dashboard.
     */
    public static function index(): void
    {
        if (!Session::isLoggedIn()) { redirect('/login'); exit; }

        $enquiries = Enquiry::all();
        include VIEWS_PATH . '/admin/cms/enquiries.php';
    }

    /**
     * Update lead pipeline status and comments.
     */
    public static function update(): void
    {
        if (!Session::isLoggedIn()) { redirect('/login'); exit; }
        Security::requireCsrfToken('/admin/cms/enquiries', 'Invalid security token. Please try again.');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $status = trim($_POST['status'] ?? 'new');
            $notes = trim($_POST['notes'] ?? '');

            if ($id > 0) {
                Enquiry::updateStatus($id, $status, $notes);
                Session::setFlash('success', 'Lead follow-up logs updated successfully.');
            } else {
                Session::setFlash('error', 'Invalid lead ID.');
            }
        }
        redirect('/admin/cms/enquiries');
    }
}
