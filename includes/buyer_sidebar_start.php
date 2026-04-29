<?php

declare(strict_types=1);

/** Opens buyer workspace layout (sidebar + main column). Pair with buyer_sidebar_end.php before footer. */
require_once __DIR__ . '/buyer_notifications.php';
$GLOBALS['hanzo_buyer_notif_unread'] = 0;
if (isset($pdo) && function_exists('auth_role') && auth_role() === 'buyer' && auth_id() !== null) {
    $GLOBALS['hanzo_buyer_notif_unread'] = buyer_order_notifications_unread_count($pdo, (int) auth_id());
}
?>
<div class="hanzo-buyer-shell container-fluid px-0">
    <div class="hanzo-buyer-mobile-bar d-lg-none border-bottom bg-white px-3 py-2 d-flex align-items-center justify-content-between shadow-sm">
        <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#hanzoBuyerSidebarOffcanvas" aria-controls="hanzoBuyerSidebarOffcanvas" aria-label="Open menu">
            <i class="fas fa-bars me-1" aria-hidden="true"></i> Menu
        </button>
        <span class="small fw-semibold text-muted">Buyer workspace</span>
        <a href="<?= e(app_url('buyer/notifications.php')) ?>" class="hanzo-buyer-notif-bar position-relative" aria-label="Order notifications<?= (int) $GLOBALS['hanzo_buyer_notif_unread'] > 0 ? ', ' . (int) $GLOBALS['hanzo_buyer_notif_unread'] . ' unread' : '' ?>">
            <i class="fas fa-bell" aria-hidden="true"></i>
            <?php if ((int) $GLOBALS['hanzo_buyer_notif_unread'] > 0): ?>
                <span class="hanzo-buyer-nav-badge rounded-pill"><?= (int) $GLOBALS['hanzo_buyer_notif_unread'] ?></span>
            <?php endif; ?>
        </a>
    </div>
    <div class="row g-0">
        <aside class="col-lg-3 col-xl-2 d-none d-lg-flex flex-column hanzo-buyer-sidebar pt-4 pb-5 px-3">
            <div class="hanzo-buyer-workspace-label mb-3 px-1">Workspace</div>
            <div class="hanzo-buyer-sidebar-panel">
                <?php buyer_render_sidebar_nav('desktop'); ?>
            </div>
            <div class="mt-auto pt-4 hanzo-buyer-sidebar-footer">
                <a href="<?= e(app_url('index.php')) ?>" class="hanzo-buyer-nav-foot"><i class="fas fa-store" aria-hidden="true"></i><span>Marketplace home</span></a>
            </div>
        </aside>
        <div class="col-12 col-lg-9 col-xl-10 hanzo-buyer-main-column px-3 px-lg-4 py-4">
