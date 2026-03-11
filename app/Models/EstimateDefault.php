<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstimateDefault extends Model
{
    protected $fillable = [
        'factory_min',
        'factory_max',
        'freight_min',
        'freight_max',
        'customs_min',
        'customs_max',
        'total_min',
        'total_max',
        'moq',
        'currency',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'factory_min' => 'decimal:2',
            'factory_max' => 'decimal:2',
            'freight_min' => 'decimal:2',
            'freight_max' => 'decimal:2',
            'customs_min' => 'decimal:2',
            'customs_max' => 'decimal:2',
            'total_min' => 'decimal:2',
            'total_max' => 'decimal:2',
            'moq' => 'integer',
            'active' => 'boolean',
        ];
    }

    public static function current(): ?self
    {
        return static::where('active', true)->latest('id')->first();
    }
}

