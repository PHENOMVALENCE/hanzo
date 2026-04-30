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

$recent = $pdo->query(
    'SELECT o.order_code, o.status, o.created_at, b.full_name AS buyer_name, p.product_name,
        (SELECT q.total_landed_cost FROM quotations q WHERE q.order_id = o.id ORDER BY q.id DESC LIMIT 1) AS latest_quote_total
     FROM orders o
     JOIN buyers b ON b.id = o.buyer_id
     JOIN products p ON p.id = o.product_id
     ORDER BY o.created_at DESC LIMIT 12'
)->fetchAll();

$monthsChart = 12;
$rOrd = $pdo->query(
    'SELECT DATE_FORMAT(created_at, "%Y-%m") AS ym, COUNT(*) AS cnt FROM orders
     WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ' . (int) $monthsChart . ' MONTH)
     GROUP BY ym ORDER BY ym ASC'
)->fetchAll(PDO::FETCH_ASSOC);
$rQuo = $pdo->query(
    'SELECT DATE_FORMAT(created_at, "%Y-%m") AS ym, COALESCE(SUM(total_landed_cost),0) AS total FROM quotations
     WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ' . (int) $monthsChart . ' MONTH)
     GROUP BY ym ORDER BY ym ASC'
)->fetchAll(PDO::FETCH_ASSOC);
$rPay = $pdo->query('SELECT status, COUNT(*) AS cnt FROM payments GROUP BY status')->fetchAll(PDO::FETCH_ASSOC);
$rCat = $pdo->query(
    'SELECT c.name AS label, COUNT(o.id) AS cnt FROM categories c
     LEFT JOIN products p ON p.category_id = c.id
     LEFT JOIN orders o ON o.product_id = p.id
     GROUP BY c.id, c.name ORDER BY cnt DESC LIMIT 8'
)->fetchAll(PDO::FETCH_ASSOC);

$dashboardCharts = [
    'ordersMonthly' => [
        'labels' => array_column($rOrd, 'ym'),
        'values' => array_map(static fn ($v) => (int) $v, array_column($rOrd, 'cnt')),
    ],
    'quotesMonthly' => [
        'labels' => array_column($rQuo, 'ym'),
        'values' => array_map(static fn ($v) => round((float) $v, 2), array_column($rQuo, 'total')),
    ],
    'payments' => [
        'labels' => array_column($rPay, 'status'),
        'values' => array_map(static fn ($v) => (int) $v, array_column($rPay, 'cnt')),
    ],
    'categoryDemand' => [
        'labels' => array_column($rCat, 'label'),
        'values' => array_map(static fn ($v) => (int) $v, array_column($rCat, 'cnt')),
    ],
];

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

$pageTitle = __('admin_dashboard');
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
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                    <h2 class="h6 mb-0">Recent Orders</h2>
                    <input type="text" class="form-control form-control-sm" style="max-width:260px;" placeholder="Filter this list…" data-admin-table-search="recentOrdersTable">
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0" id="recentOrdersTable">
                        <thead class="table-light"><tr><th data-sort>Order Code</th><th data-sort>Buyer</th><th data-sort>Product</th><th data-sort>Status</th><th data-sort>Latest quote (USD)</th><th>Action</th></tr></thead>
                        <tbody>
                        <?php foreach ($recent as $r): ?>
                            <tr>
                                <td><?= e($r['order_code']) ?></td>
                                <td><?= e($r['buyer_name']) ?></td>
                                <td><?= e($r['product_name']) ?></td>
                                <td><span class="badge <?= e(order_status_badge_class((string) $r['status'])) ?>"><?= e(order_status_label((string) $r['status'])) ?></span></td>
                                <td><?php
                                    $qv = $r['latest_quote_total'] ?? null;
                                    echo $qv !== null && $qv !== '' ? 'US$' . e(number_format((float) $qv, 2)) : '—';
                                ?></td>
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
<script>
window.HANZO_ADMIN_CHARTS = <?= json_encode($dashboardCharts, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>
<?php $footerMode = 'slim'; require __DIR__ . '/../includes/footer.php'; ?>

