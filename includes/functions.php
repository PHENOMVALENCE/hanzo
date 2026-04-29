<?php

declare(strict_types=1);

function project_root(): string
{
    return dirname(__DIR__);
}

function app_base_url(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }
    $doc = $_SERVER['DOCUMENT_ROOT'] ?? '';
    $docReal = $doc !== '' ? realpath($doc) : false;
    $rootReal = realpath(__DIR__ . '/..');
    if ($docReal !== false && $rootReal !== false) {
        $docNorm = str_replace('\\', '/', $docReal);
        $rootNorm = str_replace('\\', '/', $rootReal);
        if (str_starts_with($rootNorm, $docNorm)) {
            $rel = substr($rootNorm, strlen($docNorm));
            $base = '/' . ltrim($rel, '/');
            return $base;
        }
    }
    $base = '';
    return $base;
}

function app_url(string $path = ''): string
{
    $base = rtrim(app_base_url(), '/');
    $path = ltrim($path, '/');
    if ($path === '') {
        return $base === '' ? '/' : $base . '/';
    }
    return ($base === '' ? '' : $base) . '/' . $path;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): void
{
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        header('Location: ' . $path);
        exit;
    }
    header('Location: ' . app_url($path));
    exit;
}

function format_usd_range(float|string $min, float|string $max, string $unit): string
{
    $a = is_string($min) ? (float) $min : $min;
    $b = is_string($max) ? (float) $max : $max;
    return 'US$' . number_format($a, 2) . ' - US$' . number_format($b, 2) . ' / ' . e($unit);
}

function format_moq(int $moq, string $unit): string
{
    return number_format($moq) . ' ' . e($unit) . (strtolower($unit) === 'piece' || strtolower($unit) === 'pieces' ? ' MOQ' : ' MOQ');
}

function product_image_url(?string $path): string
{
    if ($path === null || $path === '') {
        return app_url('assets/images/placeholder-product.svg');
    }
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }
    return app_url($path);
}

/** Resolved URL for a category card: explicit DB image, else first product thumbnail, else placeholder. */
function category_image_url(array $cat): string
{
    $explicit = $cat['image'] ?? null;
    if (is_string($explicit) && trim($explicit) !== '') {
        return product_image_url($explicit);
    }
    $thumb = $cat['thumb_image'] ?? null;
    if (is_string($thumb) && trim($thumb) !== '') {
        return product_image_url($thumb);
    }
    return product_image_url(null);
}

/** Human-readable label for order workflow status (buyer-facing). */
function order_status_label(string $status): string
{
    static $map = [
        'pending' => 'Pending review',
        'assigned' => 'With supplier',
        'quoted' => 'Quoted — action needed',
        'accepted' => 'Accepted',
        'in_production' => 'In production',
        'quality_control' => 'Quality control',
        'shipped' => 'Shipped',
        'in_customs' => 'In customs',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
    ];
    return $map[$status] ?? ucwords(str_replace('_', ' ', $status));
}

function order_status_badge_class(string $status): string
{
    return match ($status) {
        'pending', 'assigned' => 'text-bg-secondary',
        'quoted' => 'text-bg-warning',
        'accepted' => 'text-bg-success',
        'in_production', 'quality_control' => 'text-bg-info',
        'shipped', 'in_customs' => 'text-bg-primary',
        'delivered' => 'text-bg-success',
        'cancelled' => 'text-bg-danger',
        default => 'text-bg-secondary',
    };
}

function format_datetime(?string $sqlDatetime): string
{
    if ($sqlDatetime === null || $sqlDatetime === '') {
        return '';
    }
    $t = strtotime($sqlDatetime);
    return $t === false ? $sqlDatetime : date('M j, Y · g:i A', $t);
}

function format_date(?string $sqlDate): string
{
    if ($sqlDate === null || $sqlDate === '') {
        return '';
    }
    $t = strtotime($sqlDate);
    return $t === false ? $sqlDate : date('M j, Y', $t);
}

function quotation_status_label(string $status): string
{
    return match ($status) {
        'draft' => 'Draft',
        'sent' => 'Awaiting your response',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
        'expired' => 'Expired',
        default => ucfirst($status),
    };
}

function quotation_status_badge_class(string $status): string
{
    return match ($status) {
        'draft' => 'text-bg-secondary',
        'sent' => 'text-bg-warning',
        'accepted' => 'text-bg-success',
        'rejected' => 'text-bg-danger',
        'expired' => 'text-bg-secondary',
        default => 'text-bg-secondary',
    };
}

function payment_status_label(string $status): string
{
    return match ($status) {
        'pending' => 'Awaiting verification',
        'verified' => 'Verified',
        'rejected' => 'Rejected',
        default => ucfirst($status),
    };
}

function payment_status_badge_class(string $status): string
{
    return match ($status) {
        'pending' => 'text-bg-warning',
        'verified' => 'text-bg-success',
        'rejected' => 'text-bg-danger',
        default => 'text-bg-secondary',
    };
}

/** Whether the current page matches a buyer sidebar nav target. */
function buyer_sidebar_nav_active(string $primaryScript, array $alsoMatch = []): bool
{
    $cur = basename($_SERVER['SCRIPT_NAME'] ?? '');
    if ($cur === $primaryScript) {
        return true;
    }
    return in_array($cur, $alsoMatch, true);
}

