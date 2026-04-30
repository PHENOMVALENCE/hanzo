<?php

declare(strict_types=1);
?>
<?php $currentLang = hanzo_current_language(); ?>

<div class="hanzo-lang-switcher">
    <div class="hanzo-lang-label small text-muted d-none d-lg-block mb-1">
        <i class="fas fa-globe-africa me-1" aria-hidden="true"></i><?= e(__('language')) ?>: <?= e(hanzo_language_label($currentLang)) ?>
    </div>
    <div class="btn-group hanzo-mobile-lang-group w-100" role="group" aria-label="<?= e(__('language')) ?>">
        <a class="btn btn-sm <?= $currentLang === 'en' ? 'btn-hanzo-primary active' : 'btn-outline-secondary' ?>" href="<?= e(hanzo_lang_switch_url('en')) ?>" title="<?= e(__('english')) ?>">EN</a>
        <a class="btn btn-sm <?= $currentLang === 'sw' ? 'btn-hanzo-primary active' : 'btn-outline-secondary' ?>" href="<?= e(hanzo_lang_switch_url('sw')) ?>" title="<?= e(__('swahili')) ?>">SW</a>
        <a class="btn btn-sm <?= $currentLang === 'zh' ? 'btn-hanzo-primary active' : 'btn-outline-secondary' ?>" href="<?= e(hanzo_lang_switch_url('zh')) ?>" title="<?= e(__('chinese')) ?>">中文</a>
    </div>
</div>

