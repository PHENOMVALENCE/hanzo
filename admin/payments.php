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
    $status = (string) ($_POST['status'] ?? 'pending');
    if ($id > 0 && in_array($status, ['pending', 'verified', 'rejected'], true)) {
        $pdo->prepare('UPDATE payments SET status=?, verified_by=? WHERE id=?')->execute([$status, auth_id(), $id]);
        if ($status === 'verified') {
            $st = $pdo->prepare('SELECT order_id, payment_type FROM payments WHERE id = ?');
            $st->execute([$id]);
            $p = $st->fetch();
            if ($p) {
                $oidPay = (int) $p['order_id'];
                $stOs = $pdo->prepare('SELECT status FROM orders WHERE id = ?');
                $stOs->execute([$oidPay]);
                $prevOs = (string) ($stOs->fetchColumn() ?: '');
                $ptype = strtolower((string) ($p['payment_type'] ?? ''));
                $nextStatus = str_contains($ptype, 'full') ? 'shipped' : 'accepted';
                $pdo->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$nextStatus, $oidPay]);
                buyer_notify_order_status_changed($pdo, $oidPay, $prevOs, $nextStatus);
                $pdo->prepare('INSERT INTO shipping_updates (order_id, status_title, description, location, tracking_number, updated_by) VALUES (?,?,?,?,?,?)')
                    ->execute([(int) $p['order_id'], 'Payment verified', 'Payment verification completed by China Chapu admin.', 'China Chapu', null, auth_id()]);
            }
        }
        flash_set('success', 'Payment verification updated.');
    }
    redirect('admin/payments.php' . $selfQs);
}

