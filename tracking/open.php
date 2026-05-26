<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/tracking.php';
require_once __DIR__ . '/../helpers/log.php';

$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$sig = isset($_GET['sig']) ? trim($_GET['sig']) : '';

$validSig = $token !== '' && isTrackingSigValid($token, $sig);
if (!$validSig && $token !== '' && !allowUnverifiedTracking()) {
    appLog('warning', 'Tracking signature invalid.', ['token' => $token]);
    $token = '';
}

if ($token !== '') {
    $stmt = $conn->prepare(
        "SELECT id, opened_at, status FROM email_messages WHERE tracking_token = ? LIMIT 1"
    );
    if ($stmt) {
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        if ($row) {
            $emailMessageId = (int)$row['id'];
            $openedAt = $row['opened_at'];

            if ($openedAt === null) {
                $update = $conn->prepare(
                    "UPDATE email_messages
                     SET status = CASE
                            WHEN UPPER(status) IN ('REPLIED','BOUNCED') THEN status
                            ELSE 'OPENED'
                         END,
                         opened_at = NOW(),
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
            $eventKey = hash('sha256', $ip . '|' . $ua . '|' . date('Y-m-d'));
            $meta = json_encode([
                'ip_hash' => hash('sha256', $ip),
                'user_agent' => $ua,
                'sig_valid' => $validSig
            ], JSON_UNESCAPED_SLASHES);

            $eventStmt = $conn->prepare(
                "INSERT INTO email_events (email_message_id, event_type, event_key, event_at, meta_json)
                 VALUES (?, 'opened', ?, NOW(), ?)
                 ON DUPLICATE KEY UPDATE event_at = event_at"
            );
            if ($eventStmt) {
                $eventStmt->bind_param('iss', $emailMessageId, $eventKey, $meta);
                $eventStmt->execute();
                $eventStmt->close();
            }
        }
    }
}

$gifData = base64_decode('R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==');
header('Content-Type: image/gif');
header('Content-Length: ' . strlen($gifData));
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

echo $gifData;

$conn->close();
