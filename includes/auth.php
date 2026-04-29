<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function auth_id(): ?int
{
    $u = auth_user();
    return $u ? (int) $u['id'] : null;
}

function auth_role(): ?string
{
    $u = auth_user();
    return $u['role'] ?? null;
}

function login_user(array $row): void
{
    $_SESSION['user'] = [
        'id'    => (int) $row['id'],
        'email' => $row['email'],
        'name'  => $row['full_name'] ?? ($row['factory_name'] ?? 'User'),
        'role'  => $row['role'],
    ];
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function require_login(): void
{
    if (!auth_user()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? app_url('index.php');
        redirect('login.php');
    }
}

function require_buyer(): void
{
    require_login();
    if (auth_role() !== 'buyer') {
        flash_set('error', 'Only buyer accounts can submit product inquiries.');
        redirect('index.php');
    }
}

function require_admin(): void
{
    require_login();
    if (auth_role() !== 'admin') {
        http_response_code(403);
        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Forbidden</title></head><body><p>Admin access only.</p><p><a href="' . e(app_url()) . '">Home</a></p></body></html>';
        exit;
    }
}

function require_factory(): void
{
    require_login();
    if (auth_role() !== 'factory') {
        http_response_code(403);
        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Forbidden</title></head><body><p>Factory access only.</p><p><a href="' . e(app_url()) . '">Home</a></p></body></html>';
        exit;
    }
}

function auth_login_by_role(PDO $pdo, string $email, string $password, string $role): ?array
{
    if ($role === 'admin') {
        $st = $pdo->prepare('SELECT id, full_name, email, password, role, status FROM admins WHERE email = ? LIMIT 1');
        $st->execute([$email]);
        $row = $st->fetch();
        if ($row && $row['status'] === 'active' && password_verify($password, $row['password'])) {
            return ['id' => $row['id'], 'full_name' => $row['full_name'], 'email' => $row['email'], 'role' => 'admin'];
        }
    } elseif ($role === 'buyer') {
        $st = $pdo->prepare('SELECT id, full_name, email, password, status FROM buyers WHERE email = ? LIMIT 1');
        $st->execute([$email]);
        $row = $st->fetch();
        if ($row && $row['status'] === 'active' && password_verify($password, $row['password'])) {
            return ['id' => $row['id'], 'full_name' => $row['full_name'], 'email' => $row['email'], 'role' => 'buyer'];
        }
    } elseif ($role === 'factory') {
        $st = $pdo->prepare('SELECT id, factory_name, email, password, status FROM factories WHERE email = ? LIMIT 1');
        $st->execute([$email]);
        $row = $st->fetch();
        if ($row && $row['status'] === 'active' && password_verify($password, $row['password'])) {
            return ['id' => $row['id'], 'factory_name' => $row['factory_name'], 'email' => $row['email'], 'role' => 'factory'];
        }
    }
    return null;
}
