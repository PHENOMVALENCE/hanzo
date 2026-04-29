<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_buyer();

$buyerId = auth_id();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qid = (int) ($_POST['quotation_id'] ?? 0);
    $decision = (string) ($_POST['decision'] ?? '');
    if ($qid > 0 && in_array($decision, ['accepted', 'rejected'], true)) {
        $st = $pdo->prepare('SELECT q.id, q.order_id FROM quotations q JOIN orders o ON o.id = q.order_id WHERE q.id = ? AND o.buyer_id = ?');
        $st->execute([$qid, $buyerId]);
        $q = $st->fetch();
        if ($q) {
            $pdo->prepare('UPDATE quotations SET status = ? WHERE id = ?')->execute([$decision, $qid]);
            $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute([$decision === 'accepted' ? 'accepted' : 'cancelled', (int) $q['order_id']]);
            if ($decision === 'accepted') {
                $pdo->prepare('INSERT INTO shipping_updates (order_id, status_title, description, location, tracking_number, updated_by) VALUES (?,?,?,?,?,?)')
                    ->execute([(int) $q['order_id'], 'Quotation accepted', 'Buyer accepted official HANZO quotation.', 'HANZO', null, $buyerId]);
            }
            flash_set('success', 'Quotation decision recorded.');
        }
    }
    redirect('buyer/quotations.php');
}

$st = $pdo->prepare('SELECT q.*, o.order_code, p.product_name 
    FROM quotations q 
    JOIN orders o ON o.id = q.order_id 
    JOIN products p ON p.id = o.product_id
    WHERE o.buyer_id = ? 
    ORDER BY q.created_at DESC');
$st->execute([$buyerId]);
$quotes = $st->fetchAll();

$pageTitle = 'My Quotations';
require __DIR__ . '/../includes/header.php';
$hideShopNav = false;
require __DIR__ . '/../includes/navbar.php';
?>
<main class="container py-4">
    <h1 class="h3 mb-3">Official Quotations</h1>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
    <div class="table-responsive border rounded bg-white">
        <table class="table mb-0">
            <thead class="table-light"><tr><th>Quote</th><th>Order</th><th>Product</th><th>Total</th><th>Status</th><th>Valid until</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($quotes as $q): ?>
                    <tr>
                        <td><?= e($q['quote_code']) ?></td>
                        <td><?= e($q['order_code']) ?></td>
                        <td><?= e($q['product_name']) ?></td>
                        <td>US$<?= e(number_format((float) $q['total_landed_cost'], 2)) ?></td>
                        <td><span class="badge bg-secondary"><?= e($q['status']) ?></span></td>
                        <td><?= e((string) $q['valid_until']) ?></td>
                        <td>
                            <?php if ($q['status'] === 'sent'): ?>
                                <form method="post" class="d-flex gap-1">
                                    <input type="hidden" name="quotation_id" value="<?= (int) $q['id'] ?>">
                                    <button name="decision" value="accepted" class="btn btn-sm btn-success">Accept</button>
                                    <button name="decision" value="rejected" class="btn btn-sm btn-outline-danger">Reject</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($quotes === []): ?><tr><td colspan="7" class="text-center text-muted py-3">No quotations yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php $footerMode = 'full'; require __DIR__ . '/../includes/footer.php'; ?>

