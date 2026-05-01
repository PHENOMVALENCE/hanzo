<?php

declare(strict_types=1);
?>
<?php
$currentLang = hanzo_current_language();
$langDropdownId = 'hanzo-lang-dd-' . bin2hex(random_bytes(4));
$langMenuLabel = e(__('locale_menu_language'));
?>

<div class="hanzo-lang-switcher hanzo-locale-panel">
    <div class="dropdown hanzo-locale-dropdown">
        <button
            class="hanzo-locale-dropdown__toggle dropdown-toggle"
            type="button"
            id="<?= e($langDropdownId) ?>"
            data-bs-toggle="dropdown"
            data-bs-display="static"
            aria-expanded="false"
            aria-haspopup="true"
            aria-label="<?= $langMenuLabel ?>"
        >
            <?= $langMenuLabel ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end hanzo-locale-dropdown__menu" aria-labelledby="<?= e($langDropdownId) ?>">
            <li>
                <a class="dropdown-item hanzo-locale-dropdown__item d-flex align-items-center justify-content-between gap-2<?= $currentLang === 'en' ? ' active' : '' ?>" href="<?= e(hanzo_lang_switch_url('en')) ?>" <?= $currentLang === 'en' ? 'aria-current="true"' : '' ?>>
                    <span><span class="fw-semibold"><?= e(__('english')) ?></span> <span class="text-muted small">EN</span></span>
                    <?php if ($currentLang === 'en'): ?><i class="bi bi-check2 hanzo-locale-dropdown__check flex-shrink-0" aria-hidden="true"></i><?php endif; ?>
                </a>
            </li>
            <li>
                <a class="dropdown-item hanzo-locale-dropdown__item d-flex align-items-center justify-content-between gap-2<?= $currentLang === 'sw' ? ' active' : '' ?>" href="<?= e(hanzo_lang_switch_url('sw')) ?>" <?= $currentLang === 'sw' ? 'aria-current="true"' : '' ?>>
                    <span><span class="fw-semibold"><?= e(__('swahili')) ?></span> <span class="text-muted small">SW</span></span>
                    <?php if ($currentLang === 'sw'): ?><i class="bi bi-check2 hanzo-locale-dropdown__check flex-shrink-0" aria-hidden="true"></i><?php endif; ?>
                </a>
            </li>
            <li>
                <a class="dropdown-item hanzo-locale-dropdown__item d-flex align-items-center justify-content-between gap-2<?= $currentLang === 'zh' ? ' active' : '' ?>" href="<?= e(hanzo_lang_switch_url('zh')) ?>" <?= $currentLang === 'zh' ? 'aria-current="true"' : '' ?>>
                    <span class="fw-semibold"><?= e(__('chinese')) ?></span>
                    <?php if ($currentLang === 'zh'): ?><i class="bi bi-check2 hanzo-locale-dropdown__check flex-shrink-0" aria-hidden="true"></i><?php endif; ?>
                </a>
            </li>
        </ul>
    </div>
</div>
