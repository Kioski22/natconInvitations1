<?php
// process_supervisor_request.php

require 'db.php'; // database connection
require 'vendor/autoload.php';
require 'gmail_api.php';
require_once('vendor/tecnickcom/tcpdf/tcpdf.php'); // TCPDF
require_once __DIR__ . '/helpers/tracking.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$eventDates = 'October 14-17, 2026';
$eventVenue = 'SMX Convention Center, Pasay City';
$eventLink = 'https://psmeinc.org.ph/#/psme/event-details/81';
$eventFb = 'https://www.facebook.com/psmeinc';

function findInvitationImage(array $candidates) {
    foreach ($candidates as $candidate) {
        $real = realpath($candidate);
        if ($real && file_exists($real)) {
            return $real;
        }
    }
    return null;
}

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
        $supervisorId = $conn->insert_id;
        // --- Generate PDF Invitation ---
        $pdf = new TCPDF('P', 'mm', array(216, 330), true, 'UTF-8', false); // Long bond size
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Page 1 with background
        $pdf->AddPage();
        $bg1 = findInvitationImage([
            __DIR__ . '/invitation/73rd-NatCon-Invitation-for-FOR-LGU-final_page-0001.jpg'
        ]);
        if ($bg1) {
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
        $bg2 = findInvitationImage([
            __DIR__ . '/invitation/73rd-NatCon-Invitation-for-FOR-LGU-final_page-0002.jpg'
        ]);
        if ($bg2) {
            $pdf->Image($bg2, 0, 0, 216, 330);
        } else {
            die('Background image 2 not found.');
        }

        // Output PDF to string
        $pdfString = $pdf->Output('', 'S');
        $pdfFilePath = __DIR__ . "/invitation/supervisor_invitation_" . time() . ".pdf";
        file_put_contents($pdfFilePath, $pdfString);

        // --- Send Email with Gmail API (OAuth) ---
        try {
            $mailFromAddress = $_ENV['GOOGLE_SENDER_EMAIL'] ?? ($_ENV['MAIL_FROM_ADDRESS'] ?? 'delegates2@psmeinc.org.ph');
            $mailFromName = $_ENV['MAIL_FROM_NAME'] ?? 'PSME Invitation Team';

            $trackingToken = bin2hex(random_bytes(16));
            $baseUrl = getAppUrl();
            $trackUrl = buildOpenTrackingUrl($baseUrl, $trackingToken);
            $eventLinkTracked = buildClickTrackingUrl($baseUrl, $trackingToken, $eventLink);
            $eventFbTracked = buildClickTrackingUrl($baseUrl, $trackingToken, $eventFb);

            $htmlBody = "
                <p>Dear <strong>$supervisor_name</strong>,</p>
                <p>We are pleased to invite you and your company, <strong>$company</strong>, to the upcoming 
                <strong>74th PSME National Convention</strong> on <strong>$eventDates</strong> at <strong>$eventVenue</strong>.</p>
                <p>Please see the attached official invitation letter.</p>
                <p>
                    Registration link: <a href='$eventLinkTracked' target='_blank'>$eventLink</a><br>
                    Official Facebook page: <a href='$eventFbTracked' target='_blank'>$eventFb</a>
                </p>
                <p>For inquiries, you may contact <strong>delegates@psmeinc.org.ph</strong>.</p>
                <br>
                <p>Thank you,</p>
                <p><strong>PSME National Office</strong></p>
                <img src='$trackUrl' width='1' height='1' alt='' style='display:none;'>
            ";

            $messageId = buildMessageId($mailFromAddress);
            $payload = [
                'from' => $mailFromName . ' <' . $mailFromAddress . '>',
                'to' => $supervisor_name !== '' ? ($supervisor_name . ' <' . $email . '>') : $email,
                'cc' => '',
                'subject' => 'Official Invitation to the 74th PSME National Convention',
                'html' => $htmlBody,
                'attachments' => [
                    [
                        'filename' => '74th_NatCon_Company_Invitation.pdf',
                        'data' => $pdfString,
                        'mime' => 'application/pdf'
                    ]
                ],
                'message_id' => $messageId
            ];

            $sendResult = sendGmailMessage($payload);
            $gmailMessageId = $sendResult['gmail_message_id'] ?? null;
            $gmailThreadId = $sendResult['gmail_thread_id'] ?? null;

            // Update DB status
            $stmtUpdate = $conn->prepare("UPDATE supervisor_invitations SET status='sent' WHERE id=?");
            if ($stmtUpdate) {
                $stmtUpdate->bind_param('i', $supervisorId);
                $stmtUpdate->execute();
                $stmtUpdate->close();
            }

            $stmt = $conn->prepare(
                "INSERT INTO email_messages (source_type, source_id, email, message_id, gmail_message_id, gmail_thread_id, tracking_token, tracking_id, status, sent_at, last_event_at)
                 VALUES ('company', ?, ?, ?, ?, ?, ?, ?, 'SENT', NOW(), NOW())"
            );
            if ($stmt) {
                $stmt->bind_param('issssss', $supervisorId, $email, $messageId, $gmailMessageId, $gmailThreadId, $trackingToken, $trackingToken);
                $stmt->execute();
                $stmt->close();
            }

            echo "Invitation successfully sent to $email.";
        } catch (Exception $e) {
            echo "Message could not be sent. Error: {$e->getMessage()}";
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
