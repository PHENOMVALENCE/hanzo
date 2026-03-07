<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('original_name')->nullable()->after('file_path');
            $table->text('description')->nullable()->after('type');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('verified_at');
            $table->text('admin_notes')->nullable()->after('rejection_reason');
            $table->string('mime_type', 100)->nullable()->after('proof_path');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['original_name', 'description']);
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['rejection_reason', 'admin_notes', 'mime_type']);
        });
    }
};
