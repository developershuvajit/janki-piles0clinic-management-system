<?php

namespace App\Controllers;

use App\Models\Discharge;
use App\Models\Ipd;
use App\Helpers\Session;
use App\Helpers\Permission;
use App\Helpers\PDFHelper;

class DischargeController
{
    public function __construct()
    {
        if (!Session::get('user_id')) {
            redirect('/login');
        }
    }

    /**
     * Show form to prepare discharge summary details.
     */
    public function summaryForm($admissionId)
    {
        Permission::check('manage_ipd');
        
        $admission = Ipd::findAdmission((int)$admissionId);
        if (!$admission) {
            Session::setFlash('error', 'Admission record not found.');
            redirect('/admin/ipd');
        }

        $summary = Discharge::find((int)$admissionId);

        include VIEWS_PATH . '/admin/ipd/discharge_summary.php';
    }

    /**
     * Save discharge summary.
     */
    public function saveSummary()
    {
        Permission::check('manage_ipd');
        
        $admId = (int)$_POST['ipd_admission_id'];
        
        // Handle files uploads for doctor signature & hospital seal
        $sigPath = null;
        $sealPath = null;

        if (isset($_FILES['doctor_signature']) && $_FILES['doctor_signature']['error'] === UPLOAD_ERR_OK) {
            $sigPath = '/uploads/' . time() . '_sig_' . basename($_FILES['doctor_signature']['name']);
            move_uploaded_file($_FILES['doctor_signature']['tmp_name'], PUBLIC_PATH . $sigPath);
        }

        if (isset($_FILES['hospital_seal']) && $_FILES['hospital_seal']['error'] === UPLOAD_ERR_OK) {
            $sealPath = '/uploads/' . time() . '_seal_' . basename($_FILES['hospital_seal']['name']);
            move_uploaded_file($_FILES['hospital_seal']['tmp_name'], PUBLIC_PATH . $sealPath);
        }

        $data = [
            'ipd_admission_id' => $admId,
            'diagnosis' => trim($_POST['diagnosis']),
            'treatment_summary' => trim($_POST['treatment_summary']),
            'advice' => trim($_POST['advice']),
            'diet' => trim($_POST['diet']),
            'follow_up_instructions' => trim($_POST['follow_up_instructions']),
            'doctor_signature' => $sigPath,
            'hospital_seal' => $sealPath
        ];

        $success = Discharge::save($data);
        if ($success) {
            Session::setFlash('success', 'Discharge summary saved successfully.');
            redirect('/admin/ipd');
        } else {
            Session::setFlash('error', 'Failed saving discharge summary.');
            redirect('/admin/ipd/discharge-summary/' . $admId);
        }
    }

    /**
     * Print discharge summary layout.
     */
    public function printSummary($admissionId)
    {
        Permission::check('manage_ipd');
        
        $summary = Discharge::getPrintData((int)$admissionId);
        if (!$summary) {
            Session::setFlash('error', 'Discharge summary not generated yet.');
            redirect('/admin/ipd');
        }

        include VIEWS_PATH . '/admin/ipd/discharge_summary_print.php';
    }

    /**
     * Download PDF format of discharge summary.
     */
    public function pdfSummary($admissionId)
    {
        Permission::check('manage_ipd');
        
        $summary = Discharge::getPrintData((int)$admissionId);
        if (!$summary) {
            Session::setFlash('error', 'Discharge summary not generated yet.');
            redirect('/admin/ipd');
        }

        // Initialize PDF library
        $pdf = new PDFHelper();
        $pdf->AddPage();
        
        // Print header
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'DISCHARGE SUMMARY', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 5, 'MedClinic Healthcare Stay File', 0, 1, 'C');
        $pdf->Ln(10);

        // Metadata box
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 6, 'Patient Name:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(60, 6, $summary['patient_name'], 0, 0);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 6, 'Patient Code:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(40, 6, $summary['patient_code'], 0, 1);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 6, 'Admit Date:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(60, 6, $summary['admission_date'], 0, 0);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 6, 'Discharge Date:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(40, 6, $summary['discharge_date'], 0, 1);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 6, 'Ward Room:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(60, 6, $summary['room_number'] . " (Bed: " . $summary['bed_number'] . ")", 0, 0);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 6, 'Doctor Assigned:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(40, 6, "Dr. " . $summary['doctor_name'], 0, 1);
        $pdf->Ln(8);

        // Sections
        $sections = [
            'Diagnosis' => $summary['diagnosis'],
            'Treatment Details' => $summary['treatment_summary'],
            'Home Medication & Advice' => $summary['advice'],
            'Recommended Diet' => $summary['diet'],
            'Follow Up Instructions' => $summary['follow_up_instructions']
        ];

        foreach ($sections as $title => $content) {
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->SetFillColor(240, 240, 240);
            $pdf->Cell(0, 7, $title, 0, 1, 'L', true);
            $pdf->Ln(2);
            $pdf->SetFont('Arial', '', 10);
            $pdf->MultiCell(0, 5, $content ?: 'N/A');
            $pdf->Ln(5);
        }

        $pdf->Output('I', "discharge_summary_" . $summary['patient_code'] . ".pdf");
        exit;
    }
}
