<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = ['order_id', 'type', 'file_path', 'original_name', 'description', 'uploaded_by'];

    public const TYPES = [
        'invoice' => 'Commercial Invoice',
        'packing_list' => 'Packing List',
        'bl_awb' => 'Bill of Lading / AWB',
        'customs' => 'Customs Documents',
        'delivery' => 'Delivery Note',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
