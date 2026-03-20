<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rfq extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'buyer_id', 'category_id', 'product_id', 'description', 'specs', 'quantity',
        'target_price_per_unit', 'timeline_weeks', 'delivery_country', 'delivery_city', 'status',
        'assigned_factory_id', 'assigned_admin_id',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function assignedFactory(): BelongsTo
    {
        return $this->belongsTo(Factory::class, 'assigned_factory_id');
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(RfqAttachment::class);
    }

    public function factoryQuotes(): HasMany
    {
        return $this->hasMany(FactoryQuote::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }
}
