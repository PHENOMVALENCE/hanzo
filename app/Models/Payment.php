<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'type', 'amount_usd', 'method', 'reference', 'proof_path', 'mime_type',
        'status', 'verified_by', 'verified_at', 'rejection_reason', 'admin_notes',
    ];

    public const TYPES = ['deposit' => 'Deposit', 'balance' => 'Balance', 'full' => 'Full Payment'];

    protected function casts(): array
    {
        return [
            'amount_usd' => 'decimal:2',
            'verified_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
