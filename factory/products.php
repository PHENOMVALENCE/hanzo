<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_factory();

$factoryId = auth_id();
$cats = $pdo->query('SELECT id, name FROM categories WHERE status="active" ORDER BY name')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save') {
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim((string) ($_POST['product_name'] ?? ''));
    $category = (int) ($_POST['category_id'] ?? 0);
    $desc = trim((string) ($_POST['description'] ?? ''));
    $moq = max(1, (int) ($_POST['moq'] ?? 1));
    $min = (float) ($_POST['min_price'] ?? 0);
    $max = (float) ($_POST['max_price'] ?? 0);
    $status = (string) ($_POST['status'] ?? 'active');
    $img = null;
    if ($id > 0) {
        $cur = $pdo->prepare('SELECT main_image FROM products WHERE id = ? AND factory_id = ?');
        $cur->execute([$id, $factoryId]);
        $img = $cur->fetchColumn() ?: null;
    }
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $check = validate_upload_image_doc($_FILES['main_image']);
        if ($check['ok']) {
            $img = save_uploaded_file($_FILES['main_image'], 'products', $check['filename']);
        }
    }
    if ($id > 0) {
        $pdo->prepare('UPDATE products SET category_id=?, product_name=?, description=?, moq=?, min_price=?, max_price=?, status=?, main_image=? WHERE id=? AND factory_id=?')
            ->execute([$category, $name, $desc, $moq, $min, $max, $status, $img, $id, $factoryId]);
    } else {
        $pdo->prepare('INSERT INTO products (factory_id, category_id, product_name, description, moq, min_price, max_price, status, main_image) VALUES (?,?,?,?,?,?,?,?,?)')
            ->execute([$factoryId, $category, $name, $desc, $moq, $min, $max, $status, $img]);
    }
    flash_set('success', 'Product saved.');
    redirect('factory/products.php');
}

$edit = null;
if (isset($_GET['edit'])) {
    $st = $pdo->prepare('SELECT * FROM products WHERE id = ? AND factory_id = ?');
    $st->execute([(int) $_GET['edit'], $factoryId]);
    $edit = $st->fetch();
}
$st = $pdo->prepare('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE p.factory_id = ? ORDER BY p.created_at DESC');
$st->execute([$factoryId]);
$products = $st->fetchAll();
$pageTitle = 'Factory Products';
require __DIR__ . '/../includes/header.php';
$hideShopNav = false;
require __DIR__ . '/../includes/navbar.php';
?>
<main class="container-fluid px-4 py-4">
    <h1 class="h4 mb-3">My Products</h1>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
    <form class="row g-2 mb-4 border rounded bg-white p-3" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
        <div class="col-md-4"><input class="form-control" name="product_name" placeholder="Product name" required value="<?= e((string) ($edit['product_name'] ?? '')) ?>"></div>
        <div class="col-md-3"><select class="form-select" name="category_id"><?php foreach ($cats as $c): ?><option value="<?= (int) $c['id'] ?>" <?= (int) ($edit['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-1"><input class="form-control" type="number" name="moq" min="1" value="<?= e((string) ($edit['moq'] ?? 1)) ?>"></div>
        <div class="col-md-2"><input class="form-control" step="0.01" type="number" name="min_price" placeholder="Min" value="<?= e((string) ($edit['min_price'] ?? '')) ?>"></div>
        <div class="col-md-2"><input class="form-control" step="0.01" type="number" name="max_price" placeholder="Max" value="<?= e((string) ($edit['max_price'] ?? '')) ?>"></div>
        <div class="col-md-8"><textarea class="form-control" name="description" rows="2" placeholder="Description"><?= e((string) ($edit['description'] ?? '')) ?></textarea></div>
        <div class="col-md-2"><select class="form-select" name="status"><option value="active">active</option><option value="draft" <?= (($edit['status'] ?? '') === 'draft') ? 'selected' : '' ?>>draft</option><option value="archived" <?= (($edit['status'] ?? '') === 'archived') ? 'selected' : '' ?>>archived</option></select></div>
        <div class="col-md-2"><input type="file" class="form-control" name="main_image" accept=".jpg,.jpeg,.png,.webp"></div>
        <div class="col-md-12"><button class="btn btn-hanzo-primary">Save Product</button></div>
    </form>
    <div class="table-responsive border rounded bg-white">
        <table class="table mb-0">
            <thead class="table-light"><tr><th>Image</th><th>Name</th><th>Category</th><th>MOQ</th><th>Range</th><th>Status</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td><img src="<?= e(product_image_url($p['main_image'])) ?>" width="40" height="40" class="rounded border"></td>
                        <td><?= e($p['product_name']) ?></td>
                        <td><?= e($p['category_name']) ?></td>
                        <td><?= (int) $p['moq'] ?></td>
                        <td>US$<?= e(number_format((float) $p['min_price'], 2)) ?> - <?= e(number_format((float) $p['max_price'], 2)) ?></td>
                        <td><?= e($p['status']) ?></td>
                        <td><a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('factory/products.php?edit=' . (int) $p['id'])) ?>">Edit</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($products === []): ?><tr><td colspan="7" class="text-center text-muted py-3">No products.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php $footerMode = 'full'; require __DIR__ . '/../includes/footer.php'; ?>

