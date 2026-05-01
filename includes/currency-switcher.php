<?php

declare(strict_types=1);

$pc = hanzo_price_display_currency();
$currencyDropdownId = 'hanzo-currency-dd-' . bin2hex(random_bytes(4));
$currencyMenuLabel = e(__('locale_menu_currency'));

$currencyChoices = [
    [
        'code' => 'USD',
        'title_key' => 'currency_usd',
        'line' => __('currency_menu_usd_line'),
    ],
    [
        'code' => 'TZS',
        'title_key' => 'currency_tzs',
        'line' => __('currency_menu_tzs_line'),
    ],
    [
        'code' => 'CNY',
        'title_key' => 'currency_cny',
        'line' => __('currency_menu_cny_line'),
    ],
];
?>

<div class="hanzo-currency-switcher hanzo-locale-panel">
    <div class="dropdown hanzo-locale-dropdown">
        <button
            class="hanzo-locale-dropdown__toggle dropdown-toggle"
            type="button"
            id="<?= e($currencyDropdownId) ?>"
            data-bs-toggle="dropdown"
            data-bs-display="static"
            aria-expanded="false"
            aria-haspopup="true"
            aria-label="<?= $currencyMenuLabel ?>"
        >
            <?= $currencyMenuLabel ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end hanzo-locale-dropdown__menu" aria-labelledby="<?= e($currencyDropdownId) ?>">
            <?php foreach ($currencyChoices as $c): ?>
                <?php
                $code = $c['code'];
                $isActive = $pc === $code;
                $href = hanzo_price_currency_switch_url($code);
                ?>
                <li>
                    <a class="dropdown-item hanzo-locale-dropdown__item d-flex align-items-center justify-content-between gap-2<?= $isActive ? ' active' : '' ?>"
                       href="<?= e($href) ?>"
                       title="<?= e(__($c['title_key'])) ?>"
                       <?= $isActive ? 'aria-current="true"' : '' ?>>
                        <span>
                            <span class="fw-semibold"><?= e($code) ?></span>
                            <span class="text-muted small"><?= e($c['line']) ?></span>
                        </span>
                        <?php if ($isActive): ?><i class="bi bi-check2 hanzo-locale-dropdown__check flex-shrink-0" aria-hidden="true"></i><?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
            <li class="hanzo-locale-dropdown__footer-note px-0">
                <hr class="dropdown-divider my-1 mx-2">
                <span class="hanzo-locale-dropdown__menu-note small text-muted px-3 py-2 d-block lh-sm"><?= e(__('exchange_rates_note')) ?></span>
            </li>
        </ul>
    </div>
    <p class="hanzo-locale-panel__hint mb-0"><?= e(__('exchange_rates_note')) ?></p>
</div>
