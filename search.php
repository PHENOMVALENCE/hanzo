<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$q = trim((string) ($_GET['q'] ?? ''));
$catId = isset($_GET['cat']) && $_GET['cat'] !== '' ? (int) $_GET['cat'] : 0;

$products = [];
if ($q !== '' || $catId > 0) {
    $like = '%' . $q . '%';
    if ($q !== '') {
        $sql = 'SELECT p.*, c.name AS category_name, MATCH(p.product_name, p.description) AGAINST (:qnat IN NATURAL LANGUAGE MODE) AS rel
                FROM products p
                JOIN categories c ON c.id = p.category_id
                WHERE p.status = "active"
                AND (
                    MATCH(p.product_name, p.description) AGAINST (:qnat2 IN NATURAL LANGUAGE MODE)
                    OR p.product_name LIKE :like1 OR p.description LIKE :like3 OR c.name LIKE :like4
                )';
        if ($catId > 0) {
            $sql .= ' AND p.category_id = :cid';
        }
        $sql .= ' ORDER BY rel DESC, p.created_at DESC LIMIT 48';
        $st = $pdo->prepare($sql);
        $st->bindValue(':qnat', $q);
        $st->bindValue(':qnat2', $q);
        $st->bindValue(':like1', $like);
        $st->bindValue(':like3', $like);
        $st->bindValue(':like4', $like);
        if ($catId > 0) {
            $st->bindValue(':cid', $catId, PDO::PARAM_INT);
        }
        try {
            $st->execute();
            $products = $st->fetchAll();
        } catch (PDOException $e) {
            // Fallback for databases without FULLTEXT index on products(product_name, description).
            $sql = 'SELECT p.*, c.name AS category_name, 0 AS rel
                    FROM products p
                    JOIN categories c ON c.id = p.category_id
                    WHERE p.status = "active"
                    AND (
                        p.product_name LIKE :like1 OR p.description LIKE :like3 OR c.name LIKE :like4
                    )';
            if ($catId > 0) {
                $sql .= ' AND p.category_id = :cid';
            }
            $sql .= ' ORDER BY p.created_at DESC LIMIT 48';

            $st = $pdo->prepare($sql);
            $st->bindValue(':like1', $like);
            $st->bindValue(':like3', $like);
            $st->bindValue(':like4', $like);
            if ($catId > 0) {
                $st->bindValue(':cid', $catId, PDO::PARAM_INT);
            }
            $st->execute();
            $products = $st->fetchAll();
        }
    } else {
        $sql = 'SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE p.status = "active" AND p.category_id = ? ORDER BY p.created_at DESC LIMIT 48';
        $st = $pdo->prepare($sql);
        $st->execute([$catId]);
        $products = $st->fetchAll();
    }
}

$pageTitle = $q !== '' ? __('search_results') . ': ' . $q : __('search_products');
$catExtraCols = db_has_column($pdo, 'categories', 'name_en') ? ', name_en, name_sw, name_zh' : '';
$sidebarCats = $pdo->query('SELECT id, name' . $catExtraCols . ' FROM categories WHERE status = "active" ORDER BY name')->fetchAll();

require __DIR__ . '/includes/header.php';
$hideShopNav = false;
require __DIR__ . '/includes/navbar.php';
?>

<main class="container-fluid px-3 px-sm-4 py-4">
    <div class="row g-4">
        <aside class="col-lg-3 d-none d-lg-block">
            <div class="hanzo-sidebar p-0">
                <div class="p-3 border-bottom fw-bold"><?= e(__('categories')) ?></div>
                <div class="list-group list-group-flush">
                    <?php foreach ($sidebarCats as $sc): ?>
                        <a class="list-group-item list-group-item-action" href="<?= e(app_url('search.php?cat=' . (int) $sc['id'])) ?>"><?= e(getLocalizedCategoryName($sc)) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </aside>
        <div class="col-lg-9">
            <h1 class="h3 mb-3"><?= e(__('search_results')) ?></h1>
            <?php if ($q === '' && $catId === 0): ?>
                <p class="text-muted"><?= e(__('enter_keyword')) ?></p>
            <?php elseif ($products === []): ?>
                <p><?= e(__('no_search_results')) ?></p>
            <?php else: ?>
                <p class="text-muted small mb-3"><?= count($products) . ' ' . e(__('listings_count')) ?></p>
                <div class="row">
                    <?php foreach ($products as $p): ?>
                        <?php require __DIR__ . '/includes/product_card.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
$footerMode = 'full';
require __DIR__ . '/includes/footer.php';
