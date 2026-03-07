<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('first_login_at')->nullable()->after('status');
            $table->text('approval_message')->nullable()->after('first_login_at');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->decimal('price_min_per_unit', 12, 2)->nullable()->after('moq_default');
            $table->decimal('price_max_per_unit', 12, 2)->nullable()->after('price_min_per_unit');
        });

        Schema::table('rfqs', function (Blueprint $table) {
            $table->decimal('target_price_per_unit', 12, 2)->nullable()->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_login_at', 'approval_message']);
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['price_min_per_unit', 'price_max_per_unit']);
        });
        Schema::table('rfqs', function (Blueprint $table) {
            $table->dropColumn('target_price_per_unit');
        });
    }
};
