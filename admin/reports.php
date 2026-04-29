<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();

$rangeMonths = 12;

// --- Summary KPIs ---
$kpi = [
    'buyers' => (int) $pdo->query('SELECT COUNT(*) FROM buyers')->fetchColumn(),
    'factories' => (int) $pdo->query('SELECT COUNT(*) FROM factories WHERE status IN ("active","invited")')->fetchColumn(),
    'products' => (int) $pdo->query('SELECT COUNT(*) FROM products WHERE status="active"')->fetchColumn(),
    'orders' => (int) $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
    'orders_open' => (int) $pdo->query('SELECT COUNT(*) FROM orders WHERE status NOT IN ("delivered","cancelled")')->fetchColumn(),
    'quotes_sent' => (int) $pdo->query('SELECT COUNT(*) FROM quotations WHERE status IN ("draft","sent")')->fetchColumn(),
    'payments_pending' => (int) $pdo->query('SELECT COUNT(*) FROM payments WHERE status="pending"')->fetchColumn(),
    'quote_value_accepted' => (float) $pdo->query('SELECT COALESCE(SUM(total_landed_cost),0) FROM quotations WHERE status="accepted"')->fetchColumn(),
    'payments_verified_sum' => (float) $pdo->query('SELECT COALESCE(SUM(amount),0) FROM payments WHERE status="verified"')->fetchColumn(),
];

// --- Chart: orders per month ---
$st = $pdo->query(
    'SELECT DATE_FORMAT(created_at, "%Y-%m") AS ym, COUNT(*) AS cnt
     FROM orders
     WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ' . (int) $rangeMonths . ' MONTH)
     GROUP BY ym ORDER BY ym ASC'
);
$ordersByMonth = $st->fetchAll(PDO::FETCH_ASSOC);
$chartOrdersLabels = [];
$chartOrdersData = [];
foreach ($ordersByMonth as $row) {
    $chartOrdersLabels[] = (string) $row['ym'];
    $chartOrdersData[] = (int) $row['cnt'];
}

// --- Chart: quotation landed value per month ---
$st = $pdo->query(
    'SELECT DATE_FORMAT(created_at, "%Y-%m") AS ym, COALESCE(SUM(total_landed_cost),0) AS total
     FROM quotations
     WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ' . (int) $rangeMonths . ' MONTH)
     GROUP BY ym ORDER BY ym ASC'
);
$quotesByMonth = $st->fetchAll(PDO::FETCH_ASSOC);
$chartQuoteLabels = [];
$chartQuoteData = [];
foreach ($quotesByMonth as $row) {
    $chartQuoteLabels[] = (string) $row['ym'];
    $chartQuoteData[] = round((float) $row['total'], 2);
}

// --- Chart: category demand (order count) ---
$st = $pdo->query(
    'SELECT c.name AS label, COUNT(o.id) AS cnt
     FROM categories c
     LEFT JOIN products p ON p.category_id = c.id
     LEFT JOIN orders o ON o.product_id = p.id
     GROUP BY c.id, c.name
     ORDER BY cnt DESC
     LIMIT 10'
);
$catRows = $st->fetchAll(PDO::FETCH_ASSOC);
$chartCatLabels = [];
$chartCatData = [];
foreach ($catRows as $row) {
    $chartCatLabels[] = (string) $row['label'];
    $chartCatData[] = (int) $row['cnt'];
}

// --- Chart: payment status ---
$st = $pdo->query('SELECT status, COUNT(*) AS cnt FROM payments GROUP BY status');
$payRows = $st->fetchAll(PDO::FETCH_ASSOC);
$chartPayLabels = [];
$chartPayData = [];
foreach ($payRows as $row) {
    $chartPayLabels[] = (string) $row['status'];
    $chartPayData[] = (int) $row['cnt'];
}

// --- Chart: order workflow status ---
$st = $pdo->query('SELECT status, COUNT(*) AS cnt FROM orders GROUP BY status ORDER BY cnt DESC');
$ordStatRows = $st->fetchAll(PDO::FETCH_ASSOC);
$chartOrdLabels = [];
$chartOrdData = [];
foreach ($ordStatRows as $row) {
    $chartOrdLabels[] = (string) $row['status'];
    $chartOrdData[] = (int) $row['cnt'];
}

// --- Table: top buyers by order volume ---
$topBuyers = $pdo->query(
    'SELECT b.full_name, b.email, b.company_name, COUNT(o.id) AS order_count
     FROM buyers b
     LEFT JOIN orders o ON o.buyer_id = b.id
     GROUP BY b.id, b.full_name, b.email, b.company_name
     ORDER BY order_count DESC
     LIMIT 15'
)->fetchAll();

