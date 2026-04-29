<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (auth_user()) {
    redirect('buyer/dashboard.php');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['full_name'] ?? ''));
    $company = trim((string) ($_POST['company_name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $country = trim((string) ($_POST['country'] ?? 'Tanzania'));
    $city = trim((string) ($_POST['city'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $password2 = (string) ($_POST['password_confirm'] ?? '');

    if ($name === '' || strlen($name) > 255) {
        $errors[] = 'Full name is required (max 255 characters).';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if ($password !== $password2) {
        $errors[] = 'Passwords do not match.';
    }

    if ($errors === []) {
        $st = $pdo->prepare('SELECT id FROM buyers WHERE email = ?');
        $st->execute([$email]);
        if ($st->fetch()) {
            $errors[] = 'An account with this email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare('INSERT INTO buyers (full_name, company_name, email, phone, country, city, password, status) VALUES (?,?,?,?,?,?,?,"active")');
            $ins->execute([$name, $company !== '' ? $company : null, $email, $phone !== '' ? $phone : null, $country !== '' ? $country : null, $city !== '' ? $city : null, $hash]);
            $id = (int) $pdo->lastInsertId();
            login_user([
                'id' => $id,
                'email' => $email,
                'full_name' => $name,
                'role' => 'buyer',
            ]);
            flash_set('success', 'Welcome to HANZO. You can now send inquiries.');
            redirect('buyer/dashboard.php');
        }
    }
}

$pageTitle = 'Register';
require __DIR__ . '/includes/header.php';
$hideShopNav = false;
require __DIR__ . '/includes/navbar.php';
?>

<main class="container py-5" style="max-width:480px;">
    <h1 class="h3 mb-2">Buyer registration</h1>
    <p class="text-muted small mb-4">Self-service signup is available for <strong>buyer</strong> accounts only. Factory and admin accounts are created by HANZO.</p>
    <?php foreach ($errors as $er): ?>
        <div class="alert alert-danger"><?= e($er) ?></div>
    <?php endforeach; ?>
    <form method="post" class="bg-white border rounded p-4 shadow-sm">
        <div class="mb-3">
            <label class="form-label">Full name</label>
            <input type="text" name="full_name" class="form-control" maxlength="255" required value="<?= e((string) ($_POST['full_name'] ?? '')) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required value="<?= e((string) ($_POST['email'] ?? '')) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Company name</label>
            <input type="text" name="company_name" class="form-control" value="<?= e((string) ($_POST['company_name'] ?? '')) ?>">
        </div>
        <div class="row g-2">
            <div class="col-md-6 mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= e((string) ($_POST['phone'] ?? '')) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Country</label>
                <input type="text" name="country" class="form-control" value="<?= e((string) ($_POST['country'] ?? 'Tanzania')) ?>">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control" value="<?= e((string) ($_POST['city'] ?? '')) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required minlength="8">
        </div>
        <div class="mb-3">
            <label class="form-label">Confirm password</label>
            <input type="password" name="password_confirm" class="form-control" required minlength="8">
        </div>
        <button type="submit" class="btn btn-hanzo-primary w-100">Create buyer account</button>
    </form>
    <p class="text-center mt-3 small">Already registered? <a href="<?= e(app_url('login.php')) ?>">Login</a></p>
</main>

<?php
$footerMode = 'full';
require __DIR__ . '/includes/footer.php';
