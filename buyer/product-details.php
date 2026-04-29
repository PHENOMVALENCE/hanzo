<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_buyer();

$id = (int) ($_GET['id'] ?? 0);
$st = $pdo->prepare('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE p.id = ? AND p.status="active"');
$st->execute([$id]);
$product = $st->fetch();
if (!$product) {
    flash_set('error', 'Product not found.');
    redirect('buyer/products.php');
}
$imgs = $pdo->prepare('SELECT image_path FROM product_images WHERE product_id = ? ORDER BY id');
$imgs->execute([$id]);
$gallery = $imgs->fetchAll(PDO::FETCH_COLUMN);
if ($gallery === []) {
    $gallery = [$product['main_image']];
}
$pageTitle = 'Product Details';
require __DIR__ . '/../includes/header.php';
$hideShopNav = false;
require __DIR__ . '/../includes/navbar.php';
?>
<main class="container py-4">
    <a href="<?= e(app_url('buyer/products.php')) ?>" class="small">&larr; Back to products</a>
    <div class="row g-4 mt-1">
        <div class="col-lg-6">
            <div class="border rounded bg-white p-2">
                <img src="<?= e(product_image_url($gallery[0] ?? null)) ?>" alt="<?= e($product['product_name']) ?>" class="w-100" style="max-height:420px;object-fit:contain;">
            </div>
            <div class="d-flex gap-2 mt-2 flex-wrap">
                <?php foreach ($gallery as $g): ?>
                    <img src="<?= e(product_image_url($g)) ?>" alt="" width="72" height="72" class="rounded border" style="object-fit:cover;">
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-lg-6">
            <span class="cat-badge"><?= e($product['category_name']) ?></span>
            <h1 class="h3 mt-2"><?= e($product['product_name']) ?></h1>
            <p class="price-range fs-5"><?= format_usd_range($product['min_price'], $product['max_price'], 'Piece') ?></p>
            <p><strong>MOQ:</strong> <?= (int) $product['moq'] ?> pieces</p>
            <p><?= nl2br(e($product['description'])) ?></p>
            <p class="small text-muted">Factory identity is protected. Quotations and communication are managed by HANZO.</p>
            <a href="<?= e(app_url('inquiry.php?product_id=' . (int) $product['id'])) ?>" class="btn btn-hanzo-primary btn-lg">Submit Order Request</a>
        </div>
    </div>
</main>
<?php $footerMode = 'full'; require __DIR__ . '/../includes/footer.php'; ?>
