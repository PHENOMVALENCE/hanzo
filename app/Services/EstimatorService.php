<?php

namespace App\Services;

use App\Models\Category;
use App\Models\FreightRate;

class EstimatorService
{
    /**
     * Return full price estimate: factory range, freight, customs, total, MOQ.
     */
    public function estimate(int $categoryId, int $quantity, string $destination): array
    {
        $category = Category::find($categoryId);
        if (! $category) {
            return ['error' => true, 'message' => 'Invalid category'];
        }

        $moq = (int) ($category->moq_default ?? 100);
        $priceMin = (float) ($category->price_min_per_unit ?? 1);
        $priceMax = (float) ($category->price_max_per_unit ?? 10);

        $factoryTotalMin = round($quantity * $priceMin, 2);
        $factoryTotalMax = round($quantity * $priceMax, 2);

        $rates = FreightRate::where('method', 'sea')
            ->where('active', true)
            ->where(function ($q) use ($destination) {
                $q->where('destination_port', 'LIKE', "%{$destination}%")
                    ->orWhere('destination_city', 'LIKE', "%{$destination}%");
            })
            ->get();

        $freightMin = 0.0;
        $freightMax = 0.0;
        if (! $rates->isEmpty()) {
            $minF = PHP_FLOAT_MAX;
            $maxF = 0.0;
            foreach ($rates as $rate) {
                $est = match ($rate->rate_type) {
                    'per_cbm' => $quantity * 0.001 * (float) $rate->rate_value,
                    'per_kg' => $quantity * 0.001 * (float) $rate->rate_value,
                    'per_container' => max((float) $rate->min_charge, (float) $rate->rate_value),
                    default => (float) $rate->min_charge,
                };
                $minF = min($minF, $est);
                $maxF = max($maxF, $est);
            }
            $freightMin = round($minF, 2);
            $freightMax = round(max($maxF, 500), 2);
        } else {
            $freightMin = round(200 + $quantity * 0.05, 2);
            $freightMax = round(800 + $quantity * 0.1, 2);
        }

        $customsMin = round($factoryTotalMin * 0.08, 2);
        $customsMax = round($factoryTotalMax * 0.15, 2);

        $totalMin = round($factoryTotalMin + $freightMin + $customsMin, 2);
        $totalMax = round($factoryTotalMax + $freightMax + $customsMax, 2);

        return [
            'factory_min' => number_format($priceMin, 2),
            'factory_max' => number_format($priceMax, 2),
            'freight_min' => number_format($freightMin, 2),
            'freight_max' => number_format($freightMax, 2),
            'customs_min' => number_format($customsMin, 2),
            'customs_max' => number_format($customsMax, 2),
            'total_min' => number_format($totalMin, 2),
            'total_max' => number_format($totalMax, 2),
            'moq' => $moq,
            'unit' => 'usd',
        ];
    }
}
