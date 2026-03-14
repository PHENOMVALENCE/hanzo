<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupplierLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name', 'product_category', 'contact_email', 'whatsapp',
        'message', 'status', 'admin_notes',
    ];
}
