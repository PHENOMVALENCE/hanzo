<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id', 'order_code', 'buyer_id', 'milestone_status',
        'tracking_number', 'estimated_arrival',
    ];

    protected function casts(): array
    {
        return ['estimated_arrival' => 'date'];
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(OrderMilestone::class);
    }

    public function productionUpdates(): HasMany
    {
        return $this->hasMany(ProductionUpdate::class);
    }

    public function displayName(): string
    {
        $rfq = $this->quotation?->rfq;
        if (!$rfq) {
            return $this->order_code;
        }
        $category = $rfq->category?->name ?? 'Order';
        $desc = $rfq->description ? Str::limit(strip_tags($rfq->description), 50) : '';
        return $desc ? "{$category}: {$desc}" : $category;
    }
}
