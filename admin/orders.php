<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $factoryId = (int) ($_POST['factory_id'] ?? 0);
    $status = (string) ($_POST['status'] ?? 'pending');
    $pdo->prepare('UPDATE orders SET factory_id=?, status=? WHERE id=?')->execute([$factoryId > 0 ? $factoryId : null, $status, $id]);
    flash_set('success', 'Order updated.');
    redirect('admin/orders.php');
}

$factories = $pdo->query('SELECT id, factory_name FROM factories WHERE status="active" ORDER BY factory_name')->fetchAll();
$orders = $pdo->query('SELECT o.*, b.full_name AS buyer_name, p.product_name, f.factory_name 
    FROM orders o 
    JOIN buyers b ON b.id = o.buyer_id 
    JOIN products p ON p.id = o.product_id 
    LEFT JOIN factories f ON f.id = o.factory_id
    ORDER BY o.created_at DESC')->fetchAll();

$pageTitle = 'Admin — Orders';
require __DIR__ . '/../includes/header.php';
$adminActive = 'orders';
$adminPageTitle = 'Orders';
require __DIR__ . '/../includes/admin_sidebar.php';
?>
<div class="admin-shell"><div class="admin-content">
<?php require __DIR__ . '/../includes/admin_topbar.php'; ?>
<main>
    <h1 class="h3 mb-3">Orders</h1>
    <p class="text-muted mb-3">Track confirmed orders flowing from accepted HANZO quotations. Future milestones (production, shipment, delivery, payment) can be layered on top of this core view.</p>

    <div class="admin-card p-3">
        <div class="admin-table-tools mb-2">
            <input type="text" class="form-control form-control-sm" placeholder="Search orders..." data-admin-table-search="ordersTable">
            <select class="form-select form-select-sm" style="max-width:170px;"><option>All statuses</option><option>pending</option><option>assigned</option><option>in_production</option><option>delivered</option></select>
        </div>
        <div class="table-responsive">
        <table class="table table-sm mb-0 align-middle" id="ordersTable">
            <thead class="table-light">
                <tr>
                    <th data-sort>Order #</th>
                    <th data-sort>Buyer</th>
                    <th data-sort>Product</th>
                    <th data-sort>Qty</th>
                    <th data-sort>Price range</th>
                    <th data-sort>Status</th>
                    <th>Assign / Update</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><?= (int) $o['id'] ?></td>
                        <td><?= e($o['buyer_name']) ?></td>
                        <td><?= e($o['product_name']) ?></td>
                        <td><?= (int) $o['quantity'] ?></td>
                        <td><?= e((string) $o['price_range']) ?></td>
                        <td><span class="badge bg-secondary"><?= e($o['status']) ?></span></td>
                        <td>
                            <form method="post" class="d-flex gap-1">
                                <input type="hidden" name="id" value="<?= (int) $o['id'] ?>">
                                <select class="form-select form-select-sm" name="factory_id" style="min-width:170px;">
                                    <option value="0">Unassigned</option>
                                    <?php foreach ($factories as $f): ?>
                                        <option value="<?= (int) $f['id'] ?>" <?= (int) $o['factory_id'] === (int) $f['id'] ? 'selected' : '' ?>><?= e($f['factory_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="status" class="form-select form-select-sm">
                                    <?php foreach (['pending','assigned','quoted','accepted','in_production','quality_control','shipped','in_customs','delivered','cancelled'] as $s): ?>
                                        <option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-sm btn-hanzo-primary">Save</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($orders === []): ?>
                    <tr><td colspan="7" class="text-muted small text-center py-3">No orders recorded yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</main>
 </div></div>

<?php
$footerMode = 'slim';
require __DIR__ . '/../includes/footer.php';


