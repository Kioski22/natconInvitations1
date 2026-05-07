<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    echo "Unauthorized";
    exit;
}

require __DIR__ . '/cron/check_replies.php';
?>