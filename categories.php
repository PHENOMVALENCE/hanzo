<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'All categories';
$sql = <<<'SQL'
SELECT c.*, (
    SELECT p.main_image FROM products p
    WHERE p.category_id = c.id AND p.status = 'active'
      AND p.main_image IS NOT NULL AND TRIM(p.main_image) != ''
    ORDER BY p.id ASC LIMIT 1
) AS thumb_image
FROM categories c
WHERE c.status = 'active'
ORDER BY c.name
SQL;
$cats = $pdo->query($sql)->fetchAll();

require __DIR__ . '/includes/header.php';
$hideShopNav = false;
require __DIR__ . '/includes/navbar.php';
?>

<main class="container-fluid px-3 px-sm-4 py-4">
    <h1 class="h3 mb-4">Product categories</h1>
    <div class="row g-4">
        <?php foreach ($cats as $cat): ?>
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <a href="<?= e(app_url('category.php?id=' . (int) $cat['id'])) ?>" class="hanzo-category-card-img-wrap d-block text-decoration-none">
                        <img src="<?= e(category_image_url($cat)) ?>" alt="<?= e($cat['name']) ?>" class="hanzo-category-card-img" loading="lazy" decoding="async" width="640" height="400">
                    </a>
                    <div class="card-body">
                        <h2 class="h5"><a href="<?= e(app_url('category.php?id=' . (int) $cat['id'])) ?>" class="text-decoration-none text-dark"><?= e($cat['name']) ?></a></h2>
                        <?php if (!empty($cat['description'])): ?>
                            <p class="small text-muted mb-3"><?= e($cat['description']) ?></p>
                        <?php endif; ?>
                        <a class="btn btn-sm btn-hanzo-primary" href="<?= e(app_url('category.php?id=' . (int) $cat['id'])) ?>">View products</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php
$footerMode = 'full';
require __DIR__ . '/includes/footer.php';
