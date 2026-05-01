<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/buyer_notifications.php';

$productId = isset($_GET['product_id']) ? (int) $_GET['product_id'] : (isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0);
if ($productId <= 0) {
    flash_set('error', 'Select a product first.');
    redirect('index.php');
}

$st = $pdo->prepare('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE p.id = ? AND p.status = "active"');
$st->execute([$productId]);
$product = $st->fetch();
if (!$product) {
    flash_set('error', 'Product not found.');
    redirect('index.php');
}

if (!auth_user()) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? app_url('inquiry.php?product_id=' . $productId);
    redirect('login.php');
}
require_buyer();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
    $delivery = trim((string) ($_POST['delivery_location'] ?? ''));
    $notes = trim((string) ($_POST['notes'] ?? ''));

    if ($delivery === '') {
        $errors[] = 'Delivery location is required.';
    }

    if ($errors === []) {
        $orderCode = 'HNZ-ORD-' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        $priceRange = 'US$' . number_format((float) $product['min_price'], 2) . ' - US$' . number_format((float) $product['max_price'], 2);
        $pdo->prepare('INSERT INTO orders (order_code, buyer_id, product_id, quantity, price_range, delivery_location, status) VALUES (?,?,?,?,?,?,"pending")')
            ->execute([$orderCode, auth_id(), $productId, $quantity, $priceRange, $delivery]);
        $orderId = (int) $pdo->lastInsertId();
        buyer_notify_order_submitted($pdo, $orderId);

        if ($notes !== '') {
            $pdo->prepare('INSERT INTO shipping_updates (order_id, status_title, description, location, updated_by) VALUES (?,?,?,?,?)')
                ->execute([$orderId, 'Request submitted', $notes, $delivery, auth_id()]);
        }
        flash_set('success', 'Order request submitted to China Chapu. Admin will assign a factory and prepare official quotation.');
        redirect('buyer/orders.php');
    }
}

$pageTitle = 'Submit request';
require __DIR__ . '/includes/header.php';
$hideShopNav = false;
require __DIR__ . '/includes/navbar.php';
?>
<main class="container py-4" style="max-width:720px;">
    <h1 class="h3 mb-3">Submit product request</h1>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex gap-3">
            <img src="<?= e(product_image_url($product['main_image'])) ?>" alt="" style="width:96px;height:96px;object-fit:cover;" class="rounded border">
            <div>
                <div class="small text-muted"><?= e($product['category_name']) ?></div>
                <div class="fw-bold"><?= e($product['product_name']) ?></div>
                <div class="small"><?= format_usd_range($product['min_price'], $product['max_price'], 'Piece') ?> · <?= format_moq((int) $product['moq'], 'Piece') ?></div>
            </div>
        </div>
    </div>

    <?php foreach ($errors as $er): ?>
        <div class="alert alert-danger"><?= e($er) ?></div>
    <?php endforeach; ?>

    <form method="post" class="bg-white border rounded p-4 shadow-sm">
        <input type="hidden" name="product_id" value="<?= (int) $productId ?>">
        <div class="mb-3">
            <label class="form-label">Quantity</label>
            <input type="number" name="quantity" class="form-control" min="1" value="<?= e((string) ($_POST['quantity'] ?? $product['moq'])) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Delivery location</label>
            <input type="text" name="delivery_location" class="form-control" maxlength="255" value="<?= e((string) ($_POST['delivery_location'] ?? '')) ?>" required aria-describedby="delivery-location-hint">
            <div id="delivery-location-hint" class="form-text">Prefer a nearby monument or another known place (not just a street name) so we can pin the delivery spot.</div>
        </div>
        <div class="mb-3">
            <label class="form-label">Notes / specifications <span class="text-muted fw-normal">(optional)</span></label>
            <textarea name="notes" class="form-control" rows="4" maxlength="5000" aria-describedby="notes-hint"><?= e((string) ($_POST['notes'] ?? '')) ?></textarea>
            <div id="notes-hint" class="form-text">Optional — add any extra details, materials, sizes, or packaging preferences if you want them on the request.</div>
        </div>
        <button type="submit" class="btn btn-hanzo-primary">Submit request</button>
    </form>
</main>
<?php
$footerMode = 'full';
require __DIR__ . '/includes/footer.php';
