<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $status = (string) ($_POST['status'] ?? 'active');
    if ($id > 0 && in_array($status, ['active', 'pending', 'suspended'], true)) {
        $pdo->prepare('UPDATE buyers SET status=? WHERE id=?')->execute([$status, $id]);
        flash_set('success', 'Buyer status updated.');
    }
    redirect('admin/buyers.php');
}

$buyers = $pdo->query('SELECT * FROM buyers ORDER BY created_at DESC')->fetchAll();
$pageTitle = 'Admin Buyers';
require __DIR__ . '/../includes/header.php';
$adminActive = 'buyers';
$adminPageTitle = 'Buyers';
require __DIR__ . '/../includes/admin_sidebar.php';
?>
<div class="admin-shell"><div class="admin-content">
<?php require __DIR__ . '/../includes/admin_topbar.php'; ?>
<main>
    <h1 class="h3 mb-3">Buyers Management</h1>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
    <div class="admin-card p-3">
        <div class="admin-table-tools mb-2">
            <input type="text" class="form-control form-control-sm" placeholder="Search buyers..." data-admin-table-search="buyersTable">
            <select class="form-select form-select-sm" style="max-width: 170px;"><option>All statuses</option><option>active</option><option>pending</option><option>suspended</option></select>
        </div>
    <div class="table-responsive">
        <table class="table mb-0" id="buyersTable">
            <thead class="table-light"><tr><th data-sort>Name</th><th data-sort>Company</th><th data-sort>Email</th><th data-sort>Phone</th><th data-sort>Location</th><th data-sort>Status</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($buyers as $b): ?>
                    <tr>
                        <td><?= e($b['full_name']) ?></td>
                        <td><?= e((string) $b['company_name']) ?></td>
                        <td><?= e($b['email']) ?></td>
                        <td><?= e((string) $b['phone']) ?></td>
                        <td><?= e(trim(((string) $b['city']) . ', ' . ((string) $b['country']), ', ')) ?></td>
                        <td><span class="badge bg-secondary"><?= e($b['status']) ?></span></td>
                        <td>
                            <form method="post" class="d-flex gap-1">
                                <input type="hidden" name="id" value="<?= (int) $b['id'] ?>">
                                <select name="status" class="form-select form-select-sm">
                                    <?php foreach (['active','pending','suspended'] as $s): ?><option value="<?= $s ?>" <?= $b['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?>
                                </select>
                                <button class="btn btn-sm btn-hanzo-primary">Save</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    </div>
</main>
 </div></div>
<?php $footerMode = 'slim'; require __DIR__ . '/../includes/footer.php'; ?>


