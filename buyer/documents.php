<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_buyer();

$buyerId = auth_id();
$st = $pdo->prepare('SELECT d.*, o.order_code 
    FROM documents d 
    JOIN orders o ON o.id = d.order_id 
    WHERE o.buyer_id = ? 
    ORDER BY d.created_at DESC');
$st->execute([$buyerId]);
$docs = $st->fetchAll();

$pageTitle = 'Buyer Documents';
require __DIR__ . '/../includes/header.php';
$hideShopNav = false;
require __DIR__ . '/../includes/navbar.php';
?>
<main class="container py-4">
    <h1 class="h3 mb-3">Order Documents</h1>
    <p class="text-muted">Invoices, shipping docs, and HANZO-issued files are available here once uploaded by admin.</p>
    <div class="table-responsive border rounded bg-white">
        <table class="table mb-0">
            <thead class="table-light"><tr><th>Order</th><th>Type</th><th>Uploaded By</th><th>Date</th><th>Download</th></tr></thead>
            <tbody>
                <?php foreach ($docs as $d): ?>
                    <tr>
                        <td><?= e($d['order_code']) ?></td>
                        <td>
                            <?php
                            $type = (string) $d['document_type'];
                            $class = match ($type) {
                                'invoice' => 'bg-primary',
                                'packing_list' => 'bg-info text-dark',
                                'bill_of_lading' => 'bg-warning text-dark',
                                'clearance_doc' => 'bg-success',
                                'shipping_doc' => 'bg-secondary',
                                default => 'bg-dark',
                            };
                            ?>
                            <span class="badge <?= e($class) ?>"><?= e($type) ?></span>
                        </td>
                        <td>
                            <span class="badge <?= $d['uploaded_by'] === 'admin' ? 'bg-success' : 'bg-secondary' ?>">
                                <?= e($d['uploaded_by']) ?>
                            </span>
                        </td>
                        <td class="small"><?= e($d['created_at']) ?></td>
                        <td><a href="<?= e(app_url($d['file_path'])) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Open</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($docs === []): ?><tr><td colspan="5" class="text-center text-muted py-3">No documents available yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php $footerMode = 'full'; require __DIR__ . '/../includes/footer.php'; ?>

