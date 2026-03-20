<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            return;
        }
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->nullable()->constrained('factories')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('specs')->nullable();
            $table->json('images')->nullable(); // ['path1', 'path2', ...]
            $table->decimal('price_min', 12, 2)->nullable();
            $table->decimal('price_max', 12, 2)->nullable();
            $table->integer('moq')->nullable();
            $table->integer('lead_time_days')->nullable(); // production lead time
            $table->string('location')->nullable(); // factory/supplier location
            $table->enum('status', ['draft', 'pending_approval', 'live', 'disabled'])->default('draft');
            $table->boolean('is_platform_product')->default(false); // admin-created, not tied to single factory
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
