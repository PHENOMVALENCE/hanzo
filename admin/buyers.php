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
    $id = (int) ($_POST['id'] ?? 0);
    $status = (string) ($_POST['status'] ?? 'active');
    if ($id > 0 && in_array($status, ['active', 'pending', 'suspended'], true)) {
        $pdo->prepare('UPDATE buyers SET status=? WHERE id=?')->execute([$status, $id]);
        flash_set('success', 'Buyer status updated.');
    }
    redirect('admin/buyers.php' . $selfQs);
}

$dt = admin_dt_params(15);
$where = ['1=1'];
$params = [];
if ($dt['q'] !== '') {
    $where[] = '(b.full_name LIKE ? OR b.email LIKE ? OR b.company_name LIKE ? OR IFNULL(b.phone, "") LIKE ?)';
    $like = '%' . $dt['q'] . '%';
    $params = array_merge($params, [$like, $like, $like, $like]);
}
if ($dt['status'] !== '' && in_array($dt['status'], ['active', 'pending', 'suspended'], true)) {
    $where[] = 'b.status = ?';
    $params[] = $dt['status'];
}
if ($dt['date_from'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dt['date_from'])) {
    $where[] = 'DATE(b.created_at) >= ?';
    $params[] = $dt['date_from'];
}
if ($dt['date_to'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dt['date_to'])) {
    $where[] = 'DATE(b.created_at) <= ?';
    $params[] = $dt['date_to'];
}
$whereSql = implode(' AND ', $where);
$sortMap = [
    'created_at' => 'b.created_at',
    'full_name' => 'b.full_name',
    'email' => 'b.email',
    'status' => 'b.status',
    'company_name' => 'b.company_name',
];
$orderSql = admin_dt_order_fragment($dt['sort'], $dt['dir'], $sortMap, 'b.created_at');

$cntSt = $pdo->prepare("SELECT COUNT(*) FROM buyers b WHERE $whereSql");
$cntSt->execute($params);
$total = (int) $cntSt->fetchColumn();
$page = admin_dt_clamp_page($dt['page'], $total, $dt['per_page']);
$offset = ($page - 1) * $dt['per_page'];
$lim = (int) $dt['per_page'];

$listSt = $pdo->prepare("SELECT b.* FROM buyers b WHERE $whereSql ORDER BY $orderSql LIMIT $lim OFFSET $offset");
$listSt->execute($params);
$buyers = $listSt->fetchAll();

$pageTitle = 'Admin Buyers';
require __DIR__ . '/../includes/header.php';
$adminActive = 'buyers';
$adminPageTitle = 'Buyers';
require __DIR__ . '/../includes/admin_sidebar.php';
$dtPath = 'admin/buyers.php';
$adminPostAction = $dtPath . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
?>
<div class="admin-shell"><div class="admin-content">
<?php require __DIR__ . '/../includes/admin_topbar.php'; ?>
<main>
    <h1 class="h3 mb-3">Buyers Management</h1>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
    <div class="admin-card p-3">
        <form method="get" class="row g-2 admin-dt-toolbar mb-3" action="<?= e(app_url($dtPath)) ?>">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-0">Search</label>
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Name, email, company…" value="<?= e($dt['q']) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-0">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <?php foreach (['active', 'pending', 'suspended'] as $s): ?>
                        <option value="<?= e($s) ?>" <?= $dt['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-0">From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="<?= e($dt['date_from']) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-0">To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="<?= e($dt['date_to']) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-0">Per page</label>
                <select name="per_page" class="form-select form-select-sm">
                    <?php foreach ([10, 15, 25, 50] as $pp): ?>
                        <option value="<?= $pp ?>" <?= (int) $dt['per_page'] === $pp ? 'selected' : '' ?>><?= $pp ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <input type="hidden" name="sort" value="<?= e($dt['sort']) ?>">
                <input type="hidden" name="dir" value="<?= e($dt['dir']) ?>">
                <button type="submit" class="btn btn-sm btn-hanzo-primary w-100">Apply</button>
            </div>
        </form>
        <div class="table-responsive">
        <table class="table mb-0" id="buyersTable">
            <thead class="table-light">
                <tr>
                    <?php admin_dt_sort_th('Name', 'full_name', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Company', 'company_name', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Email', 'email', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <th>Phone</th>
                    <th>Location</th>
                    <?php admin_dt_sort_th('Status', 'status', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Joined', 'created_at', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($buyers as $b): ?>
                    <tr>
                        <td><?= e($b['full_name']) ?></td>
                        <td><?= e((string) $b['company_name']) ?></td>
                        <td><?= e($b['email']) ?></td>
                        <td><?= e((string) $b['phone']) ?></td>
                        <td><?= e(trim(((string) $b['city']) . ', ' . ((string) $b['country']), ', ')) ?></td>
                        <td><span class="badge bg-secondary"><?= e($b['status']) ?></span></td>
                        <td class="small"><?= e(format_datetime((string) ($b['created_at'] ?? ''))) ?></td>
                        <td>
                            <form method="post" action="<?= e(app_url($adminPostAction)) ?>" class="d-flex gap-1 flex-wrap">
                                <input type="hidden" name="id" value="<?= (int) $b['id'] ?>">
                                <select name="status" class="form-select form-select-sm" style="min-width:110px;">
                                    <?php foreach (['active','pending','suspended'] as $s): ?><option value="<?= $s ?>" <?= $b['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?>
                                </select>
                                <button class="btn btn-sm btn-hanzo-primary">Save</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($buyers === []): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No buyers match these filters.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
        <?php admin_dt_render_pager($dtPath, $total, $page, $dt['per_page']); ?>
    </div>
</main>
 </div></div>
<?php $footerMode = 'slim'; require __DIR__ . '/../includes/footer.php'; ?>
