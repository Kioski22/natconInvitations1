<?php
require_once __DIR__ . '/url.php';

function getTrackingSecret(): string {
    return (string)($_ENV['TRACKING_SECRET'] ?? '');
}

function allowUnverifiedTracking(): bool {
    return strtolower($_ENV['ALLOW_UNVERIFIED_TRACKING'] ?? 'true') === 'true';
}

function buildTrackingSig(string $token): string {
    $secret = getTrackingSecret();
    if ($secret === '') {
        return '';
    }
    return hash_hmac('sha256', $token, $secret);
}

function isTrackingSigValid(string $token, string $sig): bool {
    $secret = getTrackingSecret();
    if ($secret === '') {
        return true;
    }
    if ($sig === '') {
        return false;
    }
    $expected = buildTrackingSig($token);
    return hash_equals($expected, $sig);
}

function buildOpenTrackingUrl(string $baseUrl, string $token): string {
    $url = rtrim($baseUrl, '/') . '/tracking/open.php?token=' . rawurlencode($token);
    $sig = buildTrackingSig($token);
    if ($sig !== '') {
        $url .= '&sig=' . rawurlencode($sig);
    }
    return $url;
}

function buildClickTrackingUrl(string $baseUrl, string $token, string $targetUrl): string {
    $url = rtrim($baseUrl, '/') . '/tracking/click.php?token=' . rawurlencode($token)
        . '&url=' . rawurlencode($targetUrl);
    $sig = buildTrackingSig($token);
    if ($sig !== '') {
        $url .= '&sig=' . rawurlencode($sig);
    }
    return $url;
}
