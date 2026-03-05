<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->string('order_code')->unique();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->enum('milestone_status', [
                'deposit_pending', 'deposit_paid', 'in_production', 'quality_control',
                'shipped', 'in_customs', 'delivered'
            ])->default('deposit_pending');
            $table->string('tracking_number')->nullable();
            $table->date('estimated_arrival')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
