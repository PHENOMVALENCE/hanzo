<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'name', 'description', 'moq_default', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function rfqs(): HasMany
    {
        return $this->hasMany(Rfq::class);
    }
}
