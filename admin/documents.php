<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/admin_datatable.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$qs = $_SERVER['QUERY_STRING'] ?? '';
$selfQs = $qs !== '' ? '?' . $qs : '';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int) ($_POST['order_id'] ?? 0);
    $docType = trim((string) ($_POST['document_type'] ?? ''));
    if ($orderId <= 0) {
        $errors[] = 'Select a valid order.';
    }
    if ($docType === '') {
        $errors[] = 'Document type is required.';
    }

    $filePath = null;
    if (!isset($_FILES['document_file']) || $_FILES['document_file']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Select a file to upload.';
    } else {
        $up = validate_upload_image_doc($_FILES['document_file'], 10485760);
        if (!$up['ok']) {
            $errors = array_merge($errors, $up['errors']);
        } else {
            $filePath = save_uploaded_file($_FILES['document_file'], 'documents', $up['filename']);
            if ($filePath === null) {
                $errors[] = 'Failed to save uploaded file.';
            }
        }
    }

    if ($errors === []) {
        $pdo->prepare('INSERT INTO documents (order_id, document_type, file_path, uploaded_by) VALUES (?,?,?,?)')
            ->execute([$orderId, $docType, $filePath, 'admin']);
        flash_set('success', 'Document uploaded successfully.');
        redirect('admin/documents.php' . $selfQs);
    }
}

