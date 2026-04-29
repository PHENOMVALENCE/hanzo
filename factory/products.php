<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_factory();

$factoryId = auth_id();
$cats = $pdo->query('SELECT id, name FROM categories WHERE status="active" ORDER BY name')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save') {
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim((string) ($_POST['product_name'] ?? ''));
    $category = (int) ($_POST['category_id'] ?? 0);
    $desc = trim((string) ($_POST['description'] ?? ''));
    $moq = max(1, (int) ($_POST['moq'] ?? 1));
    $min = (float) ($_POST['min_price'] ?? 0);
    $max = (float) ($_POST['max_price'] ?? 0);
    $status = (string) ($_POST['status'] ?? 'active');
    if (!in_array($status, ['active', 'draft', 'archived'], true)) {
        $status = 'draft';
    }
    $img = null;
    if ($id > 0) {
        $cur = $pdo->prepare('SELECT main_image FROM products WHERE id = ? AND factory_id = ?');
        $cur->execute([$id, $factoryId]);
        $img = $cur->fetchColumn() ?: null;
    }
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $check = validate_upload_image_doc($_FILES['main_image']);
        if ($check['ok']) {
            $img = save_uploaded_file($_FILES['main_image'], 'products', $check['filename']);
        }
    }
    if ($id > 0) {
        $pdo->prepare('UPDATE products SET category_id=?, product_name=?, description=?, moq=?, min_price=?, max_price=?, status=?, main_image=? WHERE id=? AND factory_id=?')
            ->execute([$category, $name, $desc, $moq, $min, $max, $status, $img, $id, $factoryId]);
    } else {
        $pdo->prepare('INSERT INTO products (factory_id, category_id, product_name, description, moq, min_price, max_price, status, main_image) VALUES (?,?,?,?,?,?,?,?,?)')
            ->execute([$factoryId, $category, $name, $desc, $moq, $min, $max, $status, $img]);
    }
    flash_set('success', 'Product saved.');
    redirect('factory/products.php');
}

$edit = null;
if (isset($_GET['edit'])) {
    $st = $pdo->prepare('SELECT * FROM products WHERE id = ? AND factory_id = ?');
    $st->execute([(int) $_GET['edit'], $factoryId]);
    $edit = $st->fetch() ?: null;
}
$st = $pdo->prepare('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE p.factory_id = ? ORDER BY p.created_at DESC');
$st->execute([$factoryId]);
$products = $st->fetchAll();

$ef = is_array($edit) ? $edit : [];
$currentStatus = (string) ($ef['status'] ?? 'active');

