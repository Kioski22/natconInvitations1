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
        'invitation/73rd NatCon Invitation for Member w_meals_page-0001.jpg',
        'invitation/1.jpg'
    ]);
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

    $imgPath2 = findInvitationImage([
        'invitation/73rd NatCon Invitation for Member w_meals_page-0002.jpg',
        'invitation/2.jpg'
    ]);
    if (!$imgPath2) { die('Page 2 background image not found.'); }

    $pdf->Image($imgPath2, -1, -1, 218, 333, '', '', '', true, 300, '', false, false, 0, true);

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
            <p>Good day <strong>$salutation $full_name</strong>,</p>
            <p>
                We are pleased to attach your official invitation letter to the 74th PSME National Convention, happening on <strong>$eventDates</strong> at the <strong>$eventVenue</strong>.<br>
                This event promises to be an exciting gathering of mechanical engineers, industry leaders, and professionals from across the country. We encourage you to take part in this milestone event and experience valuable learning, networking, and collaboration opportunities.
            </p>
            <p>
                <strong>To confirm your attendance and secure your slot, please register as soon as possible:</strong><br>
                <a href='$eventLinkTracked' target='_blank'>$eventLink</a>
            </p>
            <p>
                For the latest updates, announcements, and event highlights, follow our official NatCon Facebook page:<br>
                <a href='$eventFbTracked' target='_blank'>$eventFb</a>
            </p>
            <p>
                If you need any assistance or require additional documents, please feel free to contact us at any time. Our team is here to support you.
            </p>
            <p>
                Thank you for your interest, and we look forward to welcoming you at the 74th PSME National Convention!
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
            <img src='$trackUrl' width='1' height='1' alt='' style='display:none;'>
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
