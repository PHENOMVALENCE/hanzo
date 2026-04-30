<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_factory();

$factoryId = auth_id();
$st = $pdo->prepare('SELECT * FROM factories WHERE id = ?');
$st->execute([$factoryId]);
$factory = $st->fetch();
if (!$factory) {
    flash_set('error', 'Factory profile not found.');
    redirect('logout.php');
}

$stats = [
    'products_active' => 0,
    'products_draft' => 0,
    'orders_assigned' => 0,
    'pipeline' => 0,
];
$st = $pdo->prepare('SELECT COUNT(*) FROM products WHERE factory_id = ? AND status = "active"');
$st->execute([$factoryId]);
$stats['products_active'] = (int) $st->fetchColumn();
$st = $pdo->prepare('SELECT COUNT(*) FROM products WHERE factory_id = ? AND status = "draft"');
$st->execute([$factoryId]);
$stats['products_draft'] = (int) $st->fetchColumn();
$st = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE factory_id = ? AND status = "assigned"');
$st->execute([$factoryId]);
$stats['orders_assigned'] = (int) $st->fetchColumn();
$st = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE factory_id = ? AND status IN ("in_production","quality_control")');
$st->execute([$factoryId]);
$stats['pipeline'] = (int) $st->fetchColumn();

$productNameCols = db_has_column($pdo, 'products', 'name_en') ? ', p.name_en, p.name_sw, p.name_zh' : '';
$recentSt = $pdo->prepare('SELECT o.order_code, o.status, o.created_at, o.quantity, p.product_name' . $productNameCols . '
    FROM orders o
    JOIN products p ON p.id = o.product_id
    WHERE o.factory_id = ?
    ORDER BY o.created_at DESC
    LIMIT 8');
$recentSt->execute([$factoryId]);
$recentOrders = $recentSt->fetchAll();

$pageTitle = __('factory_dashboard');
require __DIR__ . '/../includes/header.php';
$hideShopNav = false;
require __DIR__ . '/../includes/navbar.php';
require __DIR__ . '/../includes/factory_sidebar_start.php';
?>
<main class="hanzo-buyer-main-inner hanzo-factory-dashboard">
    <div class="hanzo-factory-hero">
        <div class="hanzo-factory-hero-badge">HANZO verified partner</div>
        <h1 class="hanzo-factory-hero-title"><?= e((string) ($factory['factory_name'] ?? 'Factory')) ?></h1>
        <p class="hanzo-factory-hero-meta mb-0">
            <?php
            $loc = array_filter([(string) ($factory['city'] ?? ''), (string) ($factory['province'] ?? '')]);
            if ($loc !== []) {
                echo e(implode(', ', $loc)) . ' · ';
            }
            ?>
            Manage catalog, fulfil assigned orders, and post production milestones through HANZO.
        </p>
    </div>

    <?php if ($m = flash_get('success')): ?><div class="alert alert-success border-0 shadow-sm"><?= e($m) ?></div><?php endif; ?>
    <?php if ($m = flash_get('error')): ?><div class="alert alert-danger border-0 shadow-sm"><?= e($m) ?></div><?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="hanzo-dash-stat-card hanzo-factory-stat-card card border-0 shadow-sm h-100 is-accent">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <small class="text-muted text-uppercase fw-semibold letter-spacing-tight">Active SKUs</small>
                            <div class="hanzo-dash-stat-value"><?= $stats['products_active'] ?></div>
                            <small class="text-muted">Live on marketplace</small>
                        </div>
                        <span class="hanzo-dash-stat-icon hanzo-factory-stat-icon rounded-3"><i class="fas fa-check-circle"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="hanzo-dash-stat-card hanzo-factory-stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <small class="text-muted text-uppercase fw-semibold letter-spacing-tight">Draft SKUs</small>
                            <div class="hanzo-dash-stat-value"><?= $stats['products_draft'] ?></div>
                            <small class="text-muted">Finish &amp; publish</small>
                        </div>
                        <span class="hanzo-dash-stat-icon hanzo-factory-stat-icon rounded-3"><i class="fas fa-edit"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="hanzo-dash-stat-card hanzo-factory-stat-card card border-0 shadow-sm h-100 is-accent">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <small class="text-muted text-uppercase fw-semibold letter-spacing-tight">Awaiting start</small>
                            <div class="hanzo-dash-stat-value"><?= $stats['orders_assigned'] ?></div>
                            <small class="text-muted">Orders in “assigned”</small>
                        </div>
                        <span class="hanzo-dash-stat-icon hanzo-factory-stat-icon rounded-3"><i class="fas fa-hourglass-half"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="hanzo-dash-stat-card hanzo-factory-stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <small class="text-muted text-uppercase fw-semibold letter-spacing-tight">Production &amp; QC</small>
                            <div class="hanzo-dash-stat-value"><?= $stats['pipeline'] ?></div>
                            <small class="text-muted">In factory &amp; QC stages</small>
                        </div>
                        <span class="hanzo-dash-stat-icon hanzo-factory-stat-icon rounded-3"><i class="fas fa-cogs"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-7">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-2">
                <h2 class="h5 mb-0">Recent assigned orders</h2>
                <a href="<?= e(app_url('factory/assigned-orders.php')) ?>" class="small text-decoration-none">Open all →</a>
            </div>
            <div class="table-responsive hanzo-buyer-table-wrap">
                <table class="table table-hover align-middle mb-0 hanzo-buyer-table">
                    <thead>
                        <tr>
                            <th scope="col">Order</th>
                            <th scope="col">Product</th>
                            <th scope="col">Qty</th>
                            <th scope="col">Status</th>
                            <th scope="col">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $o): ?>
                            <?php $ost = (string) $o['status']; ?>
                            <tr>
                                <td class="fw-semibold text-nowrap"><?= e($o['order_code']) ?></td>
                                <td><?= e(getLocalizedProductName($o)) ?></td>
                                <td><?= (int) $o['quantity'] ?></td>
                                <td><span class="badge <?= e(order_status_badge_class($ost)) ?>"><?= e(order_status_label($ost)) ?></span></td>
                                <td class="small text-muted text-nowrap"><?= e(format_datetime((string) $o['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if ($recentOrders === []): ?>
                            <tr><td colspan="5" class="text-center text-muted py-5">No orders assigned yet. HANZO will route RFQs to you when matched.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="hanzo-factory-callout p-4 mb-3">
                <h2 class="h6 text-uppercase text-muted fw-bold letter-spacing-tight mb-3">Workspace</h2>
                <div class="d-grid gap-2">
                    <a class="btn btn-hanzo-primary" href="<?= e(app_url('factory/products.php')) ?>"><i class="fas fa-boxes me-2"></i>Manage products</a>
                    <a class="btn btn-outline-secondary" href="<?= e(app_url('factory/assigned-orders.php')) ?>"><i class="fas fa-clipboard-list me-2"></i>Assigned orders</a>
                    <a class="btn btn-outline-secondary" href="<?= e(app_url('factory/production-updates.php')) ?>"><i class="fas fa-industry me-2"></i>Production updates</a>
                    <a class="btn btn-outline-secondary" href="<?= e(app_url('profile.php')) ?>"><i class="fas fa-user-cog me-2"></i>My profile</a>
                </div>
            </div>
            <div class="alert alert-light border small mb-0" style="border-color: var(--hanzo-border) !important;">
                <strong class="d-block mb-1 text-hanzo-gold">Privacy &amp; workflow</strong>
                Buyer identities and direct contact details stay with HANZO. Update order status and production notes here so East African buyers receive accurate milestones through the platform.
            </div>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../includes/factory_sidebar_end.php'; ?>
<?php $footerMode = 'full'; require __DIR__ . '/../includes/footer.php'; ?>
