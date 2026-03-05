<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreightRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'method', 'destination_port', 'destination_city', 'rate_type',
        'rate_value', 'min_charge', 'active',
    ];

    protected function casts(): array
    {
        return [
            'rate_value' => 'decimal:2',
            'min_charge' => 'decimal:2',
            'active' => 'boolean',
        ];
    }
}
