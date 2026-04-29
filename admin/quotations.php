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

$total = static fn(array $d): float => (float) $d['product_cost'] + (float) $d['china_local_shipping'] + (float) $d['export_handling'] + (float) $d['freight_cost'] + (float) $d['insurance_cost'] + (float) $d['clearing_cost'] + (float) $d['local_delivery_cost'] + (float) $d['hanzo_margin'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'promote') {
        $qid = (int) ($_POST['id'] ?? 0);
        $st = $pdo->prepare('SELECT q.id, q.status, q.order_id FROM quotations q WHERE q.id = ?');
        $st->execute([$qid]);
        $q = $st->fetch();
        if ($q && $q['status'] === 'accepted') {
            $oidProm = (int) $q['order_id'];
            $stOs = $pdo->prepare('SELECT status FROM orders WHERE id = ?');
            $stOs->execute([$oidProm]);
            $prevOs = (string) ($stOs->fetchColumn() ?: '');
            $pdo->prepare('UPDATE orders SET status="in_production" WHERE id=?')->execute([$oidProm]);
            buyer_notify_order_status_changed($pdo, $oidProm, $prevOs, 'in_production');
            $pdo->prepare('INSERT INTO shipping_updates (order_id, status_title, description, location, updated_by) VALUES (?,?,?,?,?)')
                ->execute([(int) $q['order_id'], 'Production started', 'Order moved to production workflow by admin.', 'Factory', auth_id()]);
            flash_set('success', 'Accepted quotation promoted to active production.');
        }
        redirect('admin/quotations.php' . $selfQs);
    }

    $id = (int) ($_POST['id'] ?? 0);
    $orderId = (int) ($_POST['order_id'] ?? 0);
    $data = [
        'product_cost' => (float) ($_POST['product_cost'] ?? 0),
        'china_local_shipping' => (float) ($_POST['china_local_shipping'] ?? 0),
        'export_handling' => (float) ($_POST['export_handling'] ?? 0),
        'freight_cost' => (float) ($_POST['freight_cost'] ?? 0),
        'insurance_cost' => (float) ($_POST['insurance_cost'] ?? 0),
        'clearing_cost' => (float) ($_POST['clearing_cost'] ?? 0),
        'local_delivery_cost' => (float) ($_POST['local_delivery_cost'] ?? 0),
        'hanzo_margin' => (float) ($_POST['hanzo_margin'] ?? 0),
    ];
    $sum = $total($data);
    $status = (string) ($_POST['status'] ?? 'draft');
    $valid = (string) ($_POST['valid_until'] ?? null);
    if ($id > 0) {
        $pdo->prepare('UPDATE quotations SET product_cost=?, china_local_shipping=?, export_handling=?, freight_cost=?, insurance_cost=?, clearing_cost=?, local_delivery_cost=?, hanzo_margin=?, total_landed_cost=?, status=?, valid_until=? WHERE id=?')
            ->execute([$data['product_cost'], $data['china_local_shipping'], $data['export_handling'], $data['freight_cost'], $data['insurance_cost'], $data['clearing_cost'], $data['local_delivery_cost'], $data['hanzo_margin'], $sum, $status, $valid !== '' ? $valid : null, $id]);
        $qid = $id;
    } else {
        $code = 'HNZ-Q-' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        $pdo->prepare('INSERT INTO quotations (quote_code, order_id, product_cost, china_local_shipping, export_handling, freight_cost, insurance_cost, clearing_cost, local_delivery_cost, hanzo_margin, total_landed_cost, status, valid_until) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)')
            ->execute([$code, $orderId, $data['product_cost'], $data['china_local_shipping'], $data['export_handling'], $data['freight_cost'], $data['insurance_cost'], $data['clearing_cost'], $data['local_delivery_cost'], $data['hanzo_margin'], $sum, $status, $valid !== '' ? $valid : null]);
        $qid = (int) $pdo->lastInsertId();
    }
    $stOid = $pdo->prepare('SELECT order_id FROM quotations WHERE id = ?');
    $stOid->execute([$qid]);
    $resolvedOrderId = (int) $stOid->fetchColumn();
    $prevOrderStatus = '';
    if ($resolvedOrderId > 0) {
        $stOrd = $pdo->prepare('SELECT status FROM orders WHERE id = ?');
        $stOrd->execute([$resolvedOrderId]);
        $prevOrderStatus = (string) ($stOrd->fetchColumn() ?: '');
    }
    $newOrderStatus = $status === 'sent' ? 'quoted' : 'assigned';
    $pdo->prepare('UPDATE orders SET status=? WHERE id=(SELECT order_id FROM quotations WHERE id=?)')->execute([$newOrderStatus, $qid]);
    if ($resolvedOrderId > 0) {
        buyer_notify_order_status_changed($pdo, $resolvedOrderId, $prevOrderStatus, $newOrderStatus);
    }
    flash_set('success', 'Quotation saved.');
    redirect('admin/quotations.php' . $selfQs);
}

