<?php
require 'vendor/autoload.php';
require 'gmail_api.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? '';
$clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? '';
$redirectUri = $_ENV['GOOGLE_REDIRECT_URI'] ?? '';
$tokenPath = $_ENV['GMAIL_TOKEN_PATH'] ?? 'storage/gmail_token.json';

if ($clientId === '' || $clientSecret === '' || $redirectUri === '') {
    echo 'Missing Gmail OAuth configuration.';
    exit;
}

$client = new Google\Client();
$client->setClientId($clientId);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->setAccessType('offline');
$client->setPrompt('consent');
$client->setScopes([Google\Service\Gmail::GMAIL_SEND]);

if (!isset($_GET['code'])) {
    echo 'Authorization code missing.';
    exit;
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
if (isset($token['error'])) {
    echo 'Failed to obtain token: ' . $token['error'];
    exit;
}

if (!isset($token['refresh_token']) && file_exists($tokenPath)) {
    $existing = json_decode(file_get_contents($tokenPath), true);
    if (is_array($existing) && isset($existing['refresh_token'])) {
        $token['refresh_token'] = $existing['refresh_token'];
    }
}

if (!is_dir(dirname($tokenPath))) {
    mkdir(dirname($tokenPath), 0755, true);
}
file_put_contents($tokenPath, json_encode($token));

echo 'Gmail authorization complete. You can return to the dashboard.';
?>