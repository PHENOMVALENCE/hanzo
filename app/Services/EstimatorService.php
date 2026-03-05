<?php

namespace App\Services;

use App\Models\FreightRate;

class EstimatorService
{
    public function estimate(string $category, int $quantity, string $method, string $destination): array
    {
        $rates = FreightRate::where('method', $method)
            ->where('active', true)
            ->where(function ($q) use ($destination) {
                $q->where('destination_port', $destination)
                    ->orWhere('destination_city', $destination);
            })
            ->get();

        if ($rates->isEmpty()) {
            return [
                'min' => 0,
                'max' => 0,
                'unit' => 'usd',
                'message' => 'No freight rates found for this destination.',
            ];
        }

        $minEst = PHP_FLOAT_MAX;
        $maxEst = 0;

        foreach ($rates as $rate) {
            $est = match ($rate->rate_type) {
                'per_cbm' => $quantity * 0.001 * $rate->rate_value, // simplified: qty as cbm
                'per_kg' => $quantity * $rate->rate_value,
                'per_container' => max($rate->min_charge, $rate->rate_value),
                default => $rate->min_charge,
            };
            $minEst = min($minEst, $est);
            $maxEst = max($maxEst, $est);
        }

        return [
            'min' => round($minEst, 2),
            'max' => round($maxEst, 2),
            'unit' => 'usd',
            'method' => $method,
            'destination' => $destination,
        ];
    }
}
