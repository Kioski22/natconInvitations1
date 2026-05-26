<?php
// process_supervisor_request.php

require 'db.php'; // database connection
require 'vendor/autoload.php';
require 'gmail_api.php';
require_once('vendor/tecnickcom/tcpdf/tcpdf.php'); // TCPDF
require_once __DIR__ . '/helpers/tracking.php';

// Load environment variables without failing if .env is unavailable in production.
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

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
    $thru = trim((string)($_POST['thru'] ?? ''));
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
            __DIR__ . '/invitation/74th-NatCon-Invitation-for-FOR-LGU-final_page-0001.png'
        ]);
        if ($bg1) {
            $pdf->Image($bg1, 0, 0, 216, 330);
        } else {
            die('Background image 1 not found.');
        }

        // Overlay text
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetXY(55, 69.7);
        $pdf->Cell(0, 10, $supervisor_name, 0, 1, 'L');


        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetXY(55, 74);
        $pdf->Cell(0, 10, $designation, 0, 1, 'L');

        $pdf->SetXY(55, 78.3);
        $pdf->Cell(0, 10, $company, 0, 1, 'L');

        $pdf->SetXY(55, 85);
        $pdf->MultiCell(150, 10, $company_address, 0, 'L');
        if ($thru !== '') {
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetXY(55, $pdf->GetY() - 2.7);
            $pdf->MultiCell(150, 0, 'Thru: ' . $thru, 0, 'L', false, 1);
        }
        
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetXY(65, 115.6);
        $pdf->Cell(0, 10, $supervisor_name . ',', 0, 1, 'L');

        // Page 2 with background
        $pdf->AddPage();
        $bg2 = findInvitationImage([
            __DIR__ . '/invitation/74th-NatCon-Invitation-for-FOR-LGU-final_page-0002.png'
        ]);
        if ($bg2) {
            $pdf->Image($bg2, 0, 0, 216, 330);
        } else {
            die('Background image 2 not found.');
        }


        // Page 3 with background
        $pdf->AddPage();
        $bg3 = findInvitationImage([
            __DIR__ . '/invitation/74th-NatCon-Invitation-for-FOR-LGU-final_page-0003.png',
            __DIR__ . '/invitation/74th-NatCon-Invitation-for-FOR-LGU-final_page-0003.png'
        ]);
        if ($bg3) {
            $pdf->Image($bg3, 0, 0, 216, 330);
        } else {
            die('Background image 3 not found.');
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
            <div style='background:#e8eaf0; padding:32px 16px; font-family:Arial,Helvetica,sans-serif;'>
            <div style='max-width:620px; margin:0 auto; background:#ffffff; border-radius:4px; overflow:hidden; box-shadow:0 2px 16px rgba(0,0,0,0.10);'>

                <!-- Header -->
                <div style='background:#004085; padding:36px 40px 28px;'>
                <div style='color:#FFD700; font-size:11px; font-weight:700; letter-spacing:2px; text-transform:uppercase; margin-bottom:8px;'>
                    Philippine Society of Mechanical Engineers, Inc.
                </div>
                <div style='color:#ffffff; font-size:26px; font-weight:700; line-height:1.2; font-family:Georgia,serif; margin-bottom:4px;'>
                    74th PSME National Convention
                </div>
                <div style='color:rgba(255,255,255,0.75); font-size:13px;'>Official Invitation</div>
                <div style='height:5px; background:#FFD700; margin-top:20px; margin-left:-40px; margin-right:-40px;'></div>
                </div>

                <!-- Event Details Banner -->
                <div style='background:#003268; display:table; width:100%; box-sizing:border-box;'>
                <div style='display:table-cell; padding:16px 24px; border-right:1px solid rgba(255,255,255,0.1); width:50%;'>
                    <div style='font-size:10px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:#FFD700; margin-bottom:4px;'>Date</div>
                    <div style='font-size:13px; color:#ffffff; line-height:1.4;'>$eventDates</div>
                </div>
                <div style='display:table-cell; padding:16px 24px; width:50%;'>
                    <div style='font-size:10px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:#FFD700; margin-bottom:4px;'>Venue</div>
                    <div style='font-size:13px; color:#ffffff; line-height:1.4;'>$eventVenue</div>
                </div>
                </div>

                <!-- Body -->
                <div style='padding:32px 40px; color:#444444;'>

                <p style='font-family:Georgia,serif; font-size:17px; color:#004085; margin:0 0 20px;'>
                    Dear <strong>$supervisor_name</strong>,
                </p>

                <p style='font-size:14px; line-height:1.75; margin:0 0 16px;'>
                    We are pleased to invite you and your company, <strong>$company</strong>, to the upcoming
                    <strong>74th PSME National Convention</strong>. This event promises to be an exciting gathering
                    of mechanical engineers, industry leaders, and professionals from across the country.
                    We encourage you to take part in this milestone event and experience valuable learning,
                    networking, and collaboration opportunities.
                </p>

                <p style='font-size:14px; line-height:1.75; margin:0 0 16px;'>
                    Please see the attached official invitation letter for more details.
                </p>

                <!-- CTA Block -->
                <div style='background:#f0f5ff; border-left:4px solid #004085; border-radius:0 6px 6px 0; padding:16px 20px; margin:24px 0;'>
                    <div style='font-size:11px; font-weight:700; letter-spacing:0.5px; text-transform:uppercase; color:#004085; margin-bottom:8px;'>
                    Confirm Your Attendance
                    </div>
                    <p style='font-size:13px; line-height:1.6; margin:0 0 12px; color:#444;'>
                    Secure your slot as soon as possible &mdash; spaces are limited.
                    </p>
                    <a href='$eventLinkTracked' target='_blank'
                    style='display:inline-block; background:#004085; color:#FFD700; font-size:14px; font-weight:700;
                            padding:10px 22px; border-radius:4px; text-decoration:none;'>
                    Register Now &rarr;
                    </a>
                </div>

                <!-- Facebook Block -->
                <div style='display:table; width:100%; box-sizing:border-box; background:#f8f9fa; border:1px solid #dee2e6; border-radius:6px; padding:16px 18px; margin-bottom:24px;'>
                    <div style='display:table-cell; vertical-align:top; width:44px; padding-right:12px;'>
                    <div style='width:36px; height:36px; background:#1877F2; border-radius:50%; text-align:center; line-height:36px;'>
                        <span style='color:#ffffff; font-size:18px; font-weight:700;'>f</span>
                    </div>
                    </div>
                    <div style='display:table-cell; vertical-align:top; font-size:13px; line-height:1.6; color:#444;'>
                    For the latest updates, announcements, and event highlights, follow our official NatCon Facebook page:<br>
                    <a href='$eventFbTracked' target='_blank' style='color:#1877F2; font-weight:700; text-decoration:none;'>$eventFb</a>
                    </div>
                </div>

                <p style='font-size:14px; line-height:1.75; margin:0 0 16px;'>
                    For inquiries, please feel free to reach out to us at
                    <a href='mailto:delegates1@psmeinc.org.ph' style='color:#004085; text-decoration:none; font-weight:700;'>delegates1@psmeinc.org.ph</a>.
                    Our team is here to support you.
                </p>

                <p style='font-size:14px; line-height:1.75; margin:0 0 24px;'>
                    Thank you &mdash; we look forward to welcoming you at the <strong>74th PSME National Convention!</strong>
                </p>

                <!-- Divider -->
                <div style='border-top:1px solid #eeeeee; margin:0 0 24px;'></div>

                </div>

                <!-- Footer -->
                <div style='background:#f8f9fa; border-top:1px solid #dee2e6; padding:20px 40px;'>
                <div style='font-size:11px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#888888; margin-bottom:12px;'>
                    Contact Information
                </div>
                <div style='font-size:12px; color:#555555; margin-bottom:6px;'><strong style='color:#333;'>Phone:</strong>&nbsp;&nbsp;(02) 7752-2527</div>
                <div style='font-size:12px; color:#555555; margin-bottom:6px;'><strong style='color:#333;'>Address:</strong>&nbsp;&nbsp;19 Scout Bayoran St., Brgy. South Triangle, Diliman, Quezon City, Philippines</div>
                <div style='font-size:12px; color:#555555; margin-bottom:6px;'><strong style='color:#333;'>Email:</strong>&nbsp;&nbsp;<a href='mailto:delegates1@psmeinc.org.ph' style='color:#004085; text-decoration:none;'>delegates1@psmeinc.org.ph</a></div>
                <div style='font-size:12px; color:#555555; margin-bottom:6px;'><strong style='color:#333;'>Website:</strong>&nbsp;&nbsp;<a href='https://psmeinc.org.ph' style='color:#004085; text-decoration:none;'>psmeinc.org.ph</a></div>
                <div style='border-top:1px solid #eeeeee; margin-top:14px; padding-top:12px; font-size:11px; color:#aaaaaa; line-height:1.5;'>
                    This message was sent on behalf of PSME, Inc. to an invited delegate. If you believe this was sent in error, please disregard this email.
                </div>
                </div>

                <!-- Tracking pixel -->
                <img src='$trackUrl' width='1' height='1' alt='' style='display:none;'>

            </div>
            </div>
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
