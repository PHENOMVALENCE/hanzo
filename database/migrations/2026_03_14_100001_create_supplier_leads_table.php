<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_leads', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('product_category');
            $table->string('contact_email');
            $table->string('whatsapp')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('new'); // new, contacted, converted, declined
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_leads');
    }
};
