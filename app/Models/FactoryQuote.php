<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FactoryQuote extends Model
{
    protected $fillable = [
        'rfq_id', 'factory_id', 'unit_price_usd', 'moq_confirmed', 'lead_time_days',
        'notes_internal', 'status',
    ];

    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class);
    }

    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class);
    }
}
