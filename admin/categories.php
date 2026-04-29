<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? 'save');
    if ($action === 'delete') {
        $pdo->prepare('DELETE FROM categories WHERE id=?')->execute([(int) ($_POST['id'] ?? 0)]);
        flash_set('success', 'Category removed.');
    } else {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim((string) ($_POST['name'] ?? ''));
        $desc = trim((string) ($_POST['description'] ?? ''));
        $status = (string) ($_POST['status'] ?? 'active');
        $img = null;
        if ($id > 0) {
            $cur = $pdo->prepare('SELECT image FROM categories WHERE id=?');
            $cur->execute([$id]);
            $img = $cur->fetchColumn() ?: null;
        }
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $up = validate_upload_image_doc($_FILES['image']);
            if ($up['ok']) {
                $img = save_uploaded_file($_FILES['image'], 'categories', $up['filename']);
            }
        }
        if ($id > 0) {
            $pdo->prepare('UPDATE categories SET name=?, description=?, image=?, status=? WHERE id=?')
                ->execute([$name, $desc !== '' ? $desc : null, $img, $status, $id]);
        } else {
            $pdo->prepare('INSERT INTO categories (name, description, image, status) VALUES (?,?,?,?)')
                ->execute([$name, $desc !== '' ? $desc : null, $img, $status]);
        }
        flash_set('success', 'Category saved.');
    }
    redirect('admin/categories.php');
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editRow = null;
if ($editId > 0) {
    $st = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
    $st->execute([$editId]);
    $editRow = $st->fetch();
}

$list = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();

$pageTitle = 'Admin — Categories';
require __DIR__ . '/../includes/header.php';
$adminActive = 'categories';
$adminPageTitle = 'Categories';
require __DIR__ . '/../includes/admin_sidebar.php';
?>
<div class="admin-shell"><div class="admin-content">
<?php require __DIR__ . '/../includes/admin_topbar.php'; ?>
<main>
    <h1 class="h3 mb-3">Categories</h1>
    <?php if ($m = flash_get('success')): ?>
        <div class="alert alert-success"><?= e($m) ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-bold"><?= $editRow ? 'Edit category' : 'Add category' ?></div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data" class="row g-3">
                <input type="hidden" name="action" value="save">
                <?php if ($editRow): ?>
                    <input type="hidden" name="id" value="<?= (int) $editRow['id'] ?>">
                <?php endif; ?>
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" required value="<?= e((string) ($editRow['name'] ?? '')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" <?= (($editRow['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>active</option>
                        <option value="inactive" <?= (($editRow['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?= e((string) ($editRow['description'] ?? '')) ?></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-hanzo-primary"><?= $editRow ? 'Update' : 'Create' ?></button>
                    <?php if ($editRow): ?>
                        <a class="btn btn-outline-secondary" href="<?= e(app_url('admin/categories.php')) ?>">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-card p-3">
        <input type="text" class="form-control form-control-sm mb-2" placeholder="Search categories..." data-admin-table-search="categoriesTable">
        <div class="table-responsive">
        <table class="table mb-0 align-middle" id="categoriesTable">
            <thead class="table-light"><tr><th data-sort>ID</th><th>Image</th><th data-sort>Name</th><th>Description</th><th data-sort>Status</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($list as $c): ?>
                    <tr>
                        <td><?= (int) $c['id'] ?></td>
                        <td>
                            <?php if (!empty($c['image'])): ?>
                                <img src="<?= e(product_image_url($c['image'])) ?>" width="36" height="36" class="rounded border" style="object-fit:cover;" alt="">
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?= e($c['name']) ?></td>
                        <td class="small"><?= e((string) $c['description']) ?></td>
                        <td><?= e((string) $c['status']) ?></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('admin/categories.php?edit=' . (int) $c['id'])) ?>">Edit</a>
                            <form method="post" class="d-inline" onsubmit="return confirm('Delete this category?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</main>
 </div></div>

<?php
$footerMode = 'slim';
require __DIR__ . '/../includes/footer.php';
