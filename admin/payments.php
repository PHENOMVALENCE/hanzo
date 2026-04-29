<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

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
                $ptype = strtolower((string) ($p['payment_type'] ?? ''));
                $nextStatus = str_contains($ptype, 'full') ? 'shipped' : 'accepted';
                $pdo->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$nextStatus, (int) $p['order_id']]);
                $pdo->prepare('INSERT INTO shipping_updates (order_id, status_title, description, location, tracking_number, updated_by) VALUES (?,?,?,?,?,?)')
                    ->execute([(int) $p['order_id'], 'Payment verified', 'Payment verification completed by HANZO admin.', 'HANZO', null, auth_id()]);
            }
        }
        flash_set('success', 'Payment verification updated.');
    }
    redirect('admin/payments.php');
}

$payments = $pdo->query('SELECT py.*, o.order_code, b.full_name buyer_name
    FROM payments py 
    JOIN orders o ON o.id = py.order_id 
    JOIN buyers b ON b.id = py.buyer_id
    ORDER BY py.created_at DESC')->fetchAll();
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
        <div class="admin-table-tools mb-2">
            <input type="text" class="form-control form-control-sm" placeholder="Search payments..." data-admin-table-search="paymentsTable">
            <select class="form-select form-select-sm" style="max-width:170px;"><option>All statuses</option><option>pending</option><option>verified</option><option>rejected</option></select>
        </div>
        <div class="table-responsive">
        <table class="table mb-0" id="paymentsTable">
            <thead class="table-light"><tr><th data-sort>Order</th><th data-sort>Buyer</th><th data-sort>Amount</th><th data-sort>Type/Method</th><th data-sort>Reference</th><th>Proof</th><th data-sort>Status</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($payments as $p): ?>
                <tr>
                    <td><?= e($p['order_code']) ?></td>
                    <td><?= e($p['buyer_name']) ?></td>
                    <td>US$<?= e(number_format((float) $p['amount'], 2)) ?></td>
                    <td><?= e((string) $p['payment_type']) ?> / <?= e((string) $p['method']) ?></td>
                    <td><?= e((string) $p['reference']) ?></td>
                    <td><?php if (!empty($p['proof_file'])): ?><a href="<?= e(app_url((string) $p['proof_file'])) ?>" target="_blank">View</a><?php else: ?>-<?php endif; ?></td>
                    <td><span class="badge bg-secondary"><?= e($p['status']) ?></span></td>
                    <td>
                        <form method="post" class="d-flex gap-1">
                            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                            <select class="form-select form-select-sm" name="status">
                                <?php foreach (['pending','verified','rejected'] as $s): ?><option value="<?= $s ?>" <?= $p['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?>
                            </select>
                            <button class="btn btn-sm btn-hanzo-primary">Save</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($payments === []): ?><tr><td colspan="8" class="text-center text-muted py-3">No payments yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</main>
 </div></div>
<?php $footerMode = 'slim'; require __DIR__ . '/../includes/footer.php'; ?>


