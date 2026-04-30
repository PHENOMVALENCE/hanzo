<?php

declare(strict_types=1);

/** @var array $p keys include multilingual fields */
$p = $p ?? [];
$productName = getLocalizedProductName($p);
$productDesc = getLocalizedProductDescription($p);
$categoryName = getLocalizedCategoryName($p + ['name' => $p['category_name'] ?? '']);
?>
<div class="col-sm-6 col-lg-4 col-xl-3 mb-4">
    <div class="hanzo-card-product h-100">
        <a href="<?= e(app_url('product.php?id=' . (int) $p['id'])) ?>">
            <img class="thumb" src="<?= e(product_image_url($p['main_image'] ?? null)) ?>" alt="<?= e($productName) ?>">
        </a>
        <div class="card-body d-flex flex-column">
            <div class="mb-2"><span class="cat-badge"><?= e($categoryName) ?></span></div>
            <h3 class="h6 flex-grow-0">
                <a class="text-decoration-none text-dark" href="<?= e(app_url('product.php?id=' . (int) $p['id'])) ?>"><?= e($productName) ?></a>
            </h3>
            <p class="small text-muted flex-grow-1"><?= e(mb_strimwidth($productDesc, 0, 100, '…', 'UTF-8')) ?></p>
            <div class="price-range"><?= format_usd_range($p['min_price'] ?? 0, $p['max_price'] ?? 0, 'Piece') ?></div>
            <div class="moq mb-3"><?= format_moq((int) ($p['moq'] ?? 1), 'Piece') ?></div>
            <a class="btn btn-hanzo-primary w-100 mt-auto" href="<?= e(app_url('buyer/product-details.php?id=' . (int) $p['id'])) ?>"><?= e(__('view_details')) ?></a>
        </div>
    </div>
</div>
