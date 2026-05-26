<?php
require 'gmail_api.php';

$tokenPath = resolveTokenPath($_ENV['GMAIL_TOKEN_PATH'] ?? 'storage/gmail_token.json');

try {
    $client = buildOauthClient();
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}

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