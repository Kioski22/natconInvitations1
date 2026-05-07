<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

require_once __DIR__ . '/../helpers/log.php';

function getDbConnection(): mysqli {
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $user = $_ENV['DB_USER'] ?? 'root';
    $pass = $_ENV['DB_PASS'] ?? '';
    $name = $_ENV['DB_NAME'] ?? 'natconinv';

    $conn = new mysqli($host, $user, $pass, $name);
    if ($conn->connect_error) {
        appLog('error', 'Database connection failed.', ['error' => $conn->connect_error]);
        die('Database connection failed.');
    }

    $conn->set_charset('utf8mb4');
    return $conn;
}
