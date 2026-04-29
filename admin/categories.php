<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/admin_datatable.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();

$qs = $_SERVER['QUERY_STRING'] ?? '';
$selfQs = $qs !== '' ? '?' . $qs : '';

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
    redirect('admin/categories.php' . $selfQs);
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editRow = null;
if ($editId > 0) {
    $st = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
    $st->execute([$editId]);
    $editRow = $st->fetch();
}

$dt = admin_dt_params(15);
$where = ['1=1'];
$params = [];
if ($dt['q'] !== '') {
    $where[] = '(c.name LIKE ? OR IFNULL(c.description,"") LIKE ?)';
    $like = '%' . $dt['q'] . '%';
    $params[] = $like;
    $params[] = $like;
}
if ($dt['status'] !== '' && in_array($dt['status'], ['active', 'inactive'], true)) {
    $where[] = 'c.status = ?';
    $params[] = $dt['status'];
}
if ($dt['date_from'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dt['date_from'])) {
    $where[] = 'DATE(c.created_at) >= ?';
    $params[] = $dt['date_from'];
}
if ($dt['date_to'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dt['date_to'])) {
    $where[] = 'DATE(c.created_at) <= ?';
    $params[] = $dt['date_to'];
}
$whereSql = implode(' AND ', $where);
$sortMap = [
    'id' => 'c.id',
    'name' => 'c.name',
    'status' => 'c.status',
    'created_at' => 'c.created_at',
];
$orderSql = admin_dt_order_fragment($dt['sort'], $dt['dir'], $sortMap, 'c.name');
$cntSt = $pdo->prepare("SELECT COUNT(*) FROM categories c WHERE $whereSql");
$cntSt->execute($params);
$total = (int) $cntSt->fetchColumn();
$page = admin_dt_clamp_page($dt['page'], $total, $dt['per_page']);
$offset = ($page - 1) * $dt['per_page'];
$lim = (int) $dt['per_page'];
$listSt = $pdo->prepare("SELECT c.* FROM categories c WHERE $whereSql ORDER BY $orderSql LIMIT $lim OFFSET $offset");
$listSt->execute($params);
$list = $listSt->fetchAll();
$dtPath = 'admin/categories.php';
$adminPostAction = $dtPath . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');

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
            <form method="post" action="<?= e(app_url($adminPostAction)) ?>" enctype="multipart/form-data" class="row g-3">
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
        <form method="get" class="row g-2 admin-dt-toolbar mb-3" action="<?= e(app_url($dtPath)) ?>">
            <?php if ($editId > 0): ?><input type="hidden" name="edit" value="<?= $editId ?>"><?php endif; ?>
            <div class="col-md-3"><label class="form-label small text-muted mb-0">Search</label><input type="text" name="q" class="form-control form-control-sm" value="<?= e($dt['q']) ?>" placeholder="Name or description…"></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">Status</label><select name="status" class="form-select form-select-sm"><option value="">All</option><option value="active" <?= $dt['status'] === 'active' ? 'selected' : '' ?>>active</option><option value="inactive" <?= $dt['status'] === 'inactive' ? 'selected' : '' ?>>inactive</option></select></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">From</label><input type="date" name="date_from" class="form-control form-control-sm" value="<?= e($dt['date_from']) ?>"></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">To</label><input type="date" name="date_to" class="form-control form-control-sm" value="<?= e($dt['date_to']) ?>"></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">Per page</label><select name="per_page" class="form-select form-select-sm"><?php foreach ([10, 15, 25, 50] as $pp): ?><option value="<?= $pp ?>" <?= (int) $dt['per_page'] === $pp ? 'selected' : '' ?>><?= $pp ?></option><?php endforeach; ?></select></div>
            <div class="col-md-1 d-flex align-items-end"><input type="hidden" name="sort" value="<?= e($dt['sort']) ?>"><input type="hidden" name="dir" value="<?= e($dt['dir']) ?>"><button type="submit" class="btn btn-sm btn-hanzo-primary w-100">Apply</button></div>
        </form>
        <div class="table-responsive">
        <table class="table mb-0 align-middle" id="categoriesTable">
            <thead class="table-light">
                <tr>
                    <?php admin_dt_sort_th('ID', 'id', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <th>Image</th>
                    <?php admin_dt_sort_th('Name', 'name', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <th>Description</th>
                    <?php admin_dt_sort_th('Status', 'status', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <th>Action</th>
                </tr>
            </thead>
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
                            <form method="post" action="<?= e(app_url($adminPostAction)) ?>" class="d-inline" onsubmit="return confirm('Delete this category?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($list === []): ?><tr><td colspan="6" class="text-center text-muted py-4">No categories match.</td></tr><?php endif; ?>
            </tbody>
        </table>
        </div>
        <?php admin_dt_render_pager($dtPath, $total, $page, $dt['per_page']); ?>
    </div>
</main>
 </div></div>

<?php
$footerMode = 'slim';
require __DIR__ . '/../includes/footer.php';
