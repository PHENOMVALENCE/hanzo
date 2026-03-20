<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
<<<<<<< HEAD
use Illuminate\Support\Str;
=======
use Illuminate\Database\Eloquent\Relations\HasMany;
>>>>>>> 3a34daee (Hanzo in b2b style)

class Product extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
<<<<<<< HEAD
    public const STATUS_PENDING = 'pending_review';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'factory_id', 'category_id', 'name', 'sku', 'description', 'specs',
        'moq', 'price_per_unit', 'price_min', 'price_max', 'lead_time_days',
        'image_path', 'status',
    ];

=======
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

>>>>>>> 3a34daee (Hanzo in b2b style)
    protected function casts(): array
    {
        return [
            'specs' => 'array',
<<<<<<< HEAD
            'moq' => 'integer',
            'price_per_unit' => 'decimal:2',
            'price_min' => 'decimal:2',
            'price_max' => 'decimal:2',
=======
            'images' => 'array',
            'price_min' => 'decimal:2',
            'price_max' => 'decimal:2',
            'moq' => 'integer',
>>>>>>> 3a34daee (Hanzo in b2b style)
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

<<<<<<< HEAD
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
=======
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
            return '$' . number_format($this->price_min, 2) . ' - $' . number_format($this->price_max, 2);
        }
        if ($this->price_min) {
            return '$' . number_format($this->price_min, 2) . '+';
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
>>>>>>> 3a34daee (Hanzo in b2b style)
    }
}
