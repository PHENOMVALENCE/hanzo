<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factory_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rfq_id')->constrained()->cascadeOnDelete();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->decimal('unit_price_usd', 12, 2);
            $table->integer('moq_confirmed')->nullable();
            $table->integer('lead_time_days')->nullable();
            $table->text('notes_internal')->nullable();
            $table->enum('status', ['submitted', 'withdrawn'])->default('submitted');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factory_quotes');
    }
};
