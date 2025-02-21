<?php
require_once '../config.php';
require_once '../lib/fpdf/fpdf.php'; // Include FPDF
require_once '../lib/fpdi/src/autoload.php'; // Include FPDI

use setasign\Fpdi\Fpdi;

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Error: No invoice ID provided.");
}

// Fetch invoice details
$stmt = $conn->prepare("SELECT * FROM invoice_list WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: Invoice not found.");
}

$invoice = $result->fetch_assoc();

// Create PDF instance
$pdf = new Fpdi();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);

// Add Title
$pdf->Cell(190, 10, 'Bodhitree Invoice Report', 0, 1, 'C');
$pdf->Ln(5);

// Add Invoice Details
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 10, 'Invoice Number:', 0, 0);
$pdf->Cell(140, 10, $invoice['tax_invoice_number'], 0, 1);

$pdf->Cell(50, 10, 'Invoice Type:', 0, 0);
$pdf->Cell(140, 10, $invoice['invoice_type'], 0, 1);

$pdf->Cell(50, 10, 'Priority:', 0, 0);
$pdf->Cell(140, 10, $invoice['priority'], 0, 1);

$pdf->Cell(50, 10, 'Status:', 0, 0);
$pdf->Cell(140, 10, $invoice['approval_status'], 0, 1);

$pdf->Cell(50, 10, 'Staff Note:', 0, 0);
$pdf->MultiCell(140, 10, $invoice['notes'], 0, 1);

$pdf->Cell(50, 10, 'Date Requested:', 0, 0);
$pdf->Cell(140, 10, $invoice['date_requested'], 0, 1);

$pdf->Cell(50, 10, 'Admin Remark:', 0, 0);
$pdf->MultiCell(140, 10, $invoice['review_notes'], 0, 1);

$pdf->Cell(50, 10, 'Signed Admin:', 0, 0);
$pdf->Cell(140, 10, $invoice['admin_reviewer_name'], 0, 1);

$pdf->Ln(10);

// Add Attached Invoice PDF
$invoice_pdf_path = '../dist/invoices/' . $invoice['pdf_invoice_path'];
if (file_exists($invoice_pdf_path)) {
    $page_count = $pdf->setSourceFile($invoice_pdf_path);

    for ($i = 1; $i <= $page_count; $i++) {
        $tplId = $pdf->importPage($i);
        $pdf->AddPage();
        $pdf->useTemplate($tplId);
    }
} else {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(190, 10, 'Invoice PDF not found.', 0, 1, 'L');
}

// Output the final PDF
$pdf->Output('D', 'BTG_TAX_REPORT_' . $invoice['tax_invoice_number'] . '.pdf'); // Force download
exit;
?>