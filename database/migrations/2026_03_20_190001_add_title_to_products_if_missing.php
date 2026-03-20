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

        if (! Schema::hasColumn('products', 'title')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('title')->after('category_id')->default('Untitled');
            });

            // Populate title from name for existing rows
            \DB::statement('UPDATE products SET title = COALESCE(name, "Untitled")');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'title')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('title');
            });
        }
    }
};
