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

$stats = [];
$st = $pdo->prepare('SELECT COUNT(*) FROM products WHERE factory_id = ?');
$st->execute([$factoryId]);
$stats['products'] = (int) $st->fetchColumn();
$st = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE factory_id = ?');
$st->execute([$factoryId]);
$stats['orders'] = (int) $st->fetchColumn();
$st = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE factory_id = ? AND status IN ("in_production","quality_control")');
$st->execute([$factoryId]);
$stats['in_production'] = (int) $st->fetchColumn();
$pageTitle = 'Factory Dashboard';
require __DIR__ . '/../includes/header.php';
$hideShopNav = false;
require __DIR__ . '/../includes/navbar.php';
?>
<main class="container py-4">
    <h1 class="h3 mb-3">Factory Dashboard</h1>
    <p class="text-muted"><?= e($factory['factory_name'] ?? 'Factory Account') ?> · HANZO verified partner</p>
    <div class="row g-3 mb-4">
        <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted">My Products</small><div class="display-6"><?= $stats['products'] ?></div></div></div></div>
        <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted">Assigned Orders</small><div class="display-6"><?= $stats['orders'] ?></div></div></div></div>
        <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted">In Production</small><div class="display-6"><?= $stats['in_production'] ?></div></div></div></div>
    </div>
    <div class="mb-3 d-flex gap-2">
        <a class="btn btn-hanzo-primary" href="<?= e(app_url('factory/products.php')) ?>">Manage Products</a>
        <a class="btn btn-outline-secondary" href="<?= e(app_url('factory/assigned-orders.php')) ?>">Assigned Orders</a>
        <a class="btn btn-outline-secondary" href="<?= e(app_url('factory/production-updates.php')) ?>">Production Updates</a>
    </div>
    <p class="small text-muted">Buyer identities and contact details are hidden in factory workspace. HANZO handles communication and commercial approvals.</p>
</main>
<?php $footerMode = 'full'; require __DIR__ . '/../includes/footer.php'; ?>

