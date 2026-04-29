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
