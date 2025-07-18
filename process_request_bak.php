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
$event = $conn->real_escape_string($_POST['event']);
$status = 'pending';

// Insert into DB
$sql = "INSERT INTO invitations (email, full_name, designation, company, address, event, status)
VALUES ('$email', '$full_name', '$designation', '$company', '$address', '$event', '$status')";

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

    $pdf->Image($imgPath1, -1, -1, 218, 333, '', '', '', true, 300, '', false, false, 0, true);
    
    // Dynamic text placement
    $pdf->SetFont('helvetica', '', 12);
    $pdf->SetTextColor(0,0,0);

    $pdf->SetXY(90, 70.5);
    $pdf->Write(0, $full_name);

    $pdf->SetXY(90, 75.5);
    $pdf->Write(0, $designation);

    $pdf->SetXY(90, 80);
    $pdf->Write(0, $company);

    $pdf->SetXY(90, 85);
    $pdf->MultiCell(100, 0, $address, 0, 'L', false, 1);

    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetXY(71, 96.5);
    $pdf->Write(0, " $full_name,");

    // -------------------------
    // Page 2
    // -------------------------
    $pdf->AddPage();

    $imgPath2 = realpath('invitation/2.jpg');
    if (!$imgPath2) { die('Page 2 background image not found.'); }

    $pdf->Image($imgPath2, -1, -1, 218, 333, '', '', '', true, 300, '', false, false, 0, true);

    // -------------------------
    // Output PDF to string
    // -------------------------
    $pdfOutput = $pdf->Output('', 'S'); // return as string

    // Save PDF to server temporarily with company name
    $clean_company_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $company);
    $pdfFilePath = __DIR__ . "/invitation_{$clean_company_name}.pdf";
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
        $mail->Body = "Dear $full_name,

A pleasant day!

Thank you for your interest in the upcoming 73rd PSME National Convention. We’re delighted to assist you with your group’s registration.

For bulk registration, kindly accomplish the attached Excel form. This will serve as the basis for your delegates’ official registration and for the preparation of your Statement of Account (SOA).

To proceed with the SOA, may we also request the following details to be submitted:

Company Name:
Company Email:
Company Contact Number:
Company Address:
Company TIN:

Please input your members' names and details using the following link:
[Insert your input form link here]

Please note that food provisions and participant kits (including meal stubs) will only be provided to those who are officially registered under the 73rd NatCon.

Should you have any further questions or need additional assistance, please don’t hesitate to reach out.

Sincerely,
Randy Flores
IT Specialist
PHILIPPINE SOCIETY OF MECHANICAL ENGINEERS, INC.

Should you have any concerns? Let us know, you may contact us at:
(02) 7752-2527
19 Scout Bayoran St. Brgy, South Triangle, Diliman, Quezon City, Philippines
Email: delegates@psmeinc.org.ph
Website: psmeinc.org.ph";

        // Attach PDF with company name in filename
        $attachment_filename = "73rd_NatCon_Invitation_{$clean_company_name}.pdf";
        $mail->addAttachment($pdfFilePath, $attachment_filename);

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