$orders = $pdo->query('SELECT o.id, o.order_code, p.product_name, b.full_name buyer_name FROM orders o JOIN products p ON p.id=o.product_id JOIN buyers b ON b.id=o.buyer_id WHERE o.status IN ("pending","assigned","quoted","accepted") ORDER BY o.created_at DESC')->fetchAll();

$dt = admin_dt_params(15);
$where = ['1=1'];
$params = [];
if ($dt['q'] !== '') {
    $where[] = '(q.quote_code LIKE ? OR o.order_code LIKE ? OR b.full_name LIKE ? OR p.product_name LIKE ?)';
    $like = '%' . $dt['q'] . '%';
    $params = array_merge($params, [$like, $like, $like, $like]);
}
if ($dt['status'] !== '' && in_array($dt['status'], ['draft', 'sent', 'accepted', 'rejected', 'expired'], true)) {
    $where[] = 'q.status = ?';
    $params[] = $dt['status'];
}
if ($dt['date_from'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dt['date_from'])) {
    $where[] = 'DATE(q.created_at) >= ?';
    $params[] = $dt['date_from'];
}
if ($dt['date_to'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dt['date_to'])) {
    $where[] = 'DATE(q.created_at) <= ?';
    $params[] = $dt['date_to'];
}
$whereSql = implode(' AND ', $where);
$sortMap = [
    'created_at' => 'q.created_at',
    'quote_code' => 'q.quote_code',
    'order_code' => 'o.order_code',
    'buyer_name' => 'b.full_name',
    'product_name' => 'p.product_name',
    'total_landed_cost' => 'q.total_landed_cost',
    'status' => 'q.status',
];
$orderSql = admin_dt_order_fragment($dt['sort'], $dt['dir'], $sortMap, 'q.created_at');
$cntSt = $pdo->prepare("SELECT COUNT(*) FROM quotations q JOIN orders o ON o.id=q.order_id JOIN products p ON p.id=o.product_id JOIN buyers b ON b.id=o.buyer_id WHERE $whereSql");
$cntSt->execute($params);
$total = (int) $cntSt->fetchColumn();
$page = admin_dt_clamp_page($dt['page'], $total, $dt['per_page']);
$offset = ($page - 1) * $dt['per_page'];
$lim = (int) $dt['per_page'];
$listSt = $pdo->prepare("SELECT q.*, o.order_code, p.product_name, b.full_name buyer_name FROM quotations q JOIN orders o ON o.id=q.order_id JOIN products p ON p.id=o.product_id JOIN buyers b ON b.id=o.buyer_id WHERE $whereSql ORDER BY $orderSql LIMIT $lim OFFSET $offset");
$listSt->execute($params);
$quotes = $listSt->fetchAll();
$dtPath = 'admin/quotations.php';
$adminPostAction = $dtPath . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');

