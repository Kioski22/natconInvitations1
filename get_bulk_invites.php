<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require 'db.php';

header('Content-Type: application/json');

$rows = [];
$result = $conn->query("SELECT * FROM invitation_queue ORDER BY created_at DESC LIMIT 200");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
}

echo json_encode(['rows' => $rows]);

$conn->close();
?>