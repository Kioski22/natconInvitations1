<?php
require 'db.php'; // database connection
require 'vendor/autoload.php'; // PHPMailer via Composer + phpdotenv
require_once('vendor/tecnickcom/tcpdf/tcpdf.php'); // TCPDF direct include

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load environment variables (safeLoad prevents fatal error if .env missing)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad(); // use safeLoad so script continues if .env is not present

// Normalize SMTP env values and trim whitespace from password automatically
$smtpHost = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
$smtpPort = isset($_ENV['SMTP_PORT']) ? (int)$_ENV['SMTP_PORT'] : 587;
$smtpUser = $_ENV['SMTP_USERNAME'] ?? 'ict@psmeinc.org.ph';
$smtpPass = $_ENV['SMTP_PASSWORD'] ?? '';
// Remove any whitespace (spaces/newlines/tabs) that might be present in the pasted password
$smtpPass = preg_replace('/\s+/', '', $smtpPass);
$smtpEnc  = strtolower($_ENV['SMTP_ENCRYPTION'] ?? 'tls');

$mailFromAddress = $_ENV['MAIL_FROM_ADDRESS'] ?? 'delegates2@psmeinc.org.ph';
$mailFromName    = $_ENV['MAIL_FROM_NAME'] ?? 'PSME Invitation Team';

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

    $imgPath1 = realpath('invitation/73rd NatCon Invitation for Member w_meals_page-0001.jpg');
    if (!$imgPath1) { die('Page 1 background image not found.'); }

    $pdf->Image($imgPath1, -1, -1, 218, 333, '', '', '', true, 300, '', false, false, 0, true);
    
    // Dynamic text placement
    $pdf->SetFont('helvetica', '', 12);
    $pdf->SetTextColor(0,0,0);

    $pdf->SetXY(55, 90.5);
    $pdf->Write(0, "$salutation $full_name");

    $pdf->SetXY(55, 95.5);
    $pdf->Write(0, $designation);

    $pdf->SetXY(55, 100);
    $pdf->Write(0, $company);

    $pdf->SetXY(55, 105);
    $pdf->MultiCell(100, 0, $address, 0, 'L', false, 1);

    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetXY(62.5, 120);
    $pdf->Write(0, " $salutation $full_name,");

    // -------------------------
    // Page 2
    // -------------------------
    $pdf->AddPage();

    $imgPath2 = realpath('invitation/73rd NatCon Invitation for Member w_meals_page-0002.jpg');
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
    $mail = new PHPMailer(true);

    // Configure SMTP using .env values (with trimmed password)
    try {
        $mail->isSMTP();
        $mail->Host = $smtpHost;
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUser;
        $mail->Password = $smtpPass;

        if ($smtpEnc === 'tls' || $smtpEnc === 'starttls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } elseif ($smtpEnc === 'ssl' || $smtpEnc === 'smtps') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            // leave default or no encryption
            $mail->SMTPSecure = '';
        }

        $mail->Port = $smtpPort;

        $mail->setFrom($mailFromAddress, $mailFromName);
        $mail->addAddress($email, $full_name);
        if ($hr_email) {
            $mail->addCC($hr_email);
        }
        $mail->Subject = '73rd PSME National Convention Official Invitation';
        $mail->isHTML(true);

        $mail->Body = "
            <p>Good day <strong>$salutation $full_name</strong>,</p>
            <p>
                We are pleased to attach your official invitation letter to the 73rd PSME National Convention, happening on <strong>October 15–18, 2025</strong> at the <strong>SMX Convention Center, Pasay City</strong>.<br>
                This event promises to be an exciting gathering of mechanical engineers, industry leaders, and professionals from across the country. We encourage you to take part in this milestone event and experience valuable learning, networking, and collaboration opportunities.
            </p>
            <p>
                <strong>To confirm your attendance and secure your slot, please register as soon as possible:</strong><br>
                <a href='https://psmeinc.org.ph/#/psme/event-details/16' target='_blank'>https://psmeinc.org.ph/#/psme/event-details/16</a>
            </p>
            <p>
                For the latest updates, announcements, and event highlights, follow our official NatCon Facebook page:<br>
                <a href='https://www.facebook.com/natconpsme' target='_blank'>https://www.facebook.com/natconpsme</a>
            </p>
            <p>
                If you need any assistance or require additional documents, please feel free to contact us at any time. Our team is here to support you.
            </p>
            <p>
                Thank you for your interest, and we look forward to welcoming you at the 73rd PSME National Convention!
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

    } catch (Exception $e) {
        // PHPMailer exception message
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    } finally {
        // Delete temp PDF file
        if (file_exists($pdfFilePath)) {
            unlink($pdfFilePath);
        }
    }

} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Debug
error_log("HR Email: " . $hr_email);

$conn->close();
?>
