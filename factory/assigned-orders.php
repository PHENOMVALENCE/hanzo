<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_factory();
$factoryId = auth_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int) ($_POST['order_id'] ?? 0);
    $status = (string) ($_POST['status'] ?? '');
    $lead = trim((string) ($_POST['lead_time'] ?? ''));
    $allowed = ['assigned', 'in_production', 'quality_control', 'shipped', 'in_customs', 'delivered'];
    if ($orderId > 0 && in_array($status, $allowed, true)) {
        $pdo->prepare('UPDATE orders SET status=? WHERE id=? AND factory_id=?')->execute([$status, $orderId, $factoryId]);
        if ($lead !== '') {
            $pdo->prepare('INSERT INTO production_updates (order_id, factory_id, status_title, description) VALUES (?,?,?,?)')
                ->execute([$orderId, $factoryId, 'Lead time update', $lead]);
        }
        flash_set('success', 'Order status updated.');
    }
    redirect('factory/assigned-orders.php');
}

$st = $pdo->prepare('SELECT o.id, o.order_code, o.quantity, o.delivery_location, o.status, o.created_at, p.product_name
    FROM orders o 
    JOIN products p ON p.id = o.product_id 
    WHERE o.factory_id = ? 
    ORDER BY o.created_at DESC');
$st->execute([$factoryId]);
$orders = $st->fetchAll();

$pageTitle = 'Assigned Orders';
require __DIR__ . '/../includes/header.php';
$hideShopNav = false;
require __DIR__ . '/../includes/navbar.php';
require __DIR__ . '/../includes/factory_sidebar_start.php';
?>
<main class="hanzo-buyer-main-inner">
    <header class="hanzo-buyer-page-head">
        <h1 class="hanzo-buyer-page-title">Assigned orders</h1>
        <p class="text-muted small mb-0">Advance order status and add optional lead-time notes for HANZO operations.</p>
    </header>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success border-0 shadow-sm"><?= e($m) ?></div><?php endif; ?>
    <div class="table-responsive hanzo-buyer-table-wrap">
        <table class="table table-hover align-middle mb-0 hanzo-buyer-table">
            <thead><tr><th scope="col">Order</th><th scope="col">Product</th><th scope="col">Qty</th><th scope="col">Delivery</th><th scope="col">Status</th><th scope="col">Update</th></tr></thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <?php $ost = (string) $o['status']; ?>
                    <tr>
                        <td class="fw-semibold text-nowrap"><?= e($o['order_code']) ?></td>
                        <td><?= e($o['product_name']) ?></td>
                        <td><?= (int) $o['quantity'] ?></td>
                        <td class="small"><?= e((string) $o['delivery_location']) ?></td>
                        <td><span class="badge <?= e(order_status_badge_class($ost)) ?>"><?= e(order_status_label($ost)) ?></span></td>
                        <td>
                            <form method="post" class="d-flex gap-1">
                                <input type="hidden" name="order_id" value="<?= (int) $o['id'] ?>">
                                <select name="status" class="form-select form-select-sm">
                                    <?php foreach (['assigned','in_production','quality_control','shipped','in_customs','delivered'] as $s): ?>
                                        <option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="text" name="lead_time" class="form-control form-control-sm" placeholder="Lead time note">
                                <button class="btn btn-sm btn-hanzo-primary">Save</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($orders === []): ?><tr><td colspan="6" class="text-center text-muted py-3">No assigned orders.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require __DIR__ . '/../includes/factory_sidebar_end.php'; ?>
<?php $footerMode = 'full'; require __DIR__ . '/../includes/footer.php'; ?>

