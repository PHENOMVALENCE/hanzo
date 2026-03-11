<?php

namespace App\Services;

use App\Models\Category;
use App\Models\FreightRate;
use App\Models\EstimateDefault;
use App\Models\TransportDefault;

class EstimatorService
{
    /**
     * Return full price estimate: factory range, freight, customs, total, MOQ.
     */
    public function estimate(int $categoryId, int $quantity, string $destination): array
    {
        // If admins have defined a global default estimate, always use that
        // for RFQs/orders until an official quotation is issued.
        $default = EstimateDefault::current();
        if ($default) {
            return [
                'factory_min' => $this->formatMoney($default->factory_min),
                'factory_max' => $this->formatMoney($default->factory_max),
                'freight_min' => $this->formatMoney($default->freight_min),
                'freight_max' => $this->formatMoney($default->freight_max),
                'customs_min' => $this->formatMoney($default->customs_min),
                'customs_max' => $this->formatMoney($default->customs_max),
                'total_min' => $this->formatMoney($default->total_min),
                'total_max' => $this->formatMoney($default->total_max),
                'moq' => $default->moq,
                'unit' => $default->currency ?: 'usd',
            ];
        }

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
            $default = TransportDefault::getForMethod('sea');
            if ($default) {
                $est = $default->estimateFreight($quantity);
                $freightMin = $est['min'];
                $freightMax = $est['max'];
            } else {
                $freightMin = round(200 + $quantity * 0.05, 2);
                $freightMax = round(800 + $quantity * 0.1, 2);
            }
        }

        $customsMin = round($factoryTotalMin * 0.08, 2);
        $customsMax = round($factoryTotalMax * 0.15, 2);

        $totalMin = round($factoryTotalMin + $freightMin + $customsMin, 2);
        $totalMax = round($factoryTotalMax + $freightMax + $customsMax, 2);

        return [
            'factory_min' => $this->formatMoney($priceMin),
            'factory_max' => $this->formatMoney($priceMax),
            'freight_min' => $this->formatMoney($freightMin),
            'freight_max' => $this->formatMoney($freightMax),
            'customs_min' => $this->formatMoney($customsMin),
            'customs_max' => $this->formatMoney($customsMax),
            'total_min' => $this->formatMoney($totalMin),
            'total_max' => $this->formatMoney($totalMax),
            'moq' => $moq,
            'unit' => 'usd',
        ];
    }

    protected function formatMoney(?float $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return number_format($value, 2, '.', '');
    }
}
