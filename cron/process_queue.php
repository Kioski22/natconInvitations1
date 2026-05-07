<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../gmail_api.php';
require_once __DIR__ . '/../helpers/log.php';
require_once __DIR__ . '/../helpers/tracking.php';
require_once __DIR__ . '/../helpers/invitation_pdf.php';

$lockFile = __DIR__ . '/../logs/cron_process_queue.lock';
$lockHandle = fopen($lockFile, 'c');
if (!$lockHandle || !flock($lockHandle, LOCK_EX | LOCK_NB)) {
    echo "Queue processor already running.\n";
    exit;
}

$batchSize = (int)($_ENV['QUEUE_BATCH_SIZE'] ?? 20);
$maxAttempts = (int)($_ENV['QUEUE_MAX_ATTEMPTS'] ?? 3);
$baseUrl = getAppUrl();

$queueIds = [];
$stmtSelect = $conn->prepare(
    "SELECT id
     FROM invitation_queue
     WHERE status IN ('queued', 'retry')
       AND (attempts < ? OR attempts IS NULL)
       AND (locked_at IS NULL OR locked_at < DATE_SUB(NOW(), INTERVAL 5 MINUTE))
     ORDER BY id ASC
     LIMIT ?"
);
if ($stmtSelect) {
    $stmtSelect->bind_param('ii', $maxAttempts, $batchSize);
    $stmtSelect->execute();
    $result = $stmtSelect->get_result();
    while ($row = $result->fetch_assoc()) {
        $queueIds[] = (int)$row['id'];
    }
    $stmtSelect->close();
}

if (empty($queueIds)) {
    echo "No queued invitations to process.\n";
    $conn->close();
    exit;
}

$placeholders = implode(',', array_fill(0, count($queueIds), '?'));
$types = str_repeat('i', count($queueIds));
$processId = bin2hex(random_bytes(8));

$stmtLock = $conn->prepare(
    "UPDATE invitation_queue
     SET status = 'processing',
         locked_at = NOW(),
         locked_by = ?,
         attempts = COALESCE(attempts, 0) + 1,
         last_attempt_at = NOW()
     WHERE id IN ($placeholders)"
);
if ($stmtLock) {
    $stmtLock->bind_param('s' . $types, $processId, ...$queueIds);
    $stmtLock->execute();
    $stmtLock->close();
}

$stmtRows = $conn->prepare(
    "SELECT * FROM invitation_queue WHERE locked_by = ? AND status = 'processing' ORDER BY id ASC"
);
$rows = [];
if ($stmtRows) {
    $stmtRows->bind_param('s', $processId);
    $stmtRows->execute();
    $result = $stmtRows->get_result();
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmtRows->close();
}

$eventDates = 'October 14-17, 2026';
$eventVenue = 'SMX Convention Center, Pasay City';
$eventLink = 'https://psmeinc.org.ph/#/psme/event-details/81';
$eventFb = 'https://www.facebook.com/psmeinc';

$mailFromAddress = $_ENV['GOOGLE_SENDER_EMAIL'] ?? ($_ENV['MAIL_FROM_ADDRESS'] ?? 'delegates2@psmeinc.org.ph');
$mailFromName = $_ENV['MAIL_FROM_NAME'] ?? 'PSME Invitation Team';

$processed = 0;
$failed = 0;

