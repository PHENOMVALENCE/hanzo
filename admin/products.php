<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$categories = $pdo->query('SELECT id, name FROM categories WHERE status="active" ORDER BY name')->fetchAll();
$factories = $pdo->query('SELECT id, factory_name FROM factories WHERE status="active" ORDER BY factory_name')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'delete') {
        $pdo->prepare('DELETE FROM products WHERE id=?')->execute([(int) ($_POST['id'] ?? 0)]);
        flash_set('success', 'Product deleted.');
    } else {
        $id = (int) ($_POST['id'] ?? 0);
        $img = null;
        if ($id > 0) {
            $cur = $pdo->prepare('SELECT main_image FROM products WHERE id=?');
            $cur->execute([$id]);
            $img = $cur->fetchColumn() ?: null;
        }
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $up = validate_upload_image_doc($_FILES['main_image']);
            if ($up['ok']) {
                $img = save_uploaded_file($_FILES['main_image'], 'products', $up['filename']);
            }
        }
        $payload = [
            (int) $_POST['factory_id'],
            (int) $_POST['category_id'],
            trim((string) $_POST['product_name']),
            trim((string) $_POST['description']),
            max(1, (int) $_POST['moq']),
            (float) $_POST['min_price'],
            (float) $_POST['max_price'],
            $img,
            (string) $_POST['status'],
        ];
        if ($id > 0) {
            $payload[] = $id;
            $pdo->prepare('UPDATE products SET factory_id=?, category_id=?, product_name=?, description=?, moq=?, min_price=?, max_price=?, main_image=?, status=? WHERE id=?')->execute($payload);
        } else {
            $pdo->prepare('INSERT INTO products (factory_id, category_id, product_name, description, moq, min_price, max_price, main_image, status) VALUES (?,?,?,?,?,?,?,?,?)')->execute($payload);
        }
        flash_set('success', 'Product saved.');
    }
    redirect('admin/products.php');
}

$edit = null;
if (isset($_GET['edit'])) {
    $st = $pdo->prepare('SELECT * FROM products WHERE id=?');
    $st->execute([(int) $_GET['edit']]);
    $edit = $st->fetch();
}
$list = $pdo->query('SELECT p.*, c.name category_name, f.factory_name FROM products p JOIN categories c ON c.id=p.category_id LEFT JOIN factories f ON f.id=p.factory_id ORDER BY p.created_at DESC')->fetchAll();

$pageTitle = 'Admin Products';
require __DIR__ . '/../includes/header.php';
$adminActive = 'products';
$adminPageTitle = 'Products';
require __DIR__ . '/../includes/admin_sidebar.php';
?>
<div class="admin-shell"><div class="admin-content">
<?php require __DIR__ . '/../includes/admin_topbar.php'; ?>
<main>
    <h1 class="h3 mb-3">Products Management</h1>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data" class="row g-2 bg-white border rounded p-3 mb-3">
        <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
        <div class="col-md-3"><input class="form-control" name="product_name" placeholder="Product" required value="<?= e((string) ($edit['product_name'] ?? '')) ?>"></div>
        <div class="col-md-2"><select class="form-select" name="factory_id"><?php foreach ($factories as $f): ?><option value="<?= (int) $f['id'] ?>" <?= (int) ($edit['factory_id'] ?? 0) === (int) $f['id'] ? 'selected' : '' ?>><?= e($f['factory_name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><select class="form-select" name="category_id"><?php foreach ($categories as $c): ?><option value="<?= (int) $c['id'] ?>" <?= (int) ($edit['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-1"><input class="form-control" name="moq" type="number" min="1" value="<?= e((string) ($edit['moq'] ?? 1)) ?>"></div>
        <div class="col-md-1"><input class="form-control" name="min_price" type="number" step="0.01" value="<?= e((string) ($edit['min_price'] ?? 0)) ?>"></div>
        <div class="col-md-1"><input class="form-control" name="max_price" type="number" step="0.01" value="<?= e((string) ($edit['max_price'] ?? 0)) ?>"></div>
        <div class="col-md-2"><select class="form-select" name="status"><option value="active">active</option><option value="draft" <?= (($edit['status'] ?? '') === 'draft') ? 'selected' : '' ?>>draft</option><option value="archived" <?= (($edit['status'] ?? '') === 'archived') ? 'selected' : '' ?>>archived</option></select></div>
        <div class="col-md-10"><textarea class="form-control" name="description" rows="2" placeholder="Description"><?= e((string) ($edit['description'] ?? '')) ?></textarea></div>
        <div class="col-md-2"><input type="file" class="form-control" name="main_image" accept=".jpg,.jpeg,.png,.webp"></div>
        <div class="col-md-12"><button class="btn btn-hanzo-primary">Save Product</button></div>
    </form>
    <div class="admin-card p-3">
        <div class="admin-table-tools mb-2">
            <input type="text" class="form-control form-control-sm" placeholder="Search products..." data-admin-table-search="productsTable">
            <select class="form-select form-select-sm" style="max-width:170px;"><option>All statuses</option><option>active</option><option>draft</option><option>archived</option></select>
        </div>
        <div class="table-responsive">
        <table class="table mb-0" id="productsTable">
            <thead class="table-light"><tr><th data-sort>Product</th><th data-sort>Factory</th><th data-sort>Category</th><th data-sort>MOQ</th><th data-sort>Price Range</th><th data-sort>Status</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($list as $p): ?>
                <tr>
                    <td><?= e($p['product_name']) ?></td><td><?= e((string) $p['factory_name']) ?></td><td><?= e($p['category_name']) ?></td>
                    <td><?= (int) $p['moq'] ?></td><td>US$<?= e(number_format((float) $p['min_price'], 2)) ?> - <?= e(number_format((float) $p['max_price'], 2)) ?></td>
                    <td><?= e($p['status']) ?></td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('admin/products.php?edit=' . (int) $p['id'])) ?>">Edit</a>
                        <form method="post" class="d-inline"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $p['id'] ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</main>
 </div></div>
<?php $footerMode = 'slim'; require __DIR__ . '/../includes/footer.php'; ?>

