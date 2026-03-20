<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'created_by_admin_id')) {
                $table->foreignId('created_by_admin_id')->nullable()->after('category_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('products', 'specs')) {
                $table->json('specs')->nullable()->after('description');
            }

            if (! Schema::hasColumn('products', 'images')) {
                $table->json('images')->nullable()->after('specs');
            }

            if (! Schema::hasColumn('products', 'lead_time_days')) {
                $table->integer('lead_time_days')->nullable()->after('moq');
            }

            if (! Schema::hasColumn('products', 'location')) {
                $table->string('location')->nullable()->after('lead_time_days');
            }

            if (! Schema::hasColumn('products', 'is_platform_product')) {
                $table->boolean('is_platform_product')->default(false)->after('status');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'created_by_admin_id')) {
                $table->dropForeign(['created_by_admin_id']);
                $table->dropColumn('created_by_admin_id');
            }

            if (Schema::hasColumn('products', 'specs')) {
                $table->dropColumn('specs');
            }

            if (Schema::hasColumn('products', 'images')) {
                $table->dropColumn('images');
            }

            if (Schema::hasColumn('products', 'lead_time_days')) {
                $table->dropColumn('lead_time_days');
            }

            if (Schema::hasColumn('products', 'location')) {
                $table->dropColumn('location');
            }

            if (Schema::hasColumn('products', 'is_platform_product')) {
                $table->dropColumn('is_platform_product');
            }
        });
    }
};
