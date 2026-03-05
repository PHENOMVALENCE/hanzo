<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RfqAttachment extends Model
{
    protected $fillable = ['rfq_id', 'file_path', 'original_name', 'mime'];

    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class);
    }
}
