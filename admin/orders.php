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
    $id = (int) ($_POST['id'] ?? 0);
    $factoryId = (int) ($_POST['factory_id'] ?? 0);
    $status = (string) ($_POST['status'] ?? 'pending');
    $prev = '';
    if ($id > 0) {
        $stPrev = $pdo->prepare('SELECT status FROM orders WHERE id = ?');
        $stPrev->execute([$id]);
        $prev = (string) ($stPrev->fetchColumn() ?: '');
    }
    $pdo->prepare('UPDATE orders SET factory_id=?, status=? WHERE id=?')->execute([$factoryId > 0 ? $factoryId : null, $status, $id]);
    if ($id > 0) {
        buyer_notify_order_status_changed($pdo, $id, $prev, $status);
    }
    flash_set('success', 'Order updated.');
    redirect('admin/orders.php' . $selfQs);
}

$factories = $pdo->query('SELECT id, factory_name FROM factories WHERE status="active" ORDER BY factory_name')->fetchAll();

$dt = admin_dt_params(15);
$where = ['1=1'];
$params = [];
if ($dt['q'] !== '') {
    $where[] = '(o.order_code LIKE ? OR b.full_name LIKE ? OR p.product_name LIKE ? OR IFNULL(f.factory_name,"") LIKE ?)';
    $like = '%' . $dt['q'] . '%';
    $params = array_merge($params, [$like, $like, $like, $like]);
}
if ($dt['status'] !== '' && in_array($dt['status'], ['pending','assigned','quoted','accepted','in_production','quality_control','shipped','in_customs','delivered','cancelled'], true)) {
    $where[] = 'o.status = ?';
    $params[] = $dt['status'];
}
if ($dt['date_from'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dt['date_from'])) {
    $where[] = 'DATE(o.created_at) >= ?';
    $params[] = $dt['date_from'];
}
if ($dt['date_to'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dt['date_to'])) {
    $where[] = 'DATE(o.created_at) <= ?';
    $params[] = $dt['date_to'];
}
$whereSql = implode(' AND ', $where);
$sortMap = [
    'id' => 'o.id',
    'order_code' => 'o.order_code',
    'code' => 'o.order_code',
    'buyer_name' => 'b.full_name',
    'product_name' => 'p.product_name',
    'quantity' => 'o.quantity',
    'status' => 'o.status',
    'created_at' => 'o.created_at',
];
$orderSql = admin_dt_order_fragment($dt['sort'], $dt['dir'], $sortMap, 'o.created_at');
$cntSt = $pdo->prepare("SELECT COUNT(*) FROM orders o JOIN buyers b ON b.id=o.buyer_id JOIN products p ON p.id=o.product_id LEFT JOIN factories f ON f.id=o.factory_id WHERE $whereSql");
$cntSt->execute($params);
$total = (int) $cntSt->fetchColumn();
$page = admin_dt_clamp_page($dt['page'], $total, $dt['per_page']);
$offset = ($page - 1) * $dt['per_page'];
$lim = (int) $dt['per_page'];
$listSt = $pdo->prepare("SELECT o.*, b.full_name AS buyer_name, p.product_name, f.factory_name FROM orders o JOIN buyers b ON b.id=o.buyer_id JOIN products p ON p.id=o.product_id LEFT JOIN factories f ON f.id=o.factory_id WHERE $whereSql ORDER BY $orderSql LIMIT $lim OFFSET $offset");
$listSt->execute($params);
$orders = $listSt->fetchAll();
$dtPath = 'admin/orders.php';
$adminPostAction = $dtPath . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');

