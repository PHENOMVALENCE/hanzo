<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'status') {
    $id = (int) ($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'pending';
    $allowed = ['pending', 'reviewing', 'quoted', 'closed', 'rejected'];
    if ($id > 0 && in_array($status, $allowed, true)) {
        $pdo->prepare('UPDATE product_requests SET status = ? WHERE id = ?')->execute([$status, $id]);
        flash_set('success', 'Request status updated.');
    }
    redirect('admin/requests.php');
}

$list = $pdo->query('SELECT r.*, u.full_name AS buyer_name, u.email AS buyer_email, p.name AS product_name, p.main_image
    FROM product_requests r
    JOIN users u ON u.id = r.user_id
    JOIN products p ON p.id = r.product_id
    ORDER BY r.created_at DESC')->fetchAll();

$pageTitle = 'Admin — Requests';
require __DIR__ . '/../includes/header.php';
$hideShopNav = true;
require __DIR__ . '/../includes/navbar.php';
?>

<main class="container-fluid px-3 px-sm-4 py-4">
    <h1 class="h3 mb-3">Buyer inquiries</h1>
    <?php if ($m = flash_get('success')): ?>
        <div class="alert alert-success"><?= e($m) ?></div>
    <?php endif; ?>

    <div class="table-responsive border rounded bg-white shadow-sm">
        <table class="table table-sm mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Buyer</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Delivery</th>
                    <th>Timeline</th>
                    <th>Target USD</th>
                    <th>File</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($list as $r): ?>
                    <tr>
                        <td><?= (int) $r['id'] ?></td>
                        <td>
                            <?= e($r['buyer_name']) ?>
                            <div class="small text-muted"><?= e($r['buyer_email']) ?></div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="<?= e(product_image_url($r['main_image'])) ?>" width="36" height="36" class="rounded border" style="object-fit:cover;" alt="">
                                <?= e($r['product_name']) ?>
                            </div>
                        </td>
                        <td><?= (int) $r['quantity'] ?></td>
                        <td class="small"><?= e($r['delivery_location']) ?></td>
                        <td class="small"><?= e($r['timeline']) ?></td>
                        <td><?= $r['target_price'] !== null ? e(number_format((float) $r['target_price'], 2)) : '—' ?></td>
                        <td>
                            <?php if (!empty($r['file_path'])): ?>
                                <a href="<?= e(app_url($r['file_path'])) ?>" target="_blank" rel="noopener">View</a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td><span class="badge bg-secondary"><?= e($r['status']) ?></span></td>
                        <td>
                            <form method="post" class="d-flex gap-1">
                                <input type="hidden" name="action" value="status">
                                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                <select name="status" class="form-select form-select-sm" style="width:130px;">
                                    <?php foreach (['pending', 'reviewing', 'quoted', 'closed', 'rejected'] as $s): ?>
                                        <option value="<?= $s ?>" <?= $r['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-sm btn-hanzo-primary">Save</button>
                            </form>
                        </td>
                    </tr>
                    <tr class="table-light">
                        <td colspan="10" class="small">
                            <strong>Notes:</strong> <?= $r['notes'] !== null && $r['notes'] !== '' ? nl2br(e($r['notes'])) : '<span class="text-muted">—</span>' ?>
                            <span class="text-muted ms-3"><?= e($r['created_at']) ?></span>
                            <a class="ms-3" href="<?= e(app_url('admin/quotations.php?request_id=' . (int) $r['id'])) ?>">Quotation</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php
$footerMode = 'slim';
require __DIR__ . '/../includes/footer.php';
