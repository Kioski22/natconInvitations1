<?php
require 'db.php'; // database connection
require 'vendor/autoload.php'; // PHPMailer via Composer
require_once('vendor/tecnickcom/tcpdf/tcpdf.php'); // TCPDF direct include

use PHPMailer\PHPMailer\PHPMailer;

// Get POST data & sanitize
$email = $conn->real_escape_string($_POST['email']);
$salutation = isset($_POST['salutation']) ? $conn->real_escape_string($_POST['salutation']) : '';
$hr_email = isset($_POST['hr_email']) ? $conn->real_escape_string($_POST['hr_email']) : null;
$full_name = $conn->real_escape_string($_POST['full_name']);
$designation = $conn->real_escape_string($_POST['designation']);
$company = $conn->real_escape_string($_POST['company']);
$address = $conn->real_escape_string($_POST['address']);
$event = $conn->real_escape_string($_POST['event']);
$status = 'pending';

// Insert into DB
$sql = "INSERT INTO invitations (email, salutation, full_name, designation, company, address, event, status)
VALUES ('$email', '$salutation', '$full_name', '$designation', '$company', '$address', '$event', '$status')";

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
    $pdf->Write(0, "$salutation $full_name");

    $pdf->SetXY(90, 75.5);
    $pdf->Write(0, $designation);

    $pdf->SetXY(90, 80);
    $pdf->Write(0, $company);

    $pdf->SetXY(90, 85);
    $pdf->MultiCell(100, 0, $address, 0, 'L', false, 1);

    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetXY(62.5, 96.5);
    $pdf->Write(0, " $salutation $full_name,");

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

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'delegates2@psmeinc.org.ph';
    $mail->Password = 'oopd xwik htyu zndu';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('delegates2@psmeinc.org.ph', 'PSME Invitation Team');
    $mail->addAddress($email, $full_name);
    if ($hr_email) {
        $mail->addCC($hr_email);
    }
    $mail->Subject = '73rd PSME National Convention Official Invitation';
    $mail->isHTML(true);

    $mail->Body = "
        <p>Good day <strong>$salutation $full_name</strong>,</p>
        <p>
            Attached is your official invitation letter to the 73rd PSME National Convention, which will be held on <strong>October 15–18, 2025</strong>, at the <strong>SMX Convention Center, Pasay City</strong>.
        </p>
        <p>
            Should you require further assistance or additional documents, please don't hesitate to reach out to us.
        </p>
        <p>
            Thank you, and we look forward to your participation!
        </p>
        <p>Sincerely,<br>
            <strong>Randy Flores</strong><br>
            IT Specialist<br>
            <span style='background-color: #004085; color: yellow; font-weight: bold; padding: 2px 4px;'>
            PHILIPPINE SOCIETY OF MECHANICAL ENGINEERS, INC.
            </span>
        </p>
        <p>
            If you have any concerns, feel free to contact us:<br>
            <strong>Phone:</strong> (02) 7752-2527<br>
            <strong>Address:</strong> 19 Scout Bayoran St., Brgy. South Triangle, Diliman, Quezon City, Philippines<br>
            <strong>Email:</strong> delegates@psmeinc.org.ph<br>
            <strong>Website:</strong> <a href='https://psmeinc.org.ph'>psmeinc.org.ph</a>
        </p>
    ";
    $attachment_filename = "73rd_NatCon_Invitation_{$clean_company_name}.pdf";
    $mail->addAttachment($pdfFilePath, $attachment_filename);

    if ($mail->send()) {
        $update_sql = "UPDATE invitations SET status='sent' WHERE email='$email'";
        $conn->query($update_sql);

        echo "Invitation sent successfully with personalized PDF!";
    } else {
        echo "Message could not be sent. Error: {$mail->ErrorInfo}";
    }

    // Delete temp PDF file
    unlink($pdfFilePath);

} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Debug
error_log("HR Email: " . $hr_email);

$conn->close();
?>