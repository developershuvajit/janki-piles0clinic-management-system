<?php
declare(strict_types=1);

namespace App\Helpers;

// Attempt importing FPDF library
$fpdfPath = ROOT_PATH . '/vendor/fpdf/fpdf.php';
if (file_exists($fpdfPath)) {
    require_once $fpdfPath;
}

// If FPDF is not yet installed, register a fallback class to avoid fatal class inheritance errors
if (!class_exists('FPDF')) {
    class FPDF_Mock {
        public function __construct(string $orientation = 'P', string $unit = 'mm', string $size = 'A4') {}
        public function AddPage() {}
        public function SetFont() {}
        public function Cell() {}
        public function Ln() {}
        public function MultiCell() {}
        public function Output(string $dest = 'I', string $name = '') {
            throw new \Exception("FPDF library is not installed in 'vendor/fpdf/fpdf.php'. Please install it to generate PDFs.");
        }
    }
    class_alias('App\Helpers\FPDF_Mock', 'FPDF');
}

class PDFHelper extends \FPDF
{
    public function __construct(string $orientation = 'P', string $unit = 'mm', string $size = 'A4')
    {
        // Log warning if using mock class
        if (class_exists('App\Helpers\FPDF_Mock')) {
            Logger::warning("FPDF library is missing in vendor/fpdf/. Running PDFHelper in Mock Mode.");
        }
        parent::__construct($orientation, $unit, $size);
    }

    /**
     * Generate a simple PDF document and stream it to the browser.
     * 
     * @param string $title Document Title
     * @param string $content Document Content
     * @param string $filename File download name
     * @param string $dest Output destination ('I' = inline browser, 'D' = download, 'F' = local file)
     */
    public static function generateSimplePDF(string $title, string $content, string $filename = 'document.pdf', string $dest = 'I'): void
    {
        $pdf = new self();
        $pdf->AddPage();
        
        // Document Header
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, $title, 0, 1, 'C');
        $pdf->Ln(8);
        
        // Document Body
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 7, $content);
        
        $pdf->Output($dest, $filename);
    }
}
