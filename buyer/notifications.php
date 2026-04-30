<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/buyer_notifications.php';

require_buyer();

$buyerId = (int) auth_id();

if (empty($_SESSION['_csrf'])) {
    $_SESSION['_csrf'] = bin2hex(random_bytes(16));
}
$csrf = (string) $_SESSION['_csrf'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'mark_all_read') {
    if (!hash_equals($_SESSION['_csrf'] ?? '', (string) ($_POST['_csrf'] ?? ''))) {
        flash_set('error', 'Invalid session. Please try again.');
    } else {
        buyer_notifications_mark_all_read($pdo, $buyerId);
        flash_set('success', 'All notifications marked as read.');
    }
    redirect('buyer/notifications.php');
}

$markOne = isset($_GET['read']) ? (int) $_GET['read'] : 0;
if ($markOne > 0) {
    buyer_notification_mark_read($pdo, $buyerId, $markOne);
    redirect('buyer/notifications.php');
}

$items = buyer_order_notifications_fetch($pdo, $buyerId, 80);
$schemaReady = buyer_notifications_schema_ready($pdo);

$pageTitle = 'Order notifications';
require __DIR__ . '/../includes/header.php';
$hideShopNav = false;
require __DIR__ . '/../includes/navbar.php';
require __DIR__ . '/../includes/buyer_sidebar_start.php';
?>
<main class="hanzo-buyer-main-inner">
    <header class="hanzo-buyer-page-head d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
        <div>
            <h1 class="hanzo-buyer-page-title">Order notifications</h1>
            <p class="text-muted small mb-0">Updates when your order status changes across China Chapu, suppliers, and shipping.</p>
        </div>
        <?php if ($schemaReady && $items !== []): ?>
            <form method="post" class="m-0">
                <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                <input type="hidden" name="action" value="mark_all_read">
                <button type="submit" class="btn btn-sm btn-outline-secondary">Mark all read</button>
            </form>
        <?php endif; ?>
    </header>

    <?php if ($m = flash_get('success')): ?><div class="alert alert-success border-0 shadow-sm"><?= e($m) ?></div><?php endif; ?>
    <?php if ($m = flash_get('error')): ?><div class="alert alert-danger border-0 shadow-sm"><?= e($m) ?></div><?php endif; ?>

    <?php if (!$schemaReady): ?>
        <div class="alert alert-warning border-0 shadow-sm">
            Notifications are not enabled on this database yet. Ask your administrator to run
            <code class="small">database/migrations/003_buyer_order_notifications.sql</code>.
        </div>
    <?php elseif ($items === []): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center text-muted py-5">No order notifications yet. You will see updates here when your orders move through the pipeline.</div>
        </div>
    <?php else: ?>
        <ul class="list-group list-group-flush shadow-sm rounded-3 overflow-hidden border">
            <?php foreach ($items as $it): ?>
                <?php
                $nid = (int) $it['id'];
                $isRead = (int) $it['is_read'] === 1;
                $oid = isset($it['related_order_id']) ? (int) $it['related_order_id'] : 0;
                ?>
                <li class="list-group-item px-3 py-3 <?= $isRead ? '' : 'bg-light' ?>">
                    <div class="d-flex flex-wrap justify-content-between gap-2 align-items-start">
                        <div class="flex-grow-1" style="min-width: 200px;">
                            <?php if (!$isRead): ?><span class="badge text-bg-warning me-1">New</span><?php endif; ?>
                            <span class="fw-semibold"><?= e((string) $it['title']) ?></span>
                            <?php if ($it['message'] !== null && $it['message'] !== ''): ?>
                                <p class="small text-muted mb-1 mt-1 mb-0"><?= nl2br(e((string) $it['message'])) ?></p>
                            <?php endif; ?>
                            <div class="small text-muted mt-2"><?= e(format_datetime((string) ($it['created_at'] ?? ''))) ?></div>
                        </div>
                        <div class="d-flex flex-column flex-sm-row gap-1 align-items-stretch align-items-sm-center">
                            <?php if ($oid > 0): ?>
                                <a class="btn btn-sm btn-hanzo-outline" href="<?= e(app_url('buyer/orders.php#order-' . $oid)) ?>">View order</a>
                            <?php endif; ?>
                            <?php if (!$isRead): ?>
                                <a class="btn btn-sm btn-outline-secondary" href="<?= e(app_url('buyer/notifications.php?read=' . $nid)) ?>">Mark read</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>
<?php
require __DIR__ . '/../includes/buyer_sidebar_end.php';
$footerMode = 'slim';
require __DIR__ . '/../includes/footer.php';
