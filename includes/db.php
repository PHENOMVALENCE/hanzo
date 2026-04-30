<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/language.php';

$cfg = require __DIR__ . '/../config/db.php';
$dsn = 'mysql:host=' . $cfg['host'] . ';dbname=' . $cfg['dbname'] . ';charset=' . $cfg['charset'];

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Database error</title></head><body>';
    echo '<h3>China Chapu Database Connection Failed</h3>';
    echo '<p>Import <code>database/hanzo.sql</code> and verify credentials in <code>config/db.php</code>.</p>';
    echo '</body></html>';
    exit;
}

hanzo_language_bootstrap($pdo);
