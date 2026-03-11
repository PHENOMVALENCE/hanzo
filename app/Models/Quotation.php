<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfq_id', 'quote_code', 'valid_until', 'status',
        'rejection_reason', 'rejected_at',
        'product_cost_usd', 'china_local_shipping', 'export_handling', 'freight_cost',
        'insurance_cost', 'clearing_cost', 'local_delivery_cost', 'hanzo_fee',
        'total_landed_cost', 'factory_id',
    ];

    protected function casts(): array
    {
        return [
            'valid_until' => 'date',
            'rejected_at' => 'datetime',
            'product_cost_usd' => 'decimal:2',
            'china_local_shipping' => 'decimal:2',
            'export_handling' => 'decimal:2',
            'freight_cost' => 'decimal:2',
            'insurance_cost' => 'decimal:2',
            'clearing_cost' => 'decimal:2',
            'local_delivery_cost' => 'decimal:2',
            'hanzo_fee' => 'decimal:2',
            'total_landed_cost' => 'decimal:2',
        ];
    }

    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class);
    }

    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class);
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }
}
