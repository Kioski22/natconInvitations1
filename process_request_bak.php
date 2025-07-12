<?php
require 'db.php'; // database connection
require 'vendor/autoload.php'; // PHPMailer via Composer
require_once('vendor/tecnickcom/tcpdf/tcpdf.php'); // TCPDF direct include

use PHPMailer\PHPMailer\PHPMailer;

// Get POST data & sanitize
$email = $conn->real_escape_string($_POST['email']);
$full_name = $conn->real_escape_string($_POST['full_name']);
$designation = $conn->real_escape_string($_POST['designation']);
$company = $conn->real_escape_string($_POST['company']);
$address = $conn->real_escape_string($_POST['address']);
$status = 'pending';

// Insert into DB
$sql = "INSERT INTO invitations (email, full_name, designation, company, address, status)
VALUES ('$email', '$full_name', '$designation', '$company', '$address', '$status')";

if ($conn->query($sql) === TRUE) {

    // Generate PDF using TCPDF with long bond paper size
    $pdf = new TCPDF('P', 'mm', array(215.9, 330.2), true, 'UTF-8', false); // 8.5x13 inches

    // Set zero margins and disable auto page break
    $pdf->SetMargins(0, 0, 0);
    $pdf->SetAutoPageBreak(false, 0);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // -------------------------
    // Page 1: Invitation Letter
    // -------------------------
    $pdf->AddPage();

    $imgPath1 = realpath('invitation/1.jpg');
    if (!$imgPath1) { die('Page 1 background image not found.'); }

    // Force resize to cover entire page with slight oversize to bleed
    $pdf->Image($imgPath1, -1, -1, 218, 333, '', '', '', true, 300, '', false, false, 0, true);
    
    // Dynamic text placement
    $pdf->SetFont('helvetica', '', 12);
    $pdf->SetTextColor(0,0,0);

    $pdf->SetXY(90, 70.5); // adjust based on your template
    $pdf->Write(0, $full_name);

    $pdf->SetXY(90, 75.5);
    $pdf->Write(0, $designation);

    $pdf->SetXY(90, 80);
    $pdf->Write(0, $company);

    $pdf->SetXY(90, 85);
    $pdf->MultiCell(
        100,       // width in mm (adjust as needed based on your layout)
        0,         // height (auto)
        $address,  // text
        0,         // border (0 = no border)
        'L',       // align left
        false,     // fill
        1,         // line break after
        '', '',    // x, y (empty because SetXY used)
        true,      // reset height
        0,         // stretch
        false,     // is HTML
        true,      // autopadding
        0,         // max height
        'T',       // vertical align top
        false      // fit cell
    );

   $pdf->SetFont('helvetica', 'B', 12); // set to Bold, 12pt
    $pdf->SetXY(71, 96.5);
    $pdf->Write(0, " $full_name,");

    $pdf->SetFont('helvetica', '', 12); // revert to Regular if needed for next text

    // -------------------------
    // Page 2
    // -------------------------
    $pdf->AddPage();

    $imgPath2 = realpath('invitation/2.jpg');
    if (!$imgPath2) { die('Page 2 background image not found.'); }

    // Force resize for page 2 as well
    $pdf->Image($imgPath2, -1, -1, 218, 333, '', '', '', true, 300, '', false, false, 0, true);

    // -------------------------
    // Output PDF to string
    // -------------------------
    $pdfOutput = $pdf->Output('', 'S'); // return as string

    // Save PDF to server temporarily
    $pdfFilePath = __DIR__ . "/invitation_$full_name.pdf";
    file_put_contents($pdfFilePath, $pdfOutput);

    // -------------------------
    // Send email with PHPMailer
    // -------------------------
    $mail = new PHPMailer();
    try {
        // SMTP Config
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'delegates@psmeinc.org.ph';
        $mail->Password = 'fupz ruif lwzh xclc';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Email content
        $mail->setFrom('delegates@psmeinc.org.ph', 'PSME Invitation Team');
        $mail->addAddress($email, $full_name);
        $mail->Subject = 'Your 73rd PSME NatCon Invitation';
        $mail->Body = "Dear $full_name,\n\nPlease find attached your personalized invitation to the 73rd PSME National Convention.\n\nBest regards,\nPSME Team";

        // Attach PDF
        $mail->addAttachment($pdfFilePath, '73rd_NatCon_Invitation.pdf');

        if ($mail->send()) {
            // Update status to 'sent'
            $update_sql = "UPDATE invitations SET status='sent' WHERE email='$email'";
            $conn->query($update_sql);

            echo "Invitation sent successfully with personalized PDF!";
        } else {
            echo "Mailer Error: " . $mail->ErrorInfo;
        }

        // Delete temp PDF file
        unlink($pdfFilePath);

    } catch (Exception $e) {
        echo "Message could not be sent. Error: {$mail->ErrorInfo}";
    }

} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>