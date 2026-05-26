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
                Good day, <strong>{$payload['salutation']} {$payload['full_name']}</strong>,
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
                <a href='" . buildClickTrackingUrl($baseUrl, $trackingToken, $eventLink) . "' target='_blank'
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
                <a href='" . buildClickTrackingUrl($baseUrl, $trackingToken, $eventFb) . "' target='_blank' style='color:#1877F2; font-weight:700; text-decoration:none;'>$eventFb</a>
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
            <div style='font-size:12px; color:#555555; margin-bottom:6px;'><strong style='color:#333;'>Email:</strong>&nbsp;&nbsp;<a href='mailto:delegates1@psmeinc.org.ph' style='color:#004085; text-decoration:none;'>delegates1@psmeinc.org.ph</a></div>
            <div style='font-size:12px; color:#555555; margin-bottom:6px;'><strong style='color:#333;'>Website:</strong>&nbsp;&nbsp;<a href='https://psmeinc.org.ph' style='color:#004085; text-decoration:none;'>psmeinc.org.ph</a></div>
            <div style='border-top:1px solid #eeeeee; margin-top:14px; padding-top:12px; font-size:11px; color:#aaaaaa; line-height:1.5;'>
                This message was sent on behalf of PSME, Inc. to an invited delegate. If you believe this was sent in error, please disregard this email.
            </div>
            </div>

            <!-- Tracking pixel -->
            <img src='" . buildOpenTrackingUrl($baseUrl, $trackingToken) . "' width='1' height='1' alt='' style='display:none;'>

        </div>
        </div>
        ";
            $sourceType = 'bulk_company';
        } else {
            $pdfString = buildIndividualPdf($payload);
            $attachmentName = '74th_NatCon_Invitation_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $payload['company']) . '.pdf';
            $subject = '74th PSME National Convention Official Invitation';
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
                Good day, <strong>{$payload['salutation']} {$payload['full_name']}</strong>,
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
                <a href='" . buildClickTrackingUrl($baseUrl, $trackingToken, $eventLink) . "' target='_blank'
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
                <a href='" . buildClickTrackingUrl($baseUrl, $trackingToken, $eventFb) . "' target='_blank' style='color:#1877F2; font-weight:700; text-decoration:none;'>$eventFb</a>
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
            <div style='font-size:12px; color:#555555; margin-bottom:6px;'><strong style='color:#333;'>Email:</strong>&nbsp;&nbsp;<a href='mailto:delegates@psmeinc.org.ph' style='color:#004085; text-decoration:none;'>delegates1@psmeinc.org.ph</a></div>
            <div style='font-size:12px; color:#555555; margin-bottom:6px;'><strong style='color:#333;'>Website:</strong>&nbsp;&nbsp;<a href='https://psmeinc.org.ph' style='color:#004085; text-decoration:none;'>psmeinc.org.ph</a></div>
            <div style='border-top:1px solid #eeeeee; margin-top:14px; padding-top:12px; font-size:11px; color:#aaaaaa; line-height:1.5;'>
                This message was sent on behalf of PSME, Inc. to an invited delegate. If you believe this was sent in error, please disregard this email.
            </div>
            </div>

            <!-- Tracking pixel -->
            <img src='" . buildOpenTrackingUrl($baseUrl, $trackingToken) . "' width='1' height='1' alt='' style='display:none;'>

        </div>
        </div>
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
