<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_buyer();

$buyerId = auth_id();
$prefillOrderId = (int) ($_GET['order_id'] ?? 0);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int) ($_POST['order_id'] ?? 0);
    $amount = (float) ($_POST['amount'] ?? 0);
    $paymentType = trim((string) ($_POST['payment_type'] ?? ''));
    $method = trim((string) ($_POST['method'] ?? ''));
    $reference = trim((string) ($_POST['reference'] ?? ''));

    $st = $pdo->prepare('SELECT id FROM orders WHERE id = ? AND buyer_id = ?');
    $st->execute([$orderId, $buyerId]);
    if (!$st->fetch()) {
        $errors[] = 'Invalid order selected.';
    }
    if ($amount <= 0) {
        $errors[] = 'Amount must be greater than 0.';
    }
    if ($method === '') {
        $errors[] = 'Payment method is required.';
    }

    $proofPath = null;
    if (isset($_FILES['proof_file']) && $_FILES['proof_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $up = validate_upload_image_doc($_FILES['proof_file']);
        if (!$up['ok']) {
            $errors = array_merge($errors, $up['errors']);
        } else {
            $proofPath = save_uploaded_file($_FILES['proof_file'], 'payments', $up['filename']);
            if ($proofPath === null) {
                $errors[] = 'Failed to upload payment proof.';
            }
        }
    }

    if ($errors === []) {
        $pdo->prepare('INSERT INTO payments (order_id, buyer_id, amount, payment_type, method, reference, proof_file, status) VALUES (?,?,?,?,?,?,?,"pending")')
            ->execute([$orderId, $buyerId, $amount, $paymentType !== '' ? $paymentType : null, $method, $reference !== '' ? $reference : null, $proofPath]);
        flash_set('success', 'Payment submitted. HANZO admin will verify it.');
        redirect('buyer/payments.php');
    }
}

$orders = $pdo->prepare('SELECT id, order_code, status FROM orders WHERE buyer_id = ? ORDER BY created_at DESC');
$orders->execute([$buyerId]);
$orderList = $orders->fetchAll();

$payments = $pdo->prepare('SELECT p.*, o.order_code FROM payments p JOIN orders o ON o.id = p.order_id WHERE p.buyer_id = ? ORDER BY p.created_at DESC');
$payments->execute([$buyerId]);
$paymentRows = $payments->fetchAll();

$pageTitle = 'Buyer Payments';
require __DIR__ . '/../includes/header.php';
$hideShopNav = false;
require __DIR__ . '/../includes/navbar.php';
require __DIR__ . '/../includes/buyer_sidebar_start.php';
?>
<main class="hanzo-buyer-main-inner">
    <h1 class="h3 mb-3">Payments</h1>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
    <?php foreach ($errors as $er): ?><div class="alert alert-danger"><?= e($er) ?></div><?php endforeach; ?>

    <form method="post" enctype="multipart/form-data" class="row g-2 bg-white hanzo-buyer-form-card p-3 mb-4">
        <div class="col-md-3">
            <select class="form-select" name="order_id" required>
                <option value="">Select order</option>
                <?php foreach ($orderList as $o): ?>
                    <option value="<?= (int) $o['id'] ?>" <?= ($prefillOrderId === (int) $o['id']) ? 'selected' : '' ?>>
                        <?= e($o['order_code']) ?> (<?= e($o['status']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2"><input class="form-control" type="number" step="0.01" name="amount" placeholder="Amount USD" required></div>
        <div class="col-md-2"><input class="form-control" name="payment_type" placeholder="Deposit/Full"></div>
        <div class="col-md-2"><input class="form-control" name="method" placeholder="Bank / Mobile Money" required></div>
        <div class="col-md-2"><input class="form-control" name="reference" placeholder="Reference"></div>
        <div class="col-md-1"><input type="file" class="form-control" name="proof_file" accept=".jpg,.jpeg,.png,.webp,.pdf"></div>
        <div class="col-md-12"><button class="btn btn-hanzo-primary">Submit Payment</button></div>
    </form>

    <div class="table-responsive hanzo-buyer-table-wrap">
        <table class="table table-hover align-middle mb-0 hanzo-buyer-table">
            <thead><tr><th scope="col">Order</th><th scope="col">Amount</th><th scope="col">Type</th><th scope="col">Method</th><th scope="col">Reference</th><th scope="col">Proof</th><th scope="col">Status</th><th scope="col">Date</th></tr></thead>
            <tbody>
                <?php foreach ($paymentRows as $p): ?>
                    <?php $pst = (string) $p['status']; ?>
                    <tr>
                        <td class="fw-semibold"><?= e($p['order_code']) ?></td>
                        <td class="fw-semibold text-nowrap text-hanzo-gold">US$<?= e(number_format((float) $p['amount'], 2)) ?></td>
                        <td><?= e((string) $p['payment_type']) ?></td>
                        <td><?= e((string) $p['method']) ?></td>
                        <td><?= e((string) $p['reference']) ?></td>
                        <td><?php if (!empty($p['proof_file'])): ?><a href="<?= e(app_url((string) $p['proof_file'])) ?>" target="_blank" class="btn btn-sm btn-outline-primary">View</a><?php else: ?><span class="text-muted">—</span><?php endif; ?></td>
                        <td><span class="badge <?= e(payment_status_badge_class($pst)) ?>"><?= e(payment_status_label($pst)) ?></span></td>
                        <td class="small text-muted"><?= e(format_datetime((string) $p['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($paymentRows === []): ?><tr><td colspan="8" class="text-center text-muted py-3">No payments submitted yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require __DIR__ . '/../includes/buyer_sidebar_end.php'; ?>
<?php $footerMode = 'full'; require __DIR__ . '/../includes/footer.php'; ?>

