<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = __('featured_products');

$sqlBase = 'SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE p.status = "active" ';
$trending = $pdo->query($sqlBase . ' ORDER BY p.created_at DESC LIMIT 8')->fetchAll();
$hot = $pdo->query($sqlBase . ' ORDER BY p.id DESC LIMIT 8')->fetchAll();
$grid = $pdo->query($sqlBase . ' ORDER BY p.created_at DESC LIMIT 12')->fetchAll();

$catExtraCols = db_has_column($pdo, 'categories', 'name_en') ? ', name_en, name_sw, name_zh' : '';
$sidebarCats = $pdo->query('SELECT id, name' . $catExtraCols . ' FROM categories WHERE status = "active" ORDER BY name')->fetchAll();

require __DIR__ . '/includes/header.php';
$hideShopNav = false;
require __DIR__ . '/includes/navbar.php';
?>

<main class="container-fluid px-3 px-sm-4 py-4">
    <?php if ($m = flash_get('success')): ?>
        <div class="alert alert-success"><?= e($m) ?></div>
    <?php endif; ?>
    <?php if ($m = flash_get('error')): ?>
        <div class="alert alert-danger"><?= e($m) ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <aside class="col-lg-3 d-none d-lg-block">
            <div class="hanzo-sidebar p-0 mb-4">
                <div class="p-3 border-bottom fw-bold text-hanzo-gold bg-light"><?= e(__('categories')) ?></div>
                <div class="list-group list-group-flush">
                    <?php foreach ($sidebarCats as $sc): ?>
                        <a class="list-group-item list-group-item-action" href="<?= e(app_url('category.php?id=' . (int) $sc['id'])) ?>"><?= e(getLocalizedCategoryName($sc)) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="p-4 bg-white border rounded small text-muted">
                <strong class="d-block text-dark mb-2"><?= e(__('why_hanzo')) ?></strong>
                <?= e(__('why_hanzo_desc')) ?>
            </div>
        </aside>
        <div class="col-lg-9">
            <div class="hanzo-hero mb-4">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="display-6"><?= e(__('source_smarter')) ?></h1>
                        <p class="lead mb-4"><?= e(__('source_smarter_desc')) ?></p>
                        <p class="small text-light-emphasis mb-4"><?= e(__('hero_trust_line')) ?></p>
                        <a class="btn btn-hanzo-primary btn-lg me-2" href="<?= e(app_url('categories.php')) ?>"><?= e(__('explore_categories')) ?></a>
                        <a class="btn btn-outline-light btn-lg" href="<?= e(app_url('register.php')) ?>"><?= e(__('start_buying_now')) ?></a>
                    </div>
                    <div class="col-lg-4 d-none d-lg-block text-center">
                        <i class="fa fa-globe-africa fa-5x text-hanzo-gold opacity-75"></i>
                    </div>
                </div>
            </div>

            <h2 class="hanzo-section-title"><?= e(__('selected_trending')) ?></h2>
            <div class="row">
                <?php foreach ($trending as $p): ?>
                    <?php require __DIR__ . '/includes/product_card.php'; ?>
                <?php endforeach; ?>
            </div>

            <h2 class="hanzo-section-title mt-5"><?= e(__('hot_selling')) ?></h2>
            <div class="row">
                <?php foreach ($hot as $p): ?>
                    <?php require __DIR__ . '/includes/product_card.php'; ?>
                <?php endforeach; ?>
            </div>

            <h2 class="hanzo-section-title mt-5"><?= e(__('product_directory')) ?></h2>
            <div class="row">
                <?php foreach ($grid as $p): ?>
                    <?php require __DIR__ . '/includes/product_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>

<?php
$footerMode = 'full';
require __DIR__ . '/includes/footer.php';