$orders = $pdo->query('SELECT o.id, o.order_code, b.full_name AS buyer_name, p.product_name
    FROM orders o
    JOIN buyers b ON b.id = o.buyer_id
    JOIN products p ON p.id = o.product_id
    ORDER BY o.created_at DESC LIMIT 500')->fetchAll();

$docTypesAllowed = ['invoice', 'packing_list', 'bill_of_lading', 'clearance_doc', 'shipping_doc', 'other'];

$dt = admin_dt_params(15);
$where = ['1=1'];
$params = [];
if ($dt['q'] !== '') {
    $where[] = '(o.order_code LIKE ? OR b.full_name LIKE ? OR d.document_type LIKE ? OR IFNULL(d.uploaded_by,"") LIKE ?)';
    $like = '%' . $dt['q'] . '%';
    $params = array_merge($params, [$like, $like, $like, $like]);
}
if ($dt['status'] !== '' && in_array($dt['status'], $docTypesAllowed, true)) {
    $where[] = 'd.document_type = ?';
    $params[] = $dt['status'];
}
if ($dt['date_from'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dt['date_from'])) {
    $where[] = 'DATE(d.created_at) >= ?';
    $params[] = $dt['date_from'];
}
if ($dt['date_to'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dt['date_to'])) {
    $where[] = 'DATE(d.created_at) <= ?';
    $params[] = $dt['date_to'];
}
$whereSql = implode(' AND ', $where);
$sortMap = [
    'created_at' => 'd.created_at',
    'order_code' => 'o.order_code',
    'buyer_name' => 'b.full_name',
    'document_type' => 'd.document_type',
    'uploaded_by' => 'd.uploaded_by',
];
$orderSql = admin_dt_order_fragment($dt['sort'], $dt['dir'], $sortMap, 'd.created_at');
$cntSt = $pdo->prepare("SELECT COUNT(*) FROM documents d JOIN orders o ON o.id=d.order_id JOIN buyers b ON b.id=o.buyer_id WHERE $whereSql");
$cntSt->execute($params);
$total = (int) $cntSt->fetchColumn();
$page = admin_dt_clamp_page($dt['page'], $total, $dt['per_page']);
$offset = ($page - 1) * $dt['per_page'];
$lim = (int) $dt['per_page'];
$listSt = $pdo->prepare("SELECT d.*, o.order_code, b.full_name AS buyer_name FROM documents d JOIN orders o ON o.id=d.order_id JOIN buyers b ON b.id=o.buyer_id WHERE $whereSql ORDER BY $orderSql LIMIT $lim OFFSET $offset");
$listSt->execute($params);
$docs = $listSt->fetchAll();
$dtPath = 'admin/documents.php';
$adminPostAction = $dtPath . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');

$pageTitle = 'Admin Documents';
require __DIR__ . '/../includes/header.php';
$adminActive = 'documents';
$adminPageTitle = 'Documents';
require __DIR__ . '/../includes/admin_sidebar.php';
?>
<div class="admin-shell"><div class="admin-content">
<?php require __DIR__ . '/../includes/admin_topbar.php'; ?>
<main>
    <h1 class="h3 mb-3">Documents Management</h1>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
    <?php foreach ($errors as $er): ?><div class="alert alert-danger"><?= e($er) ?></div><?php endforeach; ?>

    <form method="post" action="<?= e(app_url($adminPostAction)) ?>" enctype="multipart/form-data" class="row g-2 bg-white border rounded p-3 mb-3">
        <div class="col-md-4">
            <select class="form-select" name="order_id" required>
                <option value="">Select order</option>
                <?php foreach ($orders as $o): ?>
                    <option value="<?= (int) $o['id'] ?>"><?= e($o['order_code'] . ' | ' . $o['buyer_name'] . ' | ' . $o['product_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" name="document_type" required>
                <option value="">Document type</option>
                <option value="invoice">Invoice</option>
                <option value="packing_list">Packing List</option>
                <option value="bill_of_lading">Bill of Lading</option>
                <option value="clearance_doc">Clearance Document</option>
                <option value="shipping_doc">Shipping Document</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="file" class="form-control" name="document_file" accept=".jpg,.jpeg,.png,.webp,.pdf" required>
        </div>
        <div class="col-md-2">
            <button class="btn btn-hanzo-primary w-100">Upload</button>
        </div>
    </form>

    <div class="admin-card p-3">
        <form method="get" class="row g-2 admin-dt-toolbar mb-3" action="<?= e(app_url($dtPath)) ?>">
            <div class="col-md-3"><label class="form-label small text-muted mb-0">Search</label><input type="text" name="q" class="form-control form-control-sm" value="<?= e($dt['q']) ?>" placeholder="Order, buyer, type…"></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">Type</label><select name="status" class="form-select form-select-sm"><option value="">All</option><?php foreach ($docTypesAllowed as $t): ?><option value="<?= e($t) ?>" <?= $dt['status'] === $t ? 'selected' : '' ?>><?= e($t) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">From</label><input type="date" name="date_from" class="form-control form-control-sm" value="<?= e($dt['date_from']) ?>"></div>
            <div class="col-md-2"><label class="form-label small text-muted mb-0">To</label><input type="date" name="date_to" class="form-control form-control-sm" value="<?= e($dt['date_to']) ?>"></div>
            <div class="col-md-1"><label class="form-label small text-muted mb-0">Per page</label><select name="per_page" class="form-select form-select-sm"><?php foreach ([10,15,25,50] as $pp): ?><option value="<?= $pp ?>" <?= (int)$dt['per_page']===$pp?'selected':'' ?>><?= $pp ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2 d-flex align-items-end"><input type="hidden" name="sort" value="<?= e($dt['sort']) ?>"><input type="hidden" name="dir" value="<?= e($dt['dir']) ?>"><button type="submit" class="btn btn-sm btn-hanzo-primary">Apply</button></div>
        </form>
        <div class="table-responsive">
        <table class="table mb-0" id="docsTable">
            <thead class="table-light">
                <tr>
                    <?php admin_dt_sort_th('Order', 'order_code', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Buyer', 'buyer_name', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Type', 'document_type', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Uploaded by', 'uploaded_by', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <?php admin_dt_sort_th('Date', 'created_at', $dt['sort'], $dt['dir'], $dtPath); ?>
                    <th>File</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($docs as $d): ?>
                    <tr>
                        <td><?= e($d['order_code']) ?></td>
                        <td><?= e($d['buyer_name']) ?></td>
                        <td><?= e($d['document_type']) ?></td>
                        <td><?= e($d['uploaded_by']) ?></td>
                        <td class="small"><?= e(format_datetime((string) ($d['created_at'] ?? ''))) ?></td>
                        <td><a class="btn btn-sm btn-outline-primary" href="<?= e(app_url($d['file_path'])) ?>" target="_blank">Open</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($docs === []): ?><tr><td colspan="6" class="text-center text-muted py-4">No documents match.</td></tr><?php endif; ?>
            </tbody>
        </table>
        </div>
        <?php admin_dt_render_pager($dtPath, $total, $page, $dt['per_page']); ?>
    </div>
</main>
 </div></div>
<?php $footerMode = 'slim'; require __DIR__ . '/../includes/footer.php'; ?>


