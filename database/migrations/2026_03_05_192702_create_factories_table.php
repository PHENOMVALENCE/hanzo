<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('factory_name')->nullable(); // admin-only
            $table->string('location_china')->nullable(); // admin-only
            $table->json('categories')->nullable();
            $table->string('contact_wechat')->nullable(); // admin-only
            $table->string('contact_phone')->nullable(); // admin-only
            $table->string('contact_email')->nullable(); // admin-only
            $table->string('verification_status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factories');
    }
};