$pageTitle = 'Admin — Quotations';
require __DIR__ . '/../includes/header.php';
$adminActive = 'quotations';
$adminPageTitle = 'Quotations';
require __DIR__ . '/../includes/admin_sidebar.php';
?>
<div class="admin-shell"><div class="admin-content">
<?php require __DIR__ . '/../includes/admin_topbar.php'; ?>
<main>
    <h1 class="h3 mb-3">Quotations</h1>
    <?php if ($m = flash_get('success')): ?>
        <div class="alert alert-success"><?= e($m) ?></div>
    <?php endif; ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-bold">Quote Builder</div>
        <div class="card-body">
            <form method="post" action="<?= e(app_url($adminPostAction)) ?>" class="row g-3">
                <div class="col-md-4"><label class="form-label">Order</label><select name="order_id" class="form-select"><?php foreach ($orders as $o): ?><option value="<?= (int) $o['id'] ?>"><?= e($o['order_code'] . ' | ' . $o['product_name'] . ' | ' . $o['buyer_name']) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-2"><label class="form-label">Product Cost</label><input class="form-control" type="number" step="0.01" name="product_cost" value="0"></div>
                <div class="col-md-2"><label class="form-label">China Local Shipping</label><input class="form-control" type="number" step="0.01" name="china_local_shipping" value="0"></div>
                <div class="col-md-2"><label class="form-label">Export Handling</label><input class="form-control" type="number" step="0.01" name="export_handling" value="0"></div>
                <div class="col-md-2"><label class="form-label">Freight</label><input class="form-control" type="number" step="0.01" name="freight_cost" value="0"></div>
                <div class="col-md-2"><label class="form-label">Insurance</label><input class="form-control" type="number" step="0.01" name="insurance_cost" value="0"></div>
                <div class="col-md-2"><label class="form-label">Clearing</label><input class="form-control" type="number" step="0.01" name="clearing_cost" value="0"></div>
                <div class="col-md-2"><label class="form-label">Local Delivery</label><input class="form-control" type="number" step="0.01" name="local_delivery_cost" value="0"></div>
                <div class="col-md-2"><label class="form-label">HANZO Margin</label><input class="form-control" type="number" step="0.01" name="hanzo_margin" value="0"></div>
                <div class="col-md-2"><label class="form-label">Status</label><select class="form-select" name="status"><option value="draft">draft</option><option value="sent">sent</option></select></div>
                <div class="col-md-2"><label class="form-label">Valid Until</label><input type="date" name="valid_until" class="form-control"></div>
                <div class="col-md-12"><button class="btn btn-hanzo-primary">Create Quotation</button></div>
            </form>
        </div>
    </div>

    <h2 class="h5 mb-2">Quotations</h2>
    <div class="admin-card p-3">
        <form method="get" class="row g-2 admin-dt-toolbar mb-3" action="<?= e(app_url($dtPath)) ?>">
            <div class="col-md-3"><label class="form-label small text-muted mb-0">Search</label><input type="text" name="q" class="form-control form-control-sm" value="<?= e($dt['q']) ?>" placeholder="Quote, order, buyer…"></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">Status</label><select name="status" class="form-select form-select-sm"><option value="">All</option><?php foreach (['draft','sent','accepted','rejected','expired'] as $s): ?><option value="<?= e($s) ?>" <?= $dt['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">From</label><input type="date" name="date_from" class="form-control form-control-sm" value="<?= e($dt['date_from']) ?>"></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">To</label><input type="date" name="date_to" class="form-control form-control-sm" value="<?= e($dt['date_to']) ?>"></div>
            <div class="col-md-1"><label class="form-label small text-muted mb-0">Per page</label><select name="per_page" class="form-select form-select-sm"><?php foreach ([10,15,25,50] as $pp): ?><option value="<?= $pp ?>" <?= (int)$dt['per_page']===$pp?'selected':'' ?>><?= $pp ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2 d-flex align-items-end"><input type="hidden" name="sort" value="<?= e($dt['sort']) ?>"><input type="hidden" name="dir" value="<?= e($dt['dir']) ?>"><button type="submit" class="btn btn-sm btn-hanzo-primary">Apply</button></div>
        </form>
        <div class="table-responsive">
        <table class="table table-sm mb-0" id="quotesTable">
            <thead class="table-light">
                <tr>
                    <?php admin_dt_sort_th('Quote', 'quote_code', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Order', 'order_code', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Buyer', 'buyer_name', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Product', 'product_name', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Total landed', 'total_landed_cost', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Status', 'status', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Created', 'created_at', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <th>Lifecycle</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quotes as $q): ?>
                    <tr>
                        <td><?= e($q['quote_code']) ?></td>
                        <td><?= e($q['order_code']) ?></td>
                        <td><?= e($q['buyer_name']) ?></td>
                        <td><?= e($q['product_name']) ?></td>
                        <td>US$<?= e(number_format((float) $q['total_landed_cost'], 2)) ?></td>
                        <td><span class="badge <?= e(quotation_status_badge_class((string) $q['status'])) ?>"><?= e(quotation_status_label((string) $q['status'])) ?></span></td>
                        <td class="small"><?= e(format_datetime((string) ($q['created_at'] ?? ''))) ?></td>
                        <td>
                            <?php if ($q['status'] === 'accepted'): ?>
                                <form method="post" action="<?= e(app_url($adminPostAction)) ?>">
                                    <input type="hidden" name="action" value="promote">
                                    <input type="hidden" name="id" value="<?= (int) $q['id'] ?>">
                                    <button class="btn btn-sm btn-hanzo-primary">Move to Production</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($quotes === []): ?><tr><td colspan="8" class="text-center text-muted py-4">No quotations match.</td></tr><?php endif; ?>
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

