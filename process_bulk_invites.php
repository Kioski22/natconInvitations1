<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require 'db.php';
require 'vendor/autoload.php';
require_once __DIR__ . '/helpers/csrf.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

header('Content-Type: application/json');

$csrfToken = $_POST['csrf_token'] ?? null;
if (!validateCsrfToken($csrfToken)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid CSRF token.']);
    exit;
}

$eventName = '74th PSME National Convention';


if (!isset($_FILES['csv_file'])) {
    echo json_encode(['error' => 'CSV upload failed. No file received.']);
    exit;
}

if ($_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
    $uploadErrors = [
        UPLOAD_ERR_INI_SIZE => 'CSV upload failed. File exceeds server limit.',
        UPLOAD_ERR_FORM_SIZE => 'CSV upload failed. File exceeds form limit.',
        UPLOAD_ERR_PARTIAL => 'CSV upload failed. File was only partially uploaded.',
        UPLOAD_ERR_NO_FILE => 'CSV upload failed. No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'CSV upload failed. Missing temp folder.',
        UPLOAD_ERR_CANT_WRITE => 'CSV upload failed. Failed to write to disk.',
        UPLOAD_ERR_EXTENSION => 'CSV upload failed. A PHP extension blocked the upload.'
    ];
    $code = $_FILES['csv_file']['error'];
    $message = $uploadErrors[$code] ?? 'CSV upload failed.';
    echo json_encode(['error' => $message]);
    exit;
}

$handle = fopen($_FILES['csv_file']['tmp_name'], 'r');
if (!$handle) {
    echo json_encode(['error' => 'Unable to read CSV file.']);
    exit;
}

$header = fgetcsv($handle);
if (!$header) {
    echo json_encode(['error' => 'CSV header row is missing.']);
    fclose($handle);
    exit;
}

$header = array_map(function ($col) {
    return strtolower(trim($col));
}, $header);

if (!empty($header)) {
    $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
}

$required = ['type', 'email', 'full_name', 'designation', 'company', 'address'];
$missing = array_diff($required, $header);
if (!empty($missing)) {
    echo json_encode(['error' => 'Missing required columns: ' . implode(', ', $missing)]);
    fclose($handle);
    exit;
}

$batchFilename = basename($_FILES['csv_file']['name']);
$batchId = null;
$stmtBatch = $conn->prepare(
    "INSERT INTO invitation_batches (filename, created_at) VALUES (?, NOW())"
);
if ($stmtBatch) {
    $stmtBatch->bind_param('s', $batchFilename);
    $stmtBatch->execute();
    $batchId = $conn->insert_id;
    $stmtBatch->close();
}

$results = [
    'batch_id' => $batchId,
    'total' => 0,
    'queued' => 0,
    'failed' => 0,
    'errors' => []
];

while (($row = fgetcsv($handle)) !== false) {
    if (count($row) === 1 && trim($row[0]) === '') {
        continue;
    }
    $results['total']++;
    $row = array_slice($row, 0, count($header));
    $data = array_combine($header, array_pad($row, count($header), ''));

    $type = strtolower(trim($data['type'] ?? 'individual'));
    if (!in_array($type, ['individual', 'company'], true)) {
        $results['failed']++;
        $results['errors'][] = ['row' => $results['total'], 'error' => 'Invalid type'];
        continue;
    }

    $email = trim($data['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $results['failed']++;
        $results['errors'][] = ['row' => $results['total'], 'error' => 'Invalid email'];
        continue;
    }

    $payload = [
        'salutation' => trim($data['salutation'] ?? ''),
        'full_name' => trim($data['full_name'] ?? ''),
        'designation' => trim($data['designation'] ?? ''),
        'company' => trim($data['company'] ?? ''),
        'address' => trim($data['address'] ?? ''),
        'hr_email' => trim($data['hr_email'] ?? '')
    ];

    if ($payload['full_name'] === '' || $payload['designation'] === '' || $payload['company'] === '' || $payload['address'] === '') {
        $results['failed']++;
        $results['errors'][] = ['row' => $results['total'], 'error' => 'Missing required fields'];
        continue;
    }

    $trackingToken = bin2hex(random_bytes(16));
    $queueId = null;

    $stmtQueue = $conn->prepare(
        "INSERT INTO invitation_queue (batch_id, type, email, salutation, full_name, designation, company, address, hr_email, tracking_token, status, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'queued', NOW())"
    );
    if ($stmtQueue) {
        $stmtQueue->bind_param(
            'isssssssss',
            $batchId,
            $type,
            $email,
            $payload['salutation'],
            $payload['full_name'],
            $payload['designation'],
            $payload['company'],
            $payload['address'],
            $payload['hr_email'],
            $trackingToken
        );
        $stmtQueue->execute();
        $queueId = $conn->insert_id;
        $stmtQueue->close();
    }

    try {
        if ($type === 'individual') {
            $stmtInvite = $conn->prepare(
                "INSERT INTO invitations (email, salutation, full_name, designation, company, address, event, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, 'queued')"
            );
            if ($stmtInvite) {
                $stmtInvite->bind_param(
                    'sssssss',
                    $email,
                    $payload['salutation'],
                    $payload['full_name'],
                    $payload['designation'],
                    $payload['company'],
                    $payload['address'],
                    $eventName
                );
                $stmtInvite->execute();
                $stmtInvite->close();
            } else {
                throw new Exception('Unable to queue invitation');
            }
        } else {
            $stmtInvite = $conn->prepare(
                "INSERT INTO supervisor_invitations (supervisor_name, company, company_address, designation, email, status)
                 VALUES (?, ?, ?, ?, ?, 'queued')"
            );
            if ($stmtInvite) {
                $stmtInvite->bind_param(
                    'sssss',
                    $payload['full_name'],
                    $payload['company'],
                    $payload['address'],
                    $payload['designation'],
                    $email
                );
                $stmtInvite->execute();
                $stmtInvite->close();
            } else {
                throw new Exception('Unable to queue company invitation');
            }
        }

        $results['queued']++;
    } catch (Exception $e) {
        $results['failed']++;
        $results['errors'][] = ['row' => $results['total'], 'error' => $e->getMessage()];
        if ($queueId) {
            $stmtFail = $conn->prepare(
                "UPDATE invitation_queue SET status='failed', error_message=? WHERE id=?"
            );
            if ($stmtFail) {
                $err = $e->getMessage();
                $stmtFail->bind_param('si', $err, $queueId);
                $stmtFail->execute();
                $stmtFail->close();
            }
        }
    }
}

fclose($handle);
$conn->close();

echo json_encode(['success' => true] + $results);
?>