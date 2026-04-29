<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_factory();
$factoryId = auth_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int) ($_POST['order_id'] ?? 0);
    $title = trim((string) ($_POST['status_title'] ?? 'Production update'));
    $desc = trim((string) ($_POST['description'] ?? ''));
    $photo = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $check = validate_upload_image_doc($_FILES['photo']);
        if ($check['ok']) {
            $photo = save_uploaded_file($_FILES['photo'], 'production', $check['filename']);
        }
    }
    if ($orderId > 0) {
        $pdo->prepare('INSERT INTO production_updates (order_id, factory_id, status_title, description, photo) VALUES (?,?,?,?,?)')
            ->execute([$orderId, $factoryId, $title, $desc !== '' ? $desc : null, $photo]);
        flash_set('success', 'Production update submitted.');
    }
    redirect('factory/production-updates.php');
}

$ordersSt = $pdo->prepare('SELECT id, order_code FROM orders WHERE factory_id = ? ORDER BY created_at DESC');
$ordersSt->execute([$factoryId]);
$orders = $ordersSt->fetchAll();

$st = $pdo->prepare('SELECT pu.*, o.order_code FROM production_updates pu JOIN orders o ON o.id = pu.order_id WHERE pu.factory_id = ? ORDER BY pu.created_at DESC');
$st->execute([$factoryId]);
$updates = $st->fetchAll();
$pageTitle = 'Production Updates';
require __DIR__ . '/../includes/header.php';
$hideShopNav = false;
require __DIR__ . '/../includes/navbar.php';
?>
<main class="container py-4">
    <h1 class="h3 mb-3">Production Updates</h1>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data" class="row g-2 bg-white border rounded p-3 mb-3">
        <div class="col-md-3">
            <select class="form-select" name="order_id" required>
                <option value="">Select order</option>
                <?php foreach ($orders as $o): ?><option value="<?= (int) $o['id'] ?>"><?= e($o['order_code']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3"><input class="form-control" name="status_title" placeholder="Status title"></div>
        <div class="col-md-4"><input class="form-control" name="description" placeholder="Production note"></div>
        <div class="col-md-2"><input type="file" name="photo" class="form-control" accept=".jpg,.jpeg,.png,.webp"></div>
        <div class="col-md-12"><button class="btn btn-hanzo-primary">Submit update</button></div>
    </form>
    <div class="table-responsive border rounded bg-white">
        <table class="table mb-0">
            <thead class="table-light"><tr><th>Order</th><th>Status</th><th>Description</th><th>Photo</th><th>Update Time</th></tr></thead>
            <tbody>
                <?php foreach ($updates as $u): ?>
                    <tr>
                        <td><?= e($u['order_code']) ?></td>
                        <td><span class="badge bg-secondary"><?= e($u['status_title']) ?></span></td>
                        <td><?= e((string) $u['description']) ?></td>
                        <td><?php if (!empty($u['photo'])): ?><a href="<?= e(app_url((string) $u['photo'])) ?>" target="_blank">View</a><?php else: ?><span class="text-muted">-</span><?php endif; ?></td>
                        <td class="small"><?= e($u['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($updates === []): ?><tr><td colspan="5" class="text-center text-muted py-3">No production updates yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php $footerMode = 'full'; require __DIR__ . '/../includes/footer.php'; ?>

