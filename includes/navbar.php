<?php

declare(strict_types=1);

/** @var PDO $pdo */
$hideShopNav = $hideShopNav ?? false;

$navCategories = [];
if (!$hideShopNav) {
    $stmt = $pdo->query('SELECT id, name FROM categories WHERE status = "active" ORDER BY name');
    $navCategories = $stmt->fetchAll();
}
?>
<?php if (!$hideShopNav): ?>
<?php $hanzoGuestTop = !auth_user(); ?>
<div class="hanzo-topbar d-none d-lg-block">
    <div class="container-fluid px-3 px-sm-4">
        <div class="row align-items-center py-2 gx-2 gx-sm-3">
            <div class="col-lg-4 text-lg-start text-center">
                <a href="<?= e(app_url('inquiry.php')) ?>">Buyer support</a>
            </div>
            <div class="<?= $hanzoGuestTop ? 'col-lg-4' : 'col-lg-8' ?> text-center small hanzo-topbar-tagline">
                East Africa sourcing desk · USD quotes · Verified suppliers via HANZO
            </div>
            <?php if ($hanzoGuestTop): ?>
                <div class="col-lg-4 text-lg-end text-center hanzo-topbar-auth-guest">
                    <a href="<?= e(app_url('login.php')) ?>">Log in</a>
                    <span class="hanzo-topbar-sep" aria-hidden="true">·</span>
                    <a href="<?= e(app_url('register.php')) ?>">Register as buyer</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-sm-4 py-3 hanzo-shop-header-main bg-white border-bottom">
    <div class="row align-items-center g-3">
        <div class="col-12 col-lg-3 col-xl-3 text-center text-lg-start">
            <a href="<?= e(app_url('index.php')) ?>" class="navbar-brand hanzo-brand d-inline-block mb-0">
                HANZO<span>.</span>
            </a>
            <div class="small text-muted d-none d-lg-block">B2B sourcing for Tanzania &amp; East Africa</div>
        </div>
        <div class="col-12 col-lg-6 col-xl-6">
            <form class="hanzo-search-wrap d-flex align-items-stretch" action="<?= e(app_url('search.php')) ?>" method="get">
                <input type="text" name="q" class="form-control py-3 ps-3" placeholder="What are you sourcing today?" value="<?= e($_GET['q'] ?? '') ?>">
                <select name="cat" class="form-select py-3 hanzo-search-cat" aria-label="Category">
                    <option value="">All categories</option>
                    <?php foreach ($navCategories as $c): ?>
                        <option value="<?= (int) $c['id'] ?>" <?= isset($_GET['cat']) && (string) $_GET['cat'] === (string) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-hanzo-primary px-4" type="submit"><i class="fa fa-search" aria-hidden="true"></i><span class="d-none d-md-inline ms-1">Search</span></button>
            </form>
        </div>
        <div class="col-12 col-lg-3 col-xl-3 text-center text-lg-end d-flex flex-wrap align-items-center justify-content-center justify-content-lg-end gap-2 hanzo-shop-header-actions">
            <a class="btn btn-hanzo-outline btn-sm" href="<?= e(app_url('categories.php')) ?>"><i class="fa fa-th-large me-1" aria-hidden="true"></i><span class="d-none d-sm-inline">Categories</span><span class="d-sm-none">Browse</span></a>
            <?php if (auth_user()): ?>
                <?php hanzo_render_shop_account_dropdown('header'); ?>
            <?php else: ?>
                <a class="btn btn-hanzo-primary btn-sm d-lg-none" href="<?= e(app_url('login.php')) ?>">Log in</a>
                <a class="btn btn-hanzo-primary btn-sm" href="<?= e(app_url('register.php')) ?>">Join free</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<nav class="navbar navbar-expand-lg hanzo-navbar sticky-top shadow-sm">
    <div class="container-fluid px-3 px-sm-4">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#hanzoMainNav" aria-controls="hanzoMainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="hanzoMainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="<?= e(app_url('index.php')) ?>">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= e(app_url('categories.php')) ?>">Product directory</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Sectors</a>
                    <ul class="dropdown-menu shadow border-0">
                        <?php foreach (array_slice($navCategories, 0, 8) as $c): ?>
                            <li><a class="dropdown-item" href="<?= e(app_url('category.php?id=' . (int) $c['id'])) ?>"><?= e($c['name']) ?></a></li>
                        <?php endforeach; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item fw-semibold" href="<?= e(app_url('categories.php')) ?>">View all sectors</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="<?= e(app_url('search.php')) ?>">Search products</a></li>
            </ul>
            <?php if (auth_user()): ?>
                <ul class="navbar-nav ms-lg-3 mb-2 mb-lg-0 hanzo-nav-account-mobile">
                    <?php hanzo_render_shop_account_dropdown('collapse'); ?>
                </ul>
            <?php endif; ?>
            <div class="d-flex flex-wrap align-items-center gap-2 ms-lg-auto hanzo-nav-cta-wrap">
                <?php if (!auth_user()): ?>
                    <a class="btn btn-outline-secondary btn-sm d-lg-none" href="<?= e(app_url('register.php')) ?>">Join free</a>
                <?php endif; ?>
                <?php if (auth_user()): ?>
                    <?php if (auth_role() === 'buyer'): ?>
                        <a class="btn btn-hanzo-primary btn-sm" href="<?= e(app_url('buyer/dashboard.php')) ?>">My inquiries</a>
                    <?php elseif (auth_role() === 'factory'): ?>
                        <a class="btn btn-hanzo-primary btn-sm" href="<?= e(app_url('factory/dashboard.php')) ?>">Factory workspace</a>
                    <?php elseif (auth_role() === 'admin'): ?>
                        <a class="btn btn-hanzo-primary btn-sm" href="<?= e(app_url('admin/dashboard.php')) ?>">Admin panel</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<?php else: ?>

<nav class="navbar navbar-expand-lg bg-hanzo-navy navbar-dark">
    <div class="container-fluid px-3 px-sm-4">
        <a class="navbar-brand hanzo-brand text-white" href="<?= e(app_url('admin/index.php')) ?>">HANZO <span class="text-hanzo-gold">Admin</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= e(app_url('admin/dashboard.php')) ?>">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= e(app_url('admin/products.php')) ?>">Products</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= e(app_url('admin/buyers.php')) ?>">Buyers</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= e(app_url('admin/factories.php')) ?>">Factories</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= e(app_url('admin/categories.php')) ?>">Categories</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= e(app_url('admin/orders.php')) ?>">Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= e(app_url('admin/quotations.php')) ?>">Quotations</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= e(app_url('admin/payments.php')) ?>">Payments</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= e(app_url('admin/shipping.php')) ?>">Shipping</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= e(app_url('admin/documents.php')) ?>">Documents</a></li>
            </ul>
            <span class="navbar-text text-white-50 small me-lg-3 text-truncate d-inline-block hanzo-admin-nav-email"><?= e(auth_user()['email'] ?? '') ?></span>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <a class="btn btn-outline-light btn-sm" href="<?= e(app_url('index.php')) ?>">View site</a>
                <a class="btn btn-hanzo-primary btn-sm" href="<?= e(app_url('logout.php')) ?>">Logout</a>
            </div>
        </div>
    </div>
</nav>

<?php endif; ?>
