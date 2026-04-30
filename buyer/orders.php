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
require __DIR__ . '/../includes/buyer_sidebar_start.php';
?>
<main class="hanzo-buyer-main-inner">
    <header class="hanzo-buyer-page-head">
        <h1 class="hanzo-buyer-page-title">My orders</h1>
        <p class="text-muted small mb-0">Order codes, quote status, and payment shortcuts for your China Chapu requests.</p>
    </header>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
    <div class="table-responsive hanzo-buyer-table-wrap">
        <table class="table table-hover align-middle mb-0 hanzo-buyer-table">
            <thead><tr><th scope="col">Order</th><th scope="col">Product</th><th scope="col">Qty</th><th scope="col">Status</th><th scope="col">Quote</th><th scope="col">Payment</th><th scope="col">Date</th></tr></thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr id="order-<?= (int) $o['id'] ?>">
                        <td><?= e($o['order_code']) ?></td>
                        <td><?= e($o['product_name']) ?></td>
                        <td><?= (int) $o['quantity'] ?></td>
                        <?php $ost = (string) $o['status']; ?>
                        <td><span class="badge <?= e(order_status_badge_class($ost)) ?>"><?= e(order_status_label($ost)) ?></span></td>
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
                        <td class="small text-muted"><?= e(format_datetime((string) $o['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($orders === []): ?><tr><td colspan="7" class="text-center text-muted py-3">No orders yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require __DIR__ . '/../includes/buyer_sidebar_end.php'; ?>
<?php $footerMode = 'full'; require __DIR__ . '/../includes/footer.php'; ?>