$pageTitle = 'Admin — Orders';
require __DIR__ . '/../includes/header.php';
$adminActive = 'orders';
$adminPageTitle = 'Orders';
require __DIR__ . '/../includes/admin_sidebar.php';
?>
<div class="admin-shell"><div class="admin-content">
<?php require __DIR__ . '/../includes/admin_topbar.php'; ?>
<main>
    <h1 class="h3 mb-3">Orders</h1>
    <p class="text-muted mb-3">Track confirmed orders flowing from accepted HANZO quotations. Future milestones (production, shipment, delivery, payment) can be layered on top of this core view.</p>

    <div class="admin-card p-3">
        <form method="get" class="row g-2 admin-dt-toolbar mb-3" action="<?= e(app_url($dtPath)) ?>">
            <div class="col-md-3"><label class="form-label small text-muted mb-0">Search</label><input type="text" name="q" class="form-control form-control-sm" value="<?= e($dt['q']) ?>" placeholder="Order code, buyer, product…"></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">Status</label><select name="status" class="form-select form-select-sm"><option value="">All</option><?php foreach (['pending','assigned','quoted','accepted','in_production','quality_control','shipped','in_customs','delivered','cancelled'] as $s): ?><option value="<?= e($s) ?>" <?= $dt['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">From</label><input type="date" name="date_from" class="form-control form-control-sm" value="<?= e($dt['date_from']) ?>"></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">To</label><input type="date" name="date_to" class="form-control form-control-sm" value="<?= e($dt['date_to']) ?>"></div>
            <div class="col-md-1"><label class="form-label small text-muted mb-0">Per page</label><select name="per_page" class="form-select form-select-sm"><?php foreach ([10,15,25,50] as $pp): ?><option value="<?= $pp ?>" <?= (int)$dt['per_page']===$pp?'selected':'' ?>><?= $pp ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2 d-flex align-items-end"><input type="hidden" name="sort" value="<?= e($dt['sort']) ?>"><input type="hidden" name="dir" value="<?= e($dt['dir']) ?>"><button type="submit" class="btn btn-sm btn-hanzo-primary">Apply</button></div>
        </form>
        <div class="table-responsive">
        <table class="table table-sm mb-0 align-middle" id="ordersTable">
            <thead class="table-light">
                <tr>
                    <?php admin_dt_sort_th('Order code', 'order_code', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Buyer', 'buyer_name', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Product', 'product_name', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Qty', 'quantity', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <th>Price range</th>
                    <?php admin_dt_sort_th('Status', 'status', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Created', 'created_at', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <th>Assign / Update</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><span class="fw-semibold"><?= e((string) $o['order_code']) ?></span><div class="small text-muted">ID <?= (int) $o['id'] ?></div></td>
                        <td><?= e($o['buyer_name']) ?></td>
                        <td><?= e($o['product_name']) ?></td>
                        <td><?= (int) $o['quantity'] ?></td>
                        <td><?= e((string) $o['price_range']) ?></td>
                        <td><span class="badge <?= e(order_status_badge_class((string) $o['status'])) ?>"><?= e(order_status_label((string) $o['status'])) ?></span></td>
                        <td class="small"><?= e(format_datetime((string) ($o['created_at'] ?? ''))) ?></td>
                        <td>
                            <form method="post" action="<?= e(app_url($adminPostAction)) ?>" class="d-flex gap-1">
                                <input type="hidden" name="id" value="<?= (int) $o['id'] ?>">
                                <select class="form-select form-select-sm" name="factory_id" style="min-width:170px;">
                                    <option value="0">Unassigned</option>
                                    <?php foreach ($factories as $f): ?>
                                        <option value="<?= (int) $f['id'] ?>" <?= (int) $o['factory_id'] === (int) $f['id'] ? 'selected' : '' ?>><?= e($f['factory_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="status" class="form-select form-select-sm">
                                    <?php foreach (['pending','assigned','quoted','accepted','in_production','quality_control','shipped','in_customs','delivered','cancelled'] as $s): ?>
                                        <option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-sm btn-hanzo-primary">Save</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($orders === []): ?>
                    <tr><td colspan="8" class="text-muted small text-center py-3">No orders match these filters.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
        <?php admin_dt_render_pager($dtPath, $total, $page, $dt['per_page']); ?>
    </div>
</main>
 </div></div>

<?php
$footerMode = 'slim';
require __DIR__ . '/../includes/footer.php';


