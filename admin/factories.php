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
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'delete') {
        $delId = (int) ($_POST['id'] ?? 0);
        if ($delId > 0) {
            $st = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE factory_id = ?');
            $st->execute([$delId]);
            $cOrders = (int) $st->fetchColumn();
            $st = $pdo->prepare('SELECT COUNT(*) FROM products WHERE factory_id = ?');
            $st->execute([$delId]);
            $cProducts = (int) $st->fetchColumn();
            if ($cOrders > 0 || $cProducts > 0) {
                flash_set('error', 'Cannot delete: this factory is linked to ' . $cOrders . ' order(s) and ' . $cProducts . ' product(s). Unassign orders and reassign or archive products first.');
            } else {
                $pdo->beginTransaction();
                try {
                    $pdo->prepare('DELETE FROM factory_products WHERE factory_id = ?')->execute([$delId]);
                    $pdo->prepare('DELETE FROM production_updates WHERE factory_id = ?')->execute([$delId]);
                    $pdo->prepare('DELETE FROM factories WHERE id = ?')->execute([$delId]);
                    $pdo->commit();
                    flash_set('success', 'Factory deleted.');
                } catch (Throwable) {
                    $pdo->rollBack();
                    flash_set('error', 'Could not delete factory. It may still be referenced elsewhere.');
                }
            }
        }
        $tailDel = admin_dt_query(['edit' => null]);
        redirect('admin/factories.php' . ($tailDel !== '' ? '?' . $tailDel : ''));
    }
    if ($action === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $factory = trim((string) ($_POST['factory_name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $pass = (string) ($_POST['password'] ?? '');
        $status = (string) ($_POST['status'] ?? 'invited');
        if (!in_array($status, ['invited', 'active', 'suspended'], true)) {
            $status = 'invited';
        }
        $contact = trim((string) ($_POST['contact_person'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $city = trim((string) ($_POST['city'] ?? ''));
        $province = trim((string) ($_POST['province'] ?? ''));
        $mainProducts = trim((string) ($_POST['main_products'] ?? ''));
        $capacity = trim((string) ($_POST['production_capacity'] ?? ''));
        $exportExp = trim((string) ($_POST['export_experience'] ?? ''));
        if ($factory === '' || $email === '') {
            flash_set('error', 'Factory name and email are required.');
        } else {
            $stDup = $pdo->prepare('SELECT id FROM factories WHERE email = ? AND id != ? LIMIT 1');
            $stDup->execute([$email, $id]);
            if ($stDup->fetch()) {
                flash_set('error', 'That email is already used by another factory.');
            } elseif ($id > 0) {
                try {
                    if ($pass !== '') {
                        $pdo->prepare('UPDATE factories SET factory_name=?, contact_person=?, email=?, phone=?, city=?, province=?, main_products=?, production_capacity=?, export_experience=?, status=?, password=? WHERE id=?')
                            ->execute([$factory, $contact !== '' ? $contact : null, $email, $phone !== '' ? $phone : null, $city !== '' ? $city : null, $province !== '' ? $province : null, $mainProducts !== '' ? $mainProducts : null, $capacity !== '' ? $capacity : null, $exportExp !== '' ? $exportExp : null, $status, password_hash($pass, PASSWORD_DEFAULT), $id]);
                    } else {
                        $pdo->prepare('UPDATE factories SET factory_name=?, contact_person=?, email=?, phone=?, city=?, province=?, main_products=?, production_capacity=?, export_experience=?, status=? WHERE id=?')
                            ->execute([$factory, $contact !== '' ? $contact : null, $email, $phone !== '' ? $phone : null, $city !== '' ? $city : null, $province !== '' ? $province : null, $mainProducts !== '' ? $mainProducts : null, $capacity !== '' ? $capacity : null, $exportExp !== '' ? $exportExp : null, $status, $id]);
                    }
                    flash_set('success', 'Factory account updated.');
                } catch (PDOException) {
                    flash_set('error', 'Could not update factory (duplicate email or database error).');
                }
            } else {
                if ($pass === '') {
                    $pass = 'Admin@123';
                }
                try {
                    $pdo->prepare('INSERT INTO factories (factory_name, contact_person, email, phone, city, province, main_products, production_capacity, export_experience, password, status, invited_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)')
                        ->execute([$factory, $contact !== '' ? $contact : null, $email, $phone !== '' ? $phone : null, $city !== '' ? $city : null, $province !== '' ? $province : null, $mainProducts !== '' ? $mainProducts : null, $capacity !== '' ? $capacity : null, $exportExp !== '' ? $exportExp : null, password_hash($pass, PASSWORD_DEFAULT), $status, auth_id()]);
                    flash_set('success', 'Factory account created.');
                } catch (PDOException) {
                    flash_set('error', 'Could not create factory (duplicate email or database error).');
                }
            }
        }
    }
    redirect('admin/factories.php' . $selfQs);
}

$edit = null;
if (isset($_GET['edit'])) {
    $st = $pdo->prepare('SELECT * FROM factories WHERE id=?');
    $st->execute([(int) $_GET['edit']]);
    $row = $st->fetch();
    $edit = $row ?: null;
}

$dt = admin_dt_params(15);
$where = ['1=1'];
$params = [];
if ($dt['q'] !== '') {
    $where[] = '(f.factory_name LIKE ? OR f.email LIKE ? OR IFNULL(f.contact_person,"") LIKE ? OR IFNULL(f.phone,"") LIKE ? OR IFNULL(f.main_products,"") LIKE ?)';
    $like = '%' . $dt['q'] . '%';
    $params = array_merge($params, [$like, $like, $like, $like, $like]);
}
if ($dt['status'] !== '' && in_array($dt['status'], ['invited', 'active', 'suspended'], true)) {
    $where[] = 'f.status = ?';
    $params[] = $dt['status'];
}
if ($dt['date_from'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dt['date_from'])) {
    $where[] = 'DATE(f.created_at) >= ?';
    $params[] = $dt['date_from'];
}
if ($dt['date_to'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dt['date_to'])) {
    $where[] = 'DATE(f.created_at) <= ?';
    $params[] = $dt['date_to'];
}
$whereSql = implode(' AND ', $where);
$sortMap = [
    'created_at' => 'f.created_at',
    'factory_name' => 'f.factory_name',
    'email' => 'f.email',
    'status' => 'f.status',
];
$orderSql = admin_dt_order_fragment($dt['sort'], $dt['dir'], $sortMap, 'f.created_at');

$cntSt = $pdo->prepare("SELECT COUNT(*) FROM factories f WHERE $whereSql");
$cntSt->execute($params);
$total = (int) $cntSt->fetchColumn();
$page = admin_dt_clamp_page($dt['page'], $total, $dt['per_page']);
$offset = ($page - 1) * $dt['per_page'];
$lim = (int) $dt['per_page'];

$listSt = $pdo->prepare("SELECT f.* FROM factories f WHERE $whereSql ORDER BY $orderSql LIMIT $lim OFFSET $offset");
$listSt->execute($params);
$factories = $listSt->fetchAll();

$dtPath = 'admin/factories.php';
$adminPostAction = $dtPath . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
$pageTitle = 'Admin Factories';
require __DIR__ . '/../includes/header.php';
$adminActive = 'factories';
$adminPageTitle = 'Factories';
require __DIR__ . '/../includes/admin_sidebar.php';
?>
<div class="admin-shell"><div class="admin-content">
<?php require __DIR__ . '/../includes/admin_topbar.php'; ?>
<main>
    <h1 class="h3 mb-3">Factories (Invite Only)</h1>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
    <?php if ($m = flash_get('error')): ?><div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
        <p class="text-muted small mb-0">Create suppliers, edit details, reset passwords, or remove factories that have no linked orders or products.</p>
        <?php if ($edit): ?>
            <a class="btn btn-sm btn-outline-secondary" href="<?= e(app_url('admin/factories.php?' . admin_dt_query(['edit' => null]))) ?>">Clear form (new factory)</a>
        <?php endif; ?>
    </div>
    <form method="post" action="<?= e(app_url($adminPostAction)) ?>" class="row g-2 bg-white border rounded p-3 mb-3">
        <input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
        <div class="col-md-3"><input class="form-control" name="factory_name" required placeholder="Factory name" value="<?= e((string) ($edit['factory_name'] ?? '')) ?>"></div>
        <div class="col-md-2"><input class="form-control" name="contact_person" placeholder="Contact person" value="<?= e((string) ($edit['contact_person'] ?? '')) ?>"></div>
        <div class="col-md-3"><input class="form-control" type="email" name="email" required placeholder="Email" value="<?= e((string) ($edit['email'] ?? '')) ?>"></div>
        <div class="col-md-2"><input class="form-control" name="phone" placeholder="Phone" value="<?= e((string) ($edit['phone'] ?? '')) ?>"></div>
        <div class="col-md-2"><select class="form-select" name="status"><?php foreach (['invited','active','suspended'] as $s): ?><option value="<?= $s ?>" <?= (($edit['status'] ?? 'invited') === $s) ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><input class="form-control" name="city" placeholder="City" value="<?= e((string) ($edit['city'] ?? '')) ?>"></div>
        <div class="col-md-2"><input class="form-control" name="province" placeholder="Province" value="<?= e((string) ($edit['province'] ?? '')) ?>"></div>
        <div class="col-md-3"><input class="form-control" name="main_products" placeholder="Main products" value="<?= e((string) ($edit['main_products'] ?? '')) ?>"></div>
        <div class="col-md-2"><input class="form-control" name="production_capacity" placeholder="Capacity" value="<?= e((string) ($edit['production_capacity'] ?? '')) ?>"></div>
        <div class="col-md-2"><input class="form-control" name="export_experience" placeholder="Export experience" value="<?= e((string) ($edit['export_experience'] ?? '')) ?>"></div>
        <div class="col-md-1"><input class="form-control" name="password" type="password" placeholder="Pass" autocomplete="new-password" title="<?= $edit ? 'Leave blank to keep current password' : 'Default Admin@123 if empty' ?>"></div>
        <div class="col-md-12 d-flex flex-wrap align-items-center gap-2">
            <button type="submit" class="btn btn-hanzo-primary"><?= $edit ? 'Update factory' : 'Save factory' ?></button>
            <?php if ($edit): ?><span class="small text-muted">Password: leave blank to keep unchanged.</span><?php else: ?><span class="small text-muted">Password optional; defaults to <code>Admin@123</code> if empty.</span><?php endif; ?>
        </div>
    </form>
    <div class="admin-card p-3">
        <form method="get" class="row g-2 admin-dt-toolbar mb-3" action="<?= e(app_url($dtPath)) ?>">
            <?php if (isset($_GET['edit'])): ?><input type="hidden" name="edit" value="<?= (int) $_GET['edit'] ?>"><?php endif; ?>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-0">Search</label>
                <input type="text" name="q" class="form-control form-control-sm" value="<?= e($dt['q']) ?>" placeholder="Name, email, products…">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-0">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <?php foreach (['invited', 'active', 'suspended'] as $s): ?>
                        <option value="<?= e($s) ?>" <?= $dt['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">From</label><input type="date" name="date_from" class="form-control form-control-sm" value="<?= e($dt['date_from']) ?>"></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">To</label><input type="date" name="date_to" class="form-control form-control-sm" value="<?= e($dt['date_to']) ?>"></div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-0">Per page</label>
                <select name="per_page" class="form-select form-select-sm"><?php foreach ([10, 15, 25, 50] as $pp): ?><option value="<?= $pp ?>" <?= (int) $dt['per_page'] === $pp ? 'selected' : '' ?>><?= $pp ?></option><?php endforeach; ?></select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <input type="hidden" name="sort" value="<?= e($dt['sort']) ?>"><input type="hidden" name="dir" value="<?= e($dt['dir']) ?>">
                <button type="submit" class="btn btn-sm btn-hanzo-primary w-100">Apply</button>
            </div>
        </form>
        <div class="table-responsive">
        <table class="table mb-0" id="factoriesTable">
            <thead class="table-light">
                <tr>
                    <?php admin_dt_sort_th('Factory', 'factory_name', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Email', 'email', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <th>Main products</th>
                    <?php admin_dt_sort_th('Status', 'status', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Joined', 'created_at', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($factories as $f): ?>
                <tr>
                    <td><?= e($f['factory_name']) ?></td>
                    <td><?= e($f['email']) ?></td>
                    <td class="small"><?= e((string) $f['main_products']) ?></td>
                    <td><span class="badge bg-secondary"><?= e($f['status']) ?></span></td>
                    <td class="small"><?= e(format_datetime((string) ($f['created_at'] ?? ''))) ?></td>
                    <td class="d-flex flex-wrap gap-1">
                        <a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('admin/factories.php?' . admin_dt_query(['edit' => (int) $f['id'], 'page' => 1]))) ?>">Edit</a>
                        <form method="post" action="<?= e(app_url($adminPostAction)) ?>" class="d-inline" onsubmit="return confirm('Delete this factory permanently? This cannot be undone.');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($factories === []): ?><tr><td colspan="6" class="text-center text-muted py-4">No factories match.</td></tr><?php endif; ?>
            </tbody>
        </table>
        </div>
        <?php admin_dt_render_pager($dtPath, $total, $page, $dt['per_page']); ?>
    </div>
</main>
 </div></div>
<?php $footerMode = 'slim'; require __DIR__ . '/../includes/footer.php'; ?>


