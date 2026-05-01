<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$st = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
$st->execute([$id]);
$category = $st->fetch();
if (!$category) {
    http_response_code(404);
    $pageTitle = __('category_not_found');
    require __DIR__ . '/includes/header.php';
    $hideShopNav = false;
    require __DIR__ . '/includes/navbar.php';
    echo '<main class="container py-5"><p>' . e(__('category_not_found')) . '</p><a href="' . e(app_url('categories.php')) . '">' . e(__('back_to_categories')) . '</a></main>';
    $footerMode = 'full';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$priceMin = isset($_GET['price_min']) && $_GET['price_min'] !== '' ? (float) $_GET['price_min'] : null;
$priceMax = isset($_GET['price_max']) && $_GET['price_max'] !== '' ? (float) $_GET['price_max'] : null;
$moqMax = isset($_GET['moq_max']) && $_GET['moq_max'] !== '' ? (int) $_GET['moq_max'] : null;
$sort = $_GET['sort'] ?? 'latest';
$allowedSort = ['latest' => 'p.created_at DESC', 'price_asc' => 'p.min_price ASC', 'price_desc' => 'p.max_price DESC'];
$orderSql = $allowedSort[$sort] ?? $allowedSort['latest'];

$where = ['p.category_id = ?', 'p.status = "active"'];
$params = [$id];
if ($priceMin !== null) {
    $where[] = 'p.max_price >= ?';
    $params[] = $priceMin;
}
if ($priceMax !== null) {
    $where[] = 'p.min_price <= ?';
    $params[] = $priceMax;
}
if ($moqMax !== null) {
    $where[] = 'p.moq <= ?';
    $params[] = $moqMax;
}

$catExtraCols = db_has_column($pdo, 'categories', 'name_en') ? ', c.name_en, c.name_sw, c.name_zh' : '';
$sql = 'SELECT p.*, c.name AS category_name' . $catExtraCols . ' FROM products p JOIN categories c ON c.id = p.category_id WHERE ' . implode(' AND ', $where) . ' ORDER BY ' . $orderSql;
$st = $pdo->prepare($sql);
$st->execute($params);
$products = $st->fetchAll();

$pageTitle = getLocalizedCategoryName($category);
$sidebarCats = $pdo->query('SELECT id, name' . (db_has_column($pdo, 'categories', 'name_en') ? ', name_en, name_sw, name_zh' : '') . ' FROM categories WHERE status = "active" ORDER BY name')->fetchAll();

require __DIR__ . '/includes/header.php';
$hideShopNav = false;
require __DIR__ . '/includes/navbar.php';
?>

<main class="container-fluid px-3 px-sm-4 py-4">
    <div class="row g-4">
        <aside class="col-lg-3 d-none d-lg-block">
            <div class="hanzo-sidebar p-0 mb-3">
                <div class="p-3 border-bottom fw-bold"><?= e(__('categories')) ?></div>
                <div class="list-group list-group-flush">
                    <?php foreach ($sidebarCats as $sc): ?>
                        <a class="list-group-item list-group-item-action <?= (int) $sc['id'] === $id ? 'active' : '' ?>" href="<?= e(app_url('category.php?id=' . (int) $sc['id'])) ?>"><?= e(getLocalizedCategoryName($sc)) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </aside>
        <div class="col-lg-9">
            <h1 class="h3 mb-2"><?= e(getLocalizedCategoryName($category)) ?></h1>
            <?php if (!empty($category['description'])): ?>
                <p class="text-muted"><?= e($category['description']) ?></p>
            <?php endif; ?>

            <form class="row g-2 align-items-end bg-white border rounded p-3 mb-4" method="get" action="">
                <input type="hidden" name="id" value="<?= (int) $id ?>">
                <div class="col-md-3">
                    <label class="form-label small mb-0"><?= e(__('min_price_usd')) ?></label>
                    <input type="number" step="0.01" name="price_min" class="form-control" value="<?= $priceMin !== null ? e((string) $priceMin) : '' ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-0"><?= e(__('max_price_usd')) ?></label>
                    <input type="number" step="0.01" name="price_max" class="form-control" value="<?= $priceMax !== null ? e((string) $priceMax) : '' ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-0"><?= e(__('max_moq')) ?></label>
                    <input type="number" name="moq_max" class="form-control" value="<?= $moqMax !== null ? (int) $moqMax : '' ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-0"><?= e(__('sort')) ?></label>
                    <select name="sort" class="form-select">
                        <option value="latest" <?= $sort === 'latest' ? 'selected' : '' ?>><?= e(__('latest')) ?></option>
                        <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>><?= e(__('price_low_high')) ?></option>
                        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>><?= e(__('price_high_low')) ?></option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-hanzo-primary w-100"><?= e(__('apply')) ?></button>
                </div>
                <div class="col-12">
                    <p class="small text-muted mb-0"><?= e(__('filter_prices_catalog_usd_note')) ?></p>
                </div>
            </form>

            <div class="row">
                <?php if ($products === []): ?>
                    <p><?= e(__('no_products_match_filters')) ?></p>
                <?php endif; ?>
                <?php foreach ($products as $p): ?>
                    <?php require __DIR__ . '/includes/product_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>

<?php
$footerMode = 'full';
require __DIR__ . '/includes/footer.php';
