<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/log.php';

$lockFile = __DIR__ . '/../logs/cron_update_status.lock';
$lockHandle = fopen($lockFile, 'c');
if (!$lockHandle || !flock($lockHandle, LOCK_EX | LOCK_NB)) {
    echo "Status updater already running.\n";
    exit;
}

$result = $conn->query(
    "UPDATE email_messages
     SET status = CASE
            WHEN replied_at IS NOT NULL THEN 'REPLIED'
            WHEN bounced_at IS NOT NULL THEN 'BOUNCED'
            WHEN clicked_at IS NOT NULL THEN 'CLICKED'
            WHEN opened_at IS NOT NULL THEN 'OPENED'
            WHEN sent_at IS NOT NULL THEN 'SENT'
            ELSE status
         END,
         last_event_at = COALESCE(last_event_at, sent_at)
     WHERE id > 0"
);

$updated = $conn->affected_rows;
$conn->close();
flock($lockHandle, LOCK_UN);
fclose($lockHandle);

echo "Status update complete. Updated: $updated\n";