$pageTitle = 'Factory Products';
require __DIR__ . '/../includes/header.php';
$hideShopNav = false;
require __DIR__ . '/../includes/navbar.php';
require __DIR__ . '/../includes/factory_sidebar_start.php';
?>
<main class="hanzo-buyer-main-inner">
    <header class="hanzo-buyer-page-head mb-4">
        <h1 class="hanzo-buyer-page-title">My products</h1>
        <p class="text-muted small mb-0">Listings visible to HANZO buyers. Use drafts until pricing and imagery are final.</p>
    </header>
    <?php if ($m = flash_get('success')): ?><div class="alert alert-success border-0 shadow-sm"><?= e($m) ?></div><?php endif; ?>

    <div class="hanzo-buyer-form-card bg-white mb-4 overflow-hidden">
        <div class="px-3 px-md-4 py-3 border-bottom bg-light bg-opacity-50 d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h2 class="h6 mb-0 fw-semibold text-dark"><?= is_array($edit) ? 'Edit product' : 'Add a product' ?></h2>
                <p class="small text-muted mb-0 mt-1">HANZO may review listings before they appear as <span class="text-success fw-semibold">active</span> in the marketplace.</p>
            </div>
            <?php if (is_array($edit)): ?>
                <a class="btn btn-sm btn-outline-secondary flex-shrink-0" href="<?= e(app_url('factory/products.php')) ?>">Clear &amp; add new</a>
            <?php endif; ?>
        </div>
        <form class="p-3 p-md-4" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= (int) ($ef['id'] ?? 0) ?>">
            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <label for="fp-name" class="form-label">Product name <span class="text-danger" aria-hidden="true">*</span></label>
                    <input class="form-control" id="fp-name" name="product_name" placeholder="e.g. LED panel kit, 400W" required value="<?= e((string) ($ef['product_name'] ?? '')) ?>">
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <label for="fp-cat" class="form-label">Category <span class="text-danger" aria-hidden="true">*</span></label>
                    <select class="form-select" id="fp-cat" name="category_id" required>
                        <?php if ($cats === []): ?>
                            <option value="">No categories available</option>
                        <?php else: ?>
                            <?php foreach ($cats as $c): ?>
                                <option value="<?= (int) $c['id'] ?>" <?= (int) ($ef['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-2">
                    <label for="fp-moq" class="form-label">MOQ <span class="text-danger" aria-hidden="true">*</span></label>
                    <input class="form-control" id="fp-moq" type="number" name="moq" min="1" step="1" inputmode="numeric" value="<?= (int) ($ef['moq'] ?? 1) ?>" required>
                    <div class="form-text">Minimum order quantity (units).</div>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <label for="fp-min" class="form-label">Min price (USD) <span class="text-danger" aria-hidden="true">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">US$</span>
                        <input class="form-control" id="fp-min" step="0.01" type="number" name="min_price" min="0" inputmode="decimal" placeholder="0.00" value="<?= e((string) ($ef['min_price'] ?? '')) ?>" required>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <label for="fp-max" class="form-label">Max price (USD) <span class="text-danger" aria-hidden="true">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">US$</span>
                        <input class="form-control" id="fp-max" step="0.01" type="number" name="max_price" min="0" inputmode="decimal" placeholder="0.00" value="<?= e((string) ($ef['max_price'] ?? '')) ?>" required>
                    </div>
                </div>
                <div class="col-12">
                    <label for="fp-desc" class="form-label">Description</label>
                    <textarea class="form-control" id="fp-desc" name="description" rows="4" placeholder="Materials, specs, packaging, lead time — anything buyers and HANZO need to quote accurately."><?= e((string) ($ef['description'] ?? '')) ?></textarea>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <label for="fp-status" class="form-label">Listing status</label>
                    <select class="form-select" id="fp-status" name="status">
                        <option value="active" <?= $currentStatus === 'active' ? 'selected' : '' ?>>Active — visible when approved</option>
                        <option value="draft" <?= $currentStatus === 'draft' ? 'selected' : '' ?>>Draft — not visible to buyers</option>
                        <option value="archived" <?= $currentStatus === 'archived' ? 'selected' : '' ?>>Archived — hidden from search</option>
                    </select>
                </div>
                <div class="col-12 col-md-6 col-lg-8">
                    <label for="fp-image" class="form-label">Main image <span class="text-muted fw-normal">(optional)</span></label>
                    <input type="file" class="form-control hanzo-buyer-file-input" id="fp-image" name="main_image" accept=".jpg,.jpeg,.png,.webp" aria-describedby="fp-image-help">
                    <div id="fp-image-help" class="form-text">JPG, PNG, or WebP. Square or 4:3 photos work best. Leave unchanged when editing to keep the current image.</div>
                </div>
            </div>
            <div class="d-flex flex-column flex-sm-row flex-wrap align-items-stretch align-items-sm-center justify-content-between gap-3 mt-4 pt-3 border-top">
                <p class="small text-muted mb-0" style="max-width: 32rem;">Prices are shown as a range to buyers. You can update this listing anytime from this page.</p>
                <button type="submit" class="btn btn-hanzo-primary btn-lg px-4 align-self-stretch align-self-sm-auto">Save product</button>
            </div>
        </form>
    </div>

    <h2 class="h6 text-uppercase text-muted fw-semibold letter-spacing-tight mb-3">Your listings</h2>
    <div class="table-responsive hanzo-buyer-table-wrap">
        <table class="table table-hover align-middle mb-0 hanzo-buyer-table">
            <thead><tr><th scope="col">Image</th><th scope="col">Name</th><th scope="col">Category</th><th scope="col">MOQ</th><th scope="col">Range</th><th scope="col">Status</th><th scope="col"></th></tr></thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <?php $pst = (string) $p['status']; ?>
                    <tr>
                        <td><img src="<?= e(product_image_url($p['main_image'])) ?>" alt="" width="40" height="40" class="rounded border"></td>
                        <td class="fw-semibold"><?= e($p['product_name']) ?></td>
                        <td><?= e($p['category_name']) ?></td>
                        <td><?= (int) $p['moq'] ?></td>
                        <td class="text-nowrap">US$<?= e(number_format((float) $p['min_price'], 2)) ?> – <?= e(number_format((float) $p['max_price'], 2)) ?></td>
                        <td><span class="badge <?= $pst === 'active' ? 'text-bg-success' : ($pst === 'draft' ? 'text-bg-secondary' : 'text-bg-warning') ?>"><?= e($pst) ?></span></td>
                        <td><a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('factory/products.php?edit=' . (int) $p['id'])) ?>">Edit</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($products === []): ?><tr><td colspan="7" class="text-center text-muted py-3">No products.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require __DIR__ . '/../includes/factory_sidebar_end.php'; ?>
<?php $footerMode = 'full'; require __DIR__ . '/../includes/footer.php'; ?>

