<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/order_finance.php';
require_buyer();

$buyerId = auth_id();
$st = $pdo->prepare('SELECT o.*, p.product_name,
    (SELECT q1.id FROM quotations q1 WHERE q1.order_id = o.id ORDER BY q1.id DESC LIMIT 1) AS quote_id,
    (SELECT q2.status FROM quotations q2 WHERE q2.order_id = o.id ORDER BY q2.id DESC LIMIT 1) AS quote_status
    FROM orders o
    JOIN products p ON p.id = o.product_id
    WHERE o.buyer_id = ?
    ORDER BY o.created_at DESC');
$st->execute([$buyerId]);
$orders = $st->fetchAll();

$orderIds = array_map(static fn (array $row): int => (int) $row['id'], $orders);
$balanceMap = hanzo_order_balance_map($pdo, $orderIds);

$pageTitle = 'My Orders';
require __DIR__ . '/../includes/header.php';
$hideShopNav = false;
require __DIR__ . '/../includes/navbar.php';
require __DIR__ . '/../includes/buyer_sidebar_start.php';
?>
<main class="hanzo-buyer-main-inner">
    <header class="hanzo-buyer-page-head">
        <h1 class="hanzo-buyer-page-title">My orders</h1>
        <p class="text-muted small mb-0">Order codes, agreed totals after you accept a quote, verified payments, and balance due (USD). TZS payments are converted at the same indicative rate as the site currency switcher.</p>
    </header>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
    <div class="table-responsive hanzo-buyer-table-wrap">
        <table class="table table-hover align-middle mb-0 hanzo-buyer-table">
            <thead><tr><th scope="col">Order</th><th scope="col">Product</th><th scope="col">Qty</th><th scope="col">Status</th><th scope="col">Agreed total</th><th scope="col">Paid (verified)</th><th scope="col">Balance due</th><th scope="col">Quote</th><th scope="col">Payment</th><th scope="col">Date</th></tr></thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <?php
                    $oid = (int) $o['id'];
                    $bal = $balanceMap[$oid] ?? ['agreed_usd' => null, 'paid_usd' => 0.0, 'due_usd' => null];
                    ?>
                    <tr id="order-<?= $oid ?>">
                        <td><?= e($o['order_code']) ?></td>
                        <td><?= e($o['product_name']) ?></td>
                        <td><?= (int) $o['quantity'] ?></td>
                        <?php $ost = (string) $o['status']; ?>
                        <td><span class="badge <?= e(order_status_badge_class($ost)) ?>"><?= e(order_status_label($ost)) ?></span></td>
                        <td class="text-nowrap small fw-semibold"><?= e(hanzo_format_order_money_usd($bal['agreed_usd'])) ?></td>
                        <td class="text-nowrap small"><?= e(hanzo_format_order_money_usd($bal['paid_usd'])) ?></td>
                        <td class="text-nowrap small fw-semibold text-hanzo-gold"><?= e(hanzo_format_order_money_usd($bal['due_usd'])) ?></td>
                        <td>
                            <?php if (!empty($o['quote_id'])): ?>
                                <a href="<?= e(app_url('buyer/quotations.php')) ?>" class="btn btn-sm btn-outline-primary"><?= e((string) $o['quote_status']) ?></a>
                            <?php else: ?>
                                <span class="text-muted small">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= e(app_url('buyer/payments.php?order_id=' . $oid)) ?>" class="btn btn-sm btn-outline-secondary">Pay</a>
                        </td>
                        <td class="small text-muted"><?= e(format_datetime((string) $o['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($orders === []): ?><tr><td colspan="10" class="text-center text-muted py-3">No orders yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require __DIR__ . '/../includes/buyer_sidebar_end.php'; ?>
<?php $footerMode = 'full'; require __DIR__ . '/../includes/footer.php'; ?>
