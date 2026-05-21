<?php
require 'db.php'; // database connection
require 'vendor/autoload.php';
require 'gmail_api.php';
require_once('vendor/tecnickcom/tcpdf/tcpdf.php'); // TCPDF direct include
require_once __DIR__ . '/helpers/tracking.php';

// Load environment variables (safeLoad prevents fatal error if .env missing)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad(); // use safeLoad so script continues if .env is not present

$mailFromAddress = $_ENV['GOOGLE_SENDER_EMAIL'] ?? ($_ENV['MAIL_FROM_ADDRESS'] ?? 'delegates2@psmeinc.org.ph');
$mailFromName    = $_ENV['MAIL_FROM_NAME'] ?? 'PSME Invitation Team';

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
$stmtInsert = $conn->prepare(
    "INSERT INTO invitations (email, salutation, full_name, designation, company, address, event, status)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);

if ($stmtInsert) {
    $stmtInsert->bind_param(
        'ssssssss',
        $email,
        $salutation,
        $full_name,
        $designation,
        $company,
        $address,
        $event,
        $status
    );
    $stmtInsert->execute();
    $invitationId = $conn->insert_id;
    $stmtInsert->close();

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

    $imgPath1 = findInvitationImage([
        'invitation/74th NatCon Invitation for Member w_meals_page-0001.png',
        'invitation/1.png'
    ]);
    if (!$imgPath1) { die('Page 1 background image not found.'); }

    $pdf->Image($imgPath1, -1, -1, 218, 333, '', '', '', true, 300, '', false, false, 0, true);
    
    // Dynamic text placement
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0,0,0);

    $pdf->SetXY(65.2, 81.9);
    $pdf->Write(0, "$salutation $full_name" . ',');

    // $pdf->SetXY(68, 95.5);
    // $pdf->Write(0, $designation);

    // $pdf->SetXY(68, 100);
    // $pdf->Write(0, $company);

    // $pdf->SetXY(68, 105);
    // $pdf->MultiCell(100, 0, $address, 0, 'L', false, 1);

    // $pdf->SetFont('helvetica', 'B', 12);
    // $pdf->SetXY(62.5, 120);
    // $pdf->Write(0, " $salutation $full_name,");

    // -------------------------
    // Page 2
    // -------------------------
    $pdf->AddPage();

    $imgPath2 = findInvitationImage([
        'invitation/74th NatCon Invitation for Member w_meals_page-0002.png',
        'invitation/2.png'
    ]);
    if (!$imgPath2) { die('Page 2 background image not found.'); }

    $pdf->Image($imgPath2, -1, -1, 218, 333, '', '', '', true, 300, '', false, false, 0, true);

    // -------------------------
    // Page 3
    // -------------------------
    $pdf->AddPage();

    $imgPath3 = findInvitationImage([
        'invitation/74th NatCon Invitation for Member w_meals_page-0003.png',
        'invitation/3.png'
    ]);
    if (!$imgPath3) { die('Page 3 background image not found.'); }

    $pdf->Image($imgPath3, -1, -1, 218, 333, '', '', '', true, 300, '', false, false, 0, true);
    // -------------------------
    // Output PDF to string
    // -------------------------
    $pdfOutput = $pdf->Output('', 'S'); // return as string

    // -------------------------
    // Send email with Gmail API (OAuth)
    // -------------------------
    try {
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
                Good day, <strong>$salutation $full_name</strong>,
            </p>

            <p style='font-size:14px; line-height:1.75; margin:0 0 16px;'>
                We are pleased to attach your official invitation letter to the <strong>74th PSME National Convention</strong>.
                This event promises to be an exciting gathering of mechanical engineers, industry leaders, and professionals
                from across the country. We encourage you to take part in this milestone event and experience valuable
                learning, networking, and collaboration opportunities.
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
                If you need any assistance or require additional documents, please feel free to contact us at any time.
                Our team is here to support you.
            </p>

            <p style='font-size:14px; line-height:1.75; margin:0 0 24px;'>
                Thank you for your interest &mdash; we look forward to welcoming you at the <strong>74th PSME National Convention!</strong>
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
            <div style='font-size:12px; color:#555555; margin-bottom:6px;'><strong style='color:#333;'>Email:</strong>&nbsp;&nbsp;<a href='mailto:delegates@psmeinc.org.ph' style='color:#004085; text-decoration:none;'>delegates@psmeinc.org.ph</a></div>
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

        $clean_company_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $company);
        $attachment_filename = "74th_NatCon_Invitation_{$clean_company_name}.pdf";
        $messageId = buildMessageId($mailFromAddress);

        $payload = [
            'from' => $mailFromName . ' <' . $mailFromAddress . '>',
            'to' => $full_name !== '' ? ($full_name . ' <' . $email . '>') : $email,
            'cc' => $hr_email ? $hr_email : '',
            'subject' => '74th PSME National Convention Official Invitation',
            'html' => $htmlBody,
            'attachments' => [
                [
                    'filename' => $attachment_filename,
                    'data' => $pdfOutput,
                    'mime' => 'application/pdf'
                ]
            ],
            'message_id' => $messageId
        ];

        $sendResult = sendGmailMessage($payload);
        $gmailMessageId = $sendResult['gmail_message_id'] ?? null;
        $gmailThreadId = $sendResult['gmail_thread_id'] ?? null;

        $stmtUpdate = $conn->prepare("UPDATE invitations SET status='sent' WHERE id=?");
        if ($stmtUpdate) {
            $stmtUpdate->bind_param('i', $invitationId);
            $stmtUpdate->execute();
            $stmtUpdate->close();
        }

        $stmt = $conn->prepare(
            "INSERT INTO email_messages (source_type, source_id, email, message_id, gmail_message_id, gmail_thread_id, tracking_token, tracking_id, status, sent_at, last_event_at)
             VALUES ('individual', ?, ?, ?, ?, ?, ?, ?, 'SENT', NOW(), NOW())"
        );
        if ($stmt) {
            $stmt->bind_param('issssss', $invitationId, $email, $messageId, $gmailMessageId, $gmailThreadId, $trackingToken, $trackingToken);
            $stmt->execute();
            $stmt->close();
        }

        echo "Invitation sent successfully with personalized PDF!";
    } catch (Exception $e) {
        echo "Message could not be sent. Error: {$e->getMessage()}";
    }

} else {
    echo "Error: " . $conn->error;
}

// Debug
error_log("HR Email: " . $hr_email);

$conn->close();
?>
