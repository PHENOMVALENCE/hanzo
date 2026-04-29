<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$st = $pdo->prepare('SELECT p.*, c.id AS category_id, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE p.id = ? AND p.status = "active"');
$st->execute([$id]);
$product = $st->fetch();
if (!$product) {
    http_response_code(404);
    $pageTitle = 'Product not found';
    require __DIR__ . '/includes/header.php';
    $hideShopNav = false;
    require __DIR__ . '/includes/navbar.php';
    echo '<main class="container py-5"><p>Product not found.</p><a href="' . e(app_url('index.php')) . '">Home</a></main>';
    $footerMode = 'full';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$imgSt = $pdo->prepare('SELECT image_path FROM product_images WHERE product_id = ? ORDER BY id');
$imgSt->execute([$id]);
$gallery = $imgSt->fetchAll(PDO::FETCH_COLUMN);

$mainImg = $product['main_image'] ?: null;
$allImages = [];
if ($mainImg) {
    $allImages[] = $mainImg;
}
foreach ($gallery as $gp) {
    if ($gp && !in_array($gp, $allImages, true)) {
        $allImages[] = $gp;
    }
}
if ($allImages === []) {
    $allImages[] = null;
}

$specs = [];

$simSt = $pdo->prepare('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE p.category_id = ? AND p.id != ? AND p.status = "active" ORDER BY p.created_at DESC LIMIT 4');
$simSt->execute([(int) $product['category_id'], $id]);
$similar = $simSt->fetchAll();

$pageTitle = $product['product_name'];
$sidebarCats = $pdo->query('SELECT id, name FROM categories WHERE status = "active" ORDER BY name')->fetchAll();

require __DIR__ . '/includes/header.php';
$hideShopNav = false;
require __DIR__ . '/includes/navbar.php';
?>

<main class="container-fluid px-3 px-sm-4 py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= e(app_url('index.php')) ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= e(app_url('category.php?id=' . (int) $product['category_id'])) ?>"><?= e($product['category_name']) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= e($product['product_name']) ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <aside class="col-lg-2 d-none d-lg-block">
            <div class="hanzo-sidebar p-0">
                <div class="p-2 small fw-bold border-bottom">Categories</div>
                <div class="list-group list-group-flush small">
                    <?php foreach ($sidebarCats as $sc): ?>
                        <a class="list-group-item list-group-item-action py-2" href="<?= e(app_url('category.php?id=' . (int) $sc['id'])) ?>"><?= e($sc['name']) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </aside>
        <div class="col-lg-10">
            <div class="row g-4">
                <div class="col-md-6">
                    <div id="productCarousel" class="carousel slide border rounded overflow-hidden bg-white">
                        <div class="carousel-inner">
                            <?php foreach ($allImages as $idx => $im): ?>
                                <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                                    <img src="<?= e(product_image_url($im)) ?>" class="d-block w-100" style="max-height:420px;object-fit:contain;" alt="<?= e($product['product_name']) ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($allImages) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            </button>
                        <?php endif; ?>
                    </div>
                    <?php if (count($allImages) > 1): ?>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <?php foreach ($allImages as $idx => $im): ?>
                                <button type="button" class="btn btn-sm btn-outline-secondary p-0 border" style="width:64px;height:64px;" data-bs-target="#productCarousel" data-bs-slide-to="<?= (int) $idx ?>" aria-label="Slide <?= (int) ($idx + 1) ?>">
                                    <img src="<?= e(product_image_url($im)) ?>" class="rounded" style="width:100%;height:100%;object-fit:cover;" alt="">
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <span class="cat-badge"><?= e($product['category_name']) ?></span>
                    <h1 class="h2 mt-2"><?= e($product['product_name']) ?></h1>
                    <p class="price-range fs-5"><?= format_usd_range($product['min_price'], $product['max_price'], 'Piece') ?></p>
                    <p class="mb-1"><strong>MOQ:</strong> <?= format_moq((int) $product['moq'], 'Piece') ?></p>
                    <p class="mb-1"><strong>Category:</strong> <?= e($product['category_name']) ?></p>
                    <p class="text-muted"><?= e(mb_strimwidth((string) $product['description'], 0, 180, '...', 'UTF-8')) ?></p>
                    <a class="btn btn-hanzo-primary btn-lg" href="<?= e(app_url('buyer/product-details.php?id=' . (int) $product['id'])) ?>">Request quotation</a>
                    <p class="small text-muted mt-3">Direct factory contact details are not published. HANZO coordinates verification, samples, and quotations.</p>
                </div>
            </div>

            <div class="mt-5">
                <h2 class="h4 hanzo-section-title">Description</h2>
                <div class="bg-white border rounded p-4">
                    <?= nl2br(e($product['description'])) ?>
                </div>
            </div>

            <?php if ($specs !== []): ?>
                <div class="mt-4">
                    <h2 class="h4 hanzo-section-title">Specifications</h2>
                    <div class="table-responsive border rounded">
                        <table class="table table-spec mb-0">
                            <tbody>
                                <?php foreach ($specs as $s): ?>
                                    <tr>
                                        <th><?= e($s['spec_label']) ?></th>
                                        <td><?= e($s['spec_value']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($similar !== []): ?>
                <div class="mt-5">
                    <h2 class="h4 hanzo-section-title">Similar products</h2>
                    <div class="row">
                        <?php foreach ($similar as $p): ?>
                            <?php require __DIR__ . '/includes/product_card.php'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
$footerMode = 'full';
require __DIR__ . '/includes/footer.php';