// --- Table: quotation pipeline ---
$quotePipeline = $pdo->query(
    'SELECT status, COUNT(*) AS cnt, COALESCE(SUM(total_landed_cost),0) AS value_sum
     FROM quotations
     GROUP BY status
     ORDER BY cnt DESC'
)->fetchAll();

$reportCharts = [
    'ordersMonthly' => ['labels' => $chartOrdersLabels, 'values' => $chartOrdersData],
    'quotesMonthly' => ['labels' => $chartQuoteLabels, 'values' => $chartQuoteData],
    'categoryDemand' => ['labels' => $chartCatLabels, 'values' => $chartCatData],
    'payments' => ['labels' => $chartPayLabels, 'values' => $chartPayData],
    'orderStatus' => ['labels' => $chartOrdLabels, 'values' => $chartOrdData],
];

$pageTitle = 'Admin — Reports & Analytics';
$bodyClass = 'hanzo-body hanzo-admin-body';
require __DIR__ . '/../includes/header.php';
$adminActive = 'reports';
$adminPageTitle = 'Reports & Analytics';
require __DIR__ . '/../includes/admin_sidebar.php';
?>
<div class="admin-shell"><div class="admin-content">
<?php require __DIR__ . '/../includes/admin_topbar.php'; ?>
<main class="container-fluid px-0">
    <nav aria-label="breadcrumb" class="mb-3 px-1">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="<?= e(app_url('admin/dashboard.php')) ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Reports</li>
        </ol>
    </nav>
    <header class="mb-4 px-1">
        <h1 class="h3 mb-2 admin-page-title">Reports &amp; analytics</h1>
        <p class="text-muted mb-0">Live aggregates from your database: trade volume, pipeline health, and category demand.</p>
    </header>

    <section class="mb-4 px-1" aria-labelledby="kpi-heading">
        <h2 id="kpi-heading" class="h6 text-uppercase text-muted fw-semibold mb-3">Overview</h2>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3">
            <div class="col">
                <div class="card admin-kpi-card h-100 shadow-sm"><div class="card-body">
                    <small class="text-muted d-block">Buyers</small>
                    <div class="h4 mb-0 fw-semibold"><?= (int) $kpi['buyers'] ?></div>
                </div></div>
            </div>
            <div class="col">
                <div class="card admin-kpi-card admin-kpi-gold h-100 shadow-sm"><div class="card-body">
                    <small class="text-muted d-block">Factories <span class="text-muted fw-normal">(active / invited)</span></small>
                    <div class="h4 mb-0 fw-semibold"><?= (int) $kpi['factories'] ?></div>
                </div></div>
            </div>
            <div class="col">
                <div class="card admin-kpi-card h-100 shadow-sm"><div class="card-body">
                    <small class="text-muted d-block">Active products</small>
                    <div class="h4 mb-0 fw-semibold"><?= (int) $kpi['products'] ?></div>
                </div></div>
            </div>
            <div class="col">
                <div class="card admin-kpi-card admin-kpi-orange h-100 shadow-sm"><div class="card-body">
                    <small class="text-muted d-block">Open orders</small>
                    <div class="h4 mb-0 fw-semibold"><?= (int) $kpi['orders_open'] ?></div>
                    <small class="text-muted">of <?= (int) $kpi['orders'] ?> total</small>
                </div></div>
            </div>
            <div class="col">
                <div class="card admin-kpi-card admin-kpi-red h-100 shadow-sm"><div class="card-body">
                    <small class="text-muted d-block">Quotations <span class="text-muted fw-normal">(draft / sent)</span></small>
                    <div class="h4 mb-0 fw-semibold"><?= (int) $kpi['quotes_sent'] ?></div>
                </div></div>
            </div>
            <div class="col">
                <div class="card admin-kpi-card h-100 shadow-sm"><div class="card-body">
                    <small class="text-muted d-block">Payments pending</small>
                    <div class="h4 mb-0 fw-semibold"><?= (int) $kpi['payments_pending'] ?></div>
                </div></div>
            </div>
            <div class="col">
                <div class="card admin-kpi-card admin-kpi-green h-100 shadow-sm"><div class="card-body">
                    <small class="text-muted d-block">Accepted quote value</small>
                    <div class="h4 mb-0 fw-semibold">US$<?= e(number_format($kpi['quote_value_accepted'], 0)) ?></div>
                </div></div>
            </div>
            <div class="col">
                <div class="card admin-kpi-card h-100 shadow-sm"><div class="card-body">
                    <small class="text-muted d-block">Verified payments</small>
                    <div class="h4 mb-0 fw-semibold">US$<?= e(number_format($kpi['payments_verified_sum'], 0)) ?></div>
                </div></div>
            </div>
        </div>
    </section>

    <section class="mb-4 px-1" aria-labelledby="charts-heading">
        <h2 id="charts-heading" class="h6 text-uppercase text-muted fw-semibold mb-3">Charts</h2>
        <div class="row g-3">
            <div class="col-12 col-xl-6">
                <div class="admin-card p-3 admin-chart-card shadow-sm">
                    <h3 class="h6 mb-0">Orders per month</h3>
                    <p class="small text-muted mb-2">Last <?= (int) $rangeMonths ?> months</p>
                    <div class="admin-chart-body"><canvas id="reportOrdersChart" aria-label="Orders per month chart"></canvas></div>
                </div>
            </div>
            <div class="col-12 col-xl-6">
                <div class="admin-card p-3 admin-chart-card shadow-sm">
                    <h3 class="h6 mb-0">Quotation value per month</h3>
                    <p class="small text-muted mb-2">Total landed cost (USD)</p>
                    <div class="admin-chart-body"><canvas id="reportQuotesChart" aria-label="Quotation value chart"></canvas></div>
                </div>
            </div>
            <div class="col-12 col-xl-6">
                <div class="admin-card p-3 admin-chart-card shadow-sm">
                    <h3 class="h6 mb-0">Category demand</h3>
                    <p class="small text-muted mb-2">Order count by category (top 10)</p>
                    <div class="admin-chart-body"><canvas id="reportCategoryChart" aria-label="Category demand chart"></canvas></div>
                </div>
            </div>
            <div class="col-12 col-xl-6">
                <div class="admin-card p-3 admin-chart-card shadow-sm">
                    <h3 class="h6 mb-0">Payment status</h3>
                    <p class="small text-muted mb-2">All recorded payments</p>
                    <div class="admin-chart-body"><canvas id="reportPaymentChart" aria-label="Payment status chart"></canvas></div>
                </div>
            </div>
            <div class="col-12">
                <div class="admin-card p-3 admin-chart-card shadow-sm" style="min-height: 320px;">
                    <h3 class="h6 mb-0">Order status distribution</h3>
                    <p class="small text-muted mb-2">Current orders in the system</p>
                    <div class="admin-chart-body" style="min-height: 240px;"><canvas id="reportOrderStatusChart" aria-label="Order status chart"></canvas></div>
                </div>
            </div>
        </div>
    </section>

    <section class="px-1 pb-3" aria-labelledby="tables-heading">
        <h2 id="tables-heading" class="visually-hidden">Detail tables</h2>
        <div class="row g-3">
        <div class="col-lg-6">
            <div class="admin-card p-3 shadow-sm">
                <h2 class="h6 mb-2">Top buyers by orders</h2>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light"><tr><th>Buyer</th><th>Company</th><th>Orders</th></tr></thead>
                        <tbody>
                        <?php foreach ($topBuyers as $tb): ?>
                            <tr>
                                <td><?= e($tb['full_name']) ?><div class="small text-muted"><?= e($tb['email']) ?></div></td>
                                <td><?= e((string) $tb['company_name']) ?></td>
                                <td><?= (int) $tb['order_count'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if ($topBuyers === []): ?><tr><td colspan="3" class="text-muted text-center py-3">No data.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="admin-card p-3 shadow-sm">
                <h2 class="h6 mb-2">Quotation pipeline</h2>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light"><tr><th>Status</th><th>Count</th><th>Total value (USD)</th></tr></thead>
                        <tbody>
                        <?php foreach ($quotePipeline as $qp): ?>
                            <tr>
                                <td><span class="badge <?= e(quotation_status_badge_class((string) $qp['status'])) ?>"><?= e(quotation_status_label((string) $qp['status'])) ?></span></td>
                                <td><?= (int) $qp['cnt'] ?></td>
                                <td><?= e(number_format((float) $qp['value_sum'], 2)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if ($quotePipeline === []): ?><tr><td colspan="3" class="text-muted text-center py-3">No quotations.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
    </section>
</main>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
window.HANZO_REPORT_CHARTS = <?= json_encode($reportCharts, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>
<?php
$footerMode = 'slim';
require __DIR__ . '/../includes/footer.php';
