<?php
require 'vendor/autoload.php';
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');
require 'db.php'; // Include your DB connection here

// Extend TCPDF to create custom Footer without page number
class MYPDF extends TCPDF {
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->SetTextColor(100, 100, 100);
        // Footer Quote centered
        $this->Cell(0, 5, '"The Home of Resilient Filipino Mechanical Engineers"', 0, 1, 'C');
    }
}

// Number to words helper function
function convertNumberToWords($number) {
    $formatter = new NumberFormatter("en", NumberFormatter::SPELLOUT);
    $words = $formatter->format($number);
    return ucwords($words) . " Pesos";
}

// ---------- Auto-generate SOA Number from DB ----------
$sql_last = "SELECT soa_number FROM soa_sequence ORDER BY id DESC LIMIT 1";
$result_last = $conn->query($sql_last);
if ($result_last && $row = $result_last->fetch_assoc()) {
    $last_number = intval(substr($row['soa_number'], -3));
} else {
    $last_number = 0;
}
$new_number = $last_number + 1;
$soa_number = 'NATCON25-' . str_pad($new_number, 3, '0', STR_PAD_LEFT);

// Insert new SOA number record
$stmt = $conn->prepare("INSERT INTO soa_sequence (soa_number) VALUES (?)");
$stmt->bind_param("s", $soa_number);
$stmt->execute();

// Get POST data
$company = $_POST['company'] ?? '';
$address = $_POST['address'] ?? '';
$participants = $_POST['participants'] ?? [];
$item_numbers = $_POST['item_number'] ?? [];
$membership_types = $_POST['membership_type'] ?? [];
$registration_fees = $_POST['registration_fee'] ?? [];
$amounts = $_POST['amount'] ?? [];
$tin = $_POST['tin'] ?? '';
$business_style = $_POST['business_style'] ?? '';
$particulars = $_POST['particulars'] ?? '';

// Initialize PDF using extended class
$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetMargins(15, 20, 15);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true); // ensure footer is printed
$pdf->AddPage();

// Add left logo
$pdf->Image('img/psme_logo.png', 15, 10, 25);

// Add right logo
$pdf->Image('img/natconlogo.jpg', 170, 10, 25);

// Set Y position after logos
$pdf->SetY(12);

// ---------- Header ----------
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetTextColor(0, 51, 102);
$pdf->Cell(0, 5, 'PHILIPPINE SOCIETY OF MECHANICAL ENGINEERS INC.', 0, 1, 'C');

$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(0, 5, 'Integrated Association of Mechanical Engineers & CPMs', 0, 1, 'C');

$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0, 5, '"The Home of Resilient Filipino Mechanical Engineers"', 0, 1, 'C');

$pdf->Ln(1);

$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(0, 4, 'PSME NATIONAL HEADQUARTERS', 0, 1, 'C');

$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(0, 4, '19 Scout Bayoran St., Bgy South Triangle', 0, 1, 'C');
$pdf->Cell(0, 4, 'Quezon City, Philippines', 0, 1, 'C');
$pdf->Cell(0, 4, 'Tel: (02) 7752-25-27', 0, 1, 'C');
$pdf->Cell(0, 4, 'Email: national@psmeinc.org.ph | Website: www.psmeinc.org.ph', 0, 1, 'C');

$pdf->Ln(5);

// ---------- Title ----------
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetTextColor(0);
$pdf->Cell(0, 0, 'STATEMENT OF ACCOUNT', 0, 1, 'C');

$pdf->Ln(3);

// Display SOA number below title
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(0, 0, 'NO: ' . $soa_number, 0, 1, 'R');

$pdf->Ln(2);

// ---------- Company Info ----------
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(30, 6, 'Date:', 0, 0);
$pdf->Cell(0, 6, date('F j, Y'), 0, 1);

$pdf->Cell(30, 6, 'Company:', 0, 0);
$pdf->Cell(0, 6, $company, 0, 1);

$pdf->Cell(30, 6, 'Address:', 0, 0);
$pdf->Cell(0, 6, $address, 0, 1);

$pdf->Cell(30, 6, 'TIN:', 0, 0);
$pdf->Cell(0, 6, $tin, 0, 1);

$pdf->Cell(30, 6, 'Business Style:', 0, 0);
$pdf->Cell(0, 6, $business_style, 0, 1);

$pdf->Cell(30, 6, 'Particulars:', 0, 0);
$pdf->Cell(0, 6, $particulars, 0, 1);

$pdf->Ln(2);

$pdf->Cell(30, 6, 'This is to formally bill you for the registration PSME 73rd National Convention', 0, 0);
$pdf->Cell(0, 6, '', 0, 1);

$pdf->Cell(30, 6, 'Please see below the details of computation:', 0, 0);
$pdf->Cell(0, 6, '', 0, 1);

// ---------- Table Header ----------
$pdf->SetFillColor(0, 64, 128);
$pdf->SetTextColor(255);
$pdf->SetFont('helvetica', 'B', 9);

$pdf->Cell(10, 8, 'NO.', 1, 0, 'C', 1);           // 10mm
$pdf->Cell(20, 8, 'ITEM NO.', 1, 0, 'C', 1);      // 20mm
$pdf->Cell(55, 8, 'PARTICIPANT NAME', 1, 0, 'C', 1); // 55mm
$pdf->Cell(38, 8, 'TYPE OF MEMBERSHIP', 1, 0, 'C', 1); // 35mm
$pdf->Cell(35, 8, 'REG. FEE', 1, 0, 'C', 1);     // 35mm
$pdf->Cell(27, 8, 'AMOUNT', 1, 1, 'C', 1);       // 25mm


