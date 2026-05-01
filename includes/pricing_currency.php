<?php

declare(strict_types=1);

const HANZO_PRICE_DISPLAY_CURRENCIES = ['USD', 'TZS', 'CNY'];
const HANZO_DEFAULT_PRICE_DISPLAY = 'USD';

function hanzo_normalize_price_currency(mixed $currency): string
{
    $c = is_string($currency) ? strtoupper(trim($currency)) : '';
    return in_array($c, HANZO_PRICE_DISPLAY_CURRENCIES, true) ? $c : HANZO_DEFAULT_PRICE_DISPLAY;
}

function hanzo_price_display_currency(): string
{
    return hanzo_normalize_price_currency($_SESSION['price_display_currency'] ?? HANZO_DEFAULT_PRICE_DISPLAY);
}

function hanzo_set_price_display_currency(string $currency): void
{
    $_SESSION['price_display_currency'] = hanzo_normalize_price_currency($currency);
}

/** @return array{TZS_PER_USD: float, CNY_PER_USD: float} */
function hanzo_exchange_rates(): array
{
    static $rates = null;
    if ($rates !== null) {
        return $rates;
    }
    $path = dirname(__DIR__) . '/config/exchange_rates.php';
    $raw = is_file($path) ? require $path : [];
    $rates = [
        'TZS_PER_USD' => isset($raw['TZS_PER_USD']) ? (float) $raw['TZS_PER_USD'] : 2580.0,
        'CNY_PER_USD' => isset($raw['CNY_PER_USD']) ? (float) $raw['CNY_PER_USD'] : 7.25,
    ];
    return $rates;
}

function hanzo_convert_usd_to_display(float $usdAmount, ?string $targetCurrency = null): float
{
    $target = hanzo_normalize_price_currency($targetCurrency ?? hanzo_price_display_currency());
    if ($target === 'USD') {
        return $usdAmount;
    }
    $r = hanzo_exchange_rates();
    if ($target === 'TZS') {
        return $usdAmount * max(0.0, $r['TZS_PER_USD']);
    }
    if ($target === 'CNY') {
        return $usdAmount * max(0.0, $r['CNY_PER_USD']);
    }
    return $usdAmount;
}

function hanzo_format_money_display(float $amount, ?string $currency = null): string
{
    $c = hanzo_normalize_price_currency($currency ?? hanzo_price_display_currency());
    if ($c === 'USD') {
        return 'US$' . number_format($amount, 2);
    }
    if ($c === 'TZS') {
        return 'TZS ' . number_format($amount, 0);
    }
    return '¥' . number_format($amount, 2);
}

function hanzo_format_product_price_range(float|string $minUsd, float|string $maxUsd, string $unit): string
{
    $min = is_string($minUsd) ? (float) $minUsd : $minUsd;
    $max = is_string($maxUsd) ? (float) $maxUsd : $maxUsd;
    $cur = hanzo_price_display_currency();
    $a = hanzo_convert_usd_to_display($min, $cur);
    $b = hanzo_convert_usd_to_display($max, $cur);
    return hanzo_format_money_display($a, $cur) . ' - ' . hanzo_format_money_display($b, $cur) . ' / ' . e($unit);
}

function hanzo_price_currency_switch_url(string $currency): string
{
    $currency = hanzo_normalize_price_currency($currency);
    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
    $parts = parse_url($uri);
    $path = (string) ($parts['path'] ?? '/');
    $query = [];
    if (isset($parts['query'])) {
        parse_str((string) $parts['query'], $query);
    }
    $query['price_currency'] = $currency;
    return $path . '?' . http_build_query($query);
}

function hanzo_price_currency_bootstrap(): void
{
    if (!isset($_SESSION['price_display_currency'])) {
        $_SESSION['price_display_currency'] = HANZO_DEFAULT_PRICE_DISPLAY;
    }

    $requested = $_GET['price_currency'] ?? null;
    if (is_string($requested) && $requested !== '') {
        hanzo_set_price_display_currency($requested);
    }

    $_SESSION['price_display_currency'] = hanzo_normalize_price_currency($_SESSION['price_display_currency']);
}
