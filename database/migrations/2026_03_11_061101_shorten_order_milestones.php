<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')
            ->where('milestone_status', 'quality_control')
            ->update(['milestone_status' => 'in_production']);

        DB::table('orders')
            ->where('milestone_status', 'in_customs')
            ->update(['milestone_status' => 'shipped']);

        DB::statement("ALTER TABLE orders MODIFY milestone_status ENUM('deposit_pending','deposit_paid','in_production','shipped','delivered') NOT NULL DEFAULT 'deposit_pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY milestone_status ENUM('deposit_pending','deposit_paid','in_production','quality_control','shipped','in_customs','delivered') NOT NULL DEFAULT 'deposit_pending'");
    }
};