/** Nav targets for buyer workspace (`rel` is path under app root, e.g. buyer/dashboard.php). */
function buyer_sidebar_nav_items(): array
{
    return [
        ['rel' => 'buyer/dashboard.php', 'label' => 'Dashboard', 'icon' => 'fa-home', 'also' => []],
        ['rel' => 'buyer/products.php', 'label' => 'Browse products', 'icon' => 'fa-th-large', 'also' => ['product-details.php']],
        ['rel' => 'buyer/orders.php', 'label' => 'My orders', 'icon' => 'fa-list-ul', 'also' => []],
        ['rel' => 'buyer/quotations.php', 'label' => 'Quotations', 'icon' => 'fa-file-invoice', 'also' => []],
        ['rel' => 'buyer/tracking.php', 'label' => 'Shipment tracking', 'icon' => 'fa-route', 'also' => []],
        ['rel' => 'buyer/payments.php', 'label' => 'Payments', 'icon' => 'fa-wallet', 'also' => []],
        ['rel' => 'buyer/documents.php', 'label' => 'Documents', 'icon' => 'fa-folder-open', 'also' => []],
        ['rel' => 'buyer/cart.php', 'label' => 'Quick request cart', 'icon' => 'fa-shopping-cart', 'also' => []],
        ['rel' => 'profile.php', 'label' => 'My profile', 'icon' => 'fa-user-cog', 'also' => []],
    ];
}

/** @param 'desktop'|'offcanvas' $context */
function buyer_render_sidebar_nav(string $context = 'desktop'): void
{
    $navClass = $context === 'offcanvas'
        ? 'nav flex-column gap-1 hanzo-buyer-nav-stack'
        : 'nav flex-column gap-1 hanzo-buyer-nav-stack';
    $aria = $context === 'offcanvas' ? 'Buyer workspace' : 'Buyer workspace sections';
    echo '<nav class="' . e($navClass) . '" aria-label="' . e($aria) . '">';
    foreach (buyer_sidebar_nav_items() as $item) {
        $base = basename(str_replace('\\', '/', $item['rel']));
        $active = buyer_sidebar_nav_active($base, $item['also']);
        $cls = 'hanzo-buyer-nav-link' . ($active ? ' is-active' : '');
        echo '<a class="' . e($cls) . '" href="' . e(app_url($item['rel'])) . '">';
        echo '<span class="hanzo-buyer-nav-icon-wrap" aria-hidden="true"><i class="fas ' . e($item['icon']) . '"></i></span>';
        echo '<span class="hanzo-buyer-nav-text">' . e($item['label']) . '</span>';
        echo '</a>';
    }
    echo '</nav>';
}

/** Nav targets for factory partner workspace (`rel` under app root). */
function factory_sidebar_nav_items(): array
{
    return [
        ['rel' => 'factory/dashboard.php', 'label' => 'Dashboard', 'icon' => 'fa-tachometer-alt', 'also' => []],
        ['rel' => 'factory/products.php', 'label' => 'My products', 'icon' => 'fa-boxes', 'also' => []],
        ['rel' => 'factory/assigned-orders.php', 'label' => 'Assigned orders', 'icon' => 'fa-clipboard-list', 'also' => []],
        ['rel' => 'factory/production-updates.php', 'label' => 'Production updates', 'icon' => 'fa-industry', 'also' => []],
        ['rel' => 'profile.php', 'label' => 'My profile', 'icon' => 'fa-user-cog', 'also' => []],
    ];
}

/** @param 'desktop'|'offcanvas' $context */
function factory_render_sidebar_nav(string $context = 'desktop'): void
{
    $navClass = 'nav flex-column gap-1 hanzo-buyer-nav-stack';
    $aria = $context === 'offcanvas' ? 'Factory workspace' : 'Factory workspace sections';
    echo '<nav class="' . e($navClass) . '" aria-label="' . e($aria) . '">';
    foreach (factory_sidebar_nav_items() as $item) {
        $base = basename(str_replace('\\', '/', $item['rel']));
        $active = buyer_sidebar_nav_active($base, $item['also']);
        $cls = 'hanzo-buyer-nav-link' . ($active ? ' is-active' : '');
        echo '<a class="' . e($cls) . '" href="' . e(app_url($item['rel'])) . '">';
        echo '<span class="hanzo-buyer-nav-icon-wrap" aria-hidden="true"><i class="fas ' . e($item['icon']) . '"></i></span>';
        echo '<span class="hanzo-buyer-nav-text">' . e($item['label']) . '</span>';
        echo '</a>';
    }
    echo '</nav>';
}

function flash_set(string $key, string $message): void
{
    $_SESSION['_flash'][$key] = $message;
}

function flash_get(string $key): ?string
{
    if (!isset($_SESSION['_flash'][$key])) {
        return null;
    }
    $m = $_SESSION['_flash'][$key];
    unset($_SESSION['_flash'][$key]);
    return $m;
}

function validate_upload_image_doc(array $file, int $maxBytes = 5242880): array
{
    $errors = [];
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['ok' => true, 'path' => null, 'errors' => []];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'path' => null, 'errors' => ['File upload failed.']];
    }
    if ($file['size'] > $maxBytes) {
        $errors[] = 'File must be at most ' . round($maxBytes / 1048576, 1) . ' MB.';
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'application/pdf' => 'pdf',
    ];
    if (!isset($allowed[$mime])) {
        $errors[] = 'Allowed types: JPG, PNG, WebP, PDF.';
    }
    if ($errors !== []) {
        return ['ok' => false, 'path' => null, 'errors' => $errors];
    }
    $ext = $allowed[$mime];
    $name = bin2hex(random_bytes(8)) . '.' . $ext;
    return ['ok' => true, 'filename' => $name, 'errors' => []];
}

function save_uploaded_file(array $file, string $subdir, string $filename): ?string
{
    $dir = project_root() . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $subdir;
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        return null;
    }
    $dest = $dir . DIRECTORY_SEPARATOR . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return null;
    }
    return 'uploads/' . $subdir . '/' . $filename;
}

function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    if (is_string($ascii) && $ascii !== '') {
        $text = $ascii;
    }
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return $text !== '' ? $text : 'item';
}
