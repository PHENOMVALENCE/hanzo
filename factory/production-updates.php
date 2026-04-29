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
require __DIR__ . '/../includes/factory_sidebar_start.php';
?>
<main class="hanzo-buyer-main-inner">
    <header class="hanzo-buyer-page-head">
        <h1 class="hanzo-buyer-page-title">Production updates</h1>
        <p class="text-muted small mb-0">Log milestones and optional photos; HANZO shares progress with buyers.</p>
    </header>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success border-0 shadow-sm"><?= e($m) ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data" class="row g-2 hanzo-buyer-form-card p-3 mb-4">
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
    <div class="table-responsive hanzo-buyer-table-wrap">
        <table class="table table-hover align-middle mb-0 hanzo-buyer-table">
            <thead><tr><th scope="col">Order</th><th scope="col">Status</th><th scope="col">Description</th><th scope="col">Photo</th><th scope="col">Update time</th></tr></thead>
            <tbody>
                <?php foreach ($updates as $u): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($u['order_code']) ?></td>
                        <td><span class="badge text-bg-secondary"><?= e($u['status_title']) ?></span></td>
                        <td class="small"><?= e((string) $u['description']) ?></td>
                        <td><?php if (!empty($u['photo'])): ?><a class="btn btn-sm btn-outline-primary" href="<?= e(app_url((string) $u['photo'])) ?>" target="_blank">View</a><?php else: ?><span class="text-muted">—</span><?php endif; ?></td>
                        <td class="small text-muted"><?= e(format_datetime((string) $u['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($updates === []): ?><tr><td colspan="5" class="text-center text-muted py-3">No production updates yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require __DIR__ . '/../includes/factory_sidebar_end.php'; ?>
<?php $footerMode = 'full'; require __DIR__ . '/../includes/footer.php'; ?>

