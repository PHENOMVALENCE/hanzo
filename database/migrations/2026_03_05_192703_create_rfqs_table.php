<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rfqs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->text('specs')->nullable();
            $table->integer('quantity');
            $table->integer('timeline_weeks')->nullable();
            $table->string('delivery_country')->nullable();
            $table->string('delivery_city')->nullable();
            $table->enum('status', [
                'new', 'assigned', 'pricing_received', 'quoted', 'accepted',
                'in_production', 'shipped', 'in_customs', 'delivered', 'cancelled'
            ])->default('new');
            $table->foreignId('assigned_factory_id')->nullable()->constrained('factories')->nullOnDelete();
            $table->foreignId('assigned_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfqs');
    }
};
