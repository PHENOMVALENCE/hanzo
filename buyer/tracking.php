<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_buyer();

$buyerId = auth_id();
$st = $pdo->prepare('SELECT su.*, o.order_code, p.product_name 
    FROM shipping_updates su 
    JOIN orders o ON o.id = su.order_id 
    JOIN products p ON p.id = o.product_id 
    WHERE o.buyer_id = ? 
    ORDER BY su.created_at DESC');
$st->execute([$buyerId]);
$updates = $st->fetchAll();

$pageTitle = 'Shipment Tracking';
require __DIR__ . '/../includes/header.php';
$hideShopNav = false;
require __DIR__ . '/../includes/navbar.php';
?>
<main class="container py-4">
    <h1 class="h3 mb-3">Shipping & Delivery Tracking</h1>
    <div class="table-responsive border rounded bg-white">
        <table class="table mb-0">
            <thead class="table-light"><tr><th>Order</th><th>Product</th><th>Status</th><th>Location</th><th>Tracking #</th><th>Update Time</th></tr></thead>
            <tbody>
                <?php foreach ($updates as $u): ?>
                    <tr>
                        <td><?= e($u['order_code']) ?></td>
                        <td><?= e($u['product_name']) ?></td>
                        <td><span class="badge bg-secondary"><?= e($u['status_title']) ?></span><br><small><?= e((string) $u['description']) ?></small></td>
                        <td><?= e((string) $u['location']) ?></td>
                        <td><?= e((string) $u['tracking_number']) ?></td>
                        <td class="small"><?= e($u['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($updates === []): ?><tr><td colspan="6" class="text-center text-muted py-3">No shipping updates yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php $footerMode = 'full'; require __DIR__ . '/../includes/footer.php'; ?>

