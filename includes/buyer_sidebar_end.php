<?php

declare(strict_types=1);

/** Closes buyer workspace main column, shell, and renders mobile offcanvas nav. */
?>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-start hanzo-buyer-offcanvas" tabindex="-1" id="hanzoBuyerSidebarOffcanvas" aria-labelledby="hanzoBuyerSidebarTitle">
    <div class="offcanvas-header hanzo-buyer-offcanvas-header border-0">
        <h2 class="offcanvas-title h6 mb-0" id="hanzoBuyerSidebarTitle">Buyer workspace</h2>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3 overflow-auto">
        <div class="hanzo-buyer-workspace-label mb-2 px-1">Menu</div>
        <div class="hanzo-buyer-sidebar-panel">
            <?php buyer_render_sidebar_nav('offcanvas'); ?>
        </div>
        <div class="mt-3 pt-3 border-top hanzo-buyer-sidebar-footer">
            <a href="<?= e(app_url('index.php')) ?>" class="hanzo-buyer-nav-foot"><i class="fas fa-store" aria-hidden="true"></i><span>Marketplace home</span></a>
        </div>
    </div>
</div>
