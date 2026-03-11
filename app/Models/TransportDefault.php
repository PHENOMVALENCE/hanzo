<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportDefault extends Model
{
    protected $fillable = [
        'method', 'base_min', 'per_unit_min', 'base_max', 'per_unit_max',
    ];

    protected function casts(): array
    {
        return [
            'base_min' => 'decimal:2',
            'per_unit_min' => 'decimal:4',
            'base_max' => 'decimal:2',
            'per_unit_max' => 'decimal:4',
        ];
    }

    public function estimateFreight(int $quantity): array
    {
        $min = (float) $this->base_min + $quantity * (float) $this->per_unit_min;
        $max = (float) $this->base_max + $quantity * (float) $this->per_unit_max;

        return [
            'min' => round($min, 2),
            'max' => round($max, 2),
        ];
    }

    public static function getForMethod(string $method): ?self
    {
        return static::where('method', $method)->first();
    }
}
