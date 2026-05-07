<?php
require_once __DIR__ . '/../db.php';

$lockFile = __DIR__ . '/../logs/cron_retry_failed.lock';
$lockHandle = fopen($lockFile, 'c');
if (!$lockHandle || !flock($lockHandle, LOCK_EX | LOCK_NB)) {
    echo "Retry job already running.\n";
    exit;
}

$maxAttempts = (int)($_ENV['QUEUE_MAX_ATTEMPTS'] ?? 3);

$stmt = $conn->prepare(
    "UPDATE invitation_queue
     SET status = 'retry',
         error_message = NULL
     WHERE status = 'failed'
       AND attempts < ?"
);
$updated = 0;
if ($stmt) {
    $stmt->bind_param('i', $maxAttempts);
    $stmt->execute();
    $updated = $stmt->affected_rows;
    $stmt->close();
}

$conn->close();
flock($lockHandle, LOCK_UN);
fclose($lockHandle);

echo "Failed items reset to retry: $updated\n";
