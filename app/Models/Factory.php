<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Factory extends Model
{
    use HasFactory;

    protected $table = 'factories';

    protected $fillable = [
        'user_id', 'factory_name', 'location_china', 'categories', 'contact_wechat',
        'contact_phone', 'contact_email', 'verification_status', 'notes',
    ];

    protected function casts(): array
    {
        return ['categories' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rfqs(): HasMany
    {
        return $this->hasMany(Rfq::class, 'assigned_factory_id');
    }

    public function factoryQuotes(): HasMany
    {
        return $this->hasMany(FactoryQuote::class, 'factory_id');
    }
}
