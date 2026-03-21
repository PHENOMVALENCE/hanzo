<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_APPROVAL = 'pending_approval';
    public const STATUS_LIVE = 'live';
    public const STATUS_DISABLED = 'disabled';

    protected $fillable = [
        'factory_id', 'category_id', 'created_by_admin_id', 'name', 'title', 'description',
        'specs', 'images', 'price_min', 'price_max', 'moq', 'lead_time_days',
        'location', 'status', 'is_platform_product',
    ];

    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            if (($product->title ?? $product->name) && empty($product->name)) {
                $product->name = $product->title;
            }
            if (($product->name ?? $product->title) && empty($product->title)) {
                $product->title = $product->name;
            }
        });
    }

    protected function casts(): array
    {
        return [
            'specs' => 'array',
            'images' => 'array',
            'price_min' => 'decimal:2',
            'price_max' => 'decimal:2',
            'moq' => 'integer',
            'lead_time_days' => 'integer',
        ];
    }

    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_admin_id');
    }

    public function rfqs(): HasMany
    {
        return $this->hasMany(Rfq::class);
    }

    public function scopeLive($query)
    {
        return $query->where('status', self::STATUS_LIVE);
    }

    public function scopeForBuyerCatalog($query)
    {
        return $query->live();
    }

    public function priceDisplay(): string
    {
        if ($this->price_min && $this->price_max) {
            return money((float) $this->price_min) . ' - ' . money((float) $this->price_max);
        }
        if ($this->price_min) {
            return money((float) $this->price_min) . '+';
        }
        return __('labels.contact_for_price');
    }

    public function primaryImage(): ?string
    {
        $images = $this->images ?? [];
        if (empty($images)) {
            return null;
        }
        return is_array($images) ? ($images[0] ?? null) : $images;
    }
}
