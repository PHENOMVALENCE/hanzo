<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_buyer();

$q = trim((string) ($_GET['q'] ?? ''));
$cat = (int) ($_GET['cat'] ?? 0);
$where = ['p.status = "active"'];
$params = [];
if ($q !== '') {
    $where[] = '(p.product_name LIKE ? OR p.description LIKE ?)';
    $params[] = '%' . $q . '%';
    $params[] = '%' . $q . '%';
}
if ($cat > 0) {
    $where[] = 'p.category_id = ?';
    $params[] = $cat;
}
$sql = 'SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE ' . implode(' AND ', $where) . ' ORDER BY p.created_at DESC';
$st = $pdo->prepare($sql);
$st->execute($params);
$products = $st->fetchAll();
$cats = $pdo->query('SELECT id, name FROM categories WHERE status = "active" ORDER BY name')->fetchAll();

$pageTitle = 'Browse Products';
require __DIR__ . '/../includes/header.php';
$hideShopNav = false;
require __DIR__ . '/../includes/navbar.php';
require __DIR__ . '/../includes/buyer_sidebar_start.php';
?>
<main class="hanzo-buyer-main-inner">
    <h1 class="h4 mb-3">Browse Products</h1>
    <form class="row g-2 mb-4" method="get">
        <div class="col-md-6"><input class="form-control" name="q" placeholder="Search products..." value="<?= e($q) ?>"></div>
        <div class="col-md-4"><select class="form-select" name="cat"><option value="0">All categories</option><?php foreach ($cats as $c): ?><option value="<?= (int) $c['id'] ?>" <?= $cat === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><button class="btn btn-hanzo-primary w-100">Filter</button></div>
    </form>
    <div class="row">
        <?php foreach ($products as $p): require __DIR__ . '/../includes/product_card.php'; endforeach; ?>
    </div>
</main>
<?php require __DIR__ . '/../includes/buyer_sidebar_end.php'; ?>
<?php $footerMode = 'full'; require __DIR__ . '/../includes/footer.php'; ?>

