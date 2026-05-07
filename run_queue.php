<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/helpers/csrf.php';
header('Content-Type: application/json');

$csrfToken = $_POST['csrf_token'] ?? null;
if (!validateCsrfToken($csrfToken)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid CSRF token.']);
    exit;
}

ob_start();
require __DIR__ . '/cron/process_queue.php';
$output = trim(ob_get_clean() ?: '');

$sent = 0;
$failed = 0;
if (preg_match('/Queue processed:\s*(\d+) sent,\s*(\d+) failed/i', $output, $matches)) {
    $sent = (int)$matches[1];
    $failed = (int)$matches[2];
}

echo json_encode(['sent' => $sent, 'failed' => $failed]);
