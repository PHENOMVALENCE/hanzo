<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

logout_user();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
flash_set('success', 'You have been logged out.');
redirect('index.php');
