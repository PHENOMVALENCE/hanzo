<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('company_name')->nullable()->after('phone');
            $table->string('country')->nullable()->after('company_name');
            $table->string('city')->nullable()->after('country');
            $table->enum('status', ['pending', 'approved', 'suspended'])->default('pending')->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'company_name', 'country', 'city', 'status']);
        });
    }
};
