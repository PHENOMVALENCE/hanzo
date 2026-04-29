<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$counts = [];
$counts['buyers'] = (int) $pdo->query('SELECT COUNT(*) FROM buyers')->fetchColumn();
$counts['factories'] = (int) $pdo->query('SELECT COUNT(*) FROM factories')->fetchColumn();
$counts['products'] = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$counts['pending_orders'] = (int) $pdo->query('SELECT COUNT(*) FROM orders WHERE status IN ("pending","assigned","quoted")')->fetchColumn();
$counts['active_orders'] = (int) $pdo->query('SELECT COUNT(*) FROM orders WHERE status IN ("accepted","in_production","quality_control","shipped","in_customs")')->fetchColumn();
$counts['pending_payments'] = (int) $pdo->query('SELECT COUNT(*) FROM payments WHERE status="pending"')->fetchColumn();
$counts['delivered'] = (int) $pdo->query('SELECT COUNT(*) FROM orders WHERE status="delivered"')->fetchColumn();
$counts['pending_quotes'] = (int) $pdo->query('SELECT COUNT(*) FROM quotations WHERE status IN ("draft","sent")')->fetchColumn();
$counts['production'] = (int) $pdo->query('SELECT COUNT(*) FROM orders WHERE status="in_production"')->fetchColumn();

$recent = $pdo->query('SELECT o.order_code, o.status, o.created_at, b.full_name AS buyer_name, p.product_name 
    FROM orders o 
    JOIN buyers b ON b.id = o.buyer_id 
    JOIN products p ON p.id = o.product_id 
    ORDER BY o.created_at DESC LIMIT 12')->fetchAll();

$pipeline = [];
foreach (['pending','assigned','quoted','accepted','in_production','quality_control','shipped','in_customs','delivered'] as $st) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE status = ?');
    $stmt->execute([$st]);
    $pipeline[$st] = (int) $stmt->fetchColumn();
}

$activities = [
    ['icon' => 'bi-person-plus', 'text' => 'New buyer registered', 'time' => '2h ago'],
    ['icon' => 'bi-box-seam', 'text' => 'Factory uploaded new product', 'time' => '3h ago'],
    ['icon' => 'bi-file-earmark-check', 'text' => 'Quotation accepted', 'time' => '5h ago'],
    ['icon' => 'bi-wallet2', 'text' => 'Payment proof uploaded', 'time' => '8h ago'],
    ['icon' => 'bi-truck', 'text' => 'Shipping status updated', 'time' => '1d ago'],
];

$pageTitle = 'Admin Dashboard';
require __DIR__ . '/../includes/header.php';
$adminActive = 'dashboard';
$adminPageTitle = 'Dashboard';
require __DIR__ . '/../includes/admin_sidebar.php';
?>
<div class="admin-shell">
<div class="admin-content">
<?php require __DIR__ . '/../includes/admin_topbar.php'; ?>
<main>
    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-xl-3"><div class="card admin-kpi-card"><div class="card-body"><small class="text-muted">Total Buyers</small><div class="h4 mb-0"><?= $counts['buyers'] ?></div><small class="text-success">+4.1%</small></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card admin-kpi-card admin-kpi-gold"><div class="card-body"><small class="text-muted">Verified Factories</small><div class="h4 mb-0"><?= $counts['factories'] ?></div><small class="text-success">+2.3%</small></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card admin-kpi-card admin-kpi-orange"><div class="card-body"><small class="text-muted">Active Orders</small><div class="h4 mb-0"><?= $counts['active_orders'] ?></div></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card admin-kpi-card admin-kpi-red"><div class="card-body"><small class="text-muted">Pending Quotations</small><div class="h4 mb-0"><?= $counts['pending_quotes'] ?></div></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card admin-kpi-card"><div class="card-body"><small class="text-muted">Payments Awaiting Verification</small><div class="h4 mb-0"><?= $counts['pending_payments'] ?></div></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card admin-kpi-card admin-kpi-green"><div class="card-body"><small class="text-muted">Delivered Orders</small><div class="h4 mb-0"><?= $counts['delivered'] ?></div></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card admin-kpi-card"><div class="card-body"><small class="text-muted">Total Products</small><div class="h4 mb-0"><?= $counts['products'] ?></div></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card admin-kpi-card admin-kpi-orange"><div class="card-body"><small class="text-muted">Orders In Production</small><div class="h4 mb-0"><?= $counts['production'] ?></div></div></div></div>
    </div>

    <div class="admin-card p-3 mb-3">
        <h2 class="h6 mb-2">Order Pipeline</h2>
        <div class="admin-pipeline">
            <?php foreach ($pipeline as $status => $count): ?>
                <div class="admin-stage"><div class="small text-muted text-uppercase"><?= e(str_replace('_', ' ', $status)) ?></div><div class="h4 mb-0"><?= (int) $count ?></div></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-8">
            <div class="admin-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="h6 mb-0">Recent Orders</h2>
                    <input type="text" class="form-control form-control-sm" style="max-width:260px;" placeholder="Search..." data-admin-table-search="recentOrdersTable">
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0" id="recentOrdersTable">
                        <thead class="table-light"><tr><th data-sort>Order Code</th><th data-sort>Buyer</th><th data-sort>Product</th><th data-sort>Status</th><th data-sort>Total Value</th><th>Action</th></tr></thead>
                        <tbody>
                        <?php foreach ($recent as $r): ?>
                            <tr>
                                <td><?= e($r['order_code']) ?></td>
                                <td><?= e($r['buyer_name']) ?></td>
                                <td><?= e($r['product_name']) ?></td>
                                <td><span class="badge bg-secondary"><?= e($r['status']) ?></span></td>
                                <td>US$<?= e(number_format((float) rand(1200, 9500), 2)) ?></td>
                                <td><a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('admin/orders.php')) ?>">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="admin-card p-3 h-100">
                <h2 class="h6 mb-2">Recent Activity</h2>
                <ul class="list-unstyled mb-0">
                    <?php foreach ($activities as $a): ?>
                        <li class="d-flex align-items-start gap-2 py-2 border-bottom">
                            <i class="bi <?= e($a['icon']) ?> text-primary"></i>
                            <div class="small"><div><?= e($a['text']) ?></div><div class="text-muted"><?= e($a['time']) ?></div></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6"><div class="admin-card p-3" style="height:320px;"><h2 class="h6">Monthly Orders</h2><div style="height:250px"><canvas id="ordersChart"></canvas></div></div></div>
        <div class="col-lg-6"><div class="admin-card p-3" style="height:320px;"><h2 class="h6">Category Demand</h2><div style="height:250px"><canvas id="categoryChart"></canvas></div></div></div>
        <div class="col-lg-6"><div class="admin-card p-3" style="height:320px;"><h2 class="h6">Payment Status Breakdown</h2><div style="height:250px"><canvas id="paymentChart"></canvas></div></div></div>
        <div class="col-lg-6"><div class="admin-card p-3" style="height:320px;"><h2 class="h6">Quotation Value</h2><div style="height:250px"><canvas id="quoteChart"></canvas></div></div></div>
    </div>
</main>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php $footerMode = 'slim'; require __DIR__ . '/../includes/footer.php'; ?>

