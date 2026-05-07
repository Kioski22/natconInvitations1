<?php
require 'db.php';
require 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$imapEnabled = strtolower($_ENV['IMAP_ENABLED'] ?? 'false') === 'true';
if (!$imapEnabled) {
    echo "IMAP sync disabled. Set IMAP_ENABLED=true to enable.\n";
    $conn->close();
    exit;
}

if (!function_exists('imap_open')) {
    echo "IMAP extension is not available.\n";
    $conn->close();
    exit;
}

$imapHost = $_ENV['IMAP_HOST'] ?? '';
$imapPort = $_ENV['IMAP_PORT'] ?? '993';
$imapUser = $_ENV['IMAP_USERNAME'] ?? '';
$imapPass = $_ENV['IMAP_PASSWORD'] ?? '';
$imapBox = $_ENV['IMAP_MAILBOX'] ?? 'INBOX';
$imapEnc = strtolower($_ENV['IMAP_ENCRYPTION'] ?? 'ssl');

if ($imapHost === '' || $imapUser === '' || $imapPass === '') {
    echo "Missing IMAP configuration.\n";
    $conn->close();
    exit;
}

$encFlag = '';
if ($imapEnc === 'ssl') {
    $encFlag = '/ssl';
} elseif ($imapEnc === 'tls') {
    $encFlag = '/tls';
}

$mailbox = '{' . $imapHost . ':' . $imapPort . '/imap' . $encFlag . '}' . $imapBox;
$inbox = @imap_open($mailbox, $imapUser, $imapPass);
if (!$inbox) {
    echo "Unable to connect to IMAP: " . imap_last_error() . "\n";
    $conn->close();
    exit;
}

$searches = [
    'FROM "MAILER-DAEMON"',
    'SUBJECT "Delivery Status Notification"',
    'SUBJECT "Undelivered Mail Returned to Sender"'
];

$emails = [];
foreach ($searches as $criteria) {
    $found = imap_search($inbox, $criteria);
    if ($found) {
        $emails = array_merge($emails, $found);
    }
}

$emails = array_unique($emails);
if (empty($emails)) {
    echo "No bounce messages found.\n";
    imap_close($inbox);
    $conn->close();
    exit;
}

$emails = array_slice($emails, -200);
$updated = 0;

$stmt = $conn->prepare(
    "UPDATE email_messages
     SET status = 'BOUNCED',
         bounced_at = COALESCE(bounced_at, NOW()),
         last_event_at = NOW()
     WHERE message_id = ?"
);

foreach ($emails as $msgno) {
    $headers = imap_fetchheader($inbox, $msgno);
    $body = imap_body($inbox, $msgno);
    if (!$headers && !$body) {
        continue;
    }

    $messageIds = [];
    $source = ($headers ?: '') . "\n" . ($body ?: '');

    if (preg_match('/Message-Id:\s*(<[^>]+>)/i', $source, $m)) {
        $messageIds[] = trim($m[1]);
    }
    if (preg_match('/Original-Message-ID:\s*(<[^>]+>)/i', $source, $m)) {
        $messageIds[] = trim($m[1]);
    }

    $messageIds = array_unique($messageIds);
    foreach ($messageIds as $messageId) {
        if ($stmt) {
            $stmt->bind_param('s', $messageId);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $updated += $stmt->affected_rows;
            }
        }
    }
}

if ($stmt) {
    $stmt->close();
}

imap_close($inbox);
$conn->close();

echo "Bounce sync complete. Updated: " . $updated . "\n";
?>