<?php
declare(strict_types=1);
$adminActive = $adminActive ?? 'dashboard';
$items = [
    'dashboard' => ['Dashboard', 'bi-speedometer2', 'admin/dashboard.php'],
    'buyers' => ['Buyers', 'bi-people', 'admin/buyers.php'],
    'factories' => ['Factories', 'bi-buildings', 'admin/factories.php'],
    'categories' => ['Categories', 'bi-tags', 'admin/categories.php'],
    'products' => ['Products', 'bi-box-seam', 'admin/products.php'],
    'orders' => ['Orders', 'bi-receipt-cutoff', 'admin/orders.php'],
    'quotations' => ['Quotations', 'bi-file-earmark-text', 'admin/quotations.php'],
    'payments' => ['Payments', 'bi-credit-card', 'admin/payments.php'],
    'shipping' => ['Shipping', 'bi-truck', 'admin/shipping.php'],
    'documents' => ['Documents', 'bi-folder2-open', 'admin/documents.php'],
    'profile' => ['My profile', 'bi-person-badge', 'profile.php'],
    'reports' => ['Reports', 'bi-bar-chart', 'admin/reports.php'],
    'settings' => ['Settings', 'bi-gear', 'admin/dashboard.php#settings'],
];
?>
<aside id="adminSidebar" class="admin-sidebar">
    <div class="admin-sidebar-brand">
        <button class="btn btn-link text-white p-0 me-2 d-lg-none" id="adminSidebarClose"><i class="bi bi-x-lg"></i></button>
        <a href="<?= e(app_url('admin/dashboard.php')) ?>" class="admin-sidebar-brand-link">
            <img src="<?= e(app_url('assets/images/logo.png')) ?>" alt="China Chapu" class="admin-sidebar-logo hanzo-brand-logo-invert" onerror="this.style.display='none';this.nextElementSibling.style.display='inline';">
            <span class="hanzo-brand-fallback text-white">China Chapu</span>
            <span class="small text-white-50">Admin</span>
        </a>
    </div>
    <nav class="admin-menu">
        <?php foreach ($items as $key => $item): ?>
            <a href="<?= e(app_url($item[2])) ?>" class="admin-menu-item <?= $adminActive === $key ? 'active' : '' ?>">
                <i class="bi <?= e($item[1]) ?>"></i>
                <span><?= e($item[0]) ?></span>
            </a>
        <?php endforeach; ?>
        <a href="<?= e(app_url('logout.php')) ?>" class="admin-menu-item mt-auto">
            <i class="bi bi-box-arrow-right"></i><span>Logout</span>
        </a>
    </nav>
</aside>
<div id="adminSidebarBackdrop" class="admin-sidebar-backdrop"></div>

