<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/admin_datatable.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$qs = $_SERVER['QUERY_STRING'] ?? '';
$selfQs = $qs !== '' ? '?' . $qs : '';

$categories = $pdo->query('SELECT id, name FROM categories WHERE status="active" ORDER BY name')->fetchAll();
$categoriesFilter = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
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
        $nameEn = trim((string) ($_POST['name_en'] ?? $_POST['product_name']));
        $nameSw = trim((string) ($_POST['name_sw'] ?? ''));
        $nameZh = trim((string) ($_POST['name_zh'] ?? ''));
        $descEn = trim((string) ($_POST['description_en'] ?? $_POST['description']));
        $descSw = trim((string) ($_POST['description_sw'] ?? ''));
        $descZh = trim((string) ($_POST['description_zh'] ?? ''));
        $payload = [
            (int) $_POST['factory_id'],
            (int) $_POST['category_id'],
            $nameEn,
            $descEn,
            $nameEn,
            $nameSw !== '' ? $nameSw : null,
            $nameZh !== '' ? $nameZh : null,
            $descEn,
            $descSw !== '' ? $descSw : null,
            $descZh !== '' ? $descZh : null,
            max(1, (int) $_POST['moq']),
            (float) $_POST['min_price'],
            (float) $_POST['max_price'],
            $img,
            (string) $_POST['status'],
        ];
        if ($nameEn === '' || $descEn === '') {
            flash_set('error', 'English product name and description are required.');
            redirect('admin/products.php' . $selfQs);
        }
        if ($id > 0) {
            $payload[] = $id;
            $pdo->prepare('UPDATE products SET factory_id=?, category_id=?, product_name=?, description=?, name_en=?, name_sw=?, name_zh=?, description_en=?, description_sw=?, description_zh=?, moq=?, min_price=?, max_price=?, main_image=?, status=? WHERE id=?')->execute($payload);
        } else {
            $pdo->prepare('INSERT INTO products (factory_id, category_id, product_name, description, name_en, name_sw, name_zh, description_en, description_sw, description_zh, moq, min_price, max_price, main_image, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')->execute($payload);
        }
        flash_set('success', 'Product saved.');
    }
    redirect('admin/products.php' . $selfQs);
}

