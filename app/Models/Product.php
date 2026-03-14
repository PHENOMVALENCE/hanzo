<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending_review';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'factory_id', 'category_id', 'name', 'sku', 'description', 'specs',
        'moq', 'price_per_unit', 'price_min', 'price_max', 'lead_time_days',
        'image_path', 'status',
    ];

    protected function casts(): array
    {
        return [
            'specs' => 'array',
            'moq' => 'integer',
            'price_per_unit' => 'decimal:2',
            'price_min' => 'decimal:2',
            'price_max' => 'decimal:2',
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

    public function scopeForFactory($query, int $factoryId)
    {
        return $query->where('factory_id', $factoryId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function getPriceRangeAttribute(): ?string
    {
        if ($this->price_per_unit) {
            return '$' . number_format($this->price_per_unit, 2) . ' / unit';
        }
        if ($this->price_min && $this->price_max) {
            return '$' . number_format($this->price_min, 0) . ' - $' . number_format($this->price_max, 0) . ' / unit';
        }
        return null;
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }
        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->image_path);
    }

    public static function makeSku(string $name): string
    {
        $base = Str::slug(Str::limit($name, 30));
        $base = $base ?: 'product';
        return strtolower($base . '-' . substr(uniqid(), -5));
    }
}