$dt = admin_dt_params(15);
$where = ['1=1'];
$params = [];
if ($dt['q'] !== '') {
    $where[] = '(o.order_code LIKE ? OR b.full_name LIKE ? OR IFNULL(py.reference,"") LIKE ? OR IFNULL(py.method,"") LIKE ? OR IFNULL(py.payment_type,"") LIKE ?)';
    $like = '%' . $dt['q'] . '%';
    $params = array_merge($params, [$like, $like, $like, $like, $like]);
}
if ($dt['status'] !== '' && in_array($dt['status'], ['pending', 'verified', 'rejected'], true)) {
    $where[] = 'py.status = ?';
    $params[] = $dt['status'];
}
if ($dt['date_from'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dt['date_from'])) {
    $where[] = 'DATE(py.created_at) >= ?';
    $params[] = $dt['date_from'];
}
if ($dt['date_to'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dt['date_to'])) {
    $where[] = 'DATE(py.created_at) <= ?';
    $params[] = $dt['date_to'];
}
$whereSql = implode(' AND ', $where);
$sortMap = [
    'created_at' => 'py.created_at',
    'order_code' => 'o.order_code',
    'buyer_name' => 'b.full_name',
    'amount' => 'py.amount',
    'status' => 'py.status',
];
$orderSql = admin_dt_order_fragment($dt['sort'], $dt['dir'], $sortMap, 'py.created_at');
$cntSt = $pdo->prepare("SELECT COUNT(*) FROM payments py JOIN orders o ON o.id=py.order_id JOIN buyers b ON b.id=py.buyer_id WHERE $whereSql");
$cntSt->execute($params);
$total = (int) $cntSt->fetchColumn();
$page = admin_dt_clamp_page($dt['page'], $total, $dt['per_page']);
$offset = ($page - 1) * $dt['per_page'];
$lim = (int) $dt['per_page'];
$listSt = $pdo->prepare("SELECT py.*, o.order_code, b.full_name buyer_name FROM payments py JOIN orders o ON o.id=py.order_id JOIN buyers b ON b.id=py.buyer_id WHERE $whereSql ORDER BY $orderSql LIMIT $lim OFFSET $offset");
$listSt->execute($params);
$payments = $listSt->fetchAll();
$dtPath = 'admin/payments.php';
$adminPostAction = $dtPath . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
$pageTitle = 'Admin Payments';
require __DIR__ . '/../includes/header.php';
$adminActive = 'payments';
$adminPageTitle = 'Payments';
require __DIR__ . '/../includes/admin_sidebar.php';
?>
<div class="admin-shell"><div class="admin-content">
<?php require __DIR__ . '/../includes/admin_topbar.php'; ?>
<main>
    <h1 class="h3 mb-3">Payment Verification</h1>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
    <div class="admin-card p-3">
        <form method="get" class="row g-2 admin-dt-toolbar mb-3" action="<?= e(app_url($dtPath)) ?>">
            <div class="col-md-3"><label class="form-label small text-muted mb-0">Search</label><input type="text" name="q" class="form-control form-control-sm" value="<?= e($dt['q']) ?>" placeholder="Order, buyer, reference…"></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">Status</label><select name="status" class="form-select form-select-sm"><option value="">All</option><?php foreach (['pending','verified','rejected'] as $s): ?><option value="<?= e($s) ?>" <?= $dt['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">From</label><input type="date" name="date_from" class="form-control form-control-sm" value="<?= e($dt['date_from']) ?>"></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">To</label><input type="date" name="date_to" class="form-control form-control-sm" value="<?= e($dt['date_to']) ?>"></div>
            <div class="col-md-1"><label class="form-label small text-muted mb-0">Per page</label><select name="per_page" class="form-select form-select-sm"><?php foreach ([10,15,25,50] as $pp): ?><option value="<?= $pp ?>" <?= (int)$dt['per_page']===$pp?'selected':'' ?>><?= $pp ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2 d-flex align-items-end"><input type="hidden" name="sort" value="<?= e($dt['sort']) ?>"><input type="hidden" name="dir" value="<?= e($dt['dir']) ?>"><button type="submit" class="btn btn-sm btn-hanzo-primary">Apply</button></div>
        </form>
        <div class="table-responsive">
        <table class="table mb-0" id="paymentsTable">
            <thead class="table-light">
                <tr>
                    <?php admin_dt_sort_th('Order', 'order_code', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Buyer', 'buyer_name', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Amount', 'amount', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <th>Type / method</th>
                    <th>Reference</th>
                    <th>Proof</th>
                    <?php admin_dt_sort_th('Status', 'status', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Submitted', 'created_at', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($payments as $p): ?>
                <tr>
                    <td><?= e($p['order_code']) ?></td>
                    <td><?= e($p['buyer_name']) ?></td>
                    <td>US$<?= e(number_format((float) $p['amount'], 2)) ?></td>
                    <td><?= e((string) $p['payment_type']) ?> / <?= e((string) $p['method']) ?></td>
                    <td><?= e((string) $p['reference']) ?></td>
                    <td><?php if (!empty($p['proof_file'])): ?><a href="<?= e(app_url((string) $p['proof_file'])) ?>" target="_blank">View</a><?php else: ?>-<?php endif; ?></td>
                    <td><span class="badge <?= e(payment_status_badge_class((string) $p['status'])) ?>"><?= e(payment_status_label((string) $p['status'])) ?></span></td>
                    <td class="small"><?= e(format_datetime((string) ($p['created_at'] ?? ''))) ?></td>
                    <td>
                        <form method="post" action="<?= e(app_url($adminPostAction)) ?>" class="d-flex gap-1">
                            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                            <select class="form-select form-select-sm" name="status">
                                <?php foreach (['pending','verified','rejected'] as $s): ?><option value="<?= $s ?>" <?= $p['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?>
                            </select>
                            <button class="btn btn-sm btn-hanzo-primary">Save</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($payments === []): ?><tr><td colspan="9" class="text-center text-muted py-4">No payments match.</td></tr><?php endif; ?>
            </tbody>
        </table>
        </div>
        <?php admin_dt_render_pager($dtPath, $total, $page, $dt['per_page']); ?>
    </div>
</main>
 </div></div>
<?php $footerMode = 'slim'; require __DIR__ . '/../includes/footer.php'; ?>


