<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_buyer();

$buyerId = auth_id();
$productNameCols = db_has_column($pdo, 'products', 'name_en') ? ', p.name_en, p.name_sw, p.name_zh' : '';
$st = $pdo->prepare('SELECT su.*, o.order_code, p.product_name' . $productNameCols . ' 
    FROM shipping_updates su 
    JOIN orders o ON o.id = su.order_id 
    JOIN products p ON p.id = o.product_id 
    WHERE o.buyer_id = ? 
    ORDER BY su.created_at DESC');
$st->execute([$buyerId]);
$updates = $st->fetchAll();

$pageTitle = __('track_order');
require __DIR__ . '/../includes/header.php';
$hideShopNav = false;
require __DIR__ . '/../includes/navbar.php';
require __DIR__ . '/../includes/buyer_sidebar_start.php';
?>
<main class="hanzo-buyer-main-inner">
    <h1 class="h3 mb-3">Shipping & Delivery Tracking</h1>
    <div class="table-responsive hanzo-buyer-table-wrap">
        <table class="table table-hover align-middle mb-0 hanzo-buyer-table">
            <thead><tr><th scope="col">Order</th><th scope="col">Product</th><th scope="col">Status</th><th scope="col">Location</th><th scope="col">Tracking #</th><th scope="col">Update time</th></tr></thead>
            <tbody>
                <?php foreach ($updates as $u): ?>
                    <tr>
                        <td><?= e($u['order_code']) ?></td>
                        <td><?= e(getLocalizedProductName($u)) ?></td>
                        <td><span class="badge bg-secondary"><?= e($u['status_title']) ?></span><br><small><?= e((string) $u['description']) ?></small></td>
                        <td><?= e((string) $u['location']) ?></td>
                        <td><?= e((string) $u['tracking_number']) ?></td>
                        <td class="small text-muted"><?= e(format_datetime((string) $u['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($updates === []): ?><tr><td colspan="6" class="text-center text-muted py-3">No shipping updates yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require __DIR__ . '/../includes/buyer_sidebar_end.php'; ?>
<?php $footerMode = 'full'; require __DIR__ . '/../includes/footer.php'; ?>

