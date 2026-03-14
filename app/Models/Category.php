<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'name', 'description', 'moq_default', 'price_min_per_unit', 'price_max_per_unit', 'active'];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'price_min_per_unit' => 'decimal:2',
            'price_max_per_unit' => 'decimal:2',
        ];
    }

    public function rfqs(): HasMany
    {
        return $this->hasMany(Rfq::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
