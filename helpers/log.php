<?php
function appLog(string $level, string $message, array $context = []): void {
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $entry = [
        'time' => date('c'),
        'level' => $level,
        'message' => $message,
        'context' => $context
    ];

    $line = json_encode($entry, JSON_UNESCAPED_SLASHES) . PHP_EOL;
    file_put_contents($logDir . '/app.log', $line, FILE_APPEND);
}