foreach ($rows as $row) {
    $queueId = (int)$row['id'];
    $type = strtolower(trim($row['type'] ?? 'individual'));
    $email = trim($row['email'] ?? '');
    $trackingToken = $row['tracking_token'] ?? '';

    $payload = [
        'salutation' => $row['salutation'] ?? '',
        'full_name' => $row['full_name'] ?? '',
        'designation' => $row['designation'] ?? '',
        'company' => $row['company'] ?? '',
        'address' => $row['address'] ?? '',
        'hr_email' => $row['hr_email'] ?? ''
    ];

    try {
        if ($type === 'company') {
            $pdfString = buildCompanyPdf($payload);
            $subject = 'Official Invitation to the 74th PSME National Convention';
            $attachmentName = '74th_NatCon_Company_Invitation.pdf';
            $htmlBody = "
                <p>Dear <strong>{$payload['full_name']}</strong>,</p>
                <p>We are pleased to invite you and your company, <strong>{$payload['company']}</strong>, to the upcoming
                <strong>74th PSME National Convention</strong> on <strong>$eventDates</strong> at <strong>$eventVenue</strong>.</p>
                <p>
                    Registration link: <a href='" . buildClickTrackingUrl($baseUrl, $trackingToken, $eventLink) . "' target='_blank'>$eventLink</a><br>
                    Official Facebook page: <a href='" . buildClickTrackingUrl($baseUrl, $trackingToken, $eventFb) . "' target='_blank'>$eventFb</a>
                </p>
                <p>Please see the attached official invitation letter.</p>
                <p>Thank you,<br><strong>PSME National Office</strong></p>
                <img src='" . buildOpenTrackingUrl($baseUrl, $trackingToken) . "' width='1' height='1' alt='' style='display:none;'>
            ";
            $sourceType = 'bulk_company';
        } else {
            $pdfString = buildIndividualPdf($payload);
            $attachmentName = '74th_NatCon_Invitation_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $payload['company']) . '.pdf';
            $subject = '74th PSME National Convention Official Invitation';
            $htmlBody = "
                <p>Good day <strong>{$payload['salutation']} {$payload['full_name']}</strong>,</p>
                <p>
                    We are pleased to attach your official invitation letter to the 74th PSME National Convention, happening on <strong>$eventDates</strong> at the <strong>$eventVenue</strong>.<br>
                    This event promises to be an exciting gathering of mechanical engineers, industry leaders, and professionals from across the country.
                </p>
                <p>
                    <strong>To confirm your attendance and secure your slot, please register as soon as possible:</strong><br>
                    <a href='" . buildClickTrackingUrl($baseUrl, $trackingToken, $eventLink) . "' target='_blank'>$eventLink</a>
                </p>
                <p>
                    For the latest updates, announcements, and event highlights, follow our official NatCon Facebook page:<br>
                    <a href='" . buildClickTrackingUrl($baseUrl, $trackingToken, $eventFb) . "' target='_blank'>$eventFb</a>
                </p>
                <p>Thank you for your interest, and we look forward to welcoming you at the 74th PSME National Convention!</p>
                <p>Sincerely,<br><strong>PSME Invitation Team</strong></p>
                <img src='" . buildOpenTrackingUrl($baseUrl, $trackingToken) . "' width='1' height='1' alt='' style='display:none;'>
            ";
            $sourceType = 'bulk_individual';
        }

        $messageId = buildMessageId($mailFromAddress);
        $payloadMessage = [
            'from' => $mailFromName . ' <' . $mailFromAddress . '>',
            'to' => $payload['full_name'] !== '' ? ($payload['full_name'] . ' <' . $email . '>') : $email,
            'cc' => $payload['hr_email'] !== '' ? $payload['hr_email'] : '',
            'subject' => $subject,
            'html' => $htmlBody,
            'attachments' => [
                [
                    'filename' => $attachmentName,
                    'data' => $pdfString,
                    'mime' => 'application/pdf'
                ]
            ],
            'message_id' => $messageId
        ];

        $sendResult = sendGmailMessage($payloadMessage);

        $stmtMsg = $conn->prepare(
            "INSERT INTO email_messages (source_type, source_id, email, message_id, gmail_message_id, gmail_thread_id, tracking_token, tracking_id, status, sent_at, last_event_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'SENT', NOW(), NOW())"
        );
        if ($stmtMsg) {
            $gmailMessageId = $sendResult['gmail_message_id'] ?? null;
            $gmailThreadId = $sendResult['gmail_thread_id'] ?? null;
            $stmtMsg->bind_param('sissssss', $sourceType, $queueId, $email, $messageId, $gmailMessageId, $gmailThreadId, $trackingToken, $trackingToken);
            $stmtMsg->execute();
            $stmtMsg->close();
        }

        $stmtUpdate = $conn->prepare(
            "UPDATE invitation_queue SET status='sent', sent_at=NOW(), error_message=NULL WHERE id=?"
        );
        if ($stmtUpdate) {
            $stmtUpdate->bind_param('i', $queueId);
            $stmtUpdate->execute();
            $stmtUpdate->close();
        }

        $processed++;
    } catch (Exception $e) {
        $failed++;
        $errorMessage = $e->getMessage();
        appLog('error', 'Queue send failed.', ['queue_id' => $queueId, 'error' => $errorMessage]);

        $stmtFail = $conn->prepare(
            "UPDATE invitation_queue
             SET status = CASE WHEN attempts >= ? THEN 'failed' ELSE 'retry' END,
                 error_message = ?
             WHERE id = ?"
        );
        if ($stmtFail) {
            $stmtFail->bind_param('isi', $maxAttempts, $errorMessage, $queueId);
            $stmtFail->execute();
            $stmtFail->close();
        }
    }
}

$conn->close();
flock($lockHandle, LOCK_UN);
fclose($lockHandle);

echo "Queue processed: $processed sent, $failed failed.\n";
