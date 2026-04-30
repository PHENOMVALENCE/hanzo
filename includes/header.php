<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'China Chapu';
$extraHead = $extraHead ?? '';
$bodyClass = $bodyClass ?? 'hanzo-body';
hanzo_start_i18n_output_buffer();
?>
<!DOCTYPE html>
<html lang="<?= e(hanzo_html_lang()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | China Chapu</title>
    <meta name="description" content="China Chapu helps East African businesses source products from verified Chinese factories, manage quotations, payments, shipping, and documentation in one platform.">
    <link rel="icon" type="image/png" href="<?= e(app_url('assets/images/logo.png')) ?>">
    <link rel="shortcut icon" type="image/png" href="<?= e(app_url('assets/images/logo.png')) ?>">
    <link rel="apple-touch-icon" href="<?= e(app_url('assets/images/logo.png')) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= e(app_url('assets/css/hanzo.css')) ?>" rel="stylesheet">
    <link href="<?= e(app_url('assets/css/admin.css')) ?>" rel="stylesheet">
    <?= $extraHead ?>
</head>
<body class="<?= e($bodyClass) ?>">
