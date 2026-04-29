<?php

declare(strict_types=1);

/** @var array $p keys: id, product_name, description, min_price, max_price, moq, category_name, main_image */
$p = $p ?? [];
?>
<div class="col-sm-6 col-lg-4 col-xl-3 mb-4">
    <div class="hanzo-card-product h-100">
        <a href="<?= e(app_url('product.php?id=' . (int) $p['id'])) ?>">
            <img class="thumb" src="<?= e(product_image_url($p['main_image'] ?? null)) ?>" alt="<?= e($p['product_name'] ?? '') ?>">
        </a>
        <div class="card-body d-flex flex-column">
            <div class="mb-2"><span class="cat-badge"><?= e($p['category_name'] ?? '') ?></span></div>
            <h3 class="h6 flex-grow-0">
                <a class="text-decoration-none text-dark" href="<?= e(app_url('product.php?id=' . (int) $p['id'])) ?>"><?= e($p['product_name'] ?? '') ?></a>
            </h3>
            <p class="small text-muted flex-grow-1"><?= e(mb_strimwidth((string) ($p['description'] ?? ''), 0, 100, '…', 'UTF-8')) ?></p>
            <div class="price-range"><?= format_usd_range($p['min_price'] ?? 0, $p['max_price'] ?? 0, 'Piece') ?></div>
            <div class="moq mb-3"><?= format_moq((int) ($p['moq'] ?? 1), 'Piece') ?></div>
            <a class="btn btn-hanzo-primary w-100 mt-auto" href="<?= e(app_url('buyer/product-details.php?id=' . (int) $p['id'])) ?>">View details</a>
        </div>
    </div>
</div>