// ---------- Table Rows ----------
$pdf->SetTextColor(0);
$pdf->SetFont('helvetica', '', 10);
$total = 0;

foreach ($participants as $i => $participant) {
    $item_no = $item_numbers[$i] ?? '';
    $membership_type = $membership_types[$i] ?? '';
    $registration_fee = isset($registration_fees[$i]) ? floatval($registration_fees[$i]) : 0;
    $amount = isset($amounts[$i]) ? floatval($amounts[$i]) : 0;

    $pdf->Cell(10, 7, $i + 1, 1, 0, 'C'); // NO.
    $pdf->Cell(20, 7, $item_no, 1, 0, 'C'); // ITEM NO.
    $pdf->Cell(55, 7, $participant, 1, 0, 'L'); // PARTICIPANT NAME
    $pdf->Cell(38, 7, $membership_type, 1, 0, 'L'); // TYPE OF MEMBERSHIP (use 38mm here to match header)
    $pdf->Cell(35, 7, number_format($registration_fee, 2), 1, 0, 'R'); // REG. FEE
    $pdf->Cell(27, 7, number_format($amount, 2), 1, 1, 'R'); // AMOUNT

    $total += $amount;
}

// ---------- Total Charges ----------
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(158, 7, 'TOTAL CHARGES', 1, 0, 'R', 1); // Adjusted to match new width sum (10+20+55+38+35=158)
$pdf->Cell(27, 7, number_format($total, 2), 1, 1, 'R', 1);

$pdf->Ln(5);

// ---------- Amount in Words ----------
$pdf->SetFont('helvetica', '', 10);

$amountInWords = convertNumberToWords($total);

// Table single row with header and value
$pdf->SetFillColor(224, 224, 224); // light gray background

// Single row: header cell + value cell
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(50, 7, 'Amount in Words', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', 'I', 10);
$pdf->Cell(135, 7, $amountInWords, 1, 1, 'R');

$pdf->Ln(3);

// ---------- Bank Details ----------
$pdf->SetFont('helvetica', 'I', 9);
$pdf->Cell(0, 5, 'Please remit payment through the following bank details:', 0, 1, 'L');

$pdf->SetFont('helvetica', 'B', 9);

$pdf->Ln(3);

// Left column details
$leftDetails = [
    "Account Name:" => "Phil Society of Mechanical Engineers Inc",
    "Account No.:" => "004708006927",
    "Bank Name:" => "BDO Unibank, Inc.",
    "PSME TIN:" => "002-304-376-000 (non-VAT)"
];

// Right column details
$rightDetails = [
    "Branch Code:" => "00470",
    "Swift Code:" => "BNORPHMM",
    "Bank Address:" => "Sct. Limbaga Tomas Morato"
];

// Determine max rows
$maxRows = max(count($leftDetails), count($rightDetails));
$leftKeys = array_keys($leftDetails);
$rightKeys = array_keys($rightDetails);

// Print as two columns with no spacing
for ($i = 0; $i < $maxRows; $i++) {
    // Left column
    if (isset($leftKeys[$i])) {
        $key = $leftKeys[$i];
        $value = $leftDetails[$key];
        $pdf->Cell(35, 5, $key, 0, 0, 'L');
        $pdf->Cell(65, 5, $value, 0, 0, 'L');
    } else {
        // Empty cells for alignment
        $pdf->Cell(35, 5, '', 0, 0, 'L');
        $pdf->Cell(65, 5, '', 0, 0, 'L');
    }

    // Right column
    if (isset($rightKeys[$i])) {
        $key = $rightKeys[$i];
        $value = $rightDetails[$key];
        $pdf->Cell(30, 5, $key, 0, 0, 'L');
        $pdf->Cell(0, 5, $value, 0, 1, 'L');
    } else {
        // Empty right cells
        $pdf->Cell(30, 5, '', 0, 0, 'L');
        $pdf->Cell(0, 5, '', 0, 1, 'L');
    }
}

$pdf->Ln(5);

// ---------- Contact Info ----------
$pdf->SetFont('helvetica', '', 10);
$pdf->MultiCell(0, 6, 
    "For any questions or clarifications, please get in touch with Mr. Randy Flores, IT Specialist, at 7752-2527. You can also reach us via email at accounting.natcon@psmeinc.org.ph / delegates@psmeinc.org.ph.", 
    0, 'L', false, 1);

$pdf->Ln(5); // add a small space if desired

// ---------- Prepared By ----------
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 6, 'Prepared by:', 0, 1);
$pdf->Ln(3);

// Signature image (adjust path, width, height, alignment as needed)
$pdf->Image('img/e-sig-jericho.png', '', '', 40); // (file, x, y, width) x,y empty = current position

$pdf->Ln(13); // slight space after signature

$pdf->Cell(0, 6, 'JERICHO MORALES', 0, 1);
$pdf->Cell(0, 6, 'Finance Assistance', 0, 1);

$pdf->Ln(10);

// ---------- Output PDF ----------
// Sanitize company name to remove special characters and spaces for filename
$filenameCompany = preg_replace('/[^A-Za-z0-9\-]/', '_', $company);

// Use soa_number from your generated variable earlier
$filename = 'SOA_' . $filenameCompany . '_' . $soa_number . '.pdf';

// Output PDF with the new filename
$pdf->Output($filename, 'I');
exit;
?>