<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../gmail_api.php';
require_once __DIR__ . '/../helpers/log.php';

use Google\Service\Gmail;

function envValue(string $key, string $default = ''): string {
    $env = $_ENV[$key] ?? null;
    if (is_string($env) && $env !== '') {
        return $env;
    }

    $server = $_SERVER[$key] ?? null;
    if (is_string($server) && $server !== '') {
        return $server;
    }

    $value = getenv($key);
    if (is_string($value) && $value !== '') {
        return $value;
    }

    return $default;
}

$lockFile = __DIR__ . '/../logs/cron_check_replies.lock';
$lockHandle = fopen($lockFile, 'c');
if (!$lockHandle || !flock($lockHandle, LOCK_EX | LOCK_NB)) {
    echo "Reply checker already running.\n";
    exit;
}

$limit = (int)($_ENV['REPLY_CHECK_LIMIT'] ?? 50);
$senderEmail = strtolower(envValue('GOOGLE_SENDER_EMAIL'));

if ($senderEmail === '') {
    echo "Missing GOOGLE_SENDER_EMAIL.\n";
    $conn->close();
    exit;
}

$client = getGmailClient();
$service = new Gmail($client);

$stmt = $conn->prepare(
    "SELECT id, gmail_thread_id, sent_at
     FROM email_messages
     WHERE gmail_thread_id IS NOT NULL
       AND replied_at IS NULL
       AND (status IS NULL OR UPPER(status) NOT IN ('REPLIED','BOUNCED'))
     ORDER BY last_checked_at ASC, sent_at DESC
     LIMIT ?"
);
$rows = [];
if ($stmt) {
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();
}

function getHeaderValue(array $headers, string $name): string {
    foreach ($headers as $header) {
        if (strcasecmp($header->getName(), $name) === 0) {
            return $header->getValue();
        }
    }
    return '';
}

$updated = 0;
$errors = [];
foreach ($rows as $row) {
    $messageId = (int)$row['id'];
    $threadId = $row['gmail_thread_id'];
    $sentAt = $row['sent_at'] ? strtotime($row['sent_at']) : 0;

    try {
        $thread = $service->users_threads->get('me', $threadId, [
            'format' => 'metadata',
            'metadataHeaders' => ['From', 'Date']
        ]);

        $messages = $thread->getMessages();
        if (!$messages) {
            continue;
        }

        $isReply = false;
        foreach ($messages as $msg) {
            $headers = $msg->getPayload()->getHeaders();
            $from = strtolower(getHeaderValue($headers, 'From'));
            $internalDateMs = (int)($msg->getInternalDate() ?? 0);
            $internalDate = $internalDateMs > 0 ? (int)($internalDateMs / 1000) : 0;

            if ($internalDate > 0 && $sentAt > 0 && $internalDate < $sentAt) {
                continue;
            }

            if ($from !== '' && strpos($from, $senderEmail) === false) {
                $isReply = true;
                break;
            }
        }

        if ($isReply) {
            $stmtUpdate = $conn->prepare(
                "UPDATE email_messages
                 SET status = 'REPLIED',
                     replied_at = NOW(),
                     last_event_at = NOW(),
                     last_checked_at = NOW()
                 WHERE id = ?"
            );
            if ($stmtUpdate) {
                $stmtUpdate->bind_param('i', $messageId);
                $stmtUpdate->execute();
                $stmtUpdate->close();
            }

            $eventStmt = $conn->prepare(
                "INSERT INTO email_events (email_message_id, event_type, event_key, event_at, meta_json)
                 VALUES (?, 'replied', 'reply', NOW(), ?)
                 ON DUPLICATE KEY UPDATE event_at = NOW()"
            );
            if ($eventStmt) {
                $meta = json_encode(['thread_id' => $threadId], JSON_UNESCAPED_SLASHES);
                $eventStmt->bind_param('is', $messageId, $meta);
                $eventStmt->execute();
                $eventStmt->close();
            }

            $updated++;
        } else {
            $stmtCheck = $conn->prepare(
                "UPDATE email_messages SET last_checked_at = NOW() WHERE id = ?"
            );
            if ($stmtCheck) {
                $stmtCheck->bind_param('i', $messageId);
                $stmtCheck->execute();
                $stmtCheck->close();
            }
        }
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
        $errors[] = $errorMessage;
        appLog('error', 'Reply check failed.', ['message_id' => $messageId, 'error' => $errorMessage]);
    }
}

$conn->close();
flock($lockHandle, LOCK_UN);
fclose($lockHandle);

echo "Reply sync complete. Updated: $updated\n";
if (!empty($errors)) {
    $errorText = implode(' ', array_unique($errors));
    if (stripos($errorText, 'insufficient authentication scopes') !== false || stripos($errorText, 'insufficient permission') !== false) {
        echo "Reply sync warning: Gmail token lacks mailbox read scope. Re-authorize via gmail_oauth_start.php, then try again.\n";
    } else {
        echo "Reply sync warning: " . substr($errorText, 0, 300) . "\n";
    }
}
