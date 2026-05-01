<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/order_finance.php';
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
    $currency = strtoupper(trim((string) ($_POST['currency'] ?? 'USD')));
    if (!in_array($currency, ['USD', 'TZS'], true)) {
        $currency = 'USD';
    }

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
        $pdo->prepare('INSERT INTO payments (order_id, buyer_id, amount, currency, payment_type, method, reference, proof_file, status) VALUES (?,?,?,?,?,?,?,?,?)')
            ->execute([$orderId, $buyerId, $amount, $currency, $paymentType !== '' ? $paymentType : null, $method, $reference !== '' ? $reference : null, $proofPath, 'pending']);
        flash_set('success', 'Payment submitted. China Chapu admin will verify it.');
        redirect('buyer/payments.php');
    }
}

$orders = $pdo->prepare('SELECT id, order_code, status FROM orders WHERE buyer_id = ? ORDER BY created_at DESC');
$orders->execute([$buyerId]);
$orderList = $orders->fetchAll();
$orderBalanceMap = hanzo_order_balance_map($pdo, array_map(static fn (array $row): int => (int) $row['id'], $orderList));
$paymentDueAfterMap = hanzo_buyer_payment_due_after_map($pdo, $buyerId);

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
        <p class="text-muted small mb-0">Record a transfer against an open order. China Chapu verifies proof before your order status updates. Agreed totals come from your accepted quotation; balance due uses verified payments only (USD + TZS converted at indicative rates).</p>
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
                            <?php
                            $oidOpt = (int) $o['id'];
                            $ob = $orderBalanceMap[$oidOpt] ?? ['agreed_usd' => null, 'paid_usd' => 0.0, 'due_usd' => null];
                            $optSuffix = '';
                            if ($ob['due_usd'] !== null) {
                                $optSuffix = ' · Due ' . hanzo_format_order_money_usd($ob['due_usd']);
                            } elseif ($ob['agreed_usd'] === null) {
                                $optSuffix = ' · No accepted quote yet';
                            }
                            ?>
                            <option value="<?= $oidOpt ?>" <?= ($prefillOrderId === $oidOpt) ? 'selected' : '' ?>>
                                <?= e($o['order_code']) ?> — <?= e(order_status_label((string) $o['status'])) ?><?= e($optSuffix) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <label for="pay-amount" class="form-label">Amount <span class="text-danger" aria-hidden="true">*</span></label>
                    <div class="input-group">
                        <select class="form-select flex-shrink-0" id="pay-currency" name="currency" style="max-width: 5.5rem;" aria-label="Payment currency">
                            <?php
                            $curPost = strtoupper(trim((string) ($_POST['currency'] ?? 'USD')));
                            if (!in_array($curPost, ['USD', 'TZS'], true)) {
                                $curPost = 'USD';
                            }
                            ?>
                            <option value="USD" <?= $curPost === 'USD' ? 'selected' : '' ?>>USD</option>
                            <option value="TZS" <?= $curPost === 'TZS' ? 'selected' : '' ?>>TZS</option>
                        </select>
                        <input class="form-control" id="pay-amount" type="number" step="0.01" min="0.01" name="amount" inputmode="decimal" placeholder="0.00" value="<?= isset($_POST['amount']) ? e((string) $_POST['amount']) : '' ?>" required>
                    </div>
                    <div class="form-text">Choose whether the amount you paid is in US dollars or Tanzanian shillings.</div>
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
            <thead><tr><th scope="col">Order</th><th scope="col">Amount</th><th scope="col">Type</th><th scope="col">Method</th><th scope="col">Reference</th><th scope="col">Proof</th><th scope="col">Status</th><th scope="col">Balance after <span class="text-muted fw-normal">(USD est.)</span></th><th scope="col">Date</th></tr></thead>
            <tbody>
                <?php foreach ($paymentRows as $p): ?>
                    <?php
                    $pst = (string) $p['status'];
                    $pid = (int) $p['id'];
                    $dueAfter = ($pst === 'verified') ? ($paymentDueAfterMap[$pid] ?? null) : null;
                    ?>
                    <tr>
                        <td class="fw-semibold"><?= e($p['order_code']) ?></td>
                        <td class="fw-semibold text-nowrap text-hanzo-gold"><?= e(format_payment_amount_display((float) $p['amount'], isset($p['currency']) ? (string) $p['currency'] : null)) ?></td>
                        <td><?= e((string) $p['payment_type']) ?></td>
                        <td><?= e((string) $p['method']) ?></td>
                        <td><?= e((string) $p['reference']) ?></td>
                        <td><?php if (!empty($p['proof_file'])): ?><a href="<?= e(app_url((string) $p['proof_file'])) ?>" target="_blank" class="btn btn-sm btn-outline-primary">View</a><?php else: ?><span class="text-muted">—</span><?php endif; ?></td>
                        <td><span class="badge <?= e(payment_status_badge_class($pst)) ?>"><?= e(payment_status_label($pst)) ?></span></td>
                        <td class="small text-nowrap"><?php if ($pst === 'verified'): ?><?= e(hanzo_format_order_money_usd($dueAfter)) ?><?php else: ?><span class="text-muted">—</span><?php endif; ?></td>
                        <td class="small text-muted"><?= e(format_datetime((string) $p['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($paymentRows === []): ?><tr><td colspan="9" class="text-center text-muted py-3">No payments submitted yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require __DIR__ . '/../includes/buyer_sidebar_end.php'; ?>
<?php $footerMode = 'full'; require __DIR__ . '/../includes/footer.php'; ?>

