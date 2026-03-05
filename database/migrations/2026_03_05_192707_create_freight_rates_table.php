<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('freight_rates', function (Blueprint $table) {
            $table->id();
            $table->enum('method', ['sea', 'air']);
            $table->string('destination_port')->nullable();
            $table->string('destination_city')->nullable();
            $table->enum('rate_type', ['per_cbm', 'per_kg', 'per_container']);
            $table->decimal('rate_value', 12, 2);
            $table->decimal('min_charge', 12, 2)->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('freight_rates');
    }
};
