<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN milestone_status VARCHAR(50) NOT NULL DEFAULT 'awaiting_factory_approval'");
        }

        // Map old statuses to new simplified flow
        DB::table('orders')->whereIn('milestone_status', ['deposit_pending', 'deposit_paid'])->update(['milestone_status' => 'awaiting_factory_approval']);
        DB::table('orders')->whereIn('milestone_status', ['quality_control', 'shipped', 'in_customs'])->update(['milestone_status' => 'ready_to_ship']);
        DB::table('orders')->where('milestone_status', 'delivered')->update(['milestone_status' => 'completed']);
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN milestone_status ENUM('deposit_pending', 'deposit_paid', 'in_production', 'quality_control', 'shipped', 'in_customs', 'delivered') NOT NULL DEFAULT 'deposit_pending'");
        }
    }
};