$edit = null;
if (isset($_GET['edit'])) {
    $st = $pdo->prepare('SELECT * FROM products WHERE id=?');
    $st->execute([(int) $_GET['edit']]);
    $edit = $st->fetch();
}
$dt = admin_dt_params(15);
$where = ['1=1'];
$params = [];
if ($dt['q'] !== '') {
    $where[] = '(p.product_name LIKE ? OR IFNULL(p.description,"") LIKE ? OR c.name LIKE ? OR IFNULL(f.factory_name,"") LIKE ?)';
    $like = '%' . $dt['q'] . '%';
    $params = array_merge($params, [$like, $like, $like, $like]);
}
if ($dt['status'] !== '' && in_array($dt['status'], ['active', 'draft', 'archived'], true)) {
    $where[] = 'p.status = ?';
    $params[] = $dt['status'];
}
if ($dt['category_id'] > 0) {
    $where[] = 'p.category_id = ?';
    $params[] = $dt['category_id'];
}
if ($dt['date_from'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dt['date_from'])) {
    $where[] = 'DATE(p.created_at) >= ?';
    $params[] = $dt['date_from'];
}
if ($dt['date_to'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dt['date_to'])) {
    $where[] = 'DATE(p.created_at) <= ?';
    $params[] = $dt['date_to'];
}
$whereSql = implode(' AND ', $where);
$sortMap = [
    'created_at' => 'p.created_at',
    'product_name' => 'p.product_name',
    'category_name' => 'c.name',
    'factory_name' => 'f.factory_name',
    'status' => 'p.status',
    'moq' => 'p.moq',
];
$orderSql = admin_dt_order_fragment($dt['sort'], $dt['dir'], $sortMap, 'p.created_at');
$cntSt = $pdo->prepare("SELECT COUNT(*) FROM products p JOIN categories c ON c.id=p.category_id LEFT JOIN factories f ON f.id=p.factory_id WHERE $whereSql");
$cntSt->execute($params);
$total = (int) $cntSt->fetchColumn();
$page = admin_dt_clamp_page($dt['page'], $total, $dt['per_page']);
$offset = ($page - 1) * $dt['per_page'];
$lim = (int) $dt['per_page'];
$listSt = $pdo->prepare("SELECT p.*, c.name category_name, f.factory_name FROM products p JOIN categories c ON c.id=p.category_id LEFT JOIN factories f ON f.id=p.factory_id WHERE $whereSql ORDER BY $orderSql LIMIT $lim OFFSET $offset");
$listSt->execute($params);
$list = $listSt->fetchAll();
$dtPath = 'admin/products.php';
$adminPostAction = $dtPath . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');

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
    <?php if ($m = flash_get('error')): ?><div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?>
    <form method="post" action="<?= e(app_url($adminPostAction)) ?>" enctype="multipart/form-data" class="row g-2 bg-white border rounded p-3 mb-3">
        <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
        <div class="col-md-3"><input class="form-control" name="name_en" placeholder="Product (English)" required value="<?= e((string) ($edit['name_en'] ?? $edit['product_name'] ?? '')) ?>"></div>
        <div class="col-md-3"><input class="form-control" name="name_sw" placeholder="Product (Swahili)" value="<?= e((string) ($edit['name_sw'] ?? '')) ?>"></div>
        <div class="col-md-3"><input class="form-control" name="name_zh" placeholder="Product (Chinese)" value="<?= e((string) ($edit['name_zh'] ?? '')) ?>"></div>
        <div class="col-md-2"><select class="form-select" name="factory_id"><?php foreach ($factories as $f): ?><option value="<?= (int) $f['id'] ?>" <?= (int) ($edit['factory_id'] ?? 0) === (int) $f['id'] ? 'selected' : '' ?>><?= e($f['factory_name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><select class="form-select" name="category_id"><?php foreach ($categories as $c): ?><option value="<?= (int) $c['id'] ?>" <?= (int) ($edit['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-1"><input class="form-control" name="moq" type="number" min="1" value="<?= e((string) ($edit['moq'] ?? 1)) ?>"></div>
        <div class="col-md-1"><input class="form-control" name="min_price" type="number" step="0.01" value="<?= e((string) ($edit['min_price'] ?? 0)) ?>"></div>
        <div class="col-md-1"><input class="form-control" name="max_price" type="number" step="0.01" value="<?= e((string) ($edit['max_price'] ?? 0)) ?>"></div>
        <div class="col-md-2"><select class="form-select" name="status"><option value="active">active</option><option value="draft" <?= (($edit['status'] ?? '') === 'draft') ? 'selected' : '' ?>>draft</option><option value="archived" <?= (($edit['status'] ?? '') === 'archived') ? 'selected' : '' ?>>archived</option></select></div>
        <div class="col-md-4"><textarea class="form-control" name="description_en" rows="2" placeholder="Description (English)" required><?= e((string) ($edit['description_en'] ?? $edit['description'] ?? '')) ?></textarea></div>
        <div class="col-md-4"><textarea class="form-control" name="description_sw" rows="2" placeholder="Description (Swahili)"><?= e((string) ($edit['description_sw'] ?? '')) ?></textarea></div>
        <div class="col-md-4"><textarea class="form-control" name="description_zh" rows="2" placeholder="Description (Chinese)"><?= e((string) ($edit['description_zh'] ?? '')) ?></textarea></div>
        <div class="col-md-2"><input type="file" class="form-control" name="main_image" accept=".jpg,.jpeg,.png,.webp"></div>
        <div class="col-md-12"><button class="btn btn-hanzo-primary">Save Product</button></div>
    </form>
    <div class="admin-card p-3">
        <form method="get" class="row g-2 admin-dt-toolbar mb-3" action="<?= e(app_url($dtPath)) ?>">
            <?php if (isset($_GET['edit'])): ?><input type="hidden" name="edit" value="<?= (int) $_GET['edit'] ?>"><?php endif; ?>
            <div class="col-md-3"><label class="form-label small text-muted mb-0">Search</label><input type="text" name="q" class="form-control form-control-sm" value="<?= e($dt['q']) ?>" placeholder="Product, category, factory…"></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">Category</label><select name="category_id" class="form-select form-select-sm"><option value="0">All</option><?php foreach ($categoriesFilter as $cf): ?><option value="<?= (int) $cf['id'] ?>" <?= (int) $dt['category_id'] === (int) $cf['id'] ? 'selected' : '' ?>><?= e($cf['name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">Status</label><select name="status" class="form-select form-select-sm"><option value="">All</option><?php foreach (['active','draft','archived'] as $s): ?><option value="<?= e($s) ?>" <?= $dt['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">From</label><input type="date" name="date_from" class="form-control form-control-sm" value="<?= e($dt['date_from']) ?>"></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">To</label><input type="date" name="date_to" class="form-control form-control-sm" value="<?= e($dt['date_to']) ?>"></div>
            <div class="col-md-1"><label class="form-label small text-muted mb-0">Per page</label><select name="per_page" class="form-select form-select-sm"><?php foreach ([10,15,25,50] as $pp): ?><option value="<?= $pp ?>" <?= (int)$dt['per_page']===$pp?'selected':'' ?>><?= $pp ?></option><?php endforeach; ?></select></div>
            <div class="col-md-12 d-flex align-items-end gap-2"><input type="hidden" name="sort" value="<?= e($dt['sort']) ?>"><input type="hidden" name="dir" value="<?= e($dt['dir']) ?>"><button type="submit" class="btn btn-sm btn-hanzo-primary">Apply</button></div>
        </form>
        <div class="table-responsive">
        <table class="table mb-0" id="productsTable">
            <thead class="table-light">
                <tr>
                    <?php admin_dt_sort_th('Product', 'product_name', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Factory', 'factory_name', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Category', 'category_name', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('MOQ', 'moq', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <th>Price range</th>
                    <?php admin_dt_sort_th('Status', 'status', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Created', 'created_at', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($list as $p): ?>
                <tr>
                    <td><?= e($p['product_name']) ?></td><td><?= e((string) $p['factory_name']) ?></td><td><?= e($p['category_name']) ?></td>
                    <td><?= (int) $p['moq'] ?></td><td>US$<?= e(number_format((float) $p['min_price'], 2)) ?> - <?= e(number_format((float) $p['max_price'], 2)) ?></td>
                    <td><?= e($p['status']) ?></td>
                    <td class="small"><?= e(format_datetime((string) ($p['created_at'] ?? ''))) ?></td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('admin/products.php?' . admin_dt_query(['edit' => (int) $p['id']]))) ?>">Edit</a>
                        <form method="post" action="<?= e(app_url($adminPostAction)) ?>" class="d-inline"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $p['id'] ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($list === []): ?><tr><td colspan="8" class="text-center text-muted py-4">No products match.</td></tr><?php endif; ?>
            </tbody>
        </table>
        </div>
        <?php admin_dt_render_pager($dtPath, $total, $page, $dt['per_page']); ?>
    </div>
</main>
 </div></div>
<?php $footerMode = 'slim'; require __DIR__ . '/../includes/footer.php'; ?>

