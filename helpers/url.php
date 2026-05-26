<?php
function getAppUrl(): string {
    $appUrl = trim($_ENV['APP_URL'] ?? '');
    if ($appUrl !== '') {
        return rtrim($appUrl, '/') . '/';
    }

    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $scheme = $isHttps ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/\\');
    return $scheme . '://' . $host . ($dir ? $dir . '/' : '/');
}
