<?php

if (! function_exists('trans_status')) {
    /**
     * Translate a status key (order milestone, payment status, etc.) into the active locale.
     */
    function trans_status(?string $status): string
    {
        if (! $status) {
            return '-';
        }
        $key = 'statuses.' . $status;
        $translated = __($key);
        return $translated === $key ? ucfirst(str_replace('_', ' ', $status)) : $translated;
    }
}

if (! function_exists('trans_category')) {
    /**
     * Translate a category name when a known slug exists; otherwise return the original name.
     */
    function trans_category(?object $category): string
    {
        if (! $category) {
            return '';
        }
        $slug = $category->slug ?? null;
        $name = $category->name ?? '';
        if ($slug) {
            $key = 'categories.' . $slug;
            $translated = __($key);
            if ($translated !== $key) {
                return $translated;
            }
        }
        return $name;
    }
}

if (! function_exists('money')) {
    /**
     * Format a USD amount in the user's selected currency.
     */
    function money(?float $amountUsd, bool $showCode = false): string
    {
        if ($amountUsd === null) {
            return '-';
        }

        $currency = session('currency', config('currencies.default', 'USD'));
        $rates = config('currencies.rates', []);
        $symbols = config('currencies.symbols', []);
        $decimals = config('currencies.decimals', []);

        $rate = $rates[$currency] ?? 1;
        $converted = $amountUsd * $rate;
        $symbol = $symbols[$currency] ?? '$';
        $decimals = $decimals[$currency] ?? 2;

        $formatted = number_format($converted, $decimals);

        if ($showCode) {
            return $symbol . $formatted . ' ' . $currency;
        }

        return $symbol . $formatted;
    }
}
