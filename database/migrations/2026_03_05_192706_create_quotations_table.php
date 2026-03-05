<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rfq_id')->constrained()->cascadeOnDelete();
            $table->string('quote_code')->unique();
            $table->date('valid_until')->nullable();
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired'])->default('draft');
            $table->decimal('product_cost_usd', 12, 2)->default(0);
            $table->decimal('china_local_shipping', 12, 2)->default(0);
            $table->decimal('export_handling', 12, 2)->default(0);
            $table->decimal('freight_cost', 12, 2)->default(0);
            $table->decimal('insurance_cost', 12, 2)->default(0);
            $table->decimal('clearing_cost', 12, 2)->default(0);
            $table->decimal('local_delivery_cost', 12, 2)->default(0);
            $table->decimal('hanzo_fee', 12, 2)->default(0);
            $table->decimal('total_landed_cost', 12, 2)->default(0);
            $table->foreignId('factory_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
