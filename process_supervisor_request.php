<?php
// process_supervisor_request.php

require 'db.php'; // database connection
require 'vendor/autoload.php'; // PHPMailer + Dotenv
require_once('vendor/tecnickcom/tcpdf/tcpdf.php'); // TCPDF

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $supervisor_name = $conn->real_escape_string($_POST['supervisor_name']);
    $company = $conn->real_escape_string($_POST['company']);
    $company_address = $conn->real_escape_string($_POST['company_address']);
    $designation = $conn->real_escape_string($_POST['designation']);
    $email = $conn->real_escape_string($_POST['email']);
    $status = 'pending';

    // Insert into database
    $sql = "INSERT INTO supervisor_invitations (supervisor_name, company, company_address, designation, email, status) 
            VALUES ('$supervisor_name', '$company', '$company_address', '$designation', '$email', '$status')";

    if ($conn->query($sql) === TRUE) {
        // --- Generate PDF Invitation ---
        $pdf = new TCPDF('P', 'mm', array(216, 330), true, 'UTF-8', false); // Long bond size
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Page 1 with background
        $pdf->AddPage();
        $bg1 = __DIR__ . '/invitation/73rd-NatCon-Invitation-for-FOR-LGU-final_page-0001.jpg';
        if (file_exists($bg1)) {
            $pdf->Image($bg1, 0, 0, 216, 330);
        } else {
            die('Background image 1 not found.');
        }

        // Overlay text
        $pdf->SetFont('times', '', 12);

        $pdf->SetXY(55, 75);
        $pdf->Cell(0, 10, $supervisor_name, 0, 1, 'L');

        $pdf->SetXY(55, 79);
        $pdf->Cell(0, 10, $designation, 0, 1, 'L');

        $pdf->SetXY(55, 83);
        $pdf->Cell(0, 10, $company, 0, 1, 'L');

        $pdf->SetXY(55, 89);
        $pdf->MultiCell(150, 10, $company_address, 0, 'L');
        
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetXY(65, 118.5);
        $pdf->Cell(0, 10, $supervisor_name . ',', 0, 1, 'L');

        // Page 2 with background
        $pdf->AddPage();
        $bg2 = __DIR__ . '/invitation/73rd-NatCon-Invitation-for-FOR-LGU-final_page-0002.jpg';
        if (file_exists($bg2)) {
            $pdf->Image($bg2, 0, 0, 216, 330);
        } else {
            die('Background image 2 not found.');
        }

        // Output PDF to string
        $pdfString = $pdf->Output('', 'S');
        $pdfFilePath = __DIR__ . "/invitation/supervisor_invitation_" . time() . ".pdf";
        file_put_contents($pdfFilePath, $pdfString);

        // --- Send Email with PHPMailer ---
        $mail = new PHPMailer(true);

        try {
            // SMTP Settings from .env
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USERNAME'];
            $mail->Password   = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = $_ENV['SMTP_ENCRYPTION'] === 'tls'
                                ? PHPMailer::ENCRYPTION_STARTTLS
                                : PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $_ENV['SMTP_PORT'];

            // Recipients
            $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "Official Invitation to the 73rd PSME National Convention";
            $mail->Body    = "
                <p>Dear <strong>$supervisor_name</strong>,</p>
                <p>We are pleased to invite you and your company, <strong>$company</strong>, to the upcoming 
                <strong>73rd PSME National Convention</strong>.</p>
                <p>Please see the attached official invitation letter.</p>
                <p>For inquiries, you may contact <strong>delegates@psmeinc.org.ph</strong>.</p>
                <br>
                <p>Thank you,</p>
                <p><strong>PSME National Office</strong></p>
            ";

            // Attach PDF
            $mail->addStringAttachment($pdfString, "73rd_NatCon_Company_Invitation.pdf");

            $mail->send();

            // Update DB status
            $conn->query("UPDATE supervisor_invitations SET status='sent' WHERE email='$email'");

            echo "Invitation successfully sent to $email.";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        } finally {
            // Cleanup temp PDF
            if (file_exists($pdfFilePath)) {
                unlink($pdfFilePath);
            }
        }
    } else {
        echo "Database Error: " . $conn->error;
    }
}
?>
