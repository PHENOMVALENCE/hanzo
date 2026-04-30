<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_buyer();

$buyerId = auth_id();
$stats = ['orders' => 0, 'quoted' => 0, 'active' => 0];
$st = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE buyer_id = ?');
$st->execute([$buyerId]);
$stats['orders'] = (int) $st->fetchColumn();
$st = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE buyer_id = ? AND status = "quoted"');
$st->execute([$buyerId]);
$stats['quoted'] = (int) $st->fetchColumn();
$st = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE buyer_id = ? AND status IN ("in_production","quality_control","shipped","in_customs")');
$st->execute([$buyerId]);
$stats['active'] = (int) $st->fetchColumn();

$productNameCols = db_has_column($pdo, 'products', 'name_en') ? ', p.name_en, p.name_sw, p.name_zh' : '';
$orders = $pdo->prepare('SELECT o.*, p.product_name' . $productNameCols . ' FROM orders o JOIN products p ON p.id = o.product_id WHERE o.buyer_id = ? ORDER BY o.created_at DESC LIMIT 10');
$orders->execute([$buyerId]);
$recentOrders = $orders->fetchAll();

$pageTitle = __('buyer_dashboard');
require __DIR__ . '/../includes/header.php';
$hideShopNav = false;
require __DIR__ . '/../includes/navbar.php';

$buyerName = auth_user()['name'] ?? 'Buyer';
require __DIR__ . '/../includes/buyer_sidebar_start.php';
?>
<main class="hanzo-buyer-main-inner hanzo-buyer-dashboard">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="hanzo-section-title mb-2"><?= e(__('buyer_dashboard')) ?></h1>
            <p class="text-muted mb-0">Welcome back, <?= e($buyerName) ?>. Track orders, quotes, and shipments in one place.</p>
        </div>
        <a class="btn btn-outline-secondary btn-lg px-4" href="<?= e(app_url('buyer/orders.php')) ?>"><i class="fas fa-list-ul me-2" aria-hidden="true"></i>All orders</a>
    </div>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="hanzo-dash-stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <small class="text-muted text-uppercase fw-semibold letter-spacing-tight">Total orders</small>
                            <div class="hanzo-dash-stat-value"><?= $stats['orders'] ?></div>
                        </div>
                        <span class="hanzo-dash-stat-icon rounded-3"><i class="fas fa-shopping-basket"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="hanzo-dash-stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <small class="text-muted text-uppercase fw-semibold letter-spacing-tight">Awaiting your decision</small>
                            <div class="hanzo-dash-stat-value"><?= $stats['quoted'] ?></div>
                            <small class="text-muted">Quotes need a response</small>
                        </div>
                        <span class="hanzo-dash-stat-icon rounded-3"><i class="fas fa-file-invoice-dollar"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="hanzo-dash-stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <small class="text-muted text-uppercase fw-semibold letter-spacing-tight">In fulfillment</small>
                            <div class="hanzo-dash-stat-value"><?= $stats['active'] ?></div>
                            <small class="text-muted">Production through customs</small>
                        </div>
                        <span class="hanzo-dash-stat-icon rounded-3"><i class="fas fa-shipping-fast"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-2">
        <h2 class="h5 mb-0">Recent orders</h2>
        <a href="<?= e(app_url('buyer/orders.php')) ?>" class="small text-decoration-none">Open full list →</a>
    </div>
    <div class="table-responsive hanzo-buyer-table-wrap">
        <table class="table table-hover align-middle mb-0 hanzo-buyer-table">
            <thead><tr><th scope="col">Order</th><th scope="col">Product</th><th scope="col">Qty</th><th scope="col">Status</th><th scope="col">Date</th></tr></thead>
            <tbody>
                <?php foreach ($recentOrders as $o): ?>
                    <?php $stRaw = (string) $o['status']; ?>
                    <tr>
                        <td><a href="<?= e(app_url('buyer/orders.php')) ?>" class="fw-semibold link-dark text-decoration-none"><?= e($o['order_code']) ?></a></td>
                        <td><?= e(getLocalizedProductName($o)) ?></td>
                        <td><?= (int) $o['quantity'] ?></td>
                        <td><span class="badge <?= e(order_status_badge_class($stRaw)) ?>"><?= e(order_status_label($stRaw)) ?></span></td>
                        <td class="small text-muted"><?= e(format_datetime((string) $o['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($recentOrders === []): ?><tr><td colspan="5" class="text-center text-muted py-5">No orders yet. <a href="<?= e(app_url('buyer/products.php')) ?>">Browse products</a> to place your first inquiry.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require __DIR__ . '/../includes/buyer_sidebar_end.php'; ?>
<?php $footerMode = 'full'; require __DIR__ . '/../includes/footer.php'; ?>

