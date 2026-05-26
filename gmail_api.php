<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

function gmailEnv(string $key, string $default = ''): string {
    $env = $_ENV[$key] ?? null;
    if (is_string($env) && $env !== '') {
        return $env;
    }

    $server = $_SERVER[$key] ?? null;
    if (is_string($server) && $server !== '') {
        return $server;
    }

    $value = getenv($key);
    if (is_string($value) && $value !== '') {
        return $value;
    }

    return $default;
}

function resolveTokenPath(string $tokenPath): string {
    if ($tokenPath === '') {
        return $tokenPath;
    }

    if (preg_match('/^[A-Za-z]:\\\\/', $tokenPath) || strpos($tokenPath, '/') === 0) {
        return $tokenPath;
    }

    return __DIR__ . '/' . ltrim($tokenPath, '/\\');
}

function buildOauthClient(): Client {
    $clientId = gmailEnv('GOOGLE_CLIENT_ID');
    $clientSecret = gmailEnv('GOOGLE_CLIENT_SECRET');
    $redirectUri = gmailEnv('GOOGLE_REDIRECT_URI');
    $tokenPath = resolveTokenPath(gmailEnv('GMAIL_TOKEN_PATH', 'storage/gmail_token.json'));

    if ($clientId === '' || $clientSecret === '' || $redirectUri === '') {
        throw new Exception('Missing Gmail OAuth configuration.');
    }

    $client = new Client();
    $client->setClientId($clientId);
    $client->setClientSecret($clientSecret);
    $client->setRedirectUri($redirectUri);
    $client->setAccessType('offline');
    $client->setPrompt('consent');
    // Request send + mailbox read scopes so reply tracking can inspect threads.
    $client->setScopes([
        Gmail::GMAIL_SEND,
        Gmail::GMAIL_READONLY,
        Gmail::GMAIL_MODIFY
    ]);

    return $client;
}

function getGmailClient(): Client {
    $tokenPath = resolveTokenPath(gmailEnv('GMAIL_TOKEN_PATH', 'storage/gmail_token.json'));
    $client = buildOauthClient();

    if (!file_exists($tokenPath)) {
        throw new Exception('Gmail token not found. Visit gmail_oauth_start.php to authorize.');
    }

    $token = json_decode(file_get_contents($tokenPath), true);
    if (!is_array($token)) {
        throw new Exception('Invalid Gmail token data.');
    }

    $client->setAccessToken($token);

    if ($client->isAccessTokenExpired()) {
        $refreshToken = $client->getRefreshToken();
        if (!$refreshToken && isset($token['refresh_token'])) {
            $refreshToken = $token['refresh_token'];
        }
        if (!$refreshToken) {
            throw new Exception('Gmail refresh token missing. Reauthorize via gmail_oauth_start.php.');
        }

        $newToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
        if (isset($newToken['error'])) {
            throw new Exception('Failed to refresh Gmail token: ' . $newToken['error']);
        }

        $newToken['refresh_token'] = $refreshToken;
        if (!is_dir(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0755, true);
        }
        file_put_contents($tokenPath, json_encode($newToken));
        $client->setAccessToken($newToken);
    }

    return $client;
}

function buildRawMessage(array $payload): string {
    $boundary = 'mix_' . bin2hex(random_bytes(8));
    $altBoundary = 'alt_' . bin2hex(random_bytes(8));

    $headers = [];
    $headers[] = 'From: ' . $payload['from'];
    $headers[] = 'To: ' . $payload['to'];
    if (!empty($payload['cc'])) {
        $headers[] = 'Cc: ' . $payload['cc'];
    }
    $headers[] = 'Subject: ' . $payload['subject'];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Message-ID: ' . $payload['message_id'];
    $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';

    $plainBody = strip_tags($payload['html']);

    $body = "--" . $boundary . "\r\n";
    $body .= 'Content-Type: multipart/alternative; boundary="' . $altBoundary . '"' . "\r\n\r\n";

    $body .= "--" . $altBoundary . "\r\n";
    $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $body .= $plainBody . "\r\n";

    $body .= "--" . $altBoundary . "\r\n";
    $body .= "Content-Type: text/html; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $body .= $payload['html'] . "\r\n";

    $body .= "--" . $altBoundary . "--\r\n";

    foreach ($payload['attachments'] as $attachment) {
        $body .= "--" . $boundary . "\r\n";
        $body .= 'Content-Type: ' . $attachment['mime'] . '; name="' . $attachment['filename'] . '"' . "\r\n";
        $body .= 'Content-Disposition: attachment; filename="' . $attachment['filename'] . '"' . "\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($attachment['data'])) . "\r\n";
    }

    $body .= "--" . $boundary . "--";

    return implode("\r\n", $headers) . "\r\n\r\n" . $body;
}

function sendGmailMessage(array $payload): array {
    $client = getGmailClient();
    $service = new Gmail($client);

    $rawMessage = buildRawMessage($payload);
    $encodedMessage = rtrim(strtr(base64_encode($rawMessage), '+/', '-_'), '=');

    $message = new Message();
    $message->setRaw($encodedMessage);

    $sent = $service->users_messages->send('me', $message);

    return [
        'rfc822_message_id' => $payload['message_id'],
        'gmail_message_id' => $sent ? $sent->getId() : null,
        'gmail_thread_id' => $sent ? $sent->getThreadId() : null
    ];
}

function buildMessageId(string $sender): string {
    $domain = 'localhost';
    if (strpos($sender, '@') !== false) {
        $domain = substr(strrchr($sender, '@'), 1);
    }
    return '<' . bin2hex(random_bytes(16)) . '@' . $domain . '>';
}
?>