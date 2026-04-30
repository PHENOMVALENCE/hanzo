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
        flash_set('success', 'Payment submitted. China Chapu admin will verify it.');
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
    <header class="hanzo-buyer-page-head mb-4">
        <h1 class="hanzo-buyer-page-title">Payments</h1>
        <p class="text-muted small mb-0">Record a transfer against an open order. China Chapu verifies proof before your order status updates.</p>
    </header>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success border-0 shadow-sm"><?= e($m) ?></div><?php endif; ?>
    <?php foreach ($errors as $er): ?><div class="alert alert-danger border-0 shadow-sm"><?= e($er) ?></div><?php endforeach; ?>

    <div class="hanzo-buyer-form-card bg-white mb-4 overflow-hidden">
        <div class="px-3 px-md-4 py-3 border-bottom bg-light bg-opacity-50">
            <h2 class="h6 mb-0 fw-semibold text-dark">Submit payment</h2>
            <p class="small text-muted mb-0 mt-1">All fields marked with <span class="text-danger">*</span> are required.</p>
        </div>
        <form method="post" enctype="multipart/form-data" class="p-3 p-md-4">
            <div class="row g-3">
                <div class="col-12 col-xl-6">
                    <label for="pay-order" class="form-label">Order <span class="text-danger" aria-hidden="true">*</span></label>
                    <select class="form-select" id="pay-order" name="order_id" required>
                        <option value=""><?= $orderList === [] ? 'No orders yet' : 'Choose an order…' ?></option>
                        <?php foreach ($orderList as $o): ?>
                            <option value="<?= (int) $o['id'] ?>" <?= ($prefillOrderId === (int) $o['id']) ? 'selected' : '' ?>>
                                <?= e($o['order_code']) ?> — <?= e(order_status_label((string) $o['status'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <label for="pay-amount" class="form-label">Amount <span class="text-danger" aria-hidden="true">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">US$</span>
                        <input class="form-control" id="pay-amount" type="number" step="0.01" min="0.01" name="amount" inputmode="decimal" placeholder="0.00" required>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <label for="pay-type" class="form-label">Payment type</label>
                    <input class="form-control" id="pay-type" name="payment_type" placeholder="e.g. Deposit, Full balance" autocomplete="off">
                    <div class="form-text">Optional — helps operations match your invoice.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label for="pay-method" class="form-label">Method <span class="text-danger" aria-hidden="true">*</span></label>
                    <input class="form-control" id="pay-method" name="method" placeholder="Bank transfer, mobile money, card…" required autocomplete="off">
                </div>
                <div class="col-12 col-md-6">
                    <label for="pay-ref" class="form-label">Reference / transaction ID</label>
                    <input class="form-control" id="pay-ref" name="reference" placeholder="Bank ref, M-Pesa code, etc." autocomplete="off">
                </div>
                <div class="col-12">
                    <label for="pay-proof" class="form-label">Proof of payment <span class="text-muted fw-normal">(optional)</span></label>
                    <input type="file" class="form-control hanzo-buyer-file-input" id="pay-proof" name="proof_file" accept=".jpg,.jpeg,.png,.webp,.pdf" aria-describedby="pay-proof-help">
                    <div id="pay-proof-help" class="form-text">JPG, PNG, WebP, or PDF — max 5 MB.</div>
                </div>
            </div>
            <div class="d-flex flex-column flex-sm-row flex-wrap align-items-stretch align-items-sm-center justify-content-between gap-3 mt-4 pt-3 border-top">
                <p class="small text-muted mb-0" style="max-width: 28rem;">After you submit, status stays <strong>Pending</strong> until an administrator marks the payment verified.</p>
                <button type="submit" class="btn btn-hanzo-primary btn-lg px-4 align-self-stretch align-self-sm-auto">Submit payment</button>
            </div>
        </form>
    </div>

    <h2 class="h6 text-uppercase text-muted fw-semibold letter-spacing-tight mb-3">Your payment history</h2>
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

