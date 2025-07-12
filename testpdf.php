<?php
require 'vendor/autoload.php';
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');

// Create new PDF document with long bond paper size (8.5 x 13 inches = 215.9 x 330.2 mm)
$pdf = new TCPDF('P', 'mm', array(215.9, 330.2), true, 'UTF-8', false);

// Remove default header/footer and set zero margins
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(0, 0, 0);
$pdf->SetAutoPageBreak(false, 0);

// Add a page
$pdf->AddPage();

// Get image path
$imgPath = realpath('invitation/1.jpg');
if (!$imgPath) {
    die('Image not found');
}

// Set the background image with forced resize and slight oversizing if needed
$pdf->Image($imgPath, -1, -1, 218, 333, '', '', '', true, 300, '', false, false, 0, true);

// Set font
$pdf->SetFont('helvetica', '', 14);
$pdf->SetTextColor(255,255,255); // white text for visibility on dark backgrounds

// Add text content
$pdf->SetXY(50, 50); // adjust X and Y as needed
$pdf->Write(0, 'Test PDF with background image');

// Output the PDF
$pdf->Output('test.pdf', 'I'); // 'I' = inline in browser
exit;
?>