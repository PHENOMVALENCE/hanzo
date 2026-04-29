<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

$uid = auth_id();
$role = auth_role();
if ($uid === null || !in_array($role, ['buyer', 'factory', 'admin'], true)) {
    redirect('index.php');
}

/** @return bool */
function profile_email_taken(PDO $pdo, string $role, string $email, int $excludeId): bool
{
    $table = match ($role) {
        'buyer' => 'buyers',
        'factory' => 'factories',
        'admin' => 'admins',
        default => '',
    };
    if ($table === '') {
        return false;
    }
    $st = $pdo->prepare("SELECT id FROM `{$table}` WHERE email = ? AND id != ? LIMIT 1");
    $st->execute([$email, $excludeId]);
    return (bool) $st->fetch();
}

$errors = [];
$row = null;

if ($role === 'buyer') {
    $st = $pdo->prepare('SELECT * FROM buyers WHERE id = ? LIMIT 1');
    $st->execute([$uid]);
    $row = $st->fetch();
} elseif ($role === 'factory') {
    $st = $pdo->prepare('SELECT * FROM factories WHERE id = ? LIMIT 1');
    $st->execute([$uid]);
    $row = $st->fetch();
} else {
    $st = $pdo->prepare('SELECT * FROM admins WHERE id = ? LIMIT 1');
    $st->execute([$uid]);
    $row = $st->fetch();
}

