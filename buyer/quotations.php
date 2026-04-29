<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/buyer_notifications.php';
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
            $oidQ = (int) $q['order_id'];
            $stOs = $pdo->prepare('SELECT status FROM orders WHERE id = ?');
            $stOs->execute([$oidQ]);
            $prevOs = (string) ($stOs->fetchColumn() ?: '');
            $pdo->prepare('UPDATE quotations SET status = ? WHERE id = ?')->execute([$decision, $qid]);
            $newOs = $decision === 'accepted' ? 'accepted' : 'cancelled';
            $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute([$newOs, $oidQ]);
            buyer_notify_order_status_changed($pdo, $oidQ, $prevOs, $newOs);
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
require __DIR__ . '/../includes/buyer_sidebar_start.php';
?>
<main class="hanzo-buyer-main-inner">
    <header class="hanzo-buyer-page-head">
        <h1 class="hanzo-buyer-page-title">Official quotations</h1>
        <p class="text-muted small mb-0">Review landed-cost quotes from HANZO and accept or reject before they expire.</p>
    </header>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success border-0 shadow-sm"><?= e($m) ?></div><?php endif; ?>
    <div class="table-responsive hanzo-buyer-table-wrap">
        <table class="table table-hover align-middle mb-0 hanzo-buyer-table">
            <thead><tr><th scope="col">Quote</th><th scope="col">Order</th><th scope="col">Product</th><th scope="col">Total</th><th scope="col">Status</th><th scope="col">Valid until</th><th scope="col">Action</th></tr></thead>
            <tbody>
                <?php foreach ($quotes as $q): ?>
                    <?php $qst = (string) $q['status']; ?>
                    <tr>
                        <td class="fw-semibold text-nowrap"><?= e($q['quote_code']) ?></td>
                        <td class="text-nowrap"><?= e($q['order_code']) ?></td>
                        <td><?= e($q['product_name']) ?></td>
                        <td class="fw-semibold text-nowrap text-hanzo-gold">US$<?= e(number_format((float) $q['total_landed_cost'], 2)) ?></td>
                        <td><span class="badge <?= e(quotation_status_badge_class($qst)) ?>"><?= e(quotation_status_label($qst)) ?></span></td>
                        <td class="text-muted small text-nowrap"><?= e(format_date((string) $q['valid_until'])) ?></td>
                        <td>
                            <?php if ($q['status'] === 'sent'): ?>
                                <form method="post" class="d-flex flex-wrap gap-1 hanzo-buyer-inline-actions">
                                    <input type="hidden" name="quotation_id" value="<?= (int) $q['id'] ?>">
                                    <button type="submit" name="decision" value="accepted" class="btn btn-sm btn-hanzo-primary">Accept</button>
                                    <button type="submit" name="decision" value="rejected" class="btn btn-sm btn-outline-danger">Reject</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($quotes === []): ?><tr><td colspan="7" class="text-center text-muted py-5">No quotations yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require __DIR__ . '/../includes/buyer_sidebar_end.php'; ?>
<?php $footerMode = 'full'; require __DIR__ . '/../includes/footer.php'; ?>

