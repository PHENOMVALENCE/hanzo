<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

require_login();
if (auth_role() === 'admin') {
    redirect('admin/dashboard.php');
}
if (auth_role() === 'factory') {
    redirect('factory/dashboard.php');
}
redirect('buyer/dashboard.php');
