<?php
function csrfToken(): string {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken(?string $token): bool {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    if ($sessionToken === '' || $token === null) {
        return false;
    }
    return hash_equals($sessionToken, $token);
}
