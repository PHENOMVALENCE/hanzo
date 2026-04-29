<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/admin_datatable.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/buyer_notifications.php';
require_admin();

$qs = $_SERVER['QUERY_STRING'] ?? '';
$selfQs = $qs !== '' ? '?' . $qs : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int) ($_POST['order_id'] ?? 0);
    $title = trim((string) ($_POST['status_title'] ?? 'Shipping Update'));
    $desc = trim((string) ($_POST['description'] ?? ''));
    $loc = trim((string) ($_POST['location'] ?? ''));
    $tn = trim((string) ($_POST['tracking_number'] ?? ''));
    if ($orderId > 0) {
        $pdo->prepare('INSERT INTO shipping_updates (order_id, status_title, description, location, tracking_number, updated_by) VALUES (?,?,?,?,?,?)')
            ->execute([$orderId, $title, $desc !== '' ? $desc : null, $loc !== '' ? $loc : null, $tn !== '' ? $tn : null, auth_id()]);
        if (!empty($_POST['order_status'])) {
            $newOs = (string) $_POST['order_status'];
            $stOs = $pdo->prepare('SELECT status FROM orders WHERE id = ?');
            $stOs->execute([$orderId]);
            $prevOs = (string) ($stOs->fetchColumn() ?: '');
            $pdo->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$newOs, $orderId]);
            buyer_notify_order_status_changed($pdo, $orderId, $prevOs, $newOs);
        }
        flash_set('success', 'Shipping update saved.');
    }
    redirect('admin/shipping.php' . $selfQs);
}

$orders = $pdo->query('SELECT o.id, o.order_code, o.status, b.full_name buyer_name, p.product_name FROM orders o JOIN buyers b ON b.id=o.buyer_id JOIN products p ON p.id=o.product_id ORDER BY o.created_at DESC LIMIT 500')->fetchAll();

