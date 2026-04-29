<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_buyer();

$buyerId = auth_id();
$st = $pdo->prepare('SELECT o.*, p.product_name, q.id AS quote_id, q.status AS quote_status
    FROM orders o 
    JOIN products p ON p.id = o.product_id 
    LEFT JOIN quotations q ON q.order_id = o.id
    WHERE o.buyer_id = ? 
    ORDER BY o.created_at DESC');
$st->execute([$buyerId]);
$orders = $st->fetchAll();

$pageTitle = 'My Orders';
require __DIR__ . '/../includes/header.php';
$hideShopNav = false;
require __DIR__ . '/../includes/navbar.php';
?>
<main class="container py-4">
    <h1 class="h3 mb-3">My Orders</h1>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
    <div class="table-responsive border rounded bg-white">
        <table class="table mb-0">
            <thead class="table-light"><tr><th>Order</th><th>Product</th><th>Qty</th><th>Status</th><th>Quote</th><th>Payment</th><th>Date</th></tr></thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><?= e($o['order_code']) ?></td>
                        <td><?= e($o['product_name']) ?></td>
                        <td><?= (int) $o['quantity'] ?></td>
                        <td><span class="badge bg-secondary"><?= e($o['status']) ?></span></td>
                        <td>
                            <?php if (!empty($o['quote_id'])): ?>
                                <a href="<?= e(app_url('buyer/quotations.php')) ?>" class="btn btn-sm btn-outline-primary"><?= e((string) $o['quote_status']) ?></a>
                            <?php else: ?>
                                <span class="text-muted small">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= e(app_url('buyer/payments.php?order_id=' . (int) $o['id'])) ?>" class="btn btn-sm btn-outline-secondary">Pay</a>
                        </td>
                        <td class="small"><?= e($o['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($orders === []): ?><tr><td colspan="7" class="text-center text-muted py-3">No orders yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php $footerMode = 'full'; require __DIR__ . '/../includes/footer.php'; ?>

