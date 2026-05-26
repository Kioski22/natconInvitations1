<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/tracking.php';
require_once __DIR__ . '/../helpers/log.php';

$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$target = isset($_GET['url']) ? trim($_GET['url']) : '';
$sig = isset($_GET['sig']) ? trim($_GET['sig']) : '';

$validSig = $token !== '' && isTrackingSigValid($token, $sig);
if (!$validSig && $token !== '' && !allowUnverifiedTracking()) {
    appLog('warning', 'Click signature invalid.', ['token' => $token]);
    $token = '';
}

$targetUrl = $target !== '' ? rawurldecode($target) : '';
if ($targetUrl === '' || !filter_var($targetUrl, FILTER_VALIDATE_URL)) {
    $targetUrl = getAppUrl();
}

if ($token !== '') {
    $stmt = $conn->prepare(
        "SELECT id, clicked_at, status FROM email_messages WHERE tracking_token = ? LIMIT 1"
    );
    if ($stmt) {
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        if ($row) {
            $emailMessageId = (int)$row['id'];
            $clickedAt = $row['clicked_at'];

            if ($clickedAt === null) {
                $update = $conn->prepare(
                    "UPDATE email_messages
                     SET status = CASE
                            WHEN UPPER(status) IN ('REPLIED','BOUNCED') THEN status
                            ELSE 'CLICKED'
                         END,
                         clicked_at = NOW(),
                         last_event_at = NOW()
                     WHERE id = ?"
                );
                if ($update) {
                    $update->bind_param('i', $emailMessageId);
                    $update->execute();
                    $update->close();
                }
            }

            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $eventKey = hash('sha256', $targetUrl . '|' . date('Y-m-d'));
            $meta = json_encode([
                'ip_hash' => hash('sha256', $ip),
                'user_agent' => $ua,
                'url' => $targetUrl,
                'sig_valid' => $validSig
            ], JSON_UNESCAPED_SLASHES);

            $eventStmt = $conn->prepare(
                "INSERT INTO email_events (email_message_id, event_type, event_key, event_at, meta_json)
                 VALUES (?, 'clicked', ?, NOW(), ?)
                 ON DUPLICATE KEY UPDATE event_at = NOW()"
            );
            if ($eventStmt) {
                $eventStmt->bind_param('iss', $emailMessageId, $eventKey, $meta);
                $eventStmt->execute();
                $eventStmt->close();
            }
        }
    }
}

header('Location: ' . $targetUrl, true, 302);
$conn->close();
