<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_buyer();

$pageTitle = 'Quick Request Cart';
require __DIR__ . '/../includes/header.php';
$hideShopNav = false;
require __DIR__ . '/../includes/navbar.php';
require __DIR__ . '/../includes/buyer_sidebar_start.php';
?>
<main class="hanzo-buyer-main-inner">
    <h1 class="h3 mb-3">Quick Request Cart</h1>
    <div class="alert alert-info">
        China Chapu uses RFQ-style sourcing. Instead of direct checkout, open a product and submit request details.
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-hanzo-primary" href="<?= e(app_url('buyer/products.php')) ?>">Browse products</a>
        <a class="btn btn-outline-secondary" href="<?= e(app_url('buyer/orders.php')) ?>">My orders</a>
    </div>
</main>
<?php require __DIR__ . '/../includes/buyer_sidebar_end.php'; ?>
<?php $footerMode = 'full'; require __DIR__ . '/../includes/footer.php'; ?>

