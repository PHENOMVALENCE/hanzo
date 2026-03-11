<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transport_defaults', function (Blueprint $table) {
            $table->id();
            $table->string('method', 20)->unique(); // sea, air
            $table->decimal('base_min', 12, 2)->default(200);
            $table->decimal('per_unit_min', 12, 4)->default(0.05);
            $table->decimal('base_max', 12, 2)->default(800);
            $table->decimal('per_unit_max', 12, 4)->default(0.1);
            $table->timestamps();
        });

        DB::table('transport_defaults')->insert([
            ['method' => 'sea', 'base_min' => 200, 'per_unit_min' => 0.05, 'base_max' => 800, 'per_unit_max' => 0.1, 'created_at' => now(), 'updated_at' => now()],
            ['method' => 'air', 'base_min' => 400, 'per_unit_min' => 0.15, 'base_max' => 1500, 'per_unit_max' => 0.3, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_defaults');
    }
};
