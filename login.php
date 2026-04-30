<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (auth_user()) {
    if (auth_role() === 'admin') {
        redirect('admin/dashboard.php');
    }
    if (auth_role() === 'factory') {
        redirect('factory/dashboard.php');
    }
    redirect('buyer/dashboard.php');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $role = (string) ($_POST['role'] ?? 'buyer');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    if ($password === '') {
        $errors[] = 'Password is required.';
    }
    if (!in_array($role, ['buyer', 'factory', 'admin'], true)) {
        $errors[] = 'Select account type.';
    }

    if ($errors === []) {
        $row = auth_login_by_role($pdo, $email, $password, $role);
        if (!$row) {
            $errors[] = 'Invalid email or password.';
        } else {
            login_user($row);
            $target = $_SESSION['redirect_after_login'] ?? null;
            unset($_SESSION['redirect_after_login']);
            if (is_string($target) && $target !== '' && str_starts_with($target, '/')) {
                header('Location: ' . $target);
                exit;
            }
            if ($role === 'admin') {
                redirect('admin/dashboard.php');
            }
            if ($role === 'factory') {
                redirect('factory/dashboard.php');
            }
            redirect('buyer/dashboard.php');
        }
    }
}

$pageTitle = __('login');
require __DIR__ . '/includes/header.php';
$hideShopNav = false;
require __DIR__ . '/includes/navbar.php';
?>

<main class="container py-5" style="max-width:440px;">
    <h1 class="h3 mb-4"><?= e(__('login')) ?></h1>
    <?php foreach ($errors as $er): ?>
        <div class="alert alert-danger"><?= e($er) ?></div>
    <?php endforeach; ?>
    <?php if ($m = flash_get('error')): ?>
        <div class="alert alert-warning"><?= e($m) ?></div>
    <?php endif; ?>
    <form method="post" class="bg-white border rounded p-4 shadow-sm">
        <div class="mb-3">
            <label class="form-label">Account type</label>
            <select name="role" class="form-select" required>
                <option value="buyer" <?= (($_POST['role'] ?? 'buyer') === 'buyer') ? 'selected' : '' ?>><?= e(__('buyers')) ?></option>
                <option value="factory" <?= (($_POST['role'] ?? '') === 'factory') ? 'selected' : '' ?>><?= e(__('factory_workspace')) ?></option>
                <option value="admin" <?= (($_POST['role'] ?? '') === 'admin') ? 'selected' : '' ?>><?= e(__('admin_panel')) ?></option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required value="<?= e((string) ($_POST['email'] ?? '')) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-hanzo-primary w-100"><?= e(__('login')) ?></button>
    </form>
    <p class="text-center mt-3 small"><?= e(__('register_buyer')) ?>? <a href="<?= e(app_url('register.php')) ?>"><?= e(__('create_account')) ?></a></p>
</main>

<?php
$footerMode = 'full';
require __DIR__ . '/includes/footer.php';
