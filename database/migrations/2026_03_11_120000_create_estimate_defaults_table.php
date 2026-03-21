<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('estimate_defaults')) {
            return;
        }

        Schema::create('estimate_defaults', function (Blueprint $table) {
            $table->id();
            $table->decimal('factory_min', 10, 2)->nullable();
            $table->decimal('factory_max', 10, 2)->nullable();
            $table->decimal('freight_min', 10, 2)->nullable();
            $table->decimal('freight_max', 10, 2)->nullable();
            $table->decimal('customs_min', 10, 2)->nullable();
            $table->decimal('customs_max', 10, 2)->nullable();
            $table->decimal('total_min', 12, 2)->nullable();
            $table->decimal('total_max', 12, 2)->nullable();
            $table->unsignedInteger('moq')->nullable();
            $table->string('currency', 10)->default('usd');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimate_defaults');
    }
};

