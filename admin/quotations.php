<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();

$total = static fn(array $d): float => (float) $d['product_cost'] + (float) $d['china_local_shipping'] + (float) $d['export_handling'] + (float) $d['freight_cost'] + (float) $d['insurance_cost'] + (float) $d['clearing_cost'] + (float) $d['local_delivery_cost'] + (float) $d['hanzo_margin'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'promote') {
        $qid = (int) ($_POST['id'] ?? 0);
        $st = $pdo->prepare('SELECT q.id, q.status, q.order_id FROM quotations q WHERE q.id = ?');
        $st->execute([$qid]);
        $q = $st->fetch();
        if ($q && $q['status'] === 'accepted') {
            $pdo->prepare('UPDATE orders SET status="in_production" WHERE id=?')->execute([(int) $q['order_id']]);
            $pdo->prepare('INSERT INTO shipping_updates (order_id, status_title, description, location, updated_by) VALUES (?,?,?,?,?)')
                ->execute([(int) $q['order_id'], 'Production started', 'Order moved to production workflow by admin.', 'Factory', auth_id()]);
            flash_set('success', 'Accepted quotation promoted to active production.');
        }
        redirect('admin/quotations.php');
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
    $pdo->prepare('UPDATE orders SET status=? WHERE id=(SELECT order_id FROM quotations WHERE id=?)')->execute([$status === 'sent' ? 'quoted' : 'assigned', $qid]);
    flash_set('success', 'Quotation saved.');
    redirect('admin/quotations.php');
}

$orders = $pdo->query('SELECT o.id, o.order_code, p.product_name, b.full_name buyer_name FROM orders o JOIN products p ON p.id=o.product_id JOIN buyers b ON b.id=o.buyer_id WHERE o.status IN ("pending","assigned","quoted","accepted") ORDER BY o.created_at DESC')->fetchAll();
$quotes = $pdo->query('SELECT q.*, o.order_code, p.product_name, b.full_name buyer_name FROM quotations q JOIN orders o ON o.id=q.order_id JOIN products p ON p.id=o.product_id JOIN buyers b ON b.id=o.buyer_id ORDER BY q.created_at DESC')->fetchAll();

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
            <form method="post" class="row g-3">
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

    <h2 class="h5 mb-2">Recent quotations</h2>
    <div class="admin-card p-3">
        <input type="text" class="form-control form-control-sm mb-2" placeholder="Search quotations..." data-admin-table-search="quotesTable">
        <div class="table-responsive">
        <table class="table table-sm mb-0" id="quotesTable">
            <thead class="table-light"><tr><th data-sort>Quote</th><th data-sort>Order</th><th data-sort>Buyer</th><th data-sort>Product</th><th data-sort>Total landed</th><th data-sort>Status</th><th>Lifecycle</th></tr></thead>
            <tbody>
                <?php foreach ($quotes as $q): ?>
                    <tr>
                        <td><?= e($q['quote_code']) ?></td>
                        <td><?= e($q['order_code']) ?></td>
                        <td><?= e($q['buyer_name']) ?></td>
                        <td><?= e($q['product_name']) ?></td>
                        <td>US$<?= e(number_format((float) $q['total_landed_cost'], 2)) ?></td>
                        <td><span class="badge bg-secondary"><?= e($q['status']) ?></span></td>
                        <td>
                            <?php if ($q['status'] === 'accepted'): ?>
                                <form method="post">
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
            </tbody>
        </table>
        </div>
    </div>
</main>
 </div></div>

<?php
$footerMode = 'slim';
require __DIR__ . '/../includes/footer.php';

