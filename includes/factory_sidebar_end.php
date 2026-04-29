<?php

declare(strict_types=1);

?>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-start hanzo-factory-offcanvas" tabindex="-1" id="hanzoFactorySidebarOffcanvas" aria-labelledby="hanzoFactorySidebarTitle">
    <div class="offcanvas-header hanzo-factory-offcanvas-header border-0">
        <h2 class="offcanvas-title h6 mb-0" id="hanzoFactorySidebarTitle">Factory workspace</h2>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3 overflow-auto">
        <div class="hanzo-factory-workspace-label mb-2 px-1">Menu</div>
        <div class="hanzo-factory-sidebar-panel">
            <?php factory_render_sidebar_nav('offcanvas'); ?>
        </div>
        <div class="mt-3 pt-3 border-top hanzo-factory-sidebar-footer">
            <a href="<?= e(app_url('index.php')) ?>" class="hanzo-buyer-nav-foot"><i class="fas fa-store" aria-hidden="true"></i><span>Marketplace home</span></a>
        </div>
    </div>
</div>
