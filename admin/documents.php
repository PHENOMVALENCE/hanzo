<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

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
        redirect('admin/documents.php');
    }
}

$orders = $pdo->query('SELECT o.id, o.order_code, b.full_name AS buyer_name, p.product_name
    FROM orders o
    JOIN buyers b ON b.id = o.buyer_id
    JOIN products p ON p.id = o.product_id
    ORDER BY o.created_at DESC')->fetchAll();

$docs = $pdo->query('SELECT d.*, o.order_code, b.full_name AS buyer_name
    FROM documents d
    JOIN orders o ON o.id = d.order_id
    JOIN buyers b ON b.id = o.buyer_id
    ORDER BY d.created_at DESC')->fetchAll();

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

    <form method="post" enctype="multipart/form-data" class="row g-2 bg-white border rounded p-3 mb-3">
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
        <input type="text" class="form-control form-control-sm mb-2" placeholder="Search documents..." data-admin-table-search="docsTable">
        <div class="table-responsive">
        <table class="table mb-0" id="docsTable">
            <thead class="table-light"><tr><th data-sort>Order</th><th data-sort>Buyer</th><th data-sort>Type</th><th data-sort>Uploaded By</th><th data-sort>Date</th><th>File</th></tr></thead>
            <tbody>
                <?php foreach ($docs as $d): ?>
                    <tr>
                        <td><?= e($d['order_code']) ?></td>
                        <td><?= e($d['buyer_name']) ?></td>
                        <td><?= e($d['document_type']) ?></td>
                        <td><?= e($d['uploaded_by']) ?></td>
                        <td class="small"><?= e($d['created_at']) ?></td>
                        <td><a class="btn btn-sm btn-outline-primary" href="<?= e(app_url($d['file_path'])) ?>" target="_blank">Open</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($docs === []): ?><tr><td colspan="6" class="text-center text-muted py-3">No documents uploaded yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</main>
 </div></div>
<?php $footerMode = 'slim'; require __DIR__ . '/../includes/footer.php'; ?>


