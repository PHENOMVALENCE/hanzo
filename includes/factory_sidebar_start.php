<?php

declare(strict_types=1);

/** Opens factory workspace layout. Pair with factory_sidebar_end.php before footer. */
?>
<div class="hanzo-factory-shell container-fluid px-0">
    <div class="hanzo-factory-mobile-bar d-lg-none border-bottom bg-white px-2 px-sm-3 py-2 d-flex align-items-center justify-content-between shadow-sm">
        <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#hanzoFactorySidebarOffcanvas" aria-controls="hanzoFactorySidebarOffcanvas" aria-label="Open menu">
            <i class="fas fa-bars me-1" aria-hidden="true"></i> Menu
        </button>
        <span class="small fw-semibold text-muted">Factory workspace</span>
        <span class="d-inline-block" style="width: 2.5rem;" aria-hidden="true"></span>
    </div>
    <div class="row g-0">
        <aside class="col-lg-3 col-xl-2 d-none d-lg-flex flex-column hanzo-factory-sidebar pt-4 pb-5 px-3">
            <div class="hanzo-factory-workspace-label mb-3 px-1">Partner workspace</div>
            <div class="hanzo-factory-sidebar-panel">
                <?php factory_render_sidebar_nav('desktop'); ?>
            </div>
            <div class="mt-auto pt-4 hanzo-factory-sidebar-footer">
                <a href="<?= e(app_url('index.php')) ?>" class="hanzo-buyer-nav-foot"><i class="fas fa-store" aria-hidden="true"></i><span>Marketplace home</span></a>
            </div>
        </aside>
        <div class="col-12 col-lg-9 col-xl-10 hanzo-factory-main-column px-2 px-sm-3 px-lg-4 py-3 py-lg-4">
