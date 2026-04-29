<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int) ($_POST['order_id'] ?? 0);
    $title = trim((string) ($_POST['status_title'] ?? 'Shipping Update'));
    $desc = trim((string) ($_POST['description'] ?? ''));
    $loc = trim((string) ($_POST['location'] ?? ''));
    $tn = trim((string) ($_POST['tracking_number'] ?? ''));
    if ($orderId > 0) {
        $pdo->prepare('INSERT INTO shipping_updates (order_id, status_title, description, location, tracking_number, updated_by) VALUES (?,?,?,?,?,?)')
            ->execute([$orderId, $title, $desc !== '' ? $desc : null, $loc !== '' ? $loc : null, $tn !== '' ? $tn : null, auth_id()]);
        if (!empty($_POST['order_status'])) {
            $pdo->prepare('UPDATE orders SET status=? WHERE id=?')->execute([(string) $_POST['order_status'], $orderId]);
        }
        flash_set('success', 'Shipping update saved.');
    }
    redirect('admin/shipping.php');
}

$orders = $pdo->query('SELECT o.id, o.order_code, o.status, b.full_name buyer_name, p.product_name FROM orders o JOIN buyers b ON b.id=o.buyer_id JOIN products p ON p.id=o.product_id ORDER BY o.created_at DESC')->fetchAll();
$updates = $pdo->query('SELECT su.*, o.order_code FROM shipping_updates su JOIN orders o ON o.id=su.order_id ORDER BY su.created_at DESC')->fetchAll();
$pageTitle = 'Admin Shipping';
require __DIR__ . '/../includes/header.php';
$adminActive = 'shipping';
$adminPageTitle = 'Shipping';
require __DIR__ . '/../includes/admin_sidebar.php';
?>
<div class="admin-shell"><div class="admin-content">
<?php require __DIR__ . '/../includes/admin_topbar.php'; ?>
<main>
    <h1 class="h3 mb-3">Shipping & Delivery Updates</h1>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
    <form method="post" class="row g-2 bg-white border rounded p-3 mb-3">
        <div class="col-md-4"><select name="order_id" class="form-select" required><option value="">Select order</option><?php foreach ($orders as $o): ?><option value="<?= (int) $o['id'] ?>"><?= e($o['order_code'] . ' | ' . $o['buyer_name'] . ' | ' . $o['product_name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><input name="status_title" class="form-control" placeholder="Status title"></div>
        <div class="col-md-2"><input name="location" class="form-control" placeholder="Location"></div>
        <div class="col-md-2"><input name="tracking_number" class="form-control" placeholder="Tracking #"></div>
        <div class="col-md-2"><select name="order_status" class="form-select"><option value="">Keep order status</option><?php foreach (['shipped','in_customs','delivered'] as $s): ?><option value="<?= $s ?>"><?= e($s) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-10"><input name="description" class="form-control" placeholder="Description"></div>
        <div class="col-md-2"><button class="btn btn-hanzo-primary w-100">Add Update</button></div>
    </form>
    <div class="admin-card p-3">
        <input type="text" class="form-control form-control-sm mb-2" placeholder="Search updates..." data-admin-table-search="shippingTable">
        <div class="table-responsive">
        <table class="table mb-0" id="shippingTable">
            <thead class="table-light"><tr><th data-sort>Order</th><th data-sort>Status</th><th>Description</th><th data-sort>Location</th><th data-sort>Tracking</th><th data-sort>Time</th></tr></thead>
            <tbody>
                <?php foreach ($updates as $u): ?>
                    <tr><td><?= e($u['order_code']) ?></td><td><?= e($u['status_title']) ?></td><td><?= e((string) $u['description']) ?></td><td><?= e((string) $u['location']) ?></td><td><?= e((string) $u['tracking_number']) ?></td><td class="small"><?= e($u['created_at']) ?></td></tr>
                <?php endforeach; ?>
                <?php if ($updates === []): ?><tr><td colspan="6" class="text-center text-muted py-3">No shipping updates.</td></tr><?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</main>
 </div></div>
<?php $footerMode = 'slim'; require __DIR__ . '/../includes/footer.php'; ?>


