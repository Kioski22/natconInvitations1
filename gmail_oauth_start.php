<?php
require 'gmail_api.php';

$client = buildOauthClient();
$authUrl = $client->createAuthUrl();

header('Location: ' . $authUrl);
exit;
?>