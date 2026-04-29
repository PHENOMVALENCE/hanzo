<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_buyer();

$buyerId = auth_id();
$stats = [
    'orders' => (int) $pdo->prepare('SELECT COUNT(*) FROM orders WHERE buyer_id = ?'),
    'quoted' => 0,
    'active' => 0,
];
$st = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE buyer_id = ?');
$st->execute([$buyerId]);
$stats['orders'] = (int) $st->fetchColumn();
$st = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE buyer_id = ? AND status IN ("quoted","accepted")');
$st->execute([$buyerId]);
$stats['quoted'] = (int) $st->fetchColumn();
$st = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE buyer_id = ? AND status IN ("in_production","quality_control","shipped","in_customs")');
$st->execute([$buyerId]);
$stats['active'] = (int) $st->fetchColumn();

$orders = $pdo->prepare('SELECT o.*, p.product_name FROM orders o JOIN products p ON p.id = o.product_id WHERE o.buyer_id = ? ORDER BY o.created_at DESC LIMIT 10');
$orders->execute([$buyerId]);
$recentOrders = $orders->fetchAll();

$pageTitle = 'Buyer Dashboard';
require __DIR__ . '/../includes/header.php';
$hideShopNav = false;
require __DIR__ . '/../includes/navbar.php';
?>
<main class="container py-4">
    <h1 class="h3 mb-3">Buyer Dashboard</h1>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
    <div class="row g-3 mb-4">
        <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted">Total Requests/Orders</small><div class="display-6"><?= $stats['orders'] ?></div></div></div></div>
        <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted">Awaiting Quote Decision</small><div class="display-6"><?= $stats['quoted'] ?></div></div></div></div>
        <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted">In Fulfillment</small><div class="display-6"><?= $stats['active'] ?></div></div></div></div>
    </div>
    <div class="mb-3 d-flex gap-2">
        <a class="btn btn-hanzo-primary" href="<?= e(app_url('buyer/products.php')) ?>">Browse Products</a>
        <a class="btn btn-outline-secondary" href="<?= e(app_url('buyer/quotations.php')) ?>">My Quotations</a>
        <a class="btn btn-outline-secondary" href="<?= e(app_url('buyer/tracking.php')) ?>">Shipment Tracking</a>
        <a class="btn btn-outline-secondary" href="<?= e(app_url('buyer/payments.php')) ?>">Payments</a>
        <a class="btn btn-outline-secondary" href="<?= e(app_url('buyer/documents.php')) ?>">Documents</a>
    </div>
    <div class="table-responsive border rounded bg-white">
        <table class="table mb-0">
            <thead class="table-light"><tr><th>Order</th><th>Product</th><th>Qty</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
                <?php foreach ($recentOrders as $o): ?>
                    <tr>
                        <td><?= e($o['order_code']) ?></td>
                        <td><?= e($o['product_name']) ?></td>
                        <td><?= (int) $o['quantity'] ?></td>
                        <td><span class="badge bg-secondary"><?= e($o['status']) ?></span></td>
                        <td class="small"><?= e($o['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($recentOrders === []): ?><tr><td colspan="5" class="text-center text-muted py-3">No orders yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php $footerMode = 'full'; require __DIR__ . '/../includes/footer.php'; ?>

