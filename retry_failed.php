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
require __DIR__ . '/cron/retry_failed.php';
$raw = trim(ob_get_clean() ?: '');

$reset = 0;
if (preg_match('/Failed items reset to retry:\s*(\d+)/i', $raw, $matches)) {
    $reset = (int)$matches[1];
}

echo json_encode(['reset' => $reset]);