$dt = admin_dt_params(15);
$where = ['1=1'];
$params = [];
if ($dt['q'] !== '') {
    $where[] = '(o.order_code LIKE ? OR IFNULL(su.status_title,"") LIKE ? OR IFNULL(su.description,"") LIKE ? OR IFNULL(su.location,"") LIKE ? OR IFNULL(su.tracking_number,"") LIKE ?)';
    $like = '%' . $dt['q'] . '%';
    $params = array_merge($params, [$like, $like, $like, $like, $like]);
}
if ($dt['date_from'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dt['date_from'])) {
    $where[] = 'DATE(su.created_at) >= ?';
    $params[] = $dt['date_from'];
}
if ($dt['date_to'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dt['date_to'])) {
    $where[] = 'DATE(su.created_at) <= ?';
    $params[] = $dt['date_to'];
}
$whereSql = implode(' AND ', $where);
$sortMap = [
    'created_at' => 'su.created_at',
    'order_code' => 'o.order_code',
    'status_title' => 'su.status_title',
    'location' => 'su.location',
    'tracking_number' => 'su.tracking_number',
];
$orderSql = admin_dt_order_fragment($dt['sort'], $dt['dir'], $sortMap, 'su.created_at');
$cntSt = $pdo->prepare("SELECT COUNT(*) FROM shipping_updates su JOIN orders o ON o.id=su.order_id WHERE $whereSql");
$cntSt->execute($params);
$total = (int) $cntSt->fetchColumn();
$page = admin_dt_clamp_page($dt['page'], $total, $dt['per_page']);
$offset = ($page - 1) * $dt['per_page'];
$lim = (int) $dt['per_page'];
$listSt = $pdo->prepare("SELECT su.*, o.order_code FROM shipping_updates su JOIN orders o ON o.id=su.order_id WHERE $whereSql ORDER BY $orderSql LIMIT $lim OFFSET $offset");
$listSt->execute($params);
$updates = $listSt->fetchAll();
$dtPath = 'admin/shipping.php';
$adminPostAction = $dtPath . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
$pageTitle = 'Admin Shipping';
require __DIR__ . '/../includes/header.php';
$adminActive = 'shipping';
$adminPageTitle = 'Shipping';
require __DIR__ . '/../includes/admin_sidebar.php';
?>
<div class="admin-shell"><div class="admin-content">
<?php require __DIR__ . '/../includes/admin_topbar.php'; ?>
<main>
    <h1 class="h3 mb-3">Shipping & Delivery Updates</h1>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
    <form method="post" action="<?= e(app_url($adminPostAction)) ?>" class="row g-2 bg-white border rounded p-3 mb-3">
        <div class="col-md-4"><select name="order_id" class="form-select" required><option value="">Select order</option><?php foreach ($orders as $o): ?><option value="<?= (int) $o['id'] ?>"><?= e($o['order_code'] . ' | ' . $o['buyer_name'] . ' | ' . $o['product_name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><input name="status_title" class="form-control" placeholder="Status title"></div>
        <div class="col-md-2"><input name="location" class="form-control" placeholder="Location"></div>
        <div class="col-md-2"><input name="tracking_number" class="form-control" placeholder="Tracking #"></div>
        <div class="col-md-2"><select name="order_status" class="form-select"><option value="">Keep order status</option><?php foreach (['shipped','in_customs','delivered'] as $s): ?><option value="<?= $s ?>"><?= e($s) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-10"><input name="description" class="form-control" placeholder="Description"></div>
        <div class="col-md-2"><button class="btn btn-hanzo-primary w-100">Add Update</button></div>
    </form>
    <div class="admin-card p-3">
        <form method="get" class="row g-2 admin-dt-toolbar mb-3" action="<?= e(app_url($dtPath)) ?>">
            <div class="col-md-4"><label class="form-label small text-muted mb-0">Search</label><input type="text" name="q" class="form-control form-control-sm" value="<?= e($dt['q']) ?>" placeholder="Order, title, tracking…"></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">From</label><input type="date" name="date_from" class="form-control form-control-sm" value="<?= e($dt['date_from']) ?>"></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">To</label><input type="date" name="date_to" class="form-control form-control-sm" value="<?= e($dt['date_to']) ?>"></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">Per page</label><select name="per_page" class="form-select form-select-sm"><?php foreach ([10,15,25,50] as $pp): ?><option value="<?= $pp ?>" <?= (int)$dt['per_page']===$pp?'selected':'' ?>><?= $pp ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2 d-flex align-items-end"><input type="hidden" name="sort" value="<?= e($dt['sort']) ?>"><input type="hidden" name="dir" value="<?= e($dt['dir']) ?>"><button type="submit" class="btn btn-sm btn-hanzo-primary">Apply</button></div>
        </form>
        <div class="table-responsive">
        <table class="table mb-0" id="shippingTable">
            <thead class="table-light">
                <tr>
                    <?php admin_dt_sort_th('Order', 'order_code', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Status', 'status_title', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <th>Description</th>
                    <?php admin_dt_sort_th('Location', 'location', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Tracking', 'tracking_number', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Time', 'created_at', $dt['sort'], $dt['dir'], $dtPath); ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($updates as $u): ?>
                    <tr><td><?= e($u['order_code']) ?></td><td><?= e($u['status_title']) ?></td><td><?= e((string) $u['description']) ?></td><td><?= e((string) $u['location']) ?></td><td><?= e((string) $u['tracking_number']) ?></td><td class="small"><?= e(format_datetime((string) ($u['created_at'] ?? ''))) ?></td></tr>
                <?php endforeach; ?>
                <?php if ($updates === []): ?><tr><td colspan="6" class="text-center text-muted py-4">No shipping updates match.</td></tr><?php endif; ?>
            </tbody>
        </table>
        </div>
        <?php admin_dt_render_pager($dtPath, $total, $page, $dt['per_page']); ?>
    </div>
</main>
 </div></div>
<?php $footerMode = 'slim'; require __DIR__ . '/../includes/footer.php'; ?>


