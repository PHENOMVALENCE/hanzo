<?php
declare(strict_types=1);
$adminPageTitle = $adminPageTitle ?? 'Dashboard';
?>
<header class="admin-topbar sticky-top flex-wrap">
    <div class="d-flex align-items-center gap-2 min-w-0 flex-grow-1 me-2" style="min-width: 0;">
        <button class="btn btn-outline-secondary d-lg-none flex-shrink-0" type="button" id="adminSidebarToggle" aria-label="Open sidebar menu"><i class="bi bi-list" aria-hidden="true"></i></button>
        <h1 class="h5 mb-0 text-truncate min-w-0"><?= e($adminPageTitle) ?></h1>
    </div>
    <div class="d-flex align-items-center gap-2 flex-shrink-0 flex-wrap justify-content-end ms-auto">
        <div class="input-group input-group-sm d-none d-md-flex admin-topbar-search">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" id="adminGlobalSearch" placeholder="Search orders, buyers, products...">
        </div>
        <button class="btn btn-outline-secondary position-relative" type="button">
            <i class="bi bi-bell"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">5</span>
        </button>
        <div class="dropdown">
            <button class="btn btn-hanzo-primary dropdown-toggle btn-sm px-2 px-sm-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-lightning-charge me-sm-1" aria-hidden="true"></i><span class="d-none d-sm-inline">Quick Actions</span><span class="d-sm-none">Actions</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#addFactoryModal">Add Factory</button></li>
                <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Product</button></li>
                <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#createQuoteModal">Create Quotation</button></li>
                <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#updateShippingModal">Update Shipping</button></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?= e(app_url('admin/reports.php')) ?>"><i class="bi bi-bar-chart-line me-1"></i> Reports &amp; analytics</a></li>
            </ul>
        </div>
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle btn-sm text-truncate" style="max-width: 10rem;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle me-1" aria-hidden="true"></i><span class="d-none d-md-inline"><?= e(auth_user()['name'] ?? 'Admin') ?></span><span class="d-md-none">Account</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="<?= e(app_url('admin/reports.php')) ?>">Reports &amp; analytics</a></li>
                <li><a class="dropdown-item" href="<?= e(app_url('profile.php')) ?>">My profile</a></li>
                <li><a class="dropdown-item" href="<?= e(app_url('logout.php')) ?>">Logout</a></li>
            </ul>
        </div>
    </div>
</header>

<!-- Quick Action Modals -->
<div class="modal fade" id="addFactoryModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Add Factory</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body row g-2"><div class="col-md-6"><input class="form-control" placeholder="Factory name"></div><div class="col-md-6"><input class="form-control" placeholder="Contact person"></div><div class="col-md-6"><input class="form-control" placeholder="Email"></div><div class="col-md-6"><input class="form-control" placeholder="Phone"></div><div class="col-12"><textarea class="form-control" rows="2" placeholder="Main products"></textarea></div></div><div class="modal-footer"><button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-hanzo-primary">Save Factory</button></div></div></div></div>
<div class="modal fade" id="addProductModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Add Product</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body row g-2"><div class="col-md-6"><input class="form-control" placeholder="Product name"></div><div class="col-md-3"><input class="form-control" placeholder="MOQ"></div><div class="col-md-3"><input class="form-control" placeholder="Price range"></div><div class="col-12"><textarea class="form-control" rows="2" placeholder="Description"></textarea></div></div><div class="modal-footer"><button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-hanzo-primary">Save Product</button></div></div></div></div>
<div class="modal fade" id="createQuoteModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Create Quotation</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><input class="form-control mb-2" placeholder="Order code"><input class="form-control mb-2" placeholder="Product cost"><input class="form-control mb-2" placeholder="Freight"><input class="form-control" placeholder="HANZO margin"></div><div class="modal-footer"><button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-hanzo-primary">Create Quote</button></div></div></div></div>
<div class="modal fade" id="updateShippingModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Update Shipping Status</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><input class="form-control mb-2" placeholder="Order code"><input class="form-control mb-2" placeholder="Tracking number"><textarea class="form-control" rows="2" placeholder="Status description"></textarea></div><div class="modal-footer"><button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-hanzo-primary">Update</button></div></div></div></div>