if (!$row) {
    flash_set('error', 'Account not found.');
    redirect('logout.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPw = (string) ($_POST['current_password'] ?? '');
    $newPw = (string) ($_POST['new_password'] ?? '');
    $newPw2 = (string) ($_POST['new_password_confirm'] ?? '');
    $wantPwChange = $newPw !== '' || $newPw2 !== '' || $currentPw !== '';

    if ($wantPwChange) {
        if ($currentPw === '') {
            $errors[] = 'Enter your current password to set a new one.';
        } elseif (!password_verify($currentPw, (string) $row['password'])) {
            $errors[] = 'Current password is incorrect.';
        } elseif (strlen($newPw) < 8) {
            $errors[] = 'New password must be at least 8 characters.';
        } elseif ($newPw !== $newPw2) {
            $errors[] = 'New password confirmation does not match.';
        }
    }

    if ($role === 'buyer') {
        $fullName = trim((string) ($_POST['full_name'] ?? ''));
        $company = trim((string) ($_POST['company_name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $country = trim((string) ($_POST['country'] ?? ''));
        $city = trim((string) ($_POST['city'] ?? ''));

        if ($fullName === '' || strlen($fullName) > 180) {
            $errors[] = 'Full name is required (max 180 characters).';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email is required.';
        } elseif (strlen($email) > 180) {
            $errors[] = 'Email is too long.';
        } elseif (profile_email_taken($pdo, 'buyer', $email, $uid)) {
            $errors[] = 'Another account already uses this email.';
        }

        if ($errors === []) {
            $hash = (string) $row['password'];
            if ($wantPwChange && $currentPw !== '' && password_verify($currentPw, (string) $row['password'])) {
                $hash = password_hash($newPw, PASSWORD_DEFAULT);
            }
            $pdo->prepare('UPDATE buyers SET full_name = ?, company_name = ?, email = ?, phone = ?, country = ?, city = ?, password = ? WHERE id = ?')
                ->execute([
                    $fullName,
                    $company !== '' ? $company : null,
                    $email,
                    $phone !== '' ? $phone : null,
                    $country !== '' ? $country : null,
                    $city !== '' ? $city : null,
                    $hash,
                    $uid,
                ]);
            auth_refresh_user($pdo);
            flash_set('success', 'Your profile has been updated.');
            redirect('profile.php');
        }
    } elseif ($role === 'factory') {
        $factoryName = trim((string) ($_POST['factory_name'] ?? ''));
        $contact = trim((string) ($_POST['contact_person'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $city = trim((string) ($_POST['city'] ?? ''));
        $province = trim((string) ($_POST['province'] ?? ''));
        $mainProducts = trim((string) ($_POST['main_products'] ?? ''));
        $capacity = trim((string) ($_POST['production_capacity'] ?? ''));
        $export = trim((string) ($_POST['export_experience'] ?? ''));

        if ($factoryName === '' || strlen($factoryName) > 200) {
            $errors[] = 'Factory name is required (max 200 characters).';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email is required.';
        } elseif (strlen($email) > 180) {
            $errors[] = 'Email is too long.';
        } elseif (profile_email_taken($pdo, 'factory', $email, $uid)) {
            $errors[] = 'Another account already uses this email.';
        }
        if (strlen($contact) > 150) {
            $errors[] = 'Contact person name is too long.';
        }

        if ($errors === []) {
            $hash = (string) $row['password'];
            if ($wantPwChange && $currentPw !== '' && password_verify($currentPw, (string) $row['password'])) {
                $hash = password_hash($newPw, PASSWORD_DEFAULT);
            }
            $pdo->prepare('UPDATE factories SET factory_name = ?, contact_person = ?, email = ?, phone = ?, city = ?, province = ?, main_products = ?, production_capacity = ?, export_experience = ?, password = ? WHERE id = ?')
                ->execute([
                    $factoryName,
                    $contact !== '' ? $contact : null,
                    $email,
                    $phone !== '' ? $phone : null,
                    $city !== '' ? $city : null,
                    $province !== '' ? $province : null,
                    $mainProducts !== '' ? $mainProducts : null,
                    $capacity !== '' ? $capacity : null,
                    $export !== '' ? $export : null,
                    $hash,
                    $uid,
                ]);
            auth_refresh_user($pdo);
            flash_set('success', 'Your profile has been updated.');
            redirect('profile.php');
        }
    } else {
        $fullName = trim((string) ($_POST['full_name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));

        if ($fullName === '' || strlen($fullName) > 180) {
            $errors[] = 'Full name is required (max 180 characters).';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email is required.';
        } elseif (strlen($email) > 180) {
            $errors[] = 'Email is too long.';
        } elseif (profile_email_taken($pdo, 'admin', $email, $uid)) {
            $errors[] = 'Another account already uses this email.';
        }

        if ($errors === []) {
            $hash = (string) $row['password'];
            if ($wantPwChange && $currentPw !== '' && password_verify($currentPw, (string) $row['password'])) {
                $hash = password_hash($newPw, PASSWORD_DEFAULT);
            }
            $pdo->prepare('UPDATE admins SET full_name = ?, email = ?, password = ? WHERE id = ?')
                ->execute([$fullName, $email, $hash, $uid]);
            auth_refresh_user($pdo);
            flash_set('success', 'Your profile has been updated.');
            redirect('profile.php');
        }
    }
}

$pageTitle = 'My profile';

if ($role === 'admin') {
    $hideShopNav = true;
    require __DIR__ . '/includes/header.php';
    $adminActive = 'profile';
    $adminPageTitle = 'My profile';
    require __DIR__ . '/includes/admin_sidebar.php';
    ?>
<div class="admin-shell">
<div class="admin-content">
<?php require __DIR__ . '/includes/admin_topbar.php'; ?>
<main>
    <div class="admin-card p-4" style="max-width: 720px;">
        <h2 class="h5 mb-3">Account &amp; security</h2>
        <p class="text-muted small mb-4">Signed in as <strong><?= e((string) $row['email']) ?></strong> · Role: <strong><?= e((string) $row['role']) ?></strong> (role changes require a super administrator).</p>
        <?php foreach ($errors as $er): ?><div class="alert alert-danger"><?= e($er) ?></div><?php endforeach; ?>
        <?php if ($m = flash_get('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
        <form method="post" class="row g-3">
            <div class="col-12">
                <label class="form-label">Full name</label>
                <input type="text" name="full_name" class="form-control" maxlength="180" required value="<?= e((string) ($row['full_name'] ?? '')) ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" maxlength="180" required value="<?= e((string) ($row['email'] ?? '')) ?>">
            </div>
            <div class="col-12"><hr class="my-2"><h3 class="h6 mt-2">Change password</h3><p class="small text-muted mb-0">Leave new password blank to keep your current password.</p></div>
            <div class="col-md-12">
                <label class="form-label">Current password</label>
                <input type="password" name="current_password" class="form-control" autocomplete="current-password">
            </div>
            <div class="col-md-6">
                <label class="form-label">New password</label>
                <input type="password" name="new_password" class="form-control" minlength="8" autocomplete="new-password">
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirm new password</label>
                <input type="password" name="new_password_confirm" class="form-control" minlength="8" autocomplete="new-password">
            </div>
            <div class="col-12"><button type="submit" class="btn btn-hanzo-primary">Save changes</button></div>
        </form>
    </div>
</main>
</div>
</div>
    <?php
    $footerMode = 'slim';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$hideShopNav = false;
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';

if ($role === 'buyer') {
    require __DIR__ . '/includes/buyer_sidebar_start.php';
}
?>
<main class="<?= $role === 'buyer' ? 'hanzo-buyer-main-inner' : 'container py-4 mx-auto' ?>"<?= $role !== 'buyer' ? ' style="max-width:720px;"' : '' ?>>
    <header class="<?= $role === 'buyer' ? 'hanzo-buyer-page-head' : 'mb-4' ?>">
        <h1 class="<?= $role === 'buyer' ? 'hanzo-buyer-page-title' : 'h3' ?>">My profile</h1>
        <p class="text-muted small mb-0">Update your contact details and password. Your email is used for login and notifications.</p>
    </header>
    <?php foreach ($errors as $er): ?><div class="alert alert-danger"><?= e($er) ?></div><?php endforeach; ?>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>

    <div class="<?= $role === 'buyer' ? 'hanzo-buyer-form-card' : 'bg-white border rounded-3 shadow-sm' ?> p-4">
        <form method="post" class="row g-3">
            <?php if ($role === 'buyer'): ?>
                <div class="col-md-6">
                    <label class="form-label">Full name</label>
                    <input type="text" name="full_name" class="form-control" maxlength="180" required value="<?= e((string) ($row['full_name'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Company name</label>
                    <input type="text" name="company_name" class="form-control" maxlength="200" value="<?= e((string) ($row['company_name'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" maxlength="180" required value="<?= e((string) ($row['email'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" maxlength="50" value="<?= e((string) ($row['phone'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Country</label>
                    <input type="text" name="country" class="form-control" maxlength="80" value="<?= e((string) ($row['country'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" maxlength="80" value="<?= e((string) ($row['city'] ?? '')) ?>">
                </div>
            <?php else: ?>
                <div class="col-12">
                    <label class="form-label">Factory name</label>
                    <input type="text" name="factory_name" class="form-control" maxlength="200" required value="<?= e((string) ($row['factory_name'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact person</label>
                    <input type="text" name="contact_person" class="form-control" maxlength="150" value="<?= e((string) ($row['contact_person'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" maxlength="180" required value="<?= e((string) ($row['email'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" maxlength="50" value="<?= e((string) ($row['phone'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" maxlength="80" value="<?= e((string) ($row['city'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Province / region</label>
                    <input type="text" name="province" class="form-control" maxlength="80" value="<?= e((string) ($row['province'] ?? '')) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Main products</label>
                    <input type="text" name="main_products" class="form-control" maxlength="255" value="<?= e((string) ($row['main_products'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Production capacity</label>
                    <input type="text" name="production_capacity" class="form-control" maxlength="255" value="<?= e((string) ($row['production_capacity'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Export experience</label>
                    <input type="text" name="export_experience" class="form-control" maxlength="255" value="<?= e((string) ($row['export_experience'] ?? '')) ?>">
                </div>
            <?php endif; ?>

            <div class="col-12"><hr class="my-2"><h2 class="h6 mt-2">Change password</h2><p class="small text-muted mb-0">Leave new password fields blank to keep your current password.</p></div>
            <div class="col-12">
                <label class="form-label">Current password</label>
                <input type="password" name="current_password" class="form-control" autocomplete="current-password">
            </div>
            <div class="col-md-6">
                <label class="form-label">New password</label>
                <input type="password" name="new_password" class="form-control" minlength="8" autocomplete="new-password">
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirm new password</label>
                <input type="password" name="new_password_confirm" class="form-control" minlength="8" autocomplete="new-password">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-hanzo-primary">Save changes</button>
            </div>
        </form>
    </div>
</main>
<?php
if ($role === 'buyer') {
    require __DIR__ . '/includes/buyer_sidebar_end.php';
}
$footerMode = 'full';
require __DIR__ . '/includes/footer.php';
