<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $factory = trim((string) ($_POST['factory_name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $pass = (string) ($_POST['password'] ?? '');
        $status = (string) ($_POST['status'] ?? 'invited');
        if ($id > 0) {
            $pdo->prepare('UPDATE factories SET factory_name=?, contact_person=?, email=?, phone=?, city=?, province=?, main_products=?, production_capacity=?, export_experience=?, status=? WHERE id=?')
                ->execute([$factory, $_POST['contact_person'] ?? null, $email, $_POST['phone'] ?? null, $_POST['city'] ?? null, $_POST['province'] ?? null, $_POST['main_products'] ?? null, $_POST['production_capacity'] ?? null, $_POST['export_experience'] ?? null, $status, $id]);
        } else {
            if ($pass === '') {
                $pass = 'Admin@123';
            }
            $pdo->prepare('INSERT INTO factories (factory_name, contact_person, email, phone, city, province, main_products, production_capacity, export_experience, password, status, invited_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)')
                ->execute([$factory, $_POST['contact_person'] ?? null, $email, $_POST['phone'] ?? null, $_POST['city'] ?? null, $_POST['province'] ?? null, $_POST['main_products'] ?? null, $_POST['production_capacity'] ?? null, $_POST['export_experience'] ?? null, password_hash($pass, PASSWORD_DEFAULT), $status, auth_id()]);
        }
        flash_set('success', 'Factory account saved.');
    }
    redirect('admin/factories.php');
}

$edit = null;
if (isset($_GET['edit'])) {
    $st = $pdo->prepare('SELECT * FROM factories WHERE id=?');
    $st->execute([(int) $_GET['edit']]);
    $edit = $st->fetch();
}
$factories = $pdo->query('SELECT * FROM factories ORDER BY created_at DESC')->fetchAll();
$pageTitle = 'Admin Factories';
require __DIR__ . '/../includes/header.php';
$adminActive = 'factories';
$adminPageTitle = 'Factories';
require __DIR__ . '/../includes/admin_sidebar.php';
?>
<div class="admin-shell"><div class="admin-content">
<?php require __DIR__ . '/../includes/admin_topbar.php'; ?>
<main>
    <h1 class="h3 mb-3">Factories (Invite Only)</h1>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
    <form method="post" class="row g-2 bg-white border rounded p-3 mb-3">
        <input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
        <div class="col-md-3"><input class="form-control" name="factory_name" required placeholder="Factory name" value="<?= e((string) ($edit['factory_name'] ?? '')) ?>"></div>
        <div class="col-md-2"><input class="form-control" name="contact_person" placeholder="Contact person" value="<?= e((string) ($edit['contact_person'] ?? '')) ?>"></div>
        <div class="col-md-3"><input class="form-control" type="email" name="email" required placeholder="Email" value="<?= e((string) ($edit['email'] ?? '')) ?>"></div>
        <div class="col-md-2"><input class="form-control" name="phone" placeholder="Phone" value="<?= e((string) ($edit['phone'] ?? '')) ?>"></div>
        <div class="col-md-2"><select class="form-select" name="status"><?php foreach (['invited','active','suspended'] as $s): ?><option value="<?= $s ?>" <?= (($edit['status'] ?? 'invited') === $s) ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><input class="form-control" name="city" placeholder="City" value="<?= e((string) ($edit['city'] ?? '')) ?>"></div>
        <div class="col-md-2"><input class="form-control" name="province" placeholder="Province" value="<?= e((string) ($edit['province'] ?? '')) ?>"></div>
        <div class="col-md-3"><input class="form-control" name="main_products" placeholder="Main products" value="<?= e((string) ($edit['main_products'] ?? '')) ?>"></div>
        <div class="col-md-2"><input class="form-control" name="production_capacity" placeholder="Capacity" value="<?= e((string) ($edit['production_capacity'] ?? '')) ?>"></div>
        <div class="col-md-2"><input class="form-control" name="export_experience" placeholder="Export experience" value="<?= e((string) ($edit['export_experience'] ?? '')) ?>"></div>
        <div class="col-md-1"><input class="form-control" name="password" type="password" placeholder="Pass"></div>
        <div class="col-md-12"><button class="btn btn-hanzo-primary">Save Factory</button></div>
    </form>
    <div class="admin-card p-3">
        <input type="text" class="form-control form-control-sm mb-2" placeholder="Search factories..." data-admin-table-search="factoriesTable">
        <div class="table-responsive">
        <table class="table mb-0" id="factoriesTable">
            <thead class="table-light"><tr><th data-sort>Factory</th><th data-sort>Email</th><th data-sort>Main products</th><th data-sort>Status</th><th>Action</th></tr></thead>
            <tbody><?php foreach ($factories as $f): ?><tr><td><?= e($f['factory_name']) ?></td><td><?= e($f['email']) ?></td><td><?= e((string) $f['main_products']) ?></td><td><?= e($f['status']) ?></td><td><a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('admin/factories.php?edit=' . (int) $f['id'])) ?>">Edit</a></td></tr><?php endforeach; ?></tbody>
        </table>
        </div>
    </div>
</main>
 </div></div>
<?php $footerMode = 'slim'; require __DIR__ . '/../includes/footer.php'; ?>


