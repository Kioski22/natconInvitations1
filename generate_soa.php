<?php
require 'db.php'; // your DB connection
require 'vendor/autoload.php';
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');

// Get email from URL
$email = isset($_GET['email']) ? $conn->real_escape_string($_GET['email']) : '';

if (empty($email)) {
    die("No email provided.");
}

// Query database for this email
$sql = "SELECT company, address FROM invitations WHERE email='$email' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("No data found for this email.");
}

$row = $result->fetch_assoc();
$company = $row['company'];
$address = $row['address'];

// Create TCPDF instance
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Document settings
$pdf->SetCreator('PSME');
$pdf->SetAuthor('PSME');
$pdf->SetTitle('Statement of Account');
$pdf->SetMargins(15, 20, 15);
$pdf->SetAutoPageBreak(TRUE, 20);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage();

// Styles
$pdf->SetFont('helvetica', '', 10);

// Header
$pdf->Cell(0, 0, 'PHILIPPINE SOCIETY OF MECHANICAL ENGINEERS INC.', 0, 1, 'C', 0, '', 0);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(0, 0, '19 Scout Bayoran St., Bgy South Triangle, Quezon City, Philippines', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, 'Tel: (632) 7752-2527 | Email: national@psmeinc.org.ph | Website: www.psmeinc.org.ph', 0, 1, 'C', 0, '', 0);

$pdf->Ln(5);

// Title
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 0, 'STATEMENT OF ACCOUNT', 0, 1, 'C', 0, '', 0);

$pdf->Ln(8);

// Company info section with dynamic data
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(30, 0, 'Date:', 0, 0);
$pdf->Cell(0, 0, date('F j, Y'), 0, 1);

$pdf->Cell(30, 0, 'Company:', 0, 0);
$pdf->Cell(0, 0, $company, 0, 1);

$pdf->Cell(30, 0, 'Address:', 0, 0);
$pdf->Cell(0, 0, $address, 0, 1);

$pdf->Cell(30, 0, 'TIN:', 0, 0);
$pdf->Cell(0, 0, '', 0, 1);

$pdf->Cell(30, 0, 'Business Style:', 0, 0);
$pdf->Cell(0, 0, '', 0, 1);

$pdf->Ln(10);

// Table header
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(10, 7, 'No.', 1, 0, 'C');
$pdf->Cell(80, 7, 'Participant', 1, 0, 'C');
$pdf->Cell(50, 7, 'Type of Membership', 1, 0, 'C');
$pdf->Cell(30, 7, 'Amount', 1, 1, 'C');

// Sample empty table rows
$pdf->SetFont('helvetica', '', 10);
for ($i = 1; $i <= 5; $i++) {
    $pdf->Cell(10, 7, '', 1, 0, 'C');
    $pdf->Cell(80, 7, '', 1, 0, 'L');
    $pdf->Cell(50, 7, '', 1, 0, 'L');
    $pdf->Cell(30, 7, '', 1, 1, 'R');
}

$pdf->Ln(5);

// Total section
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(140, 7, 'TOTAL CHARGES', 1, 0, 'R');
$pdf->Cell(30, 7, '', 1, 1, 'R');

$pdf->Ln(5);

// Amount in words
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 0, 'Amount in Words:', 0, 1);
$pdf->Cell(0, 0, '', 0, 1);

$pdf->Ln(10);

// Bank details section
$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(0, 0,
"Please remit payment through the following bank details:\n
Account Name: Phil Society of Mechanical Engineers Inc
Account No.: 004708006927
Bank Name: BDO Unibank, Inc.
Branch Code: 00470
Swift Code: BNORPHMM
Bank Address: Sct. Limbaga Tomas Morato
PSME TIN: 002-304-376-000 (non-VAT)",
0, 'L', false, 1);

$pdf->Ln(5);

// Prepared by
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 0, 'Prepared by:', 0, 1);
$pdf->Ln(10);
$pdf->Cell(0, 0, '_______________________', 0, 1);
$pdf->Cell(0, 0, 'JERICHO MORALES', 0, 1);
$pdf->Cell(0, 0, 'Finance Assistance', 0, 1);

$pdf->Ln(10);

// Footer
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0, 0, '"The Home of Resilient Filipino Mechanical Engineers"', 0, 1, 'C');

$pdf->Output('SOA_Template.pdf', 'I'); // Inline output
exit;
?